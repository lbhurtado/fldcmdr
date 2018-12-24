<?php

use App\Group;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('groups')->delete();
        Group::build('hq.staff.personnel');
        Group::build('hq.staff.intelligence');
        Group::build('hq.staff.operation');   
    }
}
