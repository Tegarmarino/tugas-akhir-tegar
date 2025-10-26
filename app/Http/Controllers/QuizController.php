<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    // Tampilkan halaman kuis (pre/post)
    public function show(Test $quiz)
    {
        $quiz->load('questions');
        return view('quiz.show', ['test' => $quiz]);
    }

    // Simpan jawaban user, nilai, dan hasil
    public function store(Request $request, Test $quiz)
    {
        $user = Auth::user();
        $answers = $request->input('answers', []);
        $score = 0;
        $total = $quiz->questions->count();

        foreach ($quiz->questions as $question) {
            $correct = $question->correct_answer;
            $userAnswer = $answers[$question->id] ?? null;
            if ($userAnswer === $correct) {
                $score++;
            }
        }

        $finalScore = $total > 0 ? round(($score / $total) * 100, 2) : 0;

        // ✅ Simpan hasil (walau gagal, tetap tersimpan agar admin bisa lihat progres)
        $result = \App\Models\Result::updateOrCreate(
            ['user_id' => $user->id, 'test_id' => $quiz->id],
            ['score' => $finalScore]
        );

        // ✅ Logika khusus untuk POST-TEST
        if ($quiz->type === 'post') {
            $passingGrade = 80;

            if ($finalScore >= $passingGrade) {
                // Lulus
                return redirect()
                    ->route('books.read', $quiz->book_id)
                    ->with('success', "🎉 Selamat! Anda lulus Post-Test dengan skor {$finalScore}. Bab ini dianggap selesai.");
            } else {
                // Gagal
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
