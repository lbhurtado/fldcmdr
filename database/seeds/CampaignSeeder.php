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
                'message'  => 'Salamat sa iyong suporta. - Levi at Malot',
                'extra_attributes'  => [
                    'air_time' => 10,
                ],
            ],
            [
                'name'     => 'lootbag',
                'message'  => implode("\n", [
                    'Nanalo ka!',
                    'Isa ka sa maswerteng napili para makatanggap ng grocery items.',
                    'Ipakita lang ang mensaheng ito sa namumuno upang makuha ang iyong premyo.',

                ]),
                'extra_attributes'  => [
                    'air_time' => 0,
                ],
            ],
            [
                'name'     => 'lootbags',
                'message'  => implode("\n", [
                    'Nanalo ka!',
                    'Isa ka sa maswerteng napili para makatanggap ng grocery items.',
                    'Ipakita lang ang mensaheng ito sa namumuno upang makuha ang iyong premyo.',

                ]),
                'extra_attributes'  => [
                    'air_time' => 0,
                ],
            ],
            [
                'name'     => 'load10',
                'message'  => implode("\n", [
                    'Nanalo ka!',
                    'Isa ka sa maswerteng napili para makatanggap ng 10 pesos load',
                    'Ipakita lang ang mensaheng ito sa namumuno upang makuha ang iyong premyo.',

                ]),
                'extra_attributes'  => [
                    'air_time' => 10,
                ],
            ],
            [
                'name'     => 'load100',
                'message'  => implode("\n", [
                    'Nanalo ka!',
                    'Isa ka sa maswerteng napili para makatanggap ng load',
                    'Ipakita lang ang mensaheng ito sa namumuno upang makuha ang iyong premyo.',

                ]),
                'extra_attributes'  => [
                    'air_time' => 100,
                ],
            ],
            [
                'name'     => 'ulit',
                'message'  => implode("\n", [
                    'Salamat sa suporta!',
                    'Naka-rehistro ka na.',

                ]),
                'extra_attributes'  => [
                    'air_time' => 0,
                ],
            ],
        ];

        foreach ($campaigns as $campaign) {
            Campaign::create($campaign);
        }

    }
}
