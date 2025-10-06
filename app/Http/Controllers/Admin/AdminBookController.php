<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use setasign\Fpdi\Fpdi; // PERBAIKAN: Menggunakan library FPDI yang benar
use Spatie\PdfToText\Pdf as PdfToText; // Untuk mengekstrak seluruh teks (seperti juru tulis)

class AdminBookController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function index()
    {
        $books = Book::latest()->paginate(10);
        return view('admin.books.index', compact('books'));
    }

    public function create()
    {
        return view('admin.books.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255|unique:books,title',
            'pdf_file' => 'required|file|mimes:pdf|max:30720', // 30MB
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $bookData = ['title' => $validatedData['title']];
        $absolutePdfPath = null;

        try {
            if ($request->hasFile('pdf_file')) {
                $bookData['file_path'] = $request->file('pdf_file')->store('books/pdfs', 'public');
                $absolutePdfPath = storage_path('app/public/' . $bookData['file_path']);
            }
            if ($request->hasFile('cover_image')) {
                $bookData['cover_image_path'] = $request->file('cover_image')->store('books/covers', 'public');
            }

            // --- TUGAS 1: Membuat Overview (pakai FPDI untuk analisis visual halaman 1) ---
            if ($absolutePdfPath) {
                $pdf = new Fpdi();
                $pageCount = $pdf->setSourceFile($absolutePdfPath);
                if ($pageCount < 1) { throw new \Exception("PDF tidak valid."); }

                $templateId = $pdf->importPage(1);
                $size = $pdf->getTemplateSize($templateId);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);
                $firstPagePdfContent = $pdf->Output('S');

                if (!empty($firstPagePdfContent)) {
                    $pdfData = ['mime_type' => 'application/pdf', 'data' => base64_encode($firstPagePdfContent)];
                    $prompt = "Analisis halaman pertama dari buku berjudul '{$validatedData['title']}' ini (disajikan dalam format PDF). Berdasarkan konten visual dan teksnya, generate informasi berikut dalam format JSON: author (string, nama penulis), publication_date (string, format YYYY-MM-DD atau perkiraan), dan overview (string, sinopsis singkat maksimal 250 kata). Format JSON yang diharapkan: {\"author\": \"Nama Penulis\", \"publication_date\": \"YYYY-MM-DD\", \"overview\": \"Overview buku...\"}";
                    $aiGenerated = $this->geminiService->generateBookDetailsFromPdf($prompt, $pdfData);

                    if ($aiGenerated && is_array($aiGenerated)) {
                        $bookData['author'] = $aiGenerated['author'] ?? null;
                        $bookData['publication_date'] = (!empty($aiGenerated['publication_date']) && strtotime($aiGenerated['publication_date'])) ? date('Y-m-d', strtotime($aiGenerated['publication_date'])) : null;
                        $bookData['overview'] = $aiGenerated['overview'] ?? null;
                    }
                }
            }

            // Simpan buku terlebih dahulu agar kita mendapatkan ID-nya
            $book = Book::create($bookData);

            // --- TUGAS 2: Membuat Kuis (pakai PdfToText untuk ekstrak seluruh teks) ---
            if ($absolutePdfPath) {
                Log::info("[QuizGen] Starting quiz generation for Book ID: {$book->id}");
                try {
                    $fullTextContent = (new PdfToText(config('services.pdftotext.path')))
                        ->setPdf($absolutePdfPath)
                        ->text();

                    if (!empty($fullTextContent)) {
                        $questionsData = $this->geminiService->generateQuizQuestions($fullTextContent, $book->title);
                        if ($questionsData && is_array($questionsData)) {
                            $quiz = $book->quiz()->create(['title' => "Kuis Pemahaman {$book->title}"]);
                            foreach ($questionsData as $questionItem) {
                                if (isset($questionItem['question_text'], $questionItem['options'], $questionItem['correct_answer'])) {
                                    $quiz->questions()->create($questionItem);
                                }
                            }
                            Log::info("[QuizGen] Successfully generated " . count($questionsData) . " questions for Quiz ID: {$quiz->id}");
                        }
                    }
                } catch (\Exception $e) {
                    Log::error("[QuizGen] Failed to generate quiz for Book ID: {$book->id}. Error: " . $e->getMessage());
                }
            }

            return redirect()->route('admin.books.index')->with('success', 'Buku berhasil ditambahkan dan kuis telah dibuat.');

        } catch (\Exception $e) {
            Log::error('General exception during book store: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan buku: ' . $e->getMessage());
        }
    }

    public function edit(Book $book)
    {
        return view('admin.books.edit', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255|unique:books,title,' . $book->id,
            'author' => 'nullable|string|max:255',
            'publication_date' => 'nullable|date_format:Y-m-d',
            'overview' => 'nullable|string',
            'pdf_file' => 'nullable|file|mimes:pdf|max:30720',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        try {
            $updateData = ['title' => $validatedData['title']];

            if ($request->hasFile('cover_image')) {
                if ($book->cover_image_path && Storage::disk('public')->exists($book->cover_image_path)) {
                    Storage::disk('public')->delete($book->cover_image_path);
                }
                $updateData['cover_image_path'] = $request->file('cover_image')->store('books/covers', 'public');
            }

            if ($request->hasFile('pdf_file')) {
                // --- LOGIKA RESET BUKU BARU ---
                Log::info("[AdminUpdate] PDF baru diunggah untuk buku ID: {$book->id}. Mereset data terkait.");

                // 1. Hapus semua progres baca pengguna untuk buku ini
                $book->readingProgress()->delete();
                Log::info("[AdminUpdate] Progres baca untuk buku ID: {$book->id} telah dihapus.");

                // 2. Hapus kuis lama dan soal-soalnya (cascade delete akan menghapus questions dan attempts)
                if ($book->quiz) {
                    $book->quiz->delete();
                    Log::info("[AdminUpdate] Kuis lama untuk buku ID: {$book->id} telah dihapus.");
                }

                // 3. Hapus file PDF lama
                if ($book->file_path && Storage::disk('public')->exists($book->file_path)) {
                    Storage::disk('public')->delete($book->file_path);
                }
                $updateData['file_path'] = $request->file('pdf_file')->store('books/pdfs', 'public');
                $absolutePdfPath = storage_path('app/public/' . $updateData['file_path']);

                // 4. Re-generate detail AI (overview, dll.)
                $pdf = new Fpdi();
                $pdf->setSourceFile($absolutePdfPath);
                $templateId = $pdf->importPage(1);
                $size = $pdf->getTemplateSize($templateId);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);
                $firstPagePdfContent = $pdf->Output('S');

                if (!empty($firstPagePdfContent)) {
                    $pdfData = ['mime_type' => 'application/pdf', 'data' => base64_encode($firstPagePdfContent)];
                    $prompt = "Analisis halaman pertama dari buku berjudul '{$updateData['title']}' ini (disajikan dalam format PDF). Berdasarkan konten visual dan teksnya, generate informasi berikut dalam format JSON: author (string, nama penulis), publication_date (string, format YYYY-MM-DD atau perkiraan), dan overview (string, sinopsis singkat maksimal 250 kata). Jika informasi tidak ada, kembalikan null. Format JSON yang diharapkan: {\"author\": \"Nama Penulis\", \"publication_date\": \"YYYY-MM-DD\", \"overview\": \"Overview buku...\"}";
                    $aiGenerated = $this->geminiService->generateBookDetailsFromPdf($prompt, $pdfData);

                    if ($aiGenerated && is_array($aiGenerated)) {
                        $updateData['author'] = $aiGenerated['author'] ?? null;
                        $updateData['publication_date'] = (!empty($aiGenerated['publication_date']) && strtotime($aiGenerated['publication_date'])) ? date('Y-m-d', strtotime($aiGenerated['publication_date'])) : null;
                        $updateData['overview'] = $aiGenerated['overview'] ?? null;
                    }
                }

                // 5. Update buku dengan info baru
                $book->update($updateData);

                // 6. Re-generate kuis baru
                Log::info("[QuizGen-Update] Starting quiz generation for updated Book ID: {$book->id}");
                $fullTextContent = (new PdfToText(config('services.pdftotext.path')))->setPdf($absolutePdfPath)->text();
                if (!empty($fullTextContent)) {
                    $questionsData = $this->geminiService->generateQuizQuestions($fullTextContent, $book->title);
                    if ($questionsData && is_array($questionsData)) {
                        $quiz = $book->quiz()->create(['title' => "Kuis Pemahaman {$book->title}"]);
                        foreach ($questionsData as $questionItem) {
                            if (isset($questionItem['question_text'], $questionItem['options'], $questionItem['correct_answer'])) {
                                $quiz->questions()->create($questionItem);
                            }
                        }
                        Log::info("[QuizGen-Update] Successfully generated new quiz for Book ID: {$book->id}");
                    }
                }

            } else {
                // --- LOGIKA UPDATE BIASA (TANPA PDF BARU) ---
                Log::info("[AdminUpdate] Tidak ada PDF baru untuk buku ID: {$book->id}. Menggunakan data dari form.");
                $updateData['author'] = $validatedData['author'];
                $updateData['publication_date'] = $validatedData['publication_date'];
                $updateData['overview'] = $validatedData['overview'];
                $book->update($updateData);
            }

            return redirect()->route('admin.books.index')->with('success', 'Buku berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error('General exception during book update for book ID ' . $book->id . ': ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui buku: ' . $e->getMessage());
        }
    }

    public function destroy(Book $book)
    {
        try {
            if ($book->file_path && Storage::disk('public')->exists($book->file_path)) {
                Storage::disk('public')->delete($book->file_path);
            }
            if ($book->cover_image_path && Storage::disk('public')->exists($book->cover_image_path)) {
                Storage::disk('public')->delete($book->cover_image_path);
            }
            $book->delete();
            return redirect()->route('admin.books.index')->with('success', 'Buku berhasil dihapus.');
        } catch (\Exception $e) {
             Log::error('Error deleting book ID ' . $book->id . ': ' . $e->getMessage());
            return redirect()->route('admin.books.index')->with('error', 'Gagal menghapus buku: ' . $e->getMessage());
        }
    }
}
