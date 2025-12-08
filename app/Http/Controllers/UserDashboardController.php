<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\ReadingProgress;
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

        // Statistik berbasis Attempt
        $uniqueTestsTaken = UserQuizAttempt::where('user_id', $user->id)->distinct('test_id')->count();
        $testsDone = $uniqueTestsTaken; // Jumlah tes unik yang pernah dicoba

        // Hitung jumlah tes unik yang PERNAH lulus (skor >= 80 minimal sekali)
        $testsPassed = UserQuizAttempt::where('user_id', $user->id)
            ->where('score', '>=', 80)
            ->distinct('test_id')
            ->count();

        $testsFailed = $testsDone - $testsPassed;

        // Rata-rata nilai dari semua percobaan
        $avgScore = UserQuizAttempt::where('user_id', $user->id)->avg('score');
        $avgScore = $avgScore ? round($avgScore, 2) : 0;


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
                // Ganti Result dengan UserQuizAttempt (Ambil yang terbaru)
                $latestAttempt = UserQuizAttempt::where('user_id', $user->id)
                    ->where('test_id', $test->id)
                    ->latest()
                    ->first();

                $chapterTitle = optional($test->chapter)->title ?? "Bab Tidak Dikenal";

                if (!$latestAttempt) {
                    // 🔹 Belum pernah dikerjakan
                    $unpassedTests[] = [
                        'book' => $book->title,
                        'chapter' => $chapterTitle,
                        'score' => null,
                        'test_id' => $test->id
                    ];
                    $status = 'Belum Dikerjakan';
                } elseif ($latestAttempt->score < 80) {
                    // 🔹 Sudah dikerjakan tapi attempt terakhir masih gagal
                    $unpassedTests[] = [
                        'book' => $book->title,
                        'chapter' => $chapterTitle,
                        'score' => $latestAttempt->score,
                        'test_id' => $test->id
                    ];
                    $status = 'Belum Lulus';
                } else {
                    $status = 'Lulus';
                }

                return [
                    'chapter_id' => $test->chapter_id,
                    'score' => $latestAttempt->score ?? null,
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
