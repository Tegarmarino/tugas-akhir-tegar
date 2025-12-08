<?php

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition(): array
    {
        return [
            'question_text' => $this->faker->sentence() . '?',
            'option_a' => $this->faker->sentence(2),
            'option_b' => $this->faker->sentence(2),
            'option_c' => $this->faker->sentence(2),
            'option_d' => $this->faker->sentence(2),
            'correct_answer' => $this->faker->randomElement(['a', 'b', 'c', 'd']),
            'tag' => $this->faker->word(),
        ];
    }
}
