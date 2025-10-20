<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Result;
use Illuminate\Support\Facades\Auth;

class CheckPreTest
{
    public function handle(Request $request, Closure $next)
    {
        $book = $request->route('book');
        $user = Auth::user();

        if ($book && $user) {
            $preTest = $book->tests()->where('type', 'pre')->first();

            if ($preTest) {
                $hasDone = Result::where('user_id', $user->id)
                    ->where('test_id', $preTest->id)
                    ->exists();

                if (!$hasDone) {
                    session()->flash('show_pretest_modal', true);
                    session()->flash('pretest_id', $preTest->id);
                }
            }
        }

        return $next($request);
    }
}
