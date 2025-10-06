<?php

namespace App\Http\Controllers; // Namespace sudah benar untuk user-facing controller

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Dibutuhkan untuk mengecek status favorit

// GeminiService dan PdfToText tidak diperlukan di sini jika controller ini
// hanya untuk menampilkan katalog dan detail buku yang datanya sudah ada.
// Pembuatan detail buku oleh AI terjadi di AdminBookController.

class BookController extends Controller
{
    // Konstruktor dengan GeminiService tidak diperlukan di sini
    // jika controller ini hanya menampilkan data yang sudah ada.

    /**
     * Display a listing of the books for regular users (book catalog).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Mengambil semua buku dengan paginasi, bisa ditambahkan filter pencarian
        $query = Book::query()->latest(); // Urutkan berdasarkan yang terbaru

        // Fitur pencarian sederhana berdasarkan judul atau penulis
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('author', 'like', "%{$searchTerm}%");
            });
        }

        $books = $query->paginate(12); // Tampilkan 12 buku per halaman

        // Mengarah ke view 'books.index' untuk tampilan pengguna
        return view('books.index', compact('books'));
    }

    /**
     * Display the specified book for regular users.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\View\View
     */
    public function show(Book $book)
    {
        // Eager load relasi yang mungkin diperlukan di view detail buku
        // Misalnya, untuk mengecek apakah user saat ini sudah memfavoritkan buku ini
        $isFavorited = false;
        if (Auth::check()) {
            $isFavorited = Auth::user()->favoriteBooks()->where('book_id', $book->id)->exists();
        }

        // Mengarah ke view 'books.show' untuk tampilan pengguna
        return view('books.show', compact('book', 'isFavorited'));
    }

    // Metode create(), store(), edit(), update(), destroy()
    // TIDAK ADA di controller ini karena itu adalah fungsi admin.
    // Logika untuk metode store() yang Anda berikan sebelumnya
    // (termasuk upload PDF dan integrasi Gemini untuk membuat detail buku)
    // seharusnya berada di App\Http\Controllers\Admin\AdminBookController.php
}
