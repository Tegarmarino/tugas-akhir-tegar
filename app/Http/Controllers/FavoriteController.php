<?php
namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{

    public function index()
    {
        $favoriteBooks = Auth::user()->favoriteBooks()->paginate(10);
        return view('favorites.index', compact('favoriteBooks'));
    }

    public function toggle(Request $request, Book $book)
    {
        $user = Auth::user();
        if ($user->favoriteBooks()->where('book_id', $book->id)->exists()) {
            $user->favoriteBooks()->detach($book->id);
            $message = 'Buku dihapus dari favorit.';
            $isFavorited = false;
        } else {
            $user->favoriteBooks()->attach($book->id);
            $message = 'Buku ditambahkan ke favorit.';
            $isFavorited = true;
        }

        if ($request->ajax()) {
            return response()->json(['message' => $message, 'is_favorited' => $isFavorited]);
        }
        return back()->with('status', $message);
    }
}
