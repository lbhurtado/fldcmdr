<?php

namespace App\Traits;

use App\Answer;
use App\Jobs\SendAskableReward;

trait Askable
{
	public function answers()
	{
		return $this->morphMany(Answer::class, 'askable');
	}

    public function sendReward($reward)
    {
        SendAskableReward::dispatch($this, $reward);

        return $this;
    }
}
