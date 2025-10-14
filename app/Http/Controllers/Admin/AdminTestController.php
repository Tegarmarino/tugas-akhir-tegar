<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Test;
use App\Models\Question;

class AdminTestController extends Controller
{
    // Tampilkan halaman assign pre-test
    public function createPreTest(Book $book)
    {
        $questions = Question::all();
        return view('admin.tests.assign-pre', compact('book', 'questions'));
    }

    // Simpan hasil assign pre-test
    public function storePreTest(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'questions' => 'required|array|min:1',
        ]);

        // Pastikan hanya 1 pre-test per buku
        if ($book->tests()->where('type', 'pre')->exists()) {
            return back()->withErrors(['error' => 'Pre-test untuk buku ini sudah ada.'])->withInput();
        }

        // Simpan test
        $test = Test::create([
            'book_id' => $book->id,
            'type' => 'pre',
            'title' => $validated['title'],
        ]);

        // Hubungkan soal
        $test->questions()->attach($validated['questions']);

        return redirect()->route('admin.books.index')->with('success', 'Pre-test berhasil ditambahkan.');
    }

    // 🧠 Tampilkan status pre-test (sudah/belum)
    public function showPreTest(Book $book)
    {
        $test = $book->tests()->where('type', 'pre')->with('questions')->first();

        if (!$test) {
            return view('admin.tests.pre-status', [
                'book' => $book,
                'hasTest' => false,
            ]);
        }

        return view('admin.tests.pre-status', [
            'book' => $book,
            'hasTest' => true,
            'test' => $test,
        ]);
    }

    // ✏️ Edit pre-test
    public function editPreTest(Book $book)
    {
        $test = $book->tests()->where('type', 'pre')->with('questions')->first();
        $questions = \App\Models\Question::all();

        if (!$test) {
            return redirect()->route('admin.tests.pre.create', $book->id);
        }

        $selectedIds = $test->questions->pluck('id')->toArray();

        return view('admin.tests.assign-pre', compact('book', 'questions', 'test', 'selectedIds'));
    }

    // 💾 Update pre-test
    public function updatePreTest(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'questions' => 'required|array|min:1',
        ]);

        $test = $book->tests()->where('type', 'pre')->first();

        if (!$test) {
            return back()->withErrors(['error' => 'Pre-test tidak ditemukan.']);
        }

        $test->update(['title' => $validated['title']]);
        $test->questions()->sync($validated['questions']);

        return redirect()->route('admin.tests.pre.show', $book->id)
            ->with('success', 'Pre-test berhasil diperbarui.');
    }

}
