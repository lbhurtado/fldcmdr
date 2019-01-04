<?php

use Illuminate\Validation\Rule;
use App\Channels\{EngageSparkChannel, TelerivetChannel};
use NotificationChannels\Twilio\TwilioChannel;
use App\Notifications\VerifiedAirTimeTransfer;

return [
    'reward' => [
        'enabled' => env('REWARD_ENABLED', true),
        'amount' => env('REWARD_AMOUNT', 25),
    ],
    'links' => [ 
            'messenger' => [
            'Telegram' => 'http://t.me/grassroots_bot',
            'Facebook' => 'http://m.me/dyagwarbot',
        ],
    ],
    'rules' => [
        'age' => 'integer|min:18|max:100',
        'sex' => 'in:male,female',
        'gender' => 'in:male,female',
        'status' => 'in:single,married,separated,widowed',
        'eyes' => 'in:brown,blue,green,others',
        'hair' => 'in:black,brown,red,others',
        'education' => 'in:elementary,high-school,college,post-grad',
        'zip' => 'digits:4',
    ],
    // 'tasks' => [
    //     'test' => [
    //         ['title' => 'Task 1'],
    //         ['title' => 'Task 2'],
    //         ['title' => 'Task 3'],
    //     ],
    //     'admin' => [
    //         ['title' => 'Activity - Recruit 15 operators'],
    //     ],
    //     'operator' => [
    //         ['title' => 'Activity - Read the manual', 'rank' => '1'],
    //         ['title' => 'Activity - Recruit 15 workers', 'rank' => '2'],
    //         ['title' => 'Activity - Recruit 15 staff', 'rank' => '3'],
    //     ],
    //     'staff' => [
    //         ['title' => 'Activity - Read the manual', 'rank' => '1'],
    //         ['title' => 'Activity - Recruit 15 voters', 'rank' => '2'],
    //     ],
    //     'subscriber' => [
    //         ['title' => 'Activity - Read the manual', 'rank' => '1'],
    //         ['title' => 'Activity - Recruit 15 voters', 'rank' => '2'],
    //     ],
    //     'worker' => [
    //         ['title' => 'Activity - Read the manual', 'rank' => '1', 'instructions' => 'Start from page 1.'],
    //         ['title' => 'Activity - Register', 'rank' => '2'],
    //         ['title' => 'Activity - Verify BEI Composition', 'rank' => '3'],
    //         ['title' => 'Witness - Ballot Box Seal', 'rank' => '4'],
    //         ['title' => 'Witness - Zero Votes Print-Out', 'rank' => '5'],
    //         ['title' => 'Activity - Vote', 'rank' => '6'],
    //         ['title' => 'Witness - Election Return Print-Out', 'rank' => '6'],
    //         ['title' => 'Witness - Election Return Trasmission', 'rank' => '7'],
    //         ['title' => 'Activity - Poll Count', 'rank' => '8'],
    //     ],
    // ],
    'permissions' => [
        'admin'      => ['send reward', 'broadcast message'],
        'operator'   => ['send reward', 'broadcast message'],
        'staff'      => ['accept reward'],
        'worker'     => ['send reward', 'accept reward'],
        'subscriber' => ['accept reward'],
    ],
    'tap_zone' => [
        'distance' => env('TAP_ZONE_DISTANCE', 5),
    ],
    'survey' => [
        'location' => env('SURVEY_LOCATION', true),
    ],
    'notification' => [
        'channels' => [
            'database',
            EngageSparkChannel::class,
            // TwilioChannel::class,
            // TelerivetChannel::class,
        ],
    ],
    'default' => [
        'password' => env('DEFAULT_PASSWORD', '1234'),
        'domain_name' => env('DEFAULT_DOMAIN_NAME', 'serbis.io'),
    ],
    'seed' => [
        'survey' => [
            'reward' => env('SEED_SURVEY_REWARD', 0),
        ],
    ],
    'verify' => [
        'reward' => [
            'enabled' => env('VERIFY_REWARD_ENABLED', false),
        ],
    ],
    'campaigns' => [
        'verified' => [
            'service_id' => env('CAMPAIGN_VERIFIED', 'SVa8cc328a77a0db75'),
            'notification' => VerifiedAirTimeTransfer::class,
        ],
    ],
    'webhook' => [
        'sms' => [
            'secret' => env('SMS_WEBHOOK_SECRET', '87-18618_87-39312'),
        ],
    ],
];
