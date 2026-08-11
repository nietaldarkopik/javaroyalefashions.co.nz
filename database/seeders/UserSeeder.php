<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'admin@example.co.nz'],
            [
                'name' => 'Store Admin',
                'password' => 'password', // hashed via the 'hashed' cast — change this immediately after first login
                'role' => 'admin',
            ]
        );
    }
}
