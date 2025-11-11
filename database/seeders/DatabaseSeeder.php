<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat Admin User
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin', // Pastikan model User kamu punya 'role'
            'password' => Hash::make('password'), // default: password
        ]);

        // 2. Buat Mahasiswa (Student) User
        User::factory()->create([
            'name' => 'Mahasiswa User',
            'email' => 'mahasiswa@example.com',
            'role' => 'user', // Sesuaikan 'mahasiswa' atau 'user'
            'password' => Hash::make('password'), // default: password
        ]);

        // 3. Panggil QuestionSeeder untuk mengisi Bank Soal
        $this->call([
            QuestionSeeder::class,
        ]);
    }
}
