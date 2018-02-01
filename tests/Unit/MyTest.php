<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
    	$response = $this->get('/sites');


        $response->assertStatus(200);

        // $this->visit('/sites')
        // ->click('Crm System')
        // ->seePageIs('/sites/crmsystem');


        // $this->assertTrue(true);
    }
}
