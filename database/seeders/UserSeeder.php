<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Informática',
            'email' => 'informatica@mdonihue.cl',
            'password' => Hash::make('MDoñihue2021#'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Vicente Flores',
            'email' => 'vflores@mdonihue.cl',
            'password' => Hash::make('12345'),
            'role' => 'user',
        ]);
    }
}
