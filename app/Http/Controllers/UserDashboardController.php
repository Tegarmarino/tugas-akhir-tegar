<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\ReadingProgress;
use App\Models\Result;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;
use App\Models\UserQuizAttempt;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Statistik utama
        $favoriteCount = Favorite::where('user_id', $user->id)->count();
        $progressBooks = ReadingProgress::where('user_id', $user->id)->pluck('book_id')->unique();
        $booksReadCount = $progressBooks->count();

        $testResults = Result::where('user_id', $user->id)->get();
        $testsDone = $testResults->count();
        $testsPassed = $testResults->where('score', '>=', 80)->count();
        $testsFailed = $testResults->where('score', '<', 80)->count();
        $avgScore = $testsDone > 0 ? round($testResults->avg('score'), 2) : 0;

        // Hitung total attempt
        $totalAttempts = UserQuizAttempt::where('user_id', $user->id)->count();

        // Hitung rata-rata nilai semua attempt
        $avgAttemptScore = UserQuizAttempt::where('user_id', $user->id)->avg('score') ?? 0;

        // Hitung attempt per jenis
        $preAttempts = UserQuizAttempt::where('user_id', $user->id)->where('type', 'pre-test')->count();
        $postAttempts = UserQuizAttempt::where('user_id', $user->id)->where('type', 'post-test')->count();

        // Buku yang pernah dibaca
        $books = Book::whereIn('id', $progressBooks)
            ->with(['chapters', 'tests' => fn($q) => $q->where('type', 'post')])
            ->get();

        $progressData = [];
        $chartLabels = [];
        $chartProgress = [];
        $unpassedTests = [];

        foreach ($books as $book) {
            $progress = ReadingProgress::where('user_id', $user->id)
                ->where('book_id', $book->id)
                ->first();

            $pageProgress = $progress
                ? round(($progress->last_page_number / max(1, $book->total_pages)) * 100)
                : 0;

            $postTests = $book->tests->map(function ($test) use ($user, $book, &$unpassedTests) {
                $result = Result::where('user_id', $user->id)
                    ->where('test_id', $test->id)
                    ->first();

                $chapterTitle = optional($test->chapter)->title ?? "Bab Tidak Dikenal";

                if (!$result) {
                    // 🔹 Belum pernah dikerjakan sama sekali
                    $unpassedTests[] = [
                        'book' => $book->title,
                        'chapter' => $chapterTitle,
                        'score' => null,
                        'test_id' => $test->id
                    ];
                    $status = 'Belum Dikerjakan';
                } elseif ($result->score < 80) {
                    // 🔹 Sudah dikerjakan tapi gagal
                    $unpassedTests[] = [
                        'book' => $book->title,
                        'chapter' => $chapterTitle,
                        'score' => $result->score,
                        'test_id' => $test->id
                    ];
                    $status = 'Belum Lulus';
                } else {
                    $status = 'Lulus';
                }

                return [
                    'chapter_id' => $test->chapter_id,
                    'score' => $result->score ?? null,
                    'status' => $status,
                ];
            });


            $progressData[] = [
                'book' => $book,
                'pageProgress' => $pageProgress,
                'postTests' => $postTests,
            ];

            $chartLabels[] = $book->title;
            $chartProgress[] = $pageProgress;
        }

        return view('dashboard', compact(
            'favoriteCount',
            'booksReadCount',
            'testsDone',
            'testsPassed',
            'testsFailed',
            'avgScore',
            'progressData',
            'chartLabels',
            'chartProgress',
            'unpassedTests',
            'totalAttempts',
            'avgAttemptScore',
            'preAttempts',
            'postAttempts',
        ));
    }
}
