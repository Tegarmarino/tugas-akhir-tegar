<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question_text',
        'options',
        'correct_answer',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array', // Otomatis konversi JSON ke array PHP
    ];

    /**
     * Get the quiz that this question belongs to.
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
