<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Book;
use App\Models\UserQuizAttempt;

class CheckPreTestCompletion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Mengambil buku dari parameter route secara otomatis (Route Model Binding)
        /** @var Book $book */
        $book = $request->route('book');
        $user = Auth::user();

        // Jika buku tidak ditemukan atau tidak memiliki kuis, izinkan akses (atau tangani sebagai error)
        if (!$book || !$book->quiz) {
            // Untuk sekarang, kita izinkan akses jika tidak ada kuis
            return $next($request);
        }

        // Cek apakah user sudah pernah menyelesaikan pre-test untuk kuis ini
        $hasCompletedPreTest = UserQuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $book->quiz->id)
            ->where('type', 'pre-test')
            ->whereNotNull('completed_at') // Kunci utama: pastikan sudah diselesaikan
            ->exists();

        if ($hasCompletedPreTest) {
            // Jika sudah, izinkan pengguna untuk melanjutkan ke halaman baca
            return $next($request);
        }

        // Jika belum, arahkan (redirect) pengguna ke halaman untuk mengerjakan pre-test
        // Kita perlu membuat route 'quiz.show' ini di langkah selanjutnya
        return redirect()->route('quiz.show', ['quiz' => $book->quiz, 'type' => 'pre-test'])
                         ->with('info', 'Anda harus menyelesaikan pre-test sebelum membaca buku ini.');
    }
}
