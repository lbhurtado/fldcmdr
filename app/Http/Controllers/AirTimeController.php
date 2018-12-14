<?php

namespace App\Http\Controllers;

use App\AirTime;
use Illuminate\Http\Request;

class AirTimeController extends Controller
{
    public function handle(Request $request)
    {
        \Log::info($request);
        //need to check if csrf is disabled in this route
    	if ($request->secret == env('TELERIVET_WEBHOOK_SECRET')) {
            switch ($request->event) {
                case 'default':
                	tap(AirTime::create([
                		'mobile' => $request->contact['phone_number'],
                	]), function($airtime) use ($request) {
                		$airtime->extra_attributes = [
                			'telerivet' => [
                				'contact' => $request->contact,
                				'state' => $request->state,
                			],
                		];
                		$airtime->campaign = $request->campaign;
                		$airtime->save();
                	});

                    return response(env('APP_NAME'), 200);
                
                default:
                    # code...
                    break;
            }
    	}

    	return response(env('APP_NAME'), 401);
    }
}

//verified service id = SVa8cc328a77a0db75
// curl -s -u DqG7x_2D6sNvwE5oL9kZB1zrDO3J4i8qRUBL: \
//  "https://api.telerivet.com/v1/projects/PJf3e398e4fb9f4a07/messages/SMb77fc2471e0dab6e"