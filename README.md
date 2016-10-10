# Mass Mailer Package for Laravel

Sending mass mails in your Laravel applications with ease. This is a wrapper for different mail service providers.

> Currently supports the sending of mass mails using Laravel's default Mail facade and [Mailgun's Official SDK](https://github.com/mailgun/mailgun-php)

> It is recommended to use third party mail service such as Mailgun instead of depending on Laravel's default Mail facade because with Laravel's Mail facade, in order to protect the privacy of all subscribers, the field "BCC" is being used instead of "TO"

### Features Included
  - Sending mass emails with queue, without bogging down your app's web request speed
  - Good for apps that need to send mass mails using different Mailgun domains (able to pass in custom domain to overwrite default domain)
  - Privacy of subscribers are properly handled ( without having their emails being exposed to one another in the "TO" field )
  - Pulling post-delivery report (e.g. number of clicks, opens, bounces, unsubscribes, complains, delivers, drops etc.)
  - Save and retrieve mass mail drafts
  - Retrieve the list of subscribers from 3rd party email service providers, such as Mailgun
  - Provide an API call for your frontend application to pull attributes (e.g. information on each of the HTML elements such as _Title_ field, _Message_ field etc.) for you to build up the User Interface. 
  - Parse all information coming from frontend application with ease (you only need to pass in the _$request_ object into a specific method that will parse and build up an appropriate object to be used within this package)

## Installation

Add this package to your project's `composer.json` file by running the following command

```
composer require simmatrix/laravel-mass-mailer
```

Add both of the service providers below to your `config/app.php` file, in the `providers` array.

```
Simmatrix\MassMailer\Providers\MassMailerServiceProvider::class,
```

Publish the config file and blade view template file to your application

```
php artisan vendor:publish --provider="Simmatrix\MassMailer\Providers\MassMailerServiceProvider"
```

## Configuration

Open `config/mass_mailer.php`, then set your default mailing list address (e.g. You may key in the alias address of the mailing list which you have created in Mailgun)

```
'mailing_list' => 'test',
```

In the same file, do fill in the section `Admin Email Address`, `Queue Name (optional)` as well.

Make sure that in your `.env` file, the value of `QUEUE_DRIVER` is NOT `sync` as this will disable all queues. Any values other than `sync` is acceptable
```
QUEUE_DRIVER=database
```

Also do make sure that you have properly setup your `config/mail.php`, particularly the section `Global "From" Address`, as this will be used by this package when blasting off your mass mails to your recipients
```
'from' => [
    'address' => 'hello@example.com',
    'name' => 'Example',
],
```    

### Running Queue in your server

Run the following command to listen to and execute any incoming queued jobs
```
sudo nohup php artisan queue:work --daemon >> storage/logs/laravel.log &
```

Whenever you make changes to your code or deploy to your server, you would need to run
```
php artisan queue:restart
```

## Usage

### Sending Mass Emails
In your controller, you would first need to pass in the `$request` parameter into `MassMailer::getParams()`, this will generate a digestible object that can be used in `MassMailer::send()`.

```
public function send(Request $request)
{
    MassMailer::send( MassMailer::getParams( $request ) );      
}
```

### Sending Mass Emails (With custom parameters to overwrite default values)

Currently supports the overwriting of 3 custom parameters, `mailingList`, `mailgunDomain`, and `presenterClassName`

```
public function send(Request $request)
{
	$custom_params = MassMailer::createCustomParams([
		'mailingList' => 'xxx@xxx.com',
		'mailgunDomain' => 'xxx@xxx.com',
		'presenterClassName' => App\MassMailer\Presenters\YourCustomPresenter::class
	]);
    MassMailer::send( MassMailer::getParams( $request ), $custom_params );      
}
```

### Creating Your Custom Presenters

This class holds all the parameters that you intended to pass them to your blade view template. You can easily generate it using the artisan command as below.
```
php artisan make:mass-mailer-presenter YourCustomPresenter
```

Specify the name of your blade view template, which is good to be placed in your app's `resources/views/vendor/simmatrix/mass-mailer/xxx.blade.php`
```
public function getTemplate()
{
	return 'vendor.simmatrix.mass-mailer.default';
}
```

Start pumping in all of the custom parameters that you wish to pass it to your blade view template!
```
private function setParameters( MassMailerParams $params )
	parent::setViewParameters([
		'lorem' => 'ipsum',
		'testing' => 'success'
	]);
}
```
### Retrieving the Attributes

Here's the sample [JSON result](https://github.com/simmatrix/laravel-mass-mailer/blob/master/src/sample-attribute-endpoint-data.json) returned from calling the following method, which you can used to build up the User Interface of your frontend application.
```
return MassMailer::getAttributes();
```

### Creating Your Custom Attributes

Default attributes that comes with the package are:
  - "Subject" Field
  - "Title" Field
  - "Sender Name" Field
  - "Sender Email" Field
  - "Recipient List" Field
  - "Message Content" Field
  - "Apply Template" Option
  - "Send to All Subscribers" Option

> The purpose of creating an attirbute to represent your HTML fields is so that it can be easily parsed and read by this package when it returned from your frontend application.

If you would like to add an additional field in your frontend application, then you can generate it using the artisan command as below.

```
php artisan make:mass-mailer-attribute YourCustomAttribute
```


### Getting Post-Delivery Report
Details such as the number of bounces, clicks, complains, deliveries, drops, opens, submits, unsubscribes can be obtained by calling this method.
```
return MassMailer::getReport();
```

### Saving Draft
You may save up the draft by passing the `$request` parameter into the `MassMailer::saveDraft()`.
```
MassMailer::saveDraft( $request );
```

### Retrieving Draft
You may retrieve all of the drafts by calling this method.
```
return MassMailer::getDrafts();
```
You may retrieve individual draft by passing an ID into the method below.
```
return MassMailer::getDraft( $id );
```

### Retrieving the Subscribers
You may retrieve all of the subscribers by calling this method.
```
return MassMailer::getSubscribers();
```

License
----
MIT