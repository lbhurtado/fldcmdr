<?php

use App\SMS;
use Illuminate\Database\Seeder;

class SMSSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $smss = $this->getSMSs();

        foreach ($smss as $sms) {
            SMS::create([
                'from' => $sms[1],
                'to' => $sms[0],
                'message' => $sms[2],
            ]);            
        }

        // SMS::create([
        // 	'from' => '+639173011987',
        // 	'to' => '+639081877788',
        // 	'message' => '@altavista',
        // ]);
        // SMS::create([
        // 	'from' => '+639173011987',
        // 	'to' => '+639081877788',
        // 	'message' => 'regular#levi',
        // ]);
        // SMS::create([
        // 	'from' => '+639171111111',
        // 	'to' => '+639081877788',
        // 	'message' => 'levi Lester',
        // ]);
        // SMS::create([
        // 	'from' => '+639171111111',
        // 	'to' => '+639081877788',
        // 	'message' => '@ambacan',
        // ]);
        // SMS::create([
        //     'from' => '+639171111111',
        //     'to' => '+639081877788',
        //     'message' => '#ambacan',
        // ]);

        // SMS::create(['from' => '+639171111111', 'to' => '+639081877788', 'message' => '#ambacan']);
    }

    protected function getSMSs()
    {
        return [
            ['+639081877788', '+639173011987', '@altavista'                 ],
            ['+639081877788', '+639173011987', 'regular#levi'               ],

            ['+639081877788', '+639171111111', 'levi Lester'                ],
            ['+639081877788', '+639171111111', '@ambacan'                   ],
            ['+639081877788', '+639171111111', '#ambacan'                   ],

        //     ['+639081877788', '+639172222222', 'ambacan Francesca'          ],
        //     ['+639081877788', '+639173333333', 'ambacan Sofia'              ],
        //     ['+639081877788', '+639174444444', 'ambacan Amelia'             ],
        //     ['+639081877788', '+639175555555', 'ambacan Nicolo'             ],
        ];
    }
}
