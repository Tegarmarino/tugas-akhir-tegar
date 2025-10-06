<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Bookmark; // Pastikan ini di-import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Import Log facade

class BookmarkController extends Controller
{
    /**
     * Display a listing of the resource for a specific book and user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, Book $book) // Menggunakan Route Model Binding untuk $book
    {
        if (!Auth::check()) {
            Log::warning("[BookmarksAPI] User not authenticated when trying to fetch bookmarks for Book ID: {$book->id}");
            return response()->json(['error' => 'User tidak terautentikasi.'], 401);
        }

        $userId = Auth::id();
        Log::info("[BookmarksAPI] Fetching bookmarks for Book ID: {$book->id}, User ID: {$userId}");

        // Ambil bookmark berdasarkan user_id dan book_id, urutkan berdasarkan page_number
        // Pilih hanya kolom yang dibutuhkan oleh frontend untuk efisiensi
        $bookmarks = $book->bookmarks()
                          ->where('user_id', $userId)
                          ->orderBy('page_number', 'asc')
                          ->get(['id', 'page_number', 'note', 'created_at']); // Ambil ID dan created_at jika diperlukan

        Log::info("[BookmarksAPI] Found " . $bookmarks->count() . " bookmarks for Book ID: {$book->id}, User ID: {$userId}.");
        // Log data mentah sebelum dikirim sebagai JSON, untuk debugging
        Log::debug("[BookmarksAPI] Raw bookmark data: ", $bookmarks->toArray());
        Log::info("[BookmarksAPI] JSON data being sent: " . $bookmarks->toJson());

        return response()->json($bookmarks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'page_number' => 'required|integer|min:1',
            'note' => 'nullable|string|max:255', // Anda bisa menambahkan input untuk 'note' di JS jika mau
        ]);

        Log::info("[BookmarksAPI] Attempting to store bookmark for User ID: " . Auth::id() . ", Book ID: " . $request->book_id . ", Page: " . $request->page_number);

        try {
            $bookmark = Bookmark::create([
                'user_id' => Auth::id(),
                'book_id' => $request->book_id,
                'page_number' => $request->page_number,
                'note' => $request->note,
            ]);
            Log::info("[BookmarksAPI] Bookmark stored successfully. ID: " . $bookmark->id);
            return response()->json(['message' => 'Bookmark berhasil ditambahkan!', 'bookmark' => $bookmark], 201);
        } catch (\Exception $e) {
            Log::error("[BookmarksAPI] Failed to store bookmark: " . $e->getMessage());
            return response()->json(['message' => 'Gagal menambahkan bookmark: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bookmark  $bookmark
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Bookmark $bookmark) // Menggunakan Route Model Binding
    {
        Log::info("[BookmarksAPI] Attempting to delete bookmark ID: {$bookmark->id} for User ID: " . Auth::id());
        if ($bookmark->user_id !== Auth::id()) {
            Log::warning("[BookmarksAPI] Unauthorized attempt to delete bookmark ID: {$bookmark->id} by User ID: " . Auth::id());
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }
        try {
            $bookmark->delete();
            Log::info("[BookmarksAPI] Bookmark ID: {$bookmark->id} deleted successfully.");
            return response()->json(['message' => 'Bookmark berhasil dihapus!']);
        } catch (\Exception $e) {
            Log::error("[BookmarksAPI] Failed to delete bookmark ID: {$bookmark->id}: " . $e->getMessage());
            return response()->json(['message' => 'Gagal menghapus bookmark: ' . $e->getMessage()], 500);
        }
    }
}
