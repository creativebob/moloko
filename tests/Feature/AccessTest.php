<?php

namespace Tests\Feature;

use App\User;
use App\Entity;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Controllers\Session;
use Illuminate\Support\Facades\Log;


use Illuminate\Support\Facades\Auth;

class AccessTest extends TestCase
{
    public function testExample()
    {

        $user_login_array = ['creativebob', 'testovik'];
        foreach($user_login_array as $user_login){


            $user = User::where('login', $user_login)->first();
            $response = $this->actingAs($user)->get('admin/getaccess');

            Log::channel('test')->info('

            ========================================== ТЕСТ НА ДОСТУПНОСТЬ СТРАНИЦ для ' . $user_login . ' ==============================================

            ');

            $entities = Entity::all();
            foreach($entities as $entity){

                $page = $entity->alias;
                $response = $this->call('GET', 'admin/'. $page . '/create'); // Пример своего запроса

                // $response->assertStatus(200);
                // $response->assertForbidden();


                $status = $response->status();
                switch ($status) {
                case 200:
                    Log::channel('test')->info('Страница ' . $page . ' доступна');
                    break;
                case 302:
                    Log::channel('test')->info('Со страницы ' . $page . ' выполнен РЕДИРЕКТ на другую страницу');
                    break;
                case 404:
                    Log::channel('test')->info('Страница ' . $page . ' НЕ доступна (404)');
                    break;
                case 403:
                    Log::channel('test')->info('На странице ' . $page . ' Доступ закрыт! (403) -------------------------------- ');
                    break;
                case 500:
                    Log::channel('test')->info('На странице ' . $page . ' ОШИБКА СЕРВЕРА (500) ================================== ');
                    break;
                default:
                    Log::channel('test')->info('На странице ' . $page . ' ПРОИЗОШЛА НЕВЕДОМАЯ ХУЙНЯ С КОДОМ: ' . $status);
                }



                $response = $this->call('GET', 'admin/'. $page . '/create'); // Пример своего запроса

                // $response->assertStatus(200);
                // $response->assertForbidden();


                $status = $response->status();
                switch ($status) {
                case 200:
                    Log::channel('test')->info('Страница ' . $page . '/create доступна');
                    break;
                case 302:
                    Log::channel('test')->info('Со страницы ' . $page . '/create выполнен РЕДИРЕКТ на другую страницу');
                    break;
                case 404:
                    Log::channel('test')->info('Страница ' . $page . '/create НЕ доступна (404)');
                    break;
                case 403:
                    Log::channel('test')->info('На странице ' . $page . '/create Доступ закрыт! (403) -------------------------------- ');
                    break;
                case 500:
                    Log::channel('test')->info('На странице ' . $page . '/create ОШИБКА СЕРВЕРА (500) ================================== ');
                    break;
                default:
                    Log::channel('test')->info('На странице ' . $page . '/create ПРОИЗОШЛА НЕВЕДОМАЯ ХУЙНЯ С КОДОМ: ' . $status);
                }

                Log::channel('test')->info('     ');


            }

            Log::channel('test')->info('

                                                                     === КОНЕЦ === 

            ');


        }

    }

}
