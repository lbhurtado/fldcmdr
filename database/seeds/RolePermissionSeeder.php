<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->delete();
        DB::table('roles')->delete();

        $roles = ['admin', 'operator', 'staff', 'worker', 'subscriber'];
        foreach ($roles as $name) {
        	Role::create(compact('name'));
        }
    }
}
