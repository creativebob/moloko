<?php

namespace Tests\Feature;

use App\User;

use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {


        // Генерируем пользователей
        $user = factory(User::class, 1)->create();


        // Определяем список пользователей под которыми будем тестировать
        $user_login_array = ['creativebob', 'testovik'];

        // Авторизуемся поочередно под указанными пользователями
        foreach($user_login_array as $user_login){


            $user = User::where('login', $user_login)->first();
            $response = $this->actingAs($user)->get('admin/getaccess');


            Log::channel('test')->info('========================================= ТЕСТ USERS для ' . $user_login . ' ============================================');
            $response = $this->call('GET', 'admin/users'); // Пример своего запроса

            $response->assertSee('Пользователи');

            Log::channel('test')->info('                                             === КОНЕЦ === ');


        }








    }
}
