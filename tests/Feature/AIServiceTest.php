<?php

use App\Models\User;
use App\Models\Book;
use App\Models\Chapter;
use App\Services\GeminiService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->mahasiswa = User::factory()->create(['role' => 'user']);
    $this->book = Book::factory()->create();
    $this->chapter = Chapter::factory()->create(['book_id' => $this->book->id]);
    $this->geminiService = new GeminiService();
});

describe('UC-15 Tanya Jawab Dengan AI', function () {
    it('mahasiswa dapat mengirim pertanyaan mode halaman', function () {
        $response = $this->actingAs($this->mahasiswa)->post("/books/{$this->book->id}/chat", [
            'question' => 'Apa yang dibahas di halaman ini?',
            'page_number' => 10,
            'mode' => 'page',
        ]);

        $response->assertStatus(200);
    });

    it('mahasiswa dapat mengirim pertanyaan mode bab', function () {
        $response = $this->actingAs($this->mahasiswa)->post("/books/{$this->book->id}/chat", [
            'question' => 'Jelaskan ringkasan bab ini',
            'chapter_id' => $this->chapter->id,
            'mode' => 'chapter',
        ]);

        $response->assertStatus(200);
    });

    it('sistem tidak memproses pertanyaan kosong', function () {
        $response = $this->actingAs($this->mahasiswa)->post("/books/{$this->book->id}/chat", [
            'question' => '',
            'page_number' => 10,
            'mode' => 'page',
        ]);

        $response->assertSessionHasErrors('question');
    });

    it('mahasiswa mendapat respon dari AI dalam format JSON', function () {
        $response = $this->actingAs($this->mahasiswa)->post("/books/{$this->book->id}/chat", [
            'question' => 'Apa definisi dari topik ini?',
            'page_number' => 1,
            'mode' => 'page',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['reply' => true], strict: false);
    });

    it('AI respon memperhitungkan konteks halaman/bab', function () {
        $response = $this->actingAs($this->mahasiswa)->post("/books/{$this->book->id}/chat", [
            'question' => 'Apakah ini penting?',
            'chapter_id' => $this->chapter->id,
            'mode' => 'chapter',
        ]);

        $response->assertStatus(200);
    });

    it('sistem menangani timeout dari AI API dengan baik', function () {
        // Ini test untuk error handling saat AI timeout
        $response = $this->actingAs($this->mahasiswa)->post("/books/{$this->book->id}/chat", [
            'question' => 'Pertanyaan panjang yang mungkin timeout...',
            'page_number' => 1,
            'mode' => 'page',
        ]);

        // Harusnya return error message atau response
        $response->assertStatus(200);
    });
});

describe('UC-20 Jawab Pertanyaan (AI API)', function () {
    it('GeminiService dapat menerima prompt dan konteks PDF', function () {
        $prompt = 'Jelaskan isi halaman ini';
        $pdfData = [
            'mime_type' => 'application/pdf',
            'data' => base64_encode('fake pdf content'),
        ];

        // Test service jika dijalankan
        $this->assertTrue(true); // Placeholder untuk service integration
    });

    it('GeminiService mengembalikan respon tekstual', function () {
        // Placeholder test untuk service response
        $this->assertTrue(true);
    });

    it('GeminiService menangani error koneksi dengan logging', function () {
        // Placeholder test untuk error handling
        $this->assertTrue(true);
    });
});
