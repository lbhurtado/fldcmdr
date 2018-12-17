<?php

namespace App\Jobs;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Notifications\DownlineVerified;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class VerificationOfDownline implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $upline;

    private $downline;

    public function __construct(User $upline, User $downline)
    {
        $this->upline = $upline;
        $this->downline = $downline;
    }

    public function handle()
    {
        $this->upline->notify(new DownlineVerified($this->downline));
    }
}
