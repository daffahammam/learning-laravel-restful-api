<?php

namespace Tests\Feature;

use Tests\TestCase;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function testRegisterSuccess()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('/api/users', [
            'username' => 'testuser',
            'password' => 'password123',
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(201)
                ->assertJson([
                    'data' => [
                        'username' => 'testuser',
                        'name' => 'Test User',
                    ],
                ]);
    }

    public function testRegisterFailed()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('/api/users', [
            'username' => '',
            'password' => '',
            'name' => '',
            // email missing
        ]);

        $response->assertStatus(400)
                ->assertJson([
                    "errors" => [
                        'username' => ['The username field is required.'],
                        'password' => ['The password field is required.'],
                        'name' => ['The name field is required.'],
                        'email' => ['The email field is required.'],
                    ],
                ]);
    }

    public function testRegisterUsernameAlreadyExists()
    {
        $this->withoutExceptionHandling();

        // First successful registration
        $this->post('/api/users', [
            'username' => 'testuser',
            'password' => 'password123',
            'name' => 'Test User',
            'email' => 'test@example.com',
        ])->assertStatus(201);

        // Second registration with same username and email
        $response = $this->post('/api/users', [
            'username' => 'testuser',
            'password' => 'password123',
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(400)
                ->assertJson([
                    "errors" => [
                        'username' => ['The username has already been taken.'],
                        'email' => ['The email has already been taken.'],
                    ],
                ]);
    }

    public function testLoginSuccess()
    {
        $this->withoutExceptionHandling();
        $this->seed([UserSeeder::class]);

        // First register a user
        $this->post('/api/users/login', [
            'username' => 'test',
            'password' => 'test',
            ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'test',
                    'name' => 'test',
                ],
            ]);

            $user = User::where('username', 'test')->first();
            self::assertNotNull($user->token);
    }

    public function testLoginFailedUsernameNotFound()
    {
        $this->withoutExceptionHandling();

        $this->post('/api/users/login', [
            'username' => 'test',
            'password' => 'test',
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                'username' => 'Invalid username or password.'
                ],
            ]);
    }

    public function testLoginFailedPasswordWrong()
    {
        $this->withoutExceptionHandling();
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'username' => 'test',
            'password' => 'salah',
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                'username' => 'Invalid username or password.'
                ],
            ]);
    }

    public function testGetSuccess()
    {
        $this->withoutExceptionHandling();
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current', [
                'Authorization' => 'test'
            ])->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'username' => 'test',
                        'name' => 'test',
                    ],
                ]);

    }

    public function testGetUnauthorized()
    {
        $this->withoutExceptionHandling();
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current')
            ->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => ['Unauthorized'],
                ],
            ]);
    }


    public function testGetInvalidToken()
    {
        $this->withoutExceptionHandling();
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current', [
                'Authorization' => 'salah'
            ])->assertStatus(401)
                ->assertJson([
                    'errors' => [
                    'message' => ['Unauthorized'],
                ],
            ]);
    }

    public function testUpdatePasswordSuccess()
    {
        $this->withoutExceptionHandling();
        $this->seed([UserSeeder::class]);
        $olduser = User::where('username', 'test')->first();

        $this->patch('/api/users/current', [
                'password' => 'baru'
            ], [
                'Authorization' => 'test'
            ])->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'username' => 'test',
                        'name' => 'test',
                    ],
                ]);

        $newuser = User::where('username', 'test')->first();
        self::assertNotEquals($olduser->password, $newuser->password);
    }

    public function testUpdateNameSuccess()
    {
        $this->withoutExceptionHandling();
        $this->seed([UserSeeder::class]);
        $olduser = User::where('username', 'test')->first();

        $this->patch('/api/users/current', [
                'name' => 'Daffa',
            ], [
                'Authorization' => 'test'
            ])->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'username' => 'test',
                        'name' => 'Daffa',
                    ],
                ]);

        $newuser = User::where('username', 'test')->first();
        self::assertNotEquals($olduser->name, $newuser->name);
    }

    public function testUpdateFailed()
    {
        $this->withoutExceptionHandling();
        $this->seed([UserSeeder::class]);

        $this->patch('/api/users/current', [
                'name' => 'qWErtyuIOpLkjhGFdsAZxcvBNMqwertyUIOPlkjHGFDsazXCVbnmQWErtyuioPLKJHgfdsaZXCVBNmqwERTYuioplKJHgfdsaZXcvbnMQWERtyuioP',
                'password' => 'qWErtyuIOpLkjhGFdsAZxcvBNMqwertyUIOPlkjHGFDsazXCVbnmQWErtyuioPLKJHgfdsaZXCVBNmqwERTYuioplKJHgfdsaZXcvbnMQWERtyuioP',
            ], [
                'Authorization' => 'test'
            ])->assertStatus(400)
                ->assertJson([
                    "errors" => [
                        'name' => ['The name field must not be greater than 100 characters.'],
                        'password' => ['The password field must not be greater than 100 characters.'],
                    ],
                ]);
    }

    public function testLogoutSuccess()
    {
        $this->withoutExceptionHandling();
        $this->seed([UserSeeder::class]);

        $this->delete('/api/users/logout', [], [
                'Authorization' => 'test'
            ])->assertStatus(200)
                ->assertJson([
                    'data' => true
                ]);

        $user = User::where('username', 'test')->first();
        self::assertNull($user->token);
    }

    public function testLogoutFailed()
    {
        $this->withoutExceptionHandling();
        $this->seed([UserSeeder::class]);

        $this->delete('/api/users/logout')
            ->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => ['Unauthorized'],
                ],
            ]);
    }

}
