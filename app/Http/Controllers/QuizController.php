<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\Result;
use App\Models\UserQuizAttempt; // ✅ Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // ✅ Tambahkan ini juga

class QuizController extends Controller
{
    // =========================
    // 🔹 Tampilkan halaman kuis
    // =========================
    public function show(Test $quiz)
    {
        // === PERBAIKAN DI SINI: EAGER LOAD RELASI BAB ===
        // Kita eager load questions dan chapter untuk mendapatkan nama bab
        $quiz->load(['questions', 'chapter']);
        // ==============================================

        // Tentukan Judul yang akan ditampilkan di halaman kuis
        $pageTitle = $quiz->title;

        if ($quiz->type === 'post' && $quiz->chapter) {
            // Jika ini Post-Test dan ada relasi bab, gunakan judul bab
            $pageTitle = "Post Test: " . $quiz->chapter->title;
        } elseif ($quiz->type === 'pre') {
            // Jika Pre-Test, gunakan judul tes standar
            $pageTitle = "Pre Test: " . $quiz->book->title;
        }

        return view('quiz.show', ['test' => $quiz, 'pageTitle' => $pageTitle]);
    }

    // =========================
    // 🔹 Simpan jawaban user, nilai, dan hasil
    // =========================
    public function store(Request $request, Test $quiz)
    {
        $user = Auth::user();
        $answers = $request->input('answers', []);
        $score = 0;
        $total = $quiz->questions->count();

        // Hitung skor
        foreach ($quiz->questions as $question) {
            $correct = $question->correct_answer;
            $userAnswer = $answers[$question->id] ?? null;
            if ($userAnswer === $correct) {
                $score++;
            }
        }

        $finalScore = $total > 0 ? round(($score / $total) * 100, 2) : 0;

        // ✅ Simpan hasil ke tabel results (untuk status lulus/gagal)
        $result = Result::updateOrCreate(
            ['user_id' => $user->id, 'test_id' => $quiz->id],
            ['score' => $finalScore]
        );

        // ==============================
        // ✅ Simpan ke tabel user_quiz_attempts
        // ==============================
        $testType = $quiz->type === 'pre' ? 'pre-test' : 'post-test';

        UserQuizAttempt::create([
            'user_id' => $user->id,
            'test_id' => $quiz->id,   // ✅ kolom baru
            'type' => $testType,
            'score' => $finalScore,
            'completed_at' => Carbon::now(),
        ]);

        // ==============================
        // ✅ Logika kelulusan khusus post-test
        // ==============================
        if ($quiz->type === 'post') {
            $passingGrade = 80;

            if ($finalScore >= $passingGrade) {
                // ✅ Lulus
                return redirect()
                    ->route('books.read', $quiz->book_id)
                    ->with('success', "🎉 Selamat! Anda lulus Post-Test dengan skor {$finalScore}. Bab ini dianggap selesai.");
            } else {
                // ❌ Gagal
                return redirect()
                    ->route('quiz.show', $quiz->id)
                    ->with('error', "❌ Anda belum lulus Post-Test. Skor Anda: {$finalScore}. Minimal nilai lulus adalah {$passingGrade}. Silakan coba lagi.");
            }
        }

        // ✅ Jika bukan post-test (pre-test)
        return redirect()
            ->route('books.read', $quiz->book_id)
            ->with('success', "✅ Pre-Test selesai! Skor Anda: {$finalScore}");
    }
}
