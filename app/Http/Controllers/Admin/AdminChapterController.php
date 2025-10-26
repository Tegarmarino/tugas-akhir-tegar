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
        $chapters = $book->chapters()->orderBy('start_page', 'asc')->get();
        return view('admin.chapters.index', compact('book', 'chapters'));
    }

    // Simpan bab baru (multi input)
   public function store(Request $request, Book $book)
    {
        $validated = $request->validate([
            'chapters' => 'required|array|min:1',
            'chapters.*.title' => 'required|string|max:255',
            'chapters.*.start_page' => [
                'required', 'integer', 'min:1',
                function ($attribute, $value, $fail) use ($book) {
                    if ($value > $book->total_pages) {
                        $fail("Halaman awal ({$value}) tidak boleh melebihi total halaman buku ({$book->total_pages}).");
                    }
                },
            ],
            'chapters.*.end_page' => [
                'required', 'integer',
                function ($attribute, $value, $fail) use ($book, $request) {
                    if ($value > $book->total_pages) {
                        $fail("Halaman akhir ({$value}) tidak boleh melebihi total halaman buku ({$book->total_pages}).");
                    }
                },
            ],
        ]);


        $existingChapters = $book->chapters()->orderBy('id')->get();
        $newChapters = collect($validated['chapters'])->map(fn($c) => (object)$c)->values();
        $errors = [];
        $validChapters = [];

        // ✅ Ambil nomor bab terakhir
        $nextNumber = $existingChapters->count() + 1;

        // ✅ 1. Cek antar bab baru (tumpang tindih)
        foreach ($newChapters as $i => $chapterA) {
            foreach ($newChapters as $j => $chapterB) {
                if ($i !== $j) {
                    if (!($chapterA->end_page < $chapterB->start_page || $chapterA->start_page > $chapterB->end_page)) {
                        $errors[] = "Bab {$chapterA->start_page}-{$chapterA->end_page} tumpang tindih dengan bab {$chapterB->start_page}-{$chapterB->end_page}.";
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

        // ✅ 3. Simpan hanya yang valid dan beri nomor otomatis
        foreach ($newChapters as $chapter) {
            if (empty($chapter->invalid)) {
                $numberedTitle = "Bab {$nextNumber}: {$chapter->title}";
                Chapter::create([
                    'book_id' => $book->id,
                    'title' => $numberedTitle,
                    'start_page' => $chapter->start_page,
                    'end_page' => $chapter->end_page,
                ]);
                $validChapters[] = $numberedTitle;
                $nextNumber++;
            }
        }

        // ✅ 4. Pesan hasil
        if (count($errors) > 0 && count($validChapters) > 0) {
            return redirect()->route('admin.chapters.index', $book->id)
                ->with('warning', 'Sebagian bab berhasil disimpan, sebagian lainnya tumpang tindih.')
                ->withErrors($errors);
        } elseif (count($errors) > 0 && count($validChapters) == 0) {
            return back()->withErrors($errors)->withInput();
        }

        return redirect()->route('admin.chapters.index', $book->id)
            ->with('success', 'Semua bab/sub-bab berhasil ditambahkan dengan penomoran otomatis.');
    }


    public function edit(Book $book, Chapter $chapter)
    {
        return view('admin.chapters.edit', compact('book', 'chapter'));
    }

    // 🧩 Update Bab
    public function update(Request $request, Book $book, Chapter $chapter)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_page' => [
                'required', 'integer', 'min:1',
                function ($attribute, $value, $fail) use ($book) {
                    if ($value > $book->total_pages) {
                        $fail("Halaman awal ({$value}) tidak boleh melebihi total halaman buku ({$book->total_pages}).");
                    }
                },
            ],
            'end_page' => [
                'required', 'integer', 'gte:start_page',
                function ($attribute, $value, $fail) use ($book) {
                    if ($value > $book->total_pages) {
                        $fail("Halaman akhir ({$value}) tidak boleh melebihi total halaman buku ({$book->total_pages}).");
                    }
                },
            ],
        ]);


        $start = $validated['start_page'];
        $end = $validated['end_page'];

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

        // ✅ Deteksi nomor lama (format Bab X:)
        preg_match('/^Bab\s*(\d+):\s*(.*)$/', $chapter->title, $matches);
        $chapterNumber = $matches[1] ?? null;

        if ($chapterNumber) {
            $newTitle = "Bab {$chapterNumber}: {$validated['title']}";
        } else {
            $nextNumber = $book->chapters()->count();
            $newTitle = "Bab {$nextNumber}: {$validated['title']}";
        }

        $chapter->update([
            'title' => $newTitle,
            'start_page' => $start,
            'end_page' => $end,
        ]);

        // ✅ Auto-renumber setelah edit
        $this->renumberChapters($book);

        return redirect()->route('admin.chapters.index', $book->id)
            ->with('success', 'Bab/Sub-bab berhasil diperbarui dan penomoran diperbarui otomatis.');
    }

    // 🧩 Hapus Bab
    public function destroy(Book $book, Chapter $chapter)
    {
        $chapter->delete();

        // ✅ Auto-renumber setelah hapus
        $this->renumberChapters($book);

        return redirect()->route('admin.chapters.index', $book->id)
            ->with('success', 'Bab/Sub-bab berhasil dihapus dan penomoran diperbarui otomatis.');
    }

    /**
     * 🔁 Renumber Bab Otomatis Berdasarkan Urutan Halaman
     */
    private function renumberChapters(Book $book)
    {
        $i = 1;
        $chapters = $book->chapters()->orderBy('start_page')->get();

        foreach ($chapters as $chapter) {
            $cleanTitle = preg_replace('/^Bab\s*\d+:\s*/', '', $chapter->title);
            $chapter->update(['title' => "Bab {$i}: {$cleanTitle}"]);
            $i++;
        }
    }

}
