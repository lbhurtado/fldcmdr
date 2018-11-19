<?php

namespace App\Channels;

use App\Services\Telerivet;
use Illuminate\Notifications\Notification;

class TelerivetChannel
{
    private $api;

    public function __construct(Telerivet $telerivet)
    {
        $this->api = $telerivet;
    }

    public function send($notifiable, Notification $notification)
    {
        if (! $notifiable->routeNotificationFor('telerivet')){
            $notifiable->refresh();
            if (! $notifiable->routeNotificationFor('telerivet')){
                $notifiable->registerTelerivet();
                sleep(2);
                $notifiable->refresh();
                if (! $notifiable->routeNotificationFor('telerivet')){
                    $notifiable->registerTelerivet();
                    sleep(2);
                }
            }
        }

        $message = $notification->toTelerivet($notifiable);
        if ($message->load)
            $this->getAPI()->getService()->invoke($this->getArguments($notifiable, $message));
        else
            $this->getAPI()->getProject()->sendMessage($this->getArguments($notifiable, $message));

        return true;
    }

    public function getArguments($notifiable, $message)
    {
        $retval['context'] = 'contact';
        $retval['content'] = $message->content;
        $telerivet_id = $notifiable->routeNotificationFor('telerivet');
        if ($telerivet_id)
            $retval['contact_id'] = $telerivet_id;
        $retval['to_number'] = $notifiable->mobile;

        return $retval;
    }

    protected function getAPI()
    {
        return $this->api;
    }
}