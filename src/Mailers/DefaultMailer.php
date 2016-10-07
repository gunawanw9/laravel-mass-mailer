<?php

namespace Simmatrix\MassMailer\Mailers;

use Simmatrix\MassMailer\Interfaces\MassMailerInterface;
use Simmatrix\MassMailer\ValueObjects\MassMailerParams;
use Mail;
use Log;

class DefaultMailer implements MassMailerInterface
{
    /**
     * Send a new message.
     *
     * @param Simmatrix\MassMailer\ValueObjects\MassMailerParams  $params An object holding all data needed for the delivery of email
     *
     * @return Boolean To indicate whether the delivery is successful or not
     */	
	public function send( MassMailerParams $params, $callback )
	{
		Mail::send( $params -> viewTemplate, $params -> viewParameters, function( $message ) use( $params, $callback ){
            $message -> to( $params -> recipientList ) -> subject( $params -> subject );
            $message -> from( $params -> senderEmail, $params -> senderName );
            $message -> replyTo( config('mail.from.address'), config('mail.from.name') );
            $callback();
        });

        if( count( Mail::failures() ) > 0 ) {

            Log::error( "One or more errors occured during the email delivery." );

            $failed_emails = [];
            foreach( Mail::failures() as $email_address ){
                $failed_emails[] = $email_address;
            }

            Log::error('Emails affected: ' . json_encode($failed_emails));

            return FALSE;

        } else {

            return TRUE;

        }
	}
}