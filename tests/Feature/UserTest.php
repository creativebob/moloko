<?php

namespace Tests\Feature;

use App\User;

use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


class UserTest extends TestCase
{

    public function testExample()
    {


        // Генерируем пользователей
        $users_fakes = factory(User::class, 30)->create();


        // Определяем список пользователей под которыми будем тестировать
        $user_login_array = ['creativebob', 'testovik'];

        // Авторизуемся поочередно под указанными пользователями
        foreach($user_login_array as $user_login){


            $user = User::where('login', $user_login)->first();
            $response = $this->actingAs($user)->get('admin/getaccess');

            $user_fake = $users_fakes->random();


            Log::channel('test')->info('===================================== ТЕСТ USERS для ' . $user_login . ' ======================================');

                $entity = 'users';

                $page = $entity;
                $response = $this->call('GET', 'admin/'. $page); // Пример своего запроса
                $status = $response->status();
                Log::channel('test')->info(response_status_info($status, $page));


                $page = $entity . '/create';
                $response = $this->call('GET', 'admin/'. $page); // Пример своего запроса
                $status = $response->status();
                Log::channel('test')->info(response_status_info($status, $page));


                $answer = operator_right('users', true, 'edit');
                $user = User::moderatorLimit($answer)->get()->random();

                // Подключение политики
                // $this->authorize('edit', $user);

                $page = $entity . '/' . $user->id . '/edit';
                $response = $this->call('GET', 'admin/'. $page); // Пример своего запроса
                $status = $response->status();
                Log::channel('test')->info(response_status_info($status, $page));

                // $response->assertStatus(200);
                // $response->assertForbidden();

            Log::channel('test')->info('

                                                           ');


        }



    }
}
