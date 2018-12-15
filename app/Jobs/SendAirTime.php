<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Notifications\AirTimeTransfer;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendAirTime implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $model;

    private $campaign;

    public function __construct($model, $campaign)
    {
        $this->model = $model;
        $this->campaign = $campaign;
    }

    public function handle()
    {
        $this->model->notify(AirTimeTransfer::invoke($this->campaign));
    }
}
