<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Notifications\UserInvitation;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendUserInvitation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $model;

    protected $driver;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($model, $driver)
    {
        $this->model = $model;

        $this->driver = $driver;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->model->notify(new UserInvitation($this->driver));
    }
}
