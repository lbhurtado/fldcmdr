<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	Schema::disableForeignKeyConstraints();
        // $this->call(CampaignSeeder::class); 
        // $this->call(GroupSeeder::class);
        // $this->call(AreaSeeder::class);
        $this->call(RolePermissionSeeder::class);
        $this->call(QuickRankSeeder::class);
        $this->call(UserSeeder::class);
        // $this->call(SurveySeeder::class);
        // $this->call(FakeAnswerSeeder::class);

        Schema::enableForeignKeyConstraints();
    }
}
