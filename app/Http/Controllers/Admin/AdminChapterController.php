<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Chapter;
use Illuminate\Http\Request;

class AdminChapterController extends Controller
{
    // Tampilkan daftar bab per buku
    public function index(Book $book)
    {
        $chapters = $book->chapters()->orderBy('start_page')->get();
        return view('admin.chapters.index', compact('book', 'chapters'));
    }

    // Simpan bab baru (multi input)
   public function store(Request $request, Book $book)
    {
        $validated = $request->validate([
            'chapters' => 'required|array|min:1',
            'chapters.*.title' => 'required|string|max:255',
            'chapters.*.start_page' => 'required|integer|min:1',
            'chapters.*.end_page' => 'required|integer|gte:chapters.*.start_page',
        ]);

        $existingChapters = $book->chapters()->get();
        $newChapters = collect($validated['chapters'])->map(fn($c) => (object)$c)->values(); // ubah ke objek
        $errors = [];
        $validChapters = [];

        // ✅ 1. Cek antar bab baru (batch)
        foreach ($newChapters as $i => $chapterA) {
            foreach ($newChapters as $j => $chapterB) {
                if ($i !== $j) {
                    if (!($chapterA->end_page < $chapterB->start_page || $chapterA->start_page > $chapterB->end_page)) {
                        $errors[] = "Bab dengan halaman {$chapterA->start_page}-{$chapterA->end_page} tumpang tindih dengan bab {$chapterB->start_page}-{$chapterB->end_page} yang juga baru ditambahkan.";
                        $chapterA->invalid = true;
                        $chapterB->invalid = true;
                    }
                }
            }
        }

        // ✅ 2. Cek overlap terhadap bab lama
        foreach ($newChapters as $chapter) {
            $start = $chapter->start_page;
            $end = $chapter->end_page;

            $overlap = $existingChapters->first(function ($existing) use ($start, $end) {
                return !($end < $existing->start_page || $start > $existing->end_page);
            });

            if ($overlap) {
                $errors[] = "Halaman {$start}-{$end} tumpang tindih dengan bab '{$overlap->title}' ({$overlap->start_page}-{$overlap->end_page}).";
                $chapter->invalid = true;
            }
        }

        // ✅ 3. Simpan hanya yang valid
        foreach ($newChapters as $chapter) {
            if (empty($chapter->invalid)) {
                Chapter::create([
                    'book_id' => $book->id,
                    'title' => $chapter->title,
                    'start_page' => $chapter->start_page,
                    'end_page' => $chapter->end_page,
                ]);
                $validChapters[] = "{$chapter->start_page}-{$chapter->end_page}";
            }
        }

        // ✅ 4. Tentukan notifikasi hasil
        if (count($errors) > 0 && count($validChapters) > 0) {
            return redirect()->route('admin.chapters.index', $book->id)
                ->with('warning', 'Sebagian bab berhasil disimpan, sebagian lainnya tumpang tindih.')
                ->withErrors($errors);
        } elseif (count($errors) > 0 && count($validChapters) == 0) {
            return back()->withErrors($errors)->withInput();
        }

        return redirect()->route('admin.chapters.index', $book->id)
            ->with('success', 'Semua bab/sub-bab berhasil ditambahkan.');
    }







    public function edit(Book $book, Chapter $chapter)
    {
        return view('admin.chapters.edit', compact('book', 'chapter'));
    }

    public function update(Request $request, Book $book, Chapter $chapter)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_page' => 'required|integer|min:1',
            'end_page' => 'required|integer|gte:start_page',
        ]);

        $start = $validated['start_page'];
        $end = $validated['end_page'];

        // ✅ Ambil bab lain dari buku yang sama (kecuali bab ini)
        $existingChapters = $book->chapters()->where('id', '!=', $chapter->id)->get();

        // ✅ Cek overlap
        $overlap = $existingChapters->first(function ($existing) use ($start, $end) {
            return !($end < $existing->start_page || $start > $existing->end_page);
        });

        if ($overlap) {
            return back()->withErrors([
                'error' => "Halaman {$start}-{$end} tumpang tindih dengan bab '{$overlap->title}' ({$overlap->start_page}-{$overlap->end_page})."
            ])->withInput();
        }

        $chapter->update($validated);

        return redirect()->route('admin.chapters.index', $book->id)
            ->with('success', 'Bab/Sub-bab berhasil diperbarui.');
    }



    // Hapus bab
    public function destroy(Book $book, Chapter $chapter)
    {
        $chapter->delete();
        return redirect()->route('admin.chapters.index', $book->id)
            ->with('success', 'Bab/Sub-bab berhasil dihapus.');
    }
}
