<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Book;
use App\Models\Bookmark; // Jika Anda punya relasi bookmark juga

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the reading progress records for the user.
     */
    public function readingProgress()
    {
        return $this->hasMany(ReadingProgress::class);
    }

    /**
     * Get the quiz attempts for the user.
     */
    public function quizAttempts()
    {
        return $this->hasMany(UserQuizAttempt::class);
    }

    public function favoriteBooks()
    {
        // Nama tabel pivot adalah 'favorites' (default dari 'book_user' atau 'user_book')
        // foreignPivotKey defaultnya adalah 'book_id'
        // relatedPivotKey defaultnya adalah 'user_id'
        // Jika nama tabel pivot Anda adalah 'favorites' dan kolomnya user_id, book_id, ini sudah benar.
        return $this->belongsToMany(Book::class, 'favorites', 'user_id', 'book_id')->withTimestamps();
    }

    /**
     * The bookmarks that belong to the user.
     */
    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }
}
