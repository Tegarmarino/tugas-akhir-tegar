<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReadingProgress extends Model
{
    use HasFactory;

    // Nama tabelnya 'reading_progress' (snake_case), Laravel akan otomatis mendeteksinya
    // dari nama model 'ReadingProgress' (PascalCase).

    protected $fillable = [
        'user_id',
        'book_id',
        'last_page_number',
        'is_finished',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_finished' => 'boolean',
    ];

    /**
     * Get the user that owns this progress record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the book associated with this progress record.
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
