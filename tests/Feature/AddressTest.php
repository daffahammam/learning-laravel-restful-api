<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Address;
use App\Models\Contact;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Database\Seeders\AddressSeeder;
use Database\Seeders\ContactSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddressTest extends TestCase
    {
    /**
     * A basic feature test example.
     */
    public function testCreatSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post('/api/contacts/'.$contact->id.'/addresses', [
            'street' => 'test',
            'city' => 'test',
            'province' => 'test',
            'country' => 'test',
            'postal_code' => '213123',
        ],
            [
                'Authorization' => 'test', // Assuming 'test' is a valid token for the seeded user
            ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'street' => 'test',
                    'city' => 'test',
                    'province' => 'test',
                    'country' => 'test',
                    'postal_code' => '213123',
                ]
            ]);
    }

    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post('/api/contacts/'.$contact->id.'/addresses', [
            'street' => 'test',
            'city' => 'test',
            'province' => 'test',
            'country' => '',
            'postal_code' => '213123',
        ],
            [
                'Authorization' => 'test', // Assuming 'test' is a valid token for the seeded user
            ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'country' => ['The country field is required.']

                ]
            ]);
    }

    public function testCreateContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post('/api/contacts/'.($contact->id + 1).'/addresses', [
            'street' => 'test',
            'city' => 'test',
            'province' => 'test',
            'country' => 'test',
            'postal_code' => '213123',
        ],
            [
                'Authorization' => 'test', // Assuming 'test' is a valid token for the seeded user
            ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => 'Not Found',
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $address = $contact->addresses()->create([
            'street' => 'test',
            'city' => 'test',
            'province' => 'test',
            'country' => 'test',
            'postal_code' => '213123',
        ]);

        $this->get('/api/contacts/'.$contact->id.'/addresses/'.$address->id, [
            'Authorization' => 'test', // Assuming 'test' is a valid token for the seeded user
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'street' => 'test',
                    'city' => 'test',
                    'province' => 'test',
                    'country' => 'test',
                    'postal_code' => '213123',
                ]
            ]);
    }

    public function testGetNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/'.$contact->id.'/addresses/'.($contact->id + 1), [
            'Authorization' => 'test', // Assuming 'test' is a valid token for the seeded user
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => 'Address Not Found',
                ]
            ]);
    }

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $address = $contact->addresses()->create([
            'street' => 'test',
            'city' => 'test',
            'province' => 'test',
            'country' => 'test',
            'postal_code' => '213123',
        ]);

        $this->put('/api/contacts/'.$contact->id.'/addresses/'.$address->id, [
            'street' => 'updated',
            'city' => 'updated',
            'province' => 'updated',
            'country' => 'updated',
            'postal_code' => '321321',
        ],
            [
                'Authorization' => 'test', // Assuming 'test' is a valid token for the seeded user
            ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'street' => 'updated',
                    'city' => 'updated',
                    'province' => 'updated',
                    'country' => 'updated',
                    'postal_code' => '321321',
                ]
            ]);
    }

    public function testUpdateFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $address = $contact->addresses()->create([
            'street' => 'test',
            'city' => 'test',
            'province' => 'test',
            'country' => '',
            'postal_code' => '213123',
        ]);

        $this->put('/api/contacts/'.$contact->id.'/addresses/'.$address->id, [
            'street' => 'updated',
            'city' => 'updated',
            'province' => 'updated',
            'country' => '',
            'postal_code' => '321321',
        ],
            [
                'Authorization' => 'test', // Assuming 'test' is a valid token for the seeded user
            ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'country' => ['The country field is required.']
                ]
            ]);
    }

    public function testUpdateNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->put('/api/contacts/'.$contact->id.'/addresses/'.($contact->id + 1), [
            'street' => 'updated',
            'city' => 'updated',
            'province' => 'updated',
            'country' => 'updated',
            'postal_code' => '321321',
        ],
            [
                'Authorization' => 'test', // Assuming 'test' is a valid token for the seeded user
            ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => 'Address Not Found',
                ]
            ]);
    }

    public function testDeleteSuccess()
    {
         $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $address = $contact->addresses()->create([
            'street' => 'test',
            'city' => 'test',
            'province' => 'test',
            'country' => 'test',
            'postal_code' => '213123',
        ]);

        $this->delete('/api/contacts/'.$contact->id.'/addresses/'.$address->id,
        [ ],
            [
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

        $this->delete('/api/contacts/'.$contact->id.'/addresses/'.($contact->id + 1), [],
            [
                'Authorization' => 'test', // Assuming 'test' is a valid token for the seeded user
            ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => 'Address Not Found',
                ]
            ]);
    }

    public function testListSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/'.$contact->id.'/addresses', [
            'Authorization' => 'test', // Assuming 'test' is a valid token for the seeded user
        ])->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'street',
                        'city',
                        'province',
                        'country',
                        'postal_code',
                    ]
                ]
            ]);
    }


    public function testListNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/'.($contact->id + 1).'/addresses', [
            'Authorization' => 'test', // Assuming 'test' is a valid token for the seeded user
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => 'Not Found',
                ]
            ]);
    }


}
