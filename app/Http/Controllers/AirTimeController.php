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
                	$this->persistAirTimeTransfer($request);

                    return response(env('APP_NAME'), 200);
                default:
                    # code...
                    break;
            }
    	}

    	return response(env('APP_NAME'), 401);
    }

    protected function persistAirTimeTransfer($request)
    {
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
    }
}