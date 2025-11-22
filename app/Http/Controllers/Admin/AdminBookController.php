<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;
use setasign\Fpdi\Fpdi;

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
        $request->validate([
            'title' => 'required|string|max:255|unique:books,title',
            'pdf_file' => 'required|file|mimes:pdf|max:20480', // 20MB
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        try {
            $data = ['title' => $request->title];

            // 1. Upload Cover
            if ($request->hasFile('cover_image')) {
                $data['cover_image_path'] = $request->file('cover_image')->store('books/covers', 'public');
            }

            // 2. Upload & Validasi PDF
            if ($request->hasFile('pdf_file')) {
                $path = $request->file('pdf_file')->store('books/pdfs', 'public');
                $data['file_path'] = $path;

                // Gunakan public_path agar aman di hosting
                $absolutePath = public_path('storage/' . $path);

                // Cek Halaman (Max 200)
                $pageCount = $this->countPages($absolutePath);
                if ($pageCount > 200) {
                    Storage::disk('public')->delete($path);
                    return back()->with('error', "❌ Buku terlalu tebal ({$pageCount} hal). Maksimal 200 halaman.");
                }
                $data['total_pages'] = $pageCount;

                // 3. AI Analysis (Ambil 10 Halaman Pertama)
                $aiData = $this->analyzePdfWithAi($absolutePath, $request->title);
                if ($aiData) {
                    $data = array_merge($data, $aiData);
                }
            }

            $book = Book::create($data);

            return redirect()->route('admin.books.index')->with('success', "📘 Buku berhasil ditambahkan.");

        } catch (\Exception $e) {
            Log::error('Store Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // 👇 INI YANG TADI HILANG, SUDAH SAYA KEMBALIKAN
    public function edit(Book $book)
    {
        return view('admin.books.edit', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:books,title,' . $book->id,
            'pdf_file' => 'nullable|file|mimes:pdf|max:20480',
            'total_pages' => 'nullable|integer|max:200',
        ]);

        try {
            $data = ['title' => $request->title];

            // Update Cover
            if ($request->hasFile('cover_image')) {
                if ($book->cover_image_path) Storage::disk('public')->delete($book->cover_image_path);
                $data['cover_image_path'] = $request->file('cover_image')->store('books/covers', 'public');
            }

            // Update PDF
            if ($request->hasFile('pdf_file')) {
                if ($book->file_path) Storage::disk('public')->delete($book->file_path);

                $path = $request->file('pdf_file')->store('books/pdfs', 'public');
                $data['file_path'] = $path;
                $absolutePath = public_path('storage/' . $path);

                // Cek Halaman
                $pageCount = $this->countPages($absolutePath);
                if ($pageCount > 200) {
                    Storage::disk('public')->delete($path);
                    return back()->with('error', "❌ Buku terlalu tebal ({$pageCount} hal).");
                }
                $data['total_pages'] = $pageCount;

                // Regenerate AI Info (Pake fungsi helper di bawah)
                $aiData = $this->analyzePdfWithAi($absolutePath, $request->title);
                if ($aiData) {
                    $data = array_merge($data, $aiData);
                }
            } elseif ($request->filled('total_pages')) {
                $data['total_pages'] = $request->total_pages;
            }

            $book->update($data);

            return redirect()->route('admin.books.index')->with('success', "📘 Buku berhasil diperbarui.");

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error Update: ' . $e->getMessage());
        }
    }

    public function destroy(Book $book)
    {
        if ($book->file_path) Storage::disk('public')->delete($book->file_path);
        if ($book->cover_image_path) Storage::disk('public')->delete($book->cover_image_path);
        $book->delete();
        return redirect()->route('admin.books.index')->with('success', 'Buku dihapus.');
    }

    // ==========================================
    // 👇 FUNGSI PENDUKUNG (HELPER) BIAR RAPI
    // ==========================================

    /**
     * Hitung jumlah halaman PDF
     */
    private function countPages($path)
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($path);
        return count($pdf->getPages());
    }

    /**
     * Ambil 10 halaman pertama & Kirim ke Gemini
     */
    private function analyzePdfWithAi($path, $title)
    {
        try {
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($path);

            // ✅ LOGIC BARU: Ambil sampai 10 halaman pertama
            // Ini biar AI bisa baca Daftar Isi & Intro, bukan cuma sampul
            $pagesToExtract = min($pageCount, 10);

            // Buat PDF baru di memori
            $pdf->AddPage();

            for ($i = 1; $i <= $pagesToExtract; $i++) {
                $tplId = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($tplId);

                // Tambah halaman jika bukan halaman pertama (karena AddPage sudah dipanggil sekali diatas)
                if ($i > 1) {
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                }
                $pdf->useTemplate($tplId);
            }

            $content = $pdf->Output('S'); // Output string

            // Kirim ke Gemini
            $pdfData = ['mime_type' => 'application/pdf', 'data' => base64_encode($content)];

            // Prompt yang diperbaiki agar baca struktur buku
            $prompt = "Analisis 10 halaman pertama dari buku '{$title}'. "
                . "Cari JUDUL, PENULIS, TAHUN TERBIT, dan DAFTAR ISI (Table of Contents). "
                . "Berdasarkan Daftar Isi dan Intro, buat ringkasan (overview) yang mencakup isi keseluruhan buku, bukan hanya sampul. "
                . "Output JSON: {\"author\": \"...\", \"publication_date\": \"YYYY-MM-DD\", \"overview\": \"...\"}";

            $result = $this->geminiService->generateBookDetailsFromPdf($prompt, $pdfData);

            if ($result && is_array($result)) {
                return [
                    'author' => $result['author'] ?? 'Unknown',
                    'publication_date' => $result['publication_date'] ?? null,
                    'overview' => $result['overview'] ?? null,
                ];
            }
        } catch (\Exception $e) {
            Log::error("AI Analysis Failed: " . $e->getMessage());
        }

        return [];
    }
}
