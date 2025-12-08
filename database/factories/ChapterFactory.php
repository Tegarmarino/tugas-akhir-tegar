<?php

namespace Database\Factories;

use App\Models\Chapter;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChapterFactory extends Factory
{
    protected $model = Chapter::class;

    public function definition(): array
    {
        return [
            'book_id' => Book::factory(),
            'title' => 'Bab ' . fake()->numberBetween(1, 10) . ': ' . fake()->sentence(2),
            'start_page' => 1,
            'end_page' => 20,
        ];
    }
}
