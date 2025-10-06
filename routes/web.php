<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminBookController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ReadingController;
use App\Http\Controllers\BookmarkController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\QuizController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard'); // Bisa jadi halaman katalog
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
    Route::post('/books/{book}/favorite', [FavoriteController::class, 'toggle'])->name('books.favorite');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');

    Route::get('/books/{book}/read', [ReadingController::class, 'show'])
         ->middleware('check.pretest') // <-- TAMBAHKAN INI
         ->name('books.read');
    Route::post('/books/{book}/chat', [ReadingController::class, 'chatWithBookAI'])->name('books.chat');
    Route::post('/books/{book}/highlight-define', [ReadingController::class, 'defineHighlightedText'])->name('books.highlight.define');
    Route::resource('bookmarks', BookmarkController::class)->only(['store', 'destroy', 'index']); // Untuk bookmark

    // Route untuk menampilkan halaman kuis
    Route::get('/quiz/{quiz}', [QuizController::class, 'show'])->name('quiz.show');
    // Route untuk memproses jawaban kuis
    Route::post('/quiz/{quiz}', [QuizController::class, 'store'])->name('quiz.store');

    // TAMBAHKAN ROUTE INI UNTUK MENYIMPAN PROGRES
    Route::patch('/books/{book}/progress', [ReadingController::class, 'updateProgress'])->name('books.progress.update');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('books', AdminBookController::class);
    // Rute admin lainnya
});

require __DIR__.'/auth.php';
