<?php

namespace Database\Seeders;

use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'test',
            'password' => Hash::make('test'), // Hash password'test',
            'name' => 'test',
            'email' => 'test@example.com',
            'token' => 'test'
            ]);
        User::create([
            'username' => 'test2',
            'password' => Hash::make('test2'), // Hash password'test',
            'name' => 'test2',
            'email' => 'test2@example.com',
            'token' => 'test2'
            ]);
    }
}
