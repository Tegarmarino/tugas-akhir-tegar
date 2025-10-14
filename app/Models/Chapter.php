<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'title',
        'start_page',
        'end_page',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function tests()
    {
        return $this->hasMany(Test::class); // untuk post-test per bab
    }
}
