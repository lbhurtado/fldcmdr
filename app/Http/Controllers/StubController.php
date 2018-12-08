<?php

namespace App\Http\Controllers;

use App\SMS;
use Illuminate\Http\Request;
use \Log;

class StubController extends Controller
{
    public function handle(Request $request)
    {
    	if ($request->secret == env('TELERIVET_WEBHOOK_SECRET')) {
    		if ($request->event == 'incoming_message') {
    			// Log::info($request);
    			SMS::create($request->only([
    				'simulated',
    				'from_number',
    				'to_number',
    				'message_type',
    				'direction',
    				'content',
    				'time_created',
    				'time_sent',
    				])
    			);

    			return response(env('APP_NAME'), 200);	
    		}
    	}

    	return response(env('APP_NAME'), 401);
    }
}
