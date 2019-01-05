<?php

namespace App\Channels;

use App\Services\EngageSpark;
use Illuminate\Notifications\Notification;

class EngageSparkChannel
{
   /** @var EngageSpark */
    protected $smsc;

    public function __construct(EngageSpark $smsc)
    {
        $this->smsc = $smsc;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     *
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (! ($to = $this->getRecipients($notifiable, $notification))) {
            return;
        }

        $message = $notification->{'toEngageSpark'}($notifiable);

        if (\is_string($message)) {
            $message = new EngageSparkMessage($message);
        }

        $this->sendMessage($to, $message);
    }

    /**
     * Gets a list of phones from the given notifiable.
     *
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     *
     * @return string[]
     */
    protected function getRecipients($notifiable, Notification $notification)
    {
        $to = $notifiable->routeNotificationFor('engage_spark', $notification);

        if ($to === null || $to === false || $to === '') {
            return [];
        }

        return \is_array($to) ? $to : [$to];
    }

    protected function sendMessage($recipients, EngageSparkMessage $message)
    {
        // if (\mb_strlen($message->content) > 800) {
        //     throw CouldNotSendNotification::contentLengthLimitExceeded();
        // }

        $params = [
            'sms' => [
                'mobile_numbers'  => $recipients,
                'message'         => $message->content,
                'recipient_type'  => $message->recipient_type,                
            ],
            'airtime' => [
                'phoneNumber'     => '639081877788',
                'maxAmount'       => '10',
                'apiToken'        => 'b3867ab758b3fea05a4f40124e0e4f52c399ed12',
                'clientRef'       => $message->generateClientReference(),
                'resultsUrl'      => 'https://75c57b3e.ngrok.io/webhook/sms'
            ],
            // 'sender_id'  	  => $message->from,
        ];

        // if ($message->sendAt instanceof \DateTimeInterface) {
        //     $params['time'] = '0'.$message->sendAt->getTimestamp();
        // }
        // dd($params[$message->mode]);
        $this->smsc->send($params[$message->mode], $message->mode);
    }
}