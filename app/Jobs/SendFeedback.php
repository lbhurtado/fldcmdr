<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Notifications\UserFeedback;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendFeedback implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $sociable;
    
    private $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($sociable, $message)
    {
        $this->sociable = $sociable;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->sociable->notify(new UserFeedback($this->message));
    }
}
