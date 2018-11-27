<?php

namespace Tests;

use App\BotManTester;
use BotMan\BotMan\BotMan;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @var BotMan
     */
    protected $botman;

    /**
     * @var BotManTester
     */
    protected $bot;

    function setUp()
    {
        parent::setUp();

        collect(config('chatbot.permissions'))->each(function ($permissions, $role) {
            $role = Role::firstOrCreate(['name' => $role]);
            foreach ($permissions as $permission) {
                $p = Permission::firstOrCreate(['name' => $permission]);
                $role->givePermissionTo($p); 
              }
        });
    }
}
