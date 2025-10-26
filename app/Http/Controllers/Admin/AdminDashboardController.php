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
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 🔹 Statistik dasar
        $totalBooks = Book::count();
        $totalChapters = Chapter::count();
        $totalTests = Test::count();
        $totalUsers = User::where('role', 'user')->count(); // hanya mahasiswa/non-admin
        $totalResults = Result::count();


        $totalAttempts = UserQuizAttempt::count();
        $avgAttemptScoreGlobal = round(UserQuizAttempt::avg('score') ?? 0, 1);
        $avgAttemptsPerUser = round(UserQuizAttempt::select('user_id')->distinct()->count() > 0
            ? $totalAttempts / UserQuizAttempt::select('user_id')->distinct()->count()
            : 0, 1);

        // 🔹 Nilai rata-rata global
        $avgScoreGlobal = Result::count() > 0
            ? round(Result::avg('score'), 2)
            : 0;

        // 🔹 Data per buku
        $bookStats = Book::with(['tests.results'])
            ->get()
            ->map(function ($book) {
                $tests = $book->tests;
                $results = $tests->flatMap->results;
                $avg = $results->count() ? round($results->avg('score'), 2) : 0;
                $taken = $results->count();
                $passed = $results->where('score', '>=', 80)->count();

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
