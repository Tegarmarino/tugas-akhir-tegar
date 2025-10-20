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
use Smalot\PdfParser\Parser; // Untuk menghitung jumlah halaman

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
            // Simpan file PDF dan cover
            if ($request->hasFile('pdf_file')) {
                $bookData['file_path'] = $request->file('pdf_file')->store('books/pdfs', 'public');
                $absolutePdfPath = storage_path('app/public/' . $bookData['file_path']);
            }
            if ($request->hasFile('cover_image')) {
                $bookData['cover_image_path'] = $request->file('cover_image')->store('books/covers', 'public');
            }

            // 🚫 Validasi jumlah halaman maksimal 200
            if ($absolutePdfPath) {
                $parser = new Parser();
                $pdf = $parser->parseFile($absolutePdfPath);
                $pageCount = count($pdf->getPages());

                if ($pageCount > 200) {
                    Storage::disk('public')->delete($bookData['file_path']);
                    return back()->with('error', "❌ E-book terlalu tebal! Maksimal 200 halaman (Anda mengunggah {$pageCount} halaman).");
                }

                $bookData['total_pages'] = $pageCount;
            }

            // --- AI: Generate metadata dari halaman pertama ---
            if ($absolutePdfPath) {
                $pdf = new \setasign\Fpdi\Fpdi();
                $pageCount = $pdf->setSourceFile($absolutePdfPath);
                if ($pageCount < 1) { throw new \Exception("PDF tidak valid."); }

                $templateId = $pdf->importPage(1);
                $size = $pdf->getTemplateSize($templateId);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);
                $firstPagePdfContent = $pdf->Output('S');

                if (!empty($firstPagePdfContent)) {
                    $pdfData = ['mime_type' => 'application/pdf', 'data' => base64_encode($firstPagePdfContent)];
                    $prompt = "Analisis halaman pertama dari buku berjudul '{$validatedData['title']}'. Buat JSON: {\"author\": \"Nama Penulis\", \"publication_date\": \"YYYY-MM-DD\", \"overview\": \"Deskripsi singkat buku\"}";
                    $aiGenerated = $this->geminiService->generateBookDetailsFromPdf($prompt, $pdfData);

                    if ($aiGenerated && is_array($aiGenerated)) {
                        $bookData['author'] = $aiGenerated['author'] ?? null;
                        $bookData['publication_date'] = (!empty($aiGenerated['publication_date']) && strtotime($aiGenerated['publication_date']))
                            ? date('Y-m-d', strtotime($aiGenerated['publication_date']))
                            : null;
                        $bookData['overview'] = $aiGenerated['overview'] ?? null;
                    }
                }
            }

            // Simpan buku
            $book = Book::create($bookData);

            // --- Generate Kuis otomatis (AI) ---
            if ($absolutePdfPath) {
                \Log::info("[QuizGen] Starting quiz generation for Book ID: {$book->id}");
                try {
                    $fullTextContent = (new \Spatie\PdfToText\Pdf(config('services.pdftotext.path')))
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
                            \Log::info("[QuizGen] Successfully generated " . count($questionsData) . " questions for Quiz ID: {$quiz->id}");
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error("[QuizGen] Failed to generate quiz for Book ID: {$book->id}. Error: " . $e->getMessage());
                }
            }

            return redirect()->route('admin.books.index')->with('success', "📘 Buku berhasil ditambahkan ({$bookData['total_pages']} halaman).");

        } catch (\Exception $e) {
            \Log::error('General exception during book store: ' . $e->getMessage());
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
            'total_pages' => 'nullable|integer|min:1|max:200',
            'pdf_file' => 'nullable|file|mimes:pdf|max:30720',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        try {
            $updateData = [
                'title' => $validatedData['title'],
                'author' => $validatedData['author'] ?? null,
                'publication_date' => $validatedData['publication_date'] ?? null,
                'overview' => $validatedData['overview'] ?? null,
            ];

            // ✅ Cover baru
            if ($request->hasFile('cover_image')) {
                if ($book->cover_image_path && Storage::disk('public')->exists($book->cover_image_path)) {
                    Storage::disk('public')->delete($book->cover_image_path);
                }
                $updateData['cover_image_path'] = $request->file('cover_image')->store('books/covers', 'public');
            }

            // ✅ Jika PDF baru diupload
            if ($request->hasFile('pdf_file')) {
                \Log::info("[AdminUpdate] PDF baru diunggah untuk buku ID: {$book->id}");

                if ($book->file_path && Storage::disk('public')->exists($book->file_path)) {
                    Storage::disk('public')->delete($book->file_path);
                }

                $updateData['file_path'] = $request->file('pdf_file')->store('books/pdfs', 'public');
                $absolutePdfPath = storage_path('app/public/' . $updateData['file_path']);

                // 🚫 Validasi jumlah halaman otomatis dari PDF
                $parser = new Parser();
                $pdf = $parser->parseFile($absolutePdfPath);
                $pageCount = count($pdf->getPages());

                if ($pageCount > 200) {
                    Storage::disk('public')->delete($updateData['file_path']);
                    return back()->with('error', "❌ E-book terlalu tebal! Maksimal 200 halaman (Anda mengunggah {$pageCount} halaman).");
                }

                $updateData['total_pages'] = $pageCount;

                // 🧠 Regenerate AI info dari halaman pertama
                $pdfFpdi = new \setasign\Fpdi\Fpdi();
                $pdfFpdi->setSourceFile($absolutePdfPath);
                $templateId = $pdfFpdi->importPage(1);
                $size = $pdfFpdi->getTemplateSize($templateId);
                $pdfFpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdfFpdi->useTemplate($templateId);
                $firstPagePdfContent = $pdfFpdi->Output('S');

                if (!empty($firstPagePdfContent)) {
                    $pdfData = [
                        'mime_type' => 'application/pdf',
                        'data' => base64_encode($firstPagePdfContent)
                    ];

                    $prompt = "Analisis halaman pertama dari buku '{$updateData['title']}'. "
                        . "Buat JSON: {\"author\":..., \"publication_date\":..., \"overview\":...}";
                    $aiGenerated = $this->geminiService->generateBookDetailsFromPdf($prompt, $pdfData);

                    if ($aiGenerated && is_array($aiGenerated)) {
                        $updateData['author'] = $aiGenerated['author'] ?? $updateData['author'];
                        $updateData['publication_date'] = (!empty($aiGenerated['publication_date']) && strtotime($aiGenerated['publication_date']))
                            ? date('Y-m-d', strtotime($aiGenerated['publication_date']))
                            : $updateData['publication_date'];
                        $updateData['overview'] = $aiGenerated['overview'] ?? $updateData['overview'];
                    }
                }
            }
            else {
                // ✅ Tidak ada PDF baru, tapi admin bisa ubah total_pages manual
                if (!empty($validatedData['total_pages'])) {
                    $updateData['total_pages'] = $validatedData['total_pages'];
                }
            }

            $book->update($updateData);

            $totalPages = $updateData['total_pages'] ?? $book->total_pages;

            return redirect()
                ->route('admin.books.index')
                ->with('success', "📘 Buku berhasil diperbarui ({$totalPages} halaman).");

        } catch (\Exception $e) {
            \Log::error("Error updating book ID {$book->id}: " . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui buku: ' . $e->getMessage());
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
