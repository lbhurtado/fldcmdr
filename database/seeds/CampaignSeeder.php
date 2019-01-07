<?php

use App\Campaign;
use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('campaigns')->delete();

        $campaigns = [
            [
                'name'     => 'ABC123',
                'message'  => implode("\n", [
                    'Welcome! Please check out this link: https://youtu.be/3yQ1T-uUhjA',
                    'Please download Telegram from https://t.me',
                    'Join the campaign thru https://t.me/grassroots_bot',

                ]),
                'extra_attributes'  => [
                    'air_time' => 10,
                ],
            ],
            [
                'name'     => 'DEF456',
                'message'  => '...jumps over the lazy dog.',
                'extra_attributes'  => [
                    'air_time' => 20,
                ],
            ],
        ];

        foreach ($campaigns as $campaign) {
            Campaign::create($campaign);
        }

    }
}
