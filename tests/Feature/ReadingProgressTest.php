<?php

use App\Models\User;
use App\Models\Book;
use App\Models\ReadingProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->mahasiswa = User::factory()->create(['role' => 'user']);
    $this->book = Book::factory()->create(['total_pages' => 100]);
});

describe('UC-12 Baca Buku - Save Progress', function () {
    it('mahasiswa dapat menyimpan progress membaca', function () {
        $response = $this->actingAs($this->mahasiswa)->patch("/books/{$this->book->id}/progress", [
            'last_page_number' => 25,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('reading_progress', [
            'user_id' => $this->mahasiswa->id,
            'book_id' => $this->book->id,
            'last_page_number' => 25,
        ]);
    });

    it('progress terupdate saat mahasiswa pindah halaman', function () {
        ReadingProgress::create([
            'user_id' => $this->mahasiswa->id,
            'book_id' => $this->book->id,
            'last_page_number' => 10,
        ]);

        $response = $this->actingAs($this->mahasiswa)->patch("/books/{$this->book->id}/progress", [
            'last_page_number' => 50,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('reading_progress', [
            'user_id' => $this->mahasiswa->id,
            'book_id' => $this->book->id,
            'last_page_number' => 50,
        ]);
    });
});

describe('UC-16 Reset Progress', function () {
    it('mahasiswa dapat mereset progress membaca', function () {
        ReadingProgress::create([
            'user_id' => $this->mahasiswa->id,
            'book_id' => $this->book->id,
            'last_page_number' => 50,
        ]);

        $response = $this->actingAs($this->mahasiswa)->delete("/books/{$this->book->id}/progress/reset");

        $response->assertRedirect();
        $this->assertDatabaseMissing('reading_progress', [
            'user_id' => $this->mahasiswa->id,
            'book_id' => $this->book->id,
        ]);
    });
});
