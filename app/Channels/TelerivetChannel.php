<?php

namespace App\Channels;

use App\Services\Telerivet;
use Illuminate\Notifications\Notification;

class TelerivetChannel
{
    private $api;

    private $notifiable;

    private $notification;

    private $message;

    private $isCampaign;

    public function __construct(Telerivet $telerivet)
    {
        $this->api = $telerivet;
    }

    public function send($notifiable, Notification $notification)
    {
        $this->setup($notifiable, $notification)->makeRoute()->setMessage()->checkCampaign();

        $retval = $this->getIsCampaign() 
                    ? $this->invokeService()
                    : $this->sendMessage()
                    ;

        return $retval;
    }

    protected function setup($notifiable, $notification)
    {
        $this->notifiable = $notifiable;
        $this->notification = $notification;

        return $this;
    }

    public function getArguments()
    {
        $retval['context'] = 'contact';
        $retval['content'] = $this->message->content;
        $telerivet_id = $this->notifiable->routeNotificationFor('telerivet');
        if ($telerivet_id)
            $retval['contact_id'] = $telerivet_id;
        $retval['to_number'] = $this->notifiable->mobile;

        return $retval;
    }

    protected function getAPI()
    {
        return $this->api;
    }

    protected function makeRoute()
    {
        $notifiable = $this->notifiable;

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

        return $this;
    }

    protected function setMessage()
    {
        $this->message = $this->notification->toTelerivet($this->notifiable);

        return $this;
    }

    protected function checkCampaign()
    {
        $this->isCampaign = $this->message->isCampaign();

        return $this;
    }

    protected function getIsCampaign()
    {
        return $this->isCampaign;
    }

    protected function invokeService()
    {
        $campaign = $this->message->getCampaign();

        return $this->getAPI()->setCampaign($campaign)->getService()->invoke($this->getArguments());
    }

    protected function sendMessage()
    {
        return $this->getAPI()->getProject()->sendMessage($this->getArguments());
    }
}