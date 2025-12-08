<?php

use App\Models\User;
use App\Models\Book;
use App\Models\Favorite;
use App\Models\ReadingProgress;
use App\Models\UserQuizAttempt;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('UC-18 View Dashboard Admin', function () {
    it('admin dapat mengakses dashboard admin', function () {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
    });

    it('mahasiswa tidak dapat mengakses dashboard admin', function () {
        $mahasiswa = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($mahasiswa)->get('/admin/dashboard');

        $response->assertStatus(403);
    });
});

describe('UC-19 View Dashboard Mahasiswa', function () {
    it('mahasiswa dapat mengakses dashboard', function () {
        $mahasiswa = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($mahasiswa)->get('/dashboard');

        $response->assertStatus(200);
    });

    it('dashboard menampilkan statistik yang benar', function () {
        $mahasiswa = User::factory()->create(['role' => 'user']);
        $book = Book::factory()->create();

        Favorite::create(['user_id' => $mahasiswa->id, 'book_id' => $book->id]);
        ReadingProgress::create([
            'user_id' => $mahasiswa->id,
            'book_id' => $book->id,
            'last_page_number' => 50,
        ]);

        $response = $this->actingAs($mahasiswa)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('1'); // Buku favorit
    });
});
