<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\{CampaignMessage, CampaignAirTimeTransfer};

class SendCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $sociable;
    
    private $campaign;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($sociable, $campaign)
    {
        $this->sociable = $sociable;
        $this->campaign = $campaign;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->sociable->notify(new CampaignMessage($this->campaign));
        if ($this->campaign->isLoadable())
            $this->sociable->notify(new CampaignAirTimeTransfer($this->campaign));
    }
}
