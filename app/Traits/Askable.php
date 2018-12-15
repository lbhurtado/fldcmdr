<?php

namespace App\Traits;

use App\Answer;
use App\Jobs\SendAirTime;
use App\Jobs\SendAskableReward;

trait Askable
{
	public function answers()
	{
		return $this->morphMany(Answer::class, 'askable');
	}

	public function sendAirTime($campaign)
	{
		SendAirTime::dispatch($this, $campaign);

		return $this;
	}

    public function sendReward($reward)
    {
        SendAskableReward::dispatch($this, $reward);

        return $this;
    }
}
