<?php

namespace App\Http\Controllers;

use App\SMS;
use App\Http\Requests\SMSWebHookRequest;

class SMSWebHookController extends Controller
{
	public function handle(SMSWebHookRequest $request)
	{
		\Log::info($request);

		// SMS::create($request->toArray());

	    return response(env('APP_NAME'), 200);				
	}
}
