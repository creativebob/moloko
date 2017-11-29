<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new Region;

	    $user->name = 'Makc_Berluskone';
	    $user->email = 'makc_berluskone@mail.ru';
	    $user->remember_token = 'WSGQtPXIBDZMIWVC4xxmcaRLeSifmIi9it9MNWBeUUXnrre5lroD1VWXQmA4';
	    $user->created_at = '2017-11-29 06:02:50';
	    $user->updated_at = '2017-11-29 06:02:50';
	    $user->password = bcrypt(123456);

	    $user->save();

    }
}
