<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UsersController extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_login(): void
    {
        $response = $this->post(route('login'), [
            'username' => 'admin',
            'password' => 'testing123',
        ]);

        $response->assertStatus(200);
    }
}
