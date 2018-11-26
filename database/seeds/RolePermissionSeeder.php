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

        // $roles = ['admin', 'operator', 'staff', 'worker', 'subscriber'];
        // foreach ($roles as $name) {
        // 	Role::create(compact('name'));
        // }

        $permissions = [
            'admin'      => ['send reward'],
            'operator'   => ['send reward', 'accept reward'],
            'staff'      => ['accept reward'],
            'worker'     => ['accept reward'],
            'subscriber' => ['accept reward'],
        ];

        collect($permissions)->each(function ($permissions, $role) {
            $role = Role::create(['name' => $role]);
            foreach ($permissions as $permission) {
                $role->givePermissionTo(Permission::firstOrCreate(['name' => $permission]));  
              }
        });
    }
}
