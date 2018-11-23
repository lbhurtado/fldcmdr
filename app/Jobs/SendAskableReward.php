<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Notifications\AskableReward;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendAskableReward implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $model;

    protected $reward;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($model, $reward)
    {
        $this->model = $model;

        $this->reward = $reward;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->model->notify(new AskableReward($this->reward));
    }
}
