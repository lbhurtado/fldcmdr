<?php

namespace App\Conversations;

use App\{User, TapZone};
use BotMan\BotMan\BotMan;
use App\Eloquent\Conversation;
use App\Http\Controllers\FldCmdrController;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Attachments\Location;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;

class Checkin extends Conversation
{   
    protected $longitude;

    protected $latitude;

    protected $messenger;

    public function ready()
    {
        $this->messenger = $this->getMessenger();

        $this->introduction()->inputLocation();
    }

    protected function introduction()
    {
    	$this->bot->reply(trans('checkin.introduction'));

    	return $this;
    }

    protected function inputLocation()
    {
        return $this->askForLocation(trans('checkin.input.location'), function (Location $location) {
            $this->latitude = $location->getLatitude();
            $this->longitude = $location->getLongitude();

            return $this->inputRemarks();
        });   
    }

    protected function inputRemarks()
    {
        $question = Question::create(trans('checkin.input.remarks'))
            ->fallback(trans('checkin.input.error'))
            ->callbackId('checkin.input.remarks')
            ;

        return $this->ask($question, function (Answer $answer) {
            $this->remarks = trim($answer->getText());

            return $this->process();
        });
    }

    protected function process()
    {
        $this->bot->reply(trans('checkin.processing'));  

        $lon = $this->longitude;
        $lat = $this->latitude;
        $remarks = $this->remarks;
        tap($this->messenger->getUser()->checkin($lon, $lat), function ($checkin) use ($remarks) {
            $checkin
                ->reverseGeocode()
                ->hydrateUserFromTapZone()
                ->forceFill(compact('remarks'));            
        })->save();

        $this->bot->reply(trans('checkin.processed'));

        if (preg_match('/#(\w+)/', $remarks, $matches)) {
            $keyword = strtolower($matches[1]);
            switch ($keyword) {
                case 'fence':
                    return app(FldCmdrController::class)->fence($this->bot);
                default:
                    # code...
                    break;
            }            
        }
    }
}




