<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Chapter;
use App\Models\Test;
use App\Models\Question;

class DummyContentSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan sudah ada bank soal dari QuestionSeeder
        $questions = Question::all();

        Book::factory()
            ->count(5)
            ->create()
            ->each(function (Book $book) use ($questions) {
                // Bab
                Chapter::factory()->count(3)->create([
                    'book_id' => $book->id,
                ]);

                // Pre & Post test untuk tiap buku
                Test::factory()->create([
                    'book_id' => $book->id,
                    'type' => 'pre',
                ]);

                Test::factory()->create([
                    'book_id' => $book->id,
                    'type' => 'post',
                ]);

                // Kalau nanti kamu butuh logika pilih soal by tag,
                // itu diimplementasi di controller / service, bukan di sini.
            });
    }
}
