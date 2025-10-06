<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book; // Pastikan model Book sudah dibuat

class UserDashboardController extends Controller
{
    public function index()
    {
        // Mungkin menampilkan beberapa buku terbaru atau rekomendasi
        $books = Book::latest()->take(6)->get();
        return view('dashboard', compact('books')); // Mengarah ke resources/views/dashboard.blade.php
    }
}
