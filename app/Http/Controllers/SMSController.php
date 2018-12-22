<?php

namespace App\Http\Controllers;

use App\SMS;
use Illuminate\Http\Request;

class SMSController extends Controller
{
    public function handle(Request $request)
    {
        \Log::info($request);

        //need to check if csrf is disabled in this route
    	if ($request->secret == env('TELERIVET_WEBHOOK_SECRET')) {
            switch ($request->event) {
                case SMS::INCOMING:
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
                
                default:
                    # code...
                    break;
            }
    	}

    	return response(env('APP_NAME'), 401);
    }

    // public function engagespark(Request $request)
    // {
    //     \Log::info($request);
    // }

    public function tag()
    {
        
    }   
}
