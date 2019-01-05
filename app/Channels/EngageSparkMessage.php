<?php

namespace App\Channels;

class EngageSparkMessage
{
    /**
     * The mode.
     *
     * @var string
     */
    public $mode = 'sms'; //sms or topup

    /**
     * The air_time.
     *
     * @var double
     */
    public $air_time;

    /**
     * The phone number the message should be sent from.
     *
     * @var string
     */
    public $from = '';

    /**
     * The recipient type.
     *
     * @var string
     */
    public $recipient_type = 'mobile_number'; //mobile_number or contact_id

    /**
     * The message content.
     *
     * @var string
     */
    public $content;

    /**
     * Create a message object.
     * @param string $content
     * @return static
     */
    public static function create($content = '')
    {
        return new static($content);
    }

    /**
     * Create a new message instance.
     *
     * @param  string $content
     */
    public function __construct($content = '')
    {
        $this->content = $content;
    }

    public function mode($mode)
    {
        $this->mode = $mode;
        
        return $this;
    }

    public function transfer($amount)
    {
        $this->air_time = $amount;
        
        return $this;
    }

    /**
     * Set the message content.
     *
     * @param  string $content
     * @return $this
     */
    public function content($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Set the phone number or sender name the message should be sent from.
     *
     * @param  string  $from
     *
     * @return $this
     */
    public function from($from)
    {
        $this->from = $from;
        
        return $this;
    }

    public function recipient_type($recipient_type)
    {
        $this->recipient_type = $recipient_type;
        
        return $this;
    }

    public function generateClientReference()
    {
        return str_random(12);
    }
}