<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->name(),               // nullable di DB, tapi factory boleh isi
            'publication_date' => $this->faker->optional()->date(), // nullable
            'overview' => $this->faker->optional()->paragraph(),    // nullable
            'file_path' => 'books/pdfs/sample.pdf',              // path contoh
            'cover_image_path' => 'covers/sample.jpg',      // nullable
            'total_pages' => $this->faker->numberBetween(50, 200),
        ];
    }
}
