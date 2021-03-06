<?= '<?php' ?>


namespace {{ $namespace }};

use Simmatrix\MassMailer\Attributes\MassMailerAttributeAbstract;
use Simmatrix\MassMailer\Interfaces\MassMailerAttributeInterface;
use Simmatrix\MassMailer\ValueObjects\MassMailerAttributeParams;

class {{ $class_name }} extends MassMailerAttributeAbstract implements MassMailerAttributeInterface
{
    /**
     * To return the key-value item that represent this attribute
     *
     * @return Array [ (class_name) => (default_value) ]
     */
    public function get()
    {
        return parent::getParam( $this @if( $has_value ), $default_value = @if( $is_boolean || $is_integer ) {{ $default_value }} @else '{{ $default_value }}' @endif @endif);
    }

    /**
     * To get internally generated value
     *
     * Let's say user returns the param { "IncludeInstagram": true }, then over here you can code to call to the Instagram API to pull images
     *
     * @return Any data which you intend to generate internally, by default returns FALSE
     */
    public function getValue()
    {
        return parent::getValue();
    }   
}