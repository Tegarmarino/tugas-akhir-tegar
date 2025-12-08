<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPreTest
{
    public function handle(Request $request, Closure $next)
    {
        $book = $request->route('book');
        $user = Auth::user();

        if (!$user) return redirect()->route('login');

        // Ambil pre-test untuk buku ini
        $preTest = $book->tests()->where('type', 'pre')->first();

        if ($preTest) {
            // Cek di tabel attempt, apakah user pernah mengerjakan (ada record-nya)
            $hasDone = \App\Models\UserQuizAttempt::where('user_id', $user->id)
                ->where('test_id', $preTest->id)
                ->exists();

            if (!$hasDone) {
                session([
                    'show_pretest_modal' => true,
                    'pretest_id' => $preTest->id,
                ]);
            }
        }

        return $next($request);
    }
}
