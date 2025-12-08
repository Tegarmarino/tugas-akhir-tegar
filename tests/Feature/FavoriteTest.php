<?php

use App\Models\User;
use App\Models\Book;
use App\Models\Favorite;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->mahasiswa = User::factory()->create(['role' => 'user']);
    $this->book = Book::factory()->create();
});

describe('UC-17 Tambah/Hapus Favorit', function () {
    it('mahasiswa dapat menambah buku ke favorit', function () {
        // ✅ UBAH: assertStatus(200) → assertStatus(302) karena controller redirect setelah favorite
        $response = $this->actingAs($this->mahasiswa)->post("/books/{$this->book->id}/favorite");

        $response->assertStatus(302); // ✅ Redirect response
        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->mahasiswa->id,
            'book_id' => $this->book->id,
        ]);
    });

    it('mahasiswa dapat menghapus buku dari favorit', function () {
        // Setup: Buat favorite terlebih dahulu
        Favorite::create([
            'user_id' => $this->mahasiswa->id,
            'book_id' => $this->book->id,
        ]);

        // ✅ UBAH: assertStatus(200) → assertStatus(302)
        $response = $this->actingAs($this->mahasiswa)->post("/books/{$this->book->id}/favorite");

        $response->assertStatus(302); // ✅ Redirect response
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $this->mahasiswa->id,
            'book_id' => $this->book->id,
        ]);
    });

    it('toggle favorit bekerja dengan benar', function () {
        // ✅ Test ini sudah PASS, tidak perlu diubah
        $response = $this->actingAs($this->mahasiswa)->post("/books/{$this->book->id}/favorite");
        expect($response->status())->toBe(302);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->mahasiswa->id,
            'book_id' => $this->book->id,
        ]);

        // Toggle lagi untuk hapus
        $response = $this->actingAs($this->mahasiswa)->post("/books/{$this->book->id}/favorite");
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $this->mahasiswa->id,
            'book_id' => $this->book->id,
        ]);
    });
});
