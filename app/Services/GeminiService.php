<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    // protected $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent';
    protected $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent';




    public function __construct()
    {
        $this->apiKey = config('gemini.api_key');
        if (empty($this->apiKey)) {
            Log::critical('[GeminiService] API Key is not configured.');
        }
    }

    private function executeMultimodalQuery(string $prompt, array $filesData, bool $expectJson = false, int $timeout = 120): ?string
    {
        if (empty($this->apiKey)) return null;

        $parts = [['text' => $prompt]];
        foreach ($filesData as $file) {
            $parts[] = [
                'inline_data' => [
                    'mime_type' => $file['mime_type'],
                    'data' => $file['data']
                ]
            ];
        }

        $payload = ['contents' => [['parts' => $parts]]];

        if ($expectJson) {
            $payload['generationConfig'] = ['responseMimeType' => 'application/json'];
        }

        try {
            $response = Http::timeout($timeout)->post($this->apiUrl . '?key=' . $this->apiKey, $payload);

            if ($response->successful()) {
                return $response->json('candidates.0.content.parts.0.text');
            }
            Log::error('[GeminiService] Multimodal API request failed.', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        } catch (\Exception $e) {
            Log::error('[GeminiService] Exception in executeMultimodalQuery: ' . $e->getMessage());
            return null;
        }
    }

    public function generateBookDetailsFromPdf(string $prompt, array $pdfData): ?array
    {
        $jsonString = $this->executeMultimodalQuery($prompt, [$pdfData], true);
        if ($jsonString) {
            $decodedJson = json_decode($jsonString, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decodedJson;
            }
            Log::error('[GeminiService] Failed to decode JSON from multimodal response.', ['json_string' => $jsonString]);
        }
        return null;
    }

    public function getChatResponseFromPdf(string $prompt, array $pdfData): ?string
    {
        return $this->executeMultimodalQuery($prompt, [$pdfData], false);
    }

    public function getChatResponse(string $prompt): ?string
    {
        if (empty($this->apiKey)) return null;
        $payload = ['contents' => [['parts' => [['text' => $prompt]]]]];
        try {
            $response = Http::timeout(60)->post($this->apiUrl . '?key=' . $this->apiKey, $payload);
            if ($response->successful()) {
                return $response->json('candidates.0.content.parts.0.text');
            }
            Log::error('[GeminiService] Text API request failed.', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        } catch (\Exception $e) {
            Log::error('[GeminiService] Exception in getChatResponse: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generates 5 quiz questions based on the full text of a book.
     *
     * @param string $bookContent
     * @param string $bookTitle
     * @return array|null
     */
    public function generateQuizQuestions(string $bookContent, string $bookTitle): ?array
    {
        // Memotong konten buku jika terlalu panjang untuk menghindari error batas token
        $trimmedContent = substr($bookContent, 0, 800000); // Batas aman sekitar 800rb karakter

        $prompt = "Anda adalah seorang ahli pembuat soal kuis. Berdasarkan seluruh teks dari buku berjudul '{$bookTitle}' berikut ini: '{$trimmedContent}', buatlah 5 soal pilihan ganda untuk menguji pemahaman pembaca. Setiap soal harus memiliki 4 pilihan jawaban (a, b, c, d) dan satu jawaban yang benar. Kembalikan hasilnya dalam format JSON yang ketat. Formatnya harus berupa array dari objek, di mana setiap objek memiliki kunci: 'question_text', 'options' (sebuah objek dengan kunci 'a', 'b', 'c', 'd'), dan 'correct_answer' (kunci dari jawaban yang benar, misalnya 'a'). Contoh: [{\"question_text\": \"Siapa penulis buku ini?\", \"options\": {\"a\": \"Penulis A\", \"b\": \"Penulis B\", \"c\": \"Penulis C\", \"d\": \"Penulis D\"}, \"correct_answer\": \"a\"}]";

        // Menggunakan executeMultimodalQuery karena prompt bisa sangat panjang, dan kita butuh timeout lebih lama
        // Meskipun tidak ada file, kita bisa gunakan ini untuk timeout yang lebih panjang.
        $jsonString = $this->executeMultimodalQuery($prompt, [], true, 180); // Timeout 3 menit

        if ($jsonString) {
            $decodedJson = json_decode($jsonString, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedJson)) {
                return $decodedJson;
            }
            Log::error('[GeminiService] Failed to decode JSON response for quiz questions.', ['json_string' => $jsonString]);
        }
        return null;
    }
}
