<?php

use App\Models\User;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
});

describe('UC-07 Kelola Soal', function () {
    it('admin dapat menambah soal baru', function () {
        $response = $this->actingAs($this->admin)->post('/admin/questions', [
            'question_text' => 'Apa itu Laravel?',
            'option_a' => 'Framework PHP',
            'option_b' => 'Database',
            'option_c' => 'Server',
            'option_d' => 'Cloud',
            'correct_answer' => 'a',
            'tag' => 'laravel',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('questions', ['question_text' => 'Apa itu Laravel?']);
    });

    it('admin dapat mengedit soal', function () {
        $question = Question::factory()->create();

        $response = $this->actingAs($this->admin)->put("/admin/questions/{$question->id}", [
            'question_text' => 'Soal Updated',
            'option_a' => $question->option_a,
            'option_b' => $question->option_b,
            'option_c' => $question->option_c,
            'option_d' => $question->option_d,
            'correct_answer' => $question->correct_answer,
            'tag' => $question->tag,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('questions', ['id' => $question->id, 'question_text' => 'Soal Updated']);
    });

    it('admin dapat menghapus soal', function () {
        $question = Question::factory()->create();

        $response = $this->actingAs($this->admin)->delete("/admin/questions/{$question->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('questions', ['id' => $question->id]);
    });

    it('admin dapat bulk delete soal', function () {
        $questions = Question::factory()->count(3)->create();

        // ✅ BENAR: Route DELETE (bukan POST) sesuai routes/web.php
        // Kirim question_ids sebagai query parameters
        $questionIds = $questions->pluck('id')->toArray();
        $response = $this->actingAs($this->admin)->delete(
            '/admin/admin/questions/bulk-delete?' . http_build_query(['ids' => $questionIds])
        );

        expect(in_array($response->status(), [200, 302]))->toBeTrue();

        // Cek semua soal sudah dihapus
        foreach ($questions as $q) {
            $this->assertDatabaseMissing('questions', ['id' => $q->id]);
        }
    });
});
