<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'title',
    ];

    /**
     * Get the book that this quiz belongs to.
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get all of the questions for the quiz.
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Get all of the attempts for the quiz.
     */
    public function attempts()
    {
        return $this->hasMany(UserQuizAttempt::class);
    }
}
