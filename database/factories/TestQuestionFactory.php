<?php

namespace Database\Factories;

use App\Models\TestQuestion;
use App\Models\Test;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestQuestionFactory extends Factory
{

    public function definition(): array
    {
        return [
            'test_id' => Test::factory(),
            'question_id' => Question::factory(),
        ];
    }
}
