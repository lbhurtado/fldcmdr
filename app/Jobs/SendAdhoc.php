<?php

namespace App\Jobs;

use App\Contracts\Sociable;
use Illuminate\Bus\Queueable;
use App\Notifications\UserBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendAdhoc implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $commander;

    private $sociable;

    private $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Sociable $commander, Sociable $sociable, $message)
    {
        $this->commander = $commander;
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
        $this->sociable->notify(new UserBroadcast($this->commander, $this->message));
    }
}
