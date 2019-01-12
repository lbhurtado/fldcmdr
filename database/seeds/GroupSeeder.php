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
        Group::build('hq.slate');
        Group::build('hq.original');
        Group::build('hq.guardian');   
    }
}
