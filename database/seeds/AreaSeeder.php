<?php

use App\Area;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('areas')->delete();
        Area::build('philippines.ilocos_norte.currimao');
        Area::build('philippines.ilocos_norte.batac');
        Area::build('philippines.ilocos_norte.laoag_city');   
    }
}
