<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    /**
     * Login Test
     */
    public function test_login(): void
    {
        $userController = new \App\Http\Controllers\v1\UsersController();
        $request = new \Illuminate\Http\Request();
        $request->replace([
            'username' => 'user_test',
            'password' => 'password_test'
        ]);
        $response = $userController->login($request);

        echo json_encode($response);die;
        $response->assertStatus(200);
    }

}
