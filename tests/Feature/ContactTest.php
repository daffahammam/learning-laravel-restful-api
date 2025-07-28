<?php

namespace Tests\Feature;

use Database\Seeders\ContactSeeder;
use Database\Seeders\SearchSeeder;
use Database\Seeders\UserSeeder;
use App\Models\User;
use App\Models\Contact;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Log;
use Tests\TestCase;

class ContactTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/contacts', [
            'first_name' => 'Daffa',
            'last_name' => 'Hammam',
            'email' => 'daffa@gmail.com',
            'phone' => '081234567890',
        ], [
            'Authorization' => 'test', // Assuming 'test' is a valid token for the seeded user
        ] )->assertStatus(201)
            ->assertJson([
                'data' => [
                    'first_name' => 'Daffa',
                    'last_name' => 'Hammam',
                    'email'  => 'daffa@gmail.com',
                    'phone' => '081234567890',
                ],
            ]);
    }

    public function testCreateFailed()
    {
         $this->seed([UserSeeder::class]);

        $this->post('/api/contacts', [
            'first_name' => '',
            'last_name' => 'Hammam',
            'email' => 'daffa',
            'phone' => '081234567890',
        ], [
            'Authorization' => 'test', // Assuming 'test' is a valid token for the seeded user
        ] )->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'first_name' => ['The first name field is required.'],
                    'email' => ['The email field must be a valid email address.'],
                ],
            ]);
    }

    public function testCreateUnauthorized()
    {
        $this->post('/api/contacts', [
            'first_name' => 'Daffa',
            'last_name' => 'Hammam',
            'email' => 'daffa@gmail.com',
            'phone' => '081234567890',
        ])->assertStatus(401);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/'.$contact->id, [
            'Authorization' => 'test', // Assuming 'test' is a valid token for the seeded user
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                        'first_name' => 'test',
                        'last_name' => 'test',
                        'email' => 'test@example.com',
                        'phone' => '081234567890',
                ],
            ]);

    }

    public function testGetNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/'.($contact->id + 1), [
            'Authorization' => 'test', // Assuming 'test' is a valid token for the seeded user
        ])->assertStatus(404);
    }

    public function testGetOtherUserContact()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/'.$contact->id, [
            'Authorization' => 'test2' // Assuming 'test2' is a valid token for another user
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                'message' =>
                    'Not Found'
                ],
            ]);
    }

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->put('/api/contacts/'.$contact->id, [
            'first_name' => 'Updated',
            'last_name' => 'Contact',
            'email' => 'test@example.com',
            'phone' => '081234567890',
        ], [
            'Authorization' => 'test', // Assuming 'test' is a valid token for the seeded user
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => 'Updated',
                    'last_name' => 'Contact',
                    'email' => 'test@example.com',
                    'phone' => '081234567890',
                ],
            ]);
    }

    public function testUpdateValidationError()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->put('/api/contacts/'.$contact->id, [
            'first_name' => '',
            'last_name' => 'Contact',
            'email' => 'test',
            'phone' => '081234567890',
        ], [
            'Authorization' => 'test', // Assuming 'test' is a valid token for the seeded user
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'first_name' => ['The first name field is required.'],
                    'email' => ['The email field must be a valid email address.'],
                ],
            ]);
    }

    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->delete('/api/contacts/'.$contact->id, [], [
            'Authorization' => 'test', // Assuming 'test' is a valid token for the seeded user
        ])->assertStatus(200)
        ->assertJson([
            'data' => true
        ]);
    }

    public function testDeleteNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->delete('/api/contacts/'.($contact->id + 1), [], [
            'Authorization' => 'test', // Assuming 'test' is a valid token for the seeded user
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => 'Not Found',
                ],
            ]);
    }

    public function testSearchByFirstName()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?name=first', [
            'Authorization' => 'test', // Assuming 'test' is a valid token for the seeded user
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, ($response['meta']['total']));
    }

    public function testSearchByLastName()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?name=last', [
            'Authorization' => 'test', // Assuming 'test' is a valid token for the seeded user
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, ($response['meta']['total']));
    }

    public function testSearchByEmail()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?email=test', [
            'Authorization' => 'test', // Assuming 'test' is a valid token for the seeded user
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, ($response['meta']['total']));
    }

    public function testSearchByPhone()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?phone=081234567890', [
            'Authorization' => 'test', // Assuming 'test' is a valid token for the seeded user
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, ($response['meta']['total']));
    }

    public function testSearchNotFound()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?name=notfound', [
            'Authorization' => 'test', // Assuming 'test' is a valid token for the seeded user
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(0, count($response['data']));
        self::assertEquals(0, ($response['meta']['total']));
    }

    public function testSearchPagination()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?size=5&page=2', [
            'Authorization' => 'test' // Assuming 'test' is a valid token for the seeded user
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(5, count($response['data']));
        self::assertEquals(20, ($response['meta']['total']));
        self::assertEquals(2, $response['meta']['current_page']);
    }
}
