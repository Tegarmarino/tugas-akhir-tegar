<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use setasign\Fpdi\Fpdi; // PERBAIKAN: Menggunakan library FPDI yang benar
use App\Models\ReadingProgress; // Pastikan model ini di-import

class ReadingController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function show(Book $book)
    {
        $progress = ReadingProgress::firstOrCreate(
                    ['user_id' => Auth::id(), 'book_id' => $book->id],
                    ['last_page_number' => 1] // Jika belum ada, buat baru mulai dari halaman 1
                );

        Log::info("[ReadingView] Loading page for Book ID: {$book->id}. Progress found: " . ($progress ? 'Yes, is_finished: ' . $progress->is_finished : 'No'));

        // PERBAIKAN UTAMA: Pastikan variabel $progress dikirim ke view
        return view('reading.show', [
            'book' => $book,
            'bookmarks' => [], // Ganti dengan logika bookmark Anda jika ada
            'progress' => $progress, // Kirim data progres ke view
        ]);
    }

    public function updateProgress(Request $request, Book $book)
        {
            $request->validate([
                'last_page_number' => 'required|integer|min:1',
            ]);

            if (Auth::check()) {
                ReadingProgress::updateOrCreate(
                    ['user_id' => Auth::id(), 'book_id' => $book->id],
                    ['last_page_number' => $request->last_page_number]
                );

                return response()->json(['status' => 'success']);
            }

            return response()->json(['status' => 'error', 'message' => 'User not authenticated'], 401);
        }

    public function chatWithBookAI(Request $request, Book $book)
    {
        $request->validate(['question' => 'required|string|max:1000']);
        $userQuestion = $request->input('question');
        $pageNumber = (int) $request->input('page_number', 1);

        Log::info("--------------------------------------------------");
        Log::info("[ChatAI-PDF] Request - Book ID: {$book->id}, Page: {$pageNumber}, Question: '{$userQuestion}'");

        $absolutePdfPath = storage_path('app/public/' . $book->file_path);
        if (!Storage::disk('public')->exists($book->file_path)) {
            Log::error("[ChatAI-PDF] CRITICAL: PDF file does not exist at path: {$absolutePdfPath}");
            return response()->json(['reply' => 'Maaf, file buku inti tidak dapat ditemukan.'], 500);
        }

        $pdfData = null;
        try {
            Log::info("[ChatAI-PDF] Extracting page {$pageNumber} from PDF using FPDI.");

            // PERBAIKAN: Menggunakan FPDI untuk mengekstrak halaman tunggal
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($absolutePdfPath);

            if ($pageNumber > $pageCount || $pageNumber < 1) {
                throw new \Exception("Nomor halaman {$pageNumber} tidak valid. Buku ini hanya memiliki {$pageCount} halaman.");
            }

            $templateId = $pdf->importPage($pageNumber);
            $size = $pdf->getTemplateSize($templateId);
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);
            $singlePagePdfContent = $pdf->Output('S');

            if (empty($singlePagePdfContent)) {
                throw new \Exception("Gagal mengekstrak konten halaman {$pageNumber} dari PDF.");
            }

            $pdfData = [
                'mime_type' => 'application/pdf',
                'data' => base64_encode($singlePagePdfContent)
            ];

            Log::info("[ChatAI-PDF] Successfully extracted and encoded page {$pageNumber}.");

        } catch (\Exception $e) {
            Log::error("[ChatAI-PDF] Failed to extract single page from PDF: " . $e->getMessage());
            return response()->json(['reply' => 'Maaf, terjadi kesalahan saat memproses halaman buku. Halaman ini mungkin tidak dapat dibaca.'], 500);
        }

        // Membangun prompt untuk Gemini (logika prompt tetap sama)
        $promptParts = [];
        $promptParts[] = "Anda adalah asisten AI yang cerdas. Anda akan diberi sebuah file PDF yang berisi satu halaman dari buku \"{$book->title}\".";
        if (!empty(trim($book->overview))) {
            $promptParts[] = "Sebagai konteks umum, ini adalah ringkasan buku tersebut: \"{$book->overview}\".";
        }
        $promptParts[] = "Analisis semua konten di halaman PDF ini—termasuk teks, gambar, grafik, atau tabel.";
        $promptParts[] = "Pertanyaan pengguna adalah: \"{$userQuestion}\".";
        $promptParts[] = "Jawablah pertanyaan pengguna secara akurat berdasarkan konten visual dari halaman PDF yang diberikan.";

        $fullPrompt = implode(" ", $promptParts);
        Log::info("[ChatAI-PDF] Sending multimodal prompt to Gemini.");

        try {
            $aiResponse = $this->geminiService->getChatResponseFromPdf($fullPrompt, $pdfData);
            if ($aiResponse === null) {
                $aiResponse = "Maaf, terjadi masalah saat berkomunikasi dengan layanan AI. Silakan coba lagi nanti.";
            }
            Log::info("[ChatAI-PDF] AI Response received.");
            return response()->json(['reply' => $aiResponse]);
        } catch (\Exception $e) {
            Log::error("[ChatAI-PDF] Exception when calling GeminiService: " . $e->getMessage());
            return response()->json(['reply' => 'Maaf, terjadi kesalahan internal saat memproses permintaan Anda.'], 500);
        }
    }

    public function defineHighlightedText(Request $request, Book $book)
    {
        $request->validate(['text' => 'required|string|max:255']);
        $selectedText = $request->input('text');
        $prompt = "Berikan definisi atau penjelasan singkat dan jelas untuk kata atau frasa berikut: \"{$selectedText}\".";
        $aiResponse = $this->geminiService->getChatResponse($prompt);
        return response()->json(['definition' => $aiResponse]);
    }
}
