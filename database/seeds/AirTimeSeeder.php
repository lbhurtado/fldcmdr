<?php

use Illuminate\Database\Seeder;

use App\AirTime;

class AirTimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('air_times')->delete();
        AirTime::create([	
    		'name' => 'TEN',
    		'amount' => 10.00,
    	]);
        AirTime::create([	
    		'name' => 'TWENTY-FIVE',
    		'amount' => 25.00,
    	]);
        AirTime::create([	
    		'name' => 'FIFTY',
    		'amount' => 50.00,
    	]);
        AirTime::create([	
    		'name' => 'ONE-HUNDRED',
    		'amount' => 100.00,
    	]);
    }
}
