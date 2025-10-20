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
use App\Http\Controllers\Admin\AdminQuestionController;
use App\Http\Controllers\Admin\AdminChapterController;
use App\Http\Controllers\Admin\AdminTestController;

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

    Route::delete('/books/{book}/progress/reset', [\App\Http\Controllers\ReadingController::class, 'resetProgress'])
    ->name('books.progress.reset')
    ->middleware('auth');

    Route::post('/books/{book}/chapters/{chapter}/chat', [ReadingController::class, 'chatWithChapterAI'])
    ->name('books.chapters.chat');


});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('books', AdminBookController::class);
    Route::resource('questions', AdminQuestionController::class);
    Route::delete('/admin/questions/bulk-delete', [AdminQuestionController::class, 'bulkDelete'])
    ->name('questions.bulk-delete');

    // Rute admin lainnya
    // ✅ Tambahkan routes chapters di sini
    Route::get('/books/{book}/chapters', [AdminChapterController::class, 'index'])->name('chapters.index');
    Route::post('/books/{book}/chapters', [AdminChapterController::class, 'store'])->name('chapters.store');
    Route::get('/books/{book}/chapters/{chapter}/edit', [AdminChapterController::class, 'edit'])->name('chapters.edit');
    Route::put('/books/{book}/chapters/{chapter}', [AdminChapterController::class, 'update'])->name('chapters.update');
    Route::delete('/books/{book}/chapters/{chapter}', [AdminChapterController::class, 'destroy'])->name('chapters.destroy');

    Route::get('books/{book}/assign-pretest', [AdminTestController::class, 'createPreTest'])->name('tests.pre.create');
    Route::post('books/{book}/assign-pretest', [AdminTestController::class, 'storePreTest'])->name('tests.pre.store');

   // routes/web.php
    Route::get('books/{book}/pretest', [AdminTestController::class, 'showPreTest'])->name('tests.pre.show');
    Route::post('books/{book}/pretest', [AdminTestController::class, 'savePreTest'])->name('tests.pre.save');



    // Post-test per bab/sub-bab
    Route::get('books/{book}/posttest', [AdminTestController::class, 'showPostTest'])->name('tests.post.show');
    Route::post('books/{book}/posttest', [AdminTestController::class, 'storePostTest'])->name('tests.post.store');
    Route::post('books/{book}/posttest/update', [AdminTestController::class, 'updatePostTest'])->name('tests.post.update');

});

require __DIR__.'/auth.php';
