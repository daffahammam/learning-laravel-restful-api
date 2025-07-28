<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Contact;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'test')->first();
        Contact::create([
            'first_name' => 'test',
            'last_name' => 'test',
            'email' => 'test@example.com',
            'phone' => '081234567890',
            'user_id' => $user->id,
            ]);
    }
}
