<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Test;
use App\Models\Question;

class AdminTestController extends Controller
{
    public function showPreTest(Book $book, Request $request)
    {
        $tagFilter = $request->get('tag');

        $query = Question::query();
        if ($tagFilter) {
            $query->where('tag', $tagFilter);
        }

        $questions = $query->get();
        $tags = Question::select('tag')->distinct()->pluck('tag')->filter()->values();

        // Ambil pre-test jika sudah ada
        $test = $book->tests()->where('type', 'pre')->with('questions')->first();

        // Ambil daftar soal yang sudah dipilih
        $selectedIds = $test ? $test->questions->pluck('id')->toArray() : [];

        return view('admin.tests.assign-pre', compact('book', 'questions', 'test', 'selectedIds', 'tags', 'tagFilter'));
    }


    public function savePreTest(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'questions' => 'nullable|array',
        ]);

        $questions = $validated['questions'] ?? [];

        // Cari pre-test, buat baru jika belum ada
        $test = $book->tests()->where('type', 'pre')->first();

        if (!$test) {
            $test = \App\Models\Test::create([
                'book_id' => $book->id,
                'type' => 'pre',
                'title' => $validated['title'],
            ]);
        } else {
            $test->update(['title' => $validated['title']]);
        }

        // Sync pertanyaan
        $test->questions()->sync($questions);

        return redirect()->route('admin.tests.pre.show', $book->id)
            ->with('success', 'Pre-Test berhasil disimpan.');
    }


    // 🧩 Tampilkan form assign post-test per bab/sub-bab
    public function showPostTest(Book $book, Request $request)
    {
        $tagFilter = $request->get('tag');

        $query = Question::query();
        if ($tagFilter) {
            $query->where('tag', $tagFilter);
        }

        $questions = $query->get();
        $tags = Question::select('tag')->distinct()->pluck('tag')->filter()->values();

        $chapters = $book->chapters()->orderBy('start_page')->get();

        // Ambil semua test post yang sudah dibuat
        $existingTests = $book->tests()->where('type', 'post')->with('questions')->get();

        // Format: [chapter_id => [question_ids]]
        $selected = [];
        foreach ($existingTests as $test) {
            $selected[$test->chapter_id] = $test->questions->pluck('id')->toArray();
        }

        return view('admin.tests.assign-post', compact('book', 'chapters', 'questions', 'selected', 'tags', 'tagFilter'));
    }


    // 💾 Simpan post-test baru per bab
    public function storePostTest(Request $request, Book $book)
    {
        $validated = $request->validate([
            'tests' => 'required|array',
            'tests.*.chapter_id' => 'required|exists:chapters,id',
            'tests.*.questions' => 'nullable|array',
        ]);

        foreach ($validated['tests'] as $testData) {
            $chapterId = $testData['chapter_id'];
            $questions = $testData['questions'] ?? [];

            // Cek apakah sudah ada post-test untuk bab ini
            $test = $book->tests()->where('type', 'post')->where('chapter_id', $chapterId)->first();

            if (!$test) {
                $test = \App\Models\Test::create([
                    'book_id' => $book->id,
                    'chapter_id' => $chapterId,
                    'type' => 'post',
                    'title' => 'Post Test Bab ' . $chapterId,
                ]);
            }

            // Update daftar soal
            $test->questions()->sync($questions);
        }

        return redirect()->route('admin.tests.post.show', $book->id)
            ->with('success', 'Post-test per bab berhasil disimpan.');
    }


}
