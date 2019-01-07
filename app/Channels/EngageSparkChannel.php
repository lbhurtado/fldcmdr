<?php

namespace App\Channels;

use App\Services\EngageSpark;
use Illuminate\Notifications\Notification;

class EngageSparkChannel
{
    /** @var string */
    private $mode;

    /** @var string */
    private $clientRef;

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

        $this->setClientRef($notification)->sendMessage($to, $message);
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

        // $params = [
        //     'sms' => [
        //         'mobile_numbers'  => $recipients,
        //         'message'         => $message->content,
        //         'recipient_type'  => $message->recipient_type,                
        //     ],
        //     'topup' => [
        //         'phoneNumber'     => array_first($recipients),//'639081877788',
        //         'maxAmount'       => $message->air_time,
        //         'apiToken'        => $this->getApiToken(),
        //         'clientRef'       => $this->getClientRef(),
        //         'resultsUrl'      => $this->getWebHook($message),
        //     ],
        // ];
        $this->setMode($message);

        switch ($mode = $this->getMode()) {
            case 'sms':
                $params = [
                    'organization_id' => $this->getOrgId(),
                    'mobile_numbers'  => $recipients,
                    'message'         => $message->content,
                    'recipient_type'  => $message->recipient_type,                
                ];
                break;
            
            case 'topup':
                $params = [
                    'organizationId'  => $this->getOrgId(),
                    'phoneNumber'     => array_first($recipients),//'639081877788',
                    'maxAmount'       => $message->air_time,
                    'apiToken'        => $this->getApiToken(),
                    'clientRef'       => $this->getClientRef(),
                    'resultsUrl'      => $this->getWebHook($message),
                ];
                break;

            default:
                # code...
                break;
        }

        // if ($message->sendAt instanceof \DateTimeInterface) {
        //     $params['time'] = '0'.$message->sendAt->getTimestamp();
        // }

        $this->smsc->send($params, $mode);
    }

    protected function getWebHook(EngageSparkMessage $message)
    {
        return $this->smsc->getWebHook($message->mode);
    }

    protected function getOrgId()
    {
        return $this->smsc->getOrgId();
    }

    protected function getApiToken()
    {
        return $this->smsc->getApiKey();
    }

    protected function getClientRef()
    {
        return $this->clientRef;
    }

    protected function setClientRef(Notification $notification)
    {
        $this->clientRef = $notification->id;

        return $this;
    }

    protected function getMode()
    {
        return $this->mode;
    }

    protected function setMode(EngageSparkMessage $message)
    {
        $this->mode = $message->mode;

        return $this;
    }
}