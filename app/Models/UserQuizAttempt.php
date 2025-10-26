<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserQuizAttempt extends Model
{
    use HasFactory;

    protected $table = 'user_quiz_attempts';

    protected $fillable = [
        'user_id',
        'test_id', // ✅ bukan quiz_id lagi
        'type',
        'score',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Quiz (atau Test)
    public function test()
    {
        return $this->belongsTo(Test::class, 'test_id');
    }

}
