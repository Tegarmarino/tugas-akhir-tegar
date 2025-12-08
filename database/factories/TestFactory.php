<?php

namespace Database\Factories;

use App\Models\Test;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestFactory extends Factory
{
    protected $model = Test::class;

    public function definition(): array
    {
        return [
            'book_id' => Book::factory(),
            'chapter_id' => null,
            'type' => $this->faker->randomElement(['pre', 'post']),
            'title' => 'Test ' . $this->faker->word(),
        ];
    }
}
