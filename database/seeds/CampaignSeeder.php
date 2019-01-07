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
                'name'     => 'regular',
                'message'  => implode("\n", [
                    'Isang MasigaBONG BaGOng Taon mula kay KUYA BONG GO!',
                    'Ang 2019 ay isa na namang taon para mas maipaabot ang serbisyong Tatak Duterte sa mga Pilipino. #GOPhilippinesGO',
                    'Bumisita sa FB page: https://www.facebook.com/bongGOma/ para mas makilala si Kuya Bong Go!',

                ]),
                'extra_attributes'  => [
                    'air_time' => 0,
                ],
            ],
            [
                'name'     => 'special',
                'message'  => 'Salamat sa iyong suporta.',
                'extra_attributes'  => [
                    'air_time' => 10,
                ],
            ],
        ];

        foreach ($campaigns as $campaign) {
            Campaign::create($campaign);
        }

    }
}
