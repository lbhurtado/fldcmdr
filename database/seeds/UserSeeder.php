<?php

use App\User;
use App\Eloquent\Phone;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // User::truncate();
        DB::table('users')->delete();

        $mobile = env('ADMIN_MOBILE', decrypt(
        	'eyJpdiI6InBNQzljQVZiaUd4Vk1LUmlcL1orMG5BPT0iLCJ2YWx1ZSI6IkFxRnZLWnI2RlpaWXNFU2hGV3N5S0hKdUgzSEtlWVFIQTJqd1dDcnNSMGs9IiwibWFjIjoiNzEzM2ZlZmI2NTE2ZWFiY2Y5MWMzYzYyMjc5ODRmMGVhOTgxZDcyNGJlMTYzYmM5NmY1ZWMzYjYxMjcwZDliNSJ9'));
        $mobile = Phone::number($mobile);
        $name = env('ADMIN_NAME', decrypt(
        	'eyJpdiI6InZkRUpOYXdoSENKOTkzYTBBYmoyVnc9PSIsInZhbHVlIjoibDQyRWFIcmVEWWEwcmhjdGhualdxQ2c1ZE5CTVB5NzlUTFdieWN2SjFRdz0iLCJtYWMiOiIxMTMwZmRlYzFkZjhlMGE2NjA1NzUyMjFiNmI3NDQ5NjNhMjdmY2U1YTdiMzk2ODVjNGQ0YzVkNDliNjY3OTllIn0='));
        $password = '$2y$10$gz7MXG5YLhKykthNDjkfWu.fV80v.WpS..xn3T5SOza2Vo7tfGHtG';
        $email = 'lester@hurtado.ph';
        $driver = 'Telegram';
        $channel_id = '592028290';
        $telerivet_id = 'CT0e9dae8539be5222'; //careful, this might be regenerated
        $node = User::create(compact('mobile', 'name', 'password', 'email', 'driver', 'channel_id', 'telerivet_id'));
        $node->makeRoot()->save();
    }

}
