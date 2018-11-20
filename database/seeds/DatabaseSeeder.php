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
        
        $this->call(RolePermissionSeeder::class);
        $this->call(QuestionAnswerSeeder::class);

        Schema::enableForeignKeyConstraints();
    }
}
