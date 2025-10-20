<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Result;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    // Tampilkan halaman tes (pre atau post)
    public function show($testId)
    {
        $test = Test::with('questions')->findOrFail($testId);

        return view('quiz.show', compact('test'));
    }

    // Simpan hasil jawaban
    public function store(Request $request, $testId)
    {
        $test = Test::with('questions')->findOrFail($testId);
        $user = Auth::user();

        $answers = $request->input('answers', []);
        $score = 0;
        $total = $test->questions->count();

        foreach ($test->questions as $question) {
            $userAnswer = $answers[$question->id] ?? null;
            if ($userAnswer && strtolower($userAnswer) === strtolower($question->correct_answer)) {
                $score++;
            }
        }

        $finalScore = $total > 0 ? round(($score / $total) * 100, 2) : 0;

        Result::updateOrCreate(
            ['user_id' => $user->id, 'test_id' => $test->id],
            ['score' => $finalScore]
        );

        return redirect()->route('dashboard')->with('success', "Tes selesai! Nilai kamu: {$finalScore}");
    }
}


// namespace App\Http\Controllers;

// use App\Models\Quiz;
// use App\Models\ReadingProgress;
// use App\Models\UserQuizAttempt;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Log;

// class QuizController extends Controller
// {
//     /**
//      * Display the specified quiz.
//      */
//     public function show(Quiz $quiz, Request $request)
//     {
//         $type = $request->query('type', 'pre-test');
//         $quiz->load('questions');

//         return view('quiz.show', [
//             'quiz' => $quiz,
//             'type' => $type,
//         ]);
//     }

//     /**
//      * Store the user's quiz attempt and return results as JSON.
//      */
//     public function store(Request $request, Quiz $quiz)
//     {
//         $user = Auth::user();
//         $type = $request->input('type', 'pre-test');
//         $answers = $request->input('answers', []);

//         $quiz->load('questions', 'book');
//         $questions = $quiz->questions;

//         $score = 0;
//         foreach ($questions as $question) {
//             if (isset($answers[$question->id]) && $answers[$question->id] === $question->correct_answer) {
//                 $score++;
//             }
//         }

//         $totalQuestions = $questions->count();
//         $finalScore = ($totalQuestions > 0) ? round(($score / $totalQuestions) * 100) : 0;

//         Log::info("[QuizStore] User ID: {$user->id} completed {$type} for Quiz ID: {$quiz->id}. Score: {$score}/{$totalQuestions} ({$finalScore}%)");

//         // Simpan atau update hasil percobaan kuis
//         $attempt = UserQuizAttempt::updateOrCreate(
//             [
//                 'user_id' => $user->id,
//                 'quiz_id' => $quiz->id,
//                 'type' => $type,
//             ],
//             [
//                 'score' => $finalScore,
//                 'completed_at' => now(),
//             ]
//         );

//         $preTestScore = null;
//         $nextRoute = null;
//         $repeatReadingRoute = null;

//         if ($type === 'pre-test') {
//             ReadingProgress::updateOrCreate(
//                 ['user_id' => $user->id, 'book_id' => $quiz->book_id],
//                 ['last_page_number' => 1, 'is_finished' => false] // Pastikan is_finished false saat pre-test
//             );
//             $nextRoute = route('books.read', $quiz->book);
//         } else { // Ini adalah post-test
//             $preTestAttempt = UserQuizAttempt::where('user_id', $user->id)
//                 ->where('quiz_id', $quiz->id)
//                 ->where('type', 'pre-test')
//                 ->first();
//             if ($preTestAttempt) {
//                 $preTestScore = $preTestAttempt->score;
//             }

//             $nextRoute = route('books.index');
//             $repeatReadingRoute = route('books.read', $quiz->book);

//             // PERBAIKAN LOGIKA: Cek skor dan update status 'is_finished'
//             $hasPassed = false;
//             if ($preTestScore !== null && $finalScore >= $preTestScore) {
//                 $hasPassed = true;
//             }

//             if ($hasPassed) {
//                 Log::info("[QuizStore] Post-test PASSED for User ID: {$user->id}, Book ID: {$quiz->book_id}. Setting is_finished to true.");
//                 $updatedRows = ReadingProgress::where('user_id', $user->id)
//                     ->where('book_id', $quiz->book_id)
//                     ->update(['is_finished' => true]);
//                 Log::info("[QuizStore] Rows updated in reading_progress: {$updatedRows}");
//             } else {
//                 Log::warning("[QuizStore] Post-test FAILED or pre-test not found for User ID: {$user->id}, Book ID: {$quiz->book_id}. is_finished remains false.");
//             }
//         }

//         return response()->json([
//             'success' => true,
//             'attempt' => $attempt,
//             'bookTitle' => $quiz->book->title,
//             'preTestScore' => $preTestScore,
//             'nextRoute' => $nextRoute,
//             'repeatReadingRoute' => $repeatReadingRoute,
//         ]);
//     }
// }
