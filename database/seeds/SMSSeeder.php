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
        SMS::create([
        	'from' => '+639173011987',
        	'to' => '+639081877788',
        	'message' => '@altavista',
        ]);
        SMS::create([
        	'from' => '+639173011987',
        	'to' => '+639081877788',
        	'message' => 'regular#levi',
        ]);
        SMS::create([
        	'from' => '+639171111111',
        	'to' => '+639081877788',
        	'message' => 'levi Lester',
        ]);
        SMS::create([
        	'from' => '+639171111111',
        	'to' => '+639081877788',
        	'message' => '@ambacan',
        ]);
        SMS::create([
            'from' => '+639171111111',
            'to' => '+639081877788',
            'message' => '#ambacan',
        ]);
        // SMS::create(['from' => '+639172222222', 'to' => '+639081877788', 'message' => 'levi706 Dene']);
    }
}
