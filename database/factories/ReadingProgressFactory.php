<?php

namespace Database\Factories;

use App\Models\ReadingProgress;
use App\Models\User;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReadingProgressFactory extends Factory
{
    protected $model = ReadingProgress::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'book_id' => Book::factory(),
            'last_page_number' => fake()->numberBetween(1, 100),
        ];
    }
}
