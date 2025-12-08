<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Chapter;
use App\Models\Test;
use App\Models\User;
use App\Models\Result;
use App\Models\ReadingProgress;
use App\Models\UserQuizAttempt;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 🔹 Statistik dasar
        $totalBooks = Book::count();
        $totalChapters = Chapter::count();
        $totalTests = Test::count();
        $totalUsers = User::where('role', 'mahasiswa')->count(); // hanya mahasiswa/non-admin
        $totalResults = UserQuizAttempt::count(); // Total semua percobaan


        $totalAttempts = UserQuizAttempt::count();
        $avgAttemptScoreGlobal = round(UserQuizAttempt::avg('score') ?? 0, 1);
        $avgAttemptsPerUser = round(UserQuizAttempt::select('user_id')->distinct()->count() > 0
            ? $totalAttempts / UserQuizAttempt::select('user_id')->distinct()->count()
            : 0, 1);

        // 🔹 Nilai rata-rata global
        $avgScoreGlobal = UserQuizAttempt::count() > 0 ? round(UserQuizAttempt::avg('score'), 2) : 0;

        // 🔹 Data per buku
        $bookStats = Book::with(['tests.userQuizAttempts'])
            ->get()
            ->map(function ($book) {
                $tests = $book->tests;
                // Ambil semua attempt dari semua tes di buku ini
                $attempts = $tests->flatMap->userQuizAttempts;

                $avg = $attempts->count() ? round($attempts->avg('score'), 2) : 0;
                $taken = $attempts->count();
                $passed = $attempts->where('score', '>=', 80)->count();

                return [
                    'title' => $book->title,
                    'tests_count' => $tests->count(),
                    'taken' => $taken,
                    'passed' => $passed,
                    'avg_score' => $avg,
                ];
            });

        // 🔹 Buku dengan performa tertinggi & terendah
        $topBook = $bookStats->sortByDesc('avg_score')->first();
        $worstBook = $bookStats->sortBy('avg_score')->first();



        // 🔹 Mahasiswa dengan progress terbanyak
        $topStudents = ReadingProgress::selectRaw('user_id, COUNT(book_id) as total_books, MAX(updated_at) as last_update')
            ->groupBy('user_id')
            ->orderByDesc('total_books')
            ->take(5)
            ->get()
            ->map(function ($progress) {
                $user = User::find($progress->user_id);

                // === PERBAIKAN: NULL CHECK MENGGUNAKAN IF ===
                if (!$user) {
                     return [
                        'name' => 'User Terhapus (ID: ' . $progress->user_id . ')', // Memberikan ID agar mudah di-debug
                        'books' => $progress->total_books,
                        'last_update' => $progress->last_update,
                    ];
                }
                // ===========================================

                return [
                    'name' => $user->name,
                    'books' => $progress->total_books,
                    'last_update' => $progress->last_update,
                ];
            });

        // 🔹 Data untuk grafik
        $chartLabels = $bookStats->pluck('title');
        $chartTests = $bookStats->pluck('tests_count');
        $chartAvgScores = $bookStats->pluck('avg_score');

        return view('admin.dashboard', compact(
            'totalBooks',
            'totalChapters',
            'totalTests',
            'totalUsers',
            'totalResults',
            'avgScoreGlobal',
            'bookStats',
            'topBook',
            'worstBook',
            'topStudents',
            'chartLabels',
            'chartTests',
            'chartAvgScores',
            'totalAttempts',
            'avgAttemptScoreGlobal',
            'avgAttemptsPerUser'
        ));
    }
}
