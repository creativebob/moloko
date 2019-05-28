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
                $response = $this->call('GET', 'admin/'. $page); // Пример своего запроса
                $status = $response->status();
                Log::channel('test')->info(response_status_info($status, $page));


                $page = $entity->alias . '/create';
                $response = $this->call('GET', 'admin/'. $page); // Пример своего запроса
                $status = $response->status();
                Log::channel('test')->info(response_status_info($status, $page));

                Log::channel('test')->info('     ');


            }

            Log::channel('test')->info('

                                                                     === КОНЕЦ === 

            ');


        }

    }

}
