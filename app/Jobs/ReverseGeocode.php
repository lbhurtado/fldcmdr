<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ReverseGeocode implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $checkin;

    public function __construct($checkin)
    {
        $this->checkin = $checkin;
    }

    public function handle()
    {
        $location = $this->getGeoCode()['formatted_address'];
        $accuracy = $this->getGeoCode()['accuracy'];

        $this->checkin->forceFill(compact('location', 'accuracy'))->save();
    }

    protected function getGeoCode()
    {
        return \Geocoder::getAddressForCoordinates(
            $this->checkin->latitude, 
            $this->checkin->longitude
        );
    }
}
