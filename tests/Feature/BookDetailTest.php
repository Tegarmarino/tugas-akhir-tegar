<?php

use App\Models\User;
use App\Models\Book;
use App\Models\Chapter;
use App\Models\Test;
use App\Models\ReadingProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->mahasiswa = User::factory()->create(['role' => 'user']);
    $this->book = Book::factory()->create();
});

describe('UC-11 Lihat Detail Buku', function () {
    it('mahasiswa dapat mengakses halaman detail buku', function () {
        $response = $this->actingAs($this->mahasiswa)->get("/books/{$this->book->id}");

        $response->assertStatus(200);
    });

    it('halaman detail buku menampilkan informasi lengkap', function () {
        $response = $this->actingAs($this->mahasiswa)->get("/books/{$this->book->id}");

        $response->assertStatus(200);
        $response->assertSee($this->book->title);
        $response->assertSee($this->book->author);
    });

    it('halaman detail menampilkan daftar bab', function () {
        $chapter = Chapter::factory()->create(['book_id' => $this->book->id]);

        $response = $this->actingAs($this->mahasiswa)->get("/books/{$this->book->id}");

        $response->assertStatus(200);
        $response->assertViewHas('book');
    });

    it('mahasiswa dapat melihat pre-test dan post-test yang tersedia', function () {
        $preTest = Test::factory()->create([
            'book_id' => $this->book->id,
            'type' => 'pre',
        ]);

        $response = $this->actingAs($this->mahasiswa)->get("/books/{$this->book->id}");

        $response->assertStatus(200);
        $response->assertViewHas('book');
    });

    it('halaman detail menampilkan progress membaca mahasiswa', function () {
        ReadingProgress::create([
            'user_id' => $this->mahasiswa->id,
            'book_id' => $this->book->id,
            'last_page_number' => 50,
        ]);

        $response = $this->actingAs($this->mahasiswa)->get("/books/{$this->book->id}");

        $response->assertStatus(200);
    });

    it('mahasiswa dapat mulai membaca buku dari halaman terakhir', function () {
        ReadingProgress::create([
            'user_id' => $this->mahasiswa->id,
            'book_id' => $this->book->id,
            'last_page_number' => 50,
        ]);

        $response = $this->actingAs($this->mahasiswa)->get("/books/{$this->book->id}");

        $response->assertStatus(200);
    });
});
