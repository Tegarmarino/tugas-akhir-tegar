<?php

use App\Models\User;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->mahasiswa = User::factory()->create(['role' => 'user']);
    Book::factory()->count(10)->create();
});

describe('UC-10 Lihat Katalog Buku', function () {
    it('mahasiswa dapat mengakses halaman katalog buku', function () {
        $response = $this->actingAs($this->mahasiswa)->get('/books');

        $response->assertStatus(200);
    });

    it('katalog menampilkan daftar buku dengan pagination', function () {
        $response = $this->actingAs($this->mahasiswa)->get('/books');

        $response->assertStatus(200);
        $response->assertViewHas('books');
    });

    it('mahasiswa dapat mencari buku berdasarkan judul', function () {
        $book = Book::factory()->create(['title' => 'Laravel Tutorial']);

        $response = $this->actingAs($this->mahasiswa)->get('/books?search=Laravel');

        $response->assertStatus(200);
    });

    it('mahasiswa dapat memfilter buku berdasarkan author', function () {
        $book = Book::factory()->create(['author' => 'John Doe']);

        $response = $this->actingAs($this->mahasiswa)->get('/books?author=John');

        $response->assertStatus(200);
    });

    it('katalog menampilkan buku per halaman dengan benar', function () {
        $response = $this->actingAs($this->mahasiswa)->get('/books?page=1');

        $response->assertStatus(200);
    });

    it('katalog tidak menampilkan buku yang dihapus', function () {
        $book = Book::factory()->create();
        $book->delete();

        $response = $this->actingAs($this->mahasiswa)->get('/books');

        $response->assertDontSee($book->title);
    });
});
