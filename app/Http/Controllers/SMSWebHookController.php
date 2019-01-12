<?php

namespace App\Http\Controllers;

use App\SMS;

use Illuminate\Http\Request;
// use App\Http\Requests\SMSWebHookRequest;

class SMSWebHookController extends Controller
{
	public function handle(Request $request)
	{
		\Log::info($request);

		optional(SMS::create($request->toArray()), function ($sms) {
			
		});

	    return response(env('APP_NAME'), 200);				
	}
}
