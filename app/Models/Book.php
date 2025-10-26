<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Bookmark;
use App\Models\User;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'publication_date',
        'overview',
        'file_path',
        'cover_image_path',
        'total_pages',
    ];

    protected $casts = [
        'publication_date' => 'date',
    ];

    /**
     * Get the quiz associated with the book.
     */
    public function quiz()
    {
        return $this->hasOne(Quiz::class);
    }

    /**
     * Get the reading progress records for the book.
     */
    public function readingProgress()
    {
        return $this->hasMany(ReadingProgress::class);
    }

    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    // public function chapters()
    // {
    //     return $this->hasMany(Chapter::class);
    // }

    public function chapters()
    {
        return $this->hasMany(Chapter::class)->orderBy('start_page', 'asc');
    }


    public function tests()
    {
        return $this->hasMany(Test::class);
    }


}
