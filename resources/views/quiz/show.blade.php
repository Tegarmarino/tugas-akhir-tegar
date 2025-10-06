<x-app-layout>
    <x-slot name="title">
        {{ ucfirst($type) }} - {{ $quiz->book->title }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">

                    @if(session('info'))
                        <div class="mb-6 p-4 bg-blue-100 dark:bg-blue-900 border-l-4 border-blue-500 text-blue-700 dark:text-blue-200">
                            <p>{{ session('info') }}</p>
                        </div>
                    @endif

                    <h1 class="text-2xl md:text-3xl font-bold mb-2">
                        {{ ucfirst($type) }}: {{ $quiz->book->title }}
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mb-8">
                        Selesaikan kuis ini untuk melanjutkan.
                    </p>

                    @if($quiz->questions->isEmpty())
                        <p class="text-center text-red-500">Maaf, kuis ini belum memiliki soal.</p>
                    @else
                        <form id="quiz-form" action="{{ route('quiz.store', $quiz) }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" value="{{ $type }}">

                            <div class="space-y-8">
                                @foreach($quiz->questions as $index => $question)
                                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                                        <p class="font-semibold mb-4">
                                            {{ $index + 1 }}. {{ $question->question_text }}
                                        </p>
                                        <div class="space-y-3">
                                            @foreach($question->options as $key => $option)
                                                <label for="question_{{ $question->id }}_{{ $key }}" class="flex items-center p-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                                                    <input type="radio"
                                                           name="answers[{{ $question->id }}]"
                                                           id="question_{{ $question->id }}_{{ $key }}"
                                                           value="{{ $key }}"
                                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-500 dark:bg-gray-900 dark:focus:ring-blue-600 dark:ring-offset-gray-800"
                                                           required>
                                                    <span class="ml-3 text-gray-700 dark:text-gray-300">{{ $option }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-8">
                                <button type="submit" id="submit-quiz-btn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition-transform transform hover:scale-105">
                                    Selesaikan Kuis
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Hasil Kuis (Tersembunyi secara default) -->
    <div id="result-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
        <div class="relative p-6 md:p-10 border w-full max-w-2xl shadow-lg rounded-md bg-white dark:bg-gray-800 text-center">
            <h1 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100" id="modal-title"></h1>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Untuk buku: <span class="font-medium" id="modal-book-title"></span>
            </p>

            <div class="my-8">
                <p class="text-lg text-gray-500 dark:text-gray-400" id="modal-score-label"></p>
                <p class="text-6xl font-bold text-blue-600 dark:text-blue-400 my-2"><span id="modal-score">0</span><span class="text-3xl">%</span></p>
            </div>

            <!-- Bagian Perbandingan Skor (Tersembunyi secara default) -->
            <div id="modal-comparison" class="hidden my-8 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
                <p class="text-md text-gray-600 dark:text-gray-300">Perbandingan Skor:</p>
                <div class="mt-2 flex justify-around items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pre-test</p>
                        <p class="text-2xl font-bold" id="modal-pre-test-score">0%</p>
                    </div>
                    <div>
                        <span class="font-bold text-xl" id="modal-score-difference"></span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Post-test</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400" id="modal-post-test-score">0%</p>
                    </div>
                </div>
            </div>

            <p id="modal-conditional-message" class="text-sm text-yellow-600 dark:text-yellow-400 mb-6 hidden"></p>

            {{-- Kontainer untuk Tombol Aksi --}}
            <div id="modal-action-buttons" class="flex flex-col sm:flex-row gap-4">
                {{-- Tombol akan dibuat oleh JavaScript --}}
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const quizForm = document.getElementById('quiz-form');
        const submitButton = document.getElementById('submit-quiz-btn');
        const resultModal = document.getElementById('result-modal');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        if(quizForm) { // Pastikan form ada sebelum menambahkan event listener
            quizForm.addEventListener('submit', async function(e) {
                e.preventDefault(); // Mencegah form submit biasa

                const originalButtonText = submitButton.textContent;
                submitButton.textContent = 'Memproses...';
                submitButton.disabled = true;

                const formData = new FormData(quizForm);

                try {
                    const response = await fetch(quizForm.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Terjadi kesalahan.');
                    }

                    // Jika sukses, tampilkan modal
                    showResultModal(data);

                } catch (error) {
                    console.error('Error submitting quiz:', error);
                    alert('Gagal mengirim jawaban: ' + error.message);
                    submitButton.textContent = originalButtonText;
                    submitButton.disabled = false;
                }
            });
        }

        function showResultModal(data) {
            const attempt = data.attempt;
            const typeText = attempt.type === 'pre-test' ? 'Pre-test' : 'Post-test';

            // Isi konten modal
            document.getElementById('modal-title').textContent = `Hasil ${typeText} Selesai!`;
            document.getElementById('modal-book-title').textContent = data.bookTitle;
            document.getElementById('modal-score-label').textContent = `Skor ${typeText} Anda:`;
            document.getElementById('modal-score').textContent = attempt.score;

            const comparisonDiv = document.getElementById('modal-comparison');
            const actionButtonsDiv = document.getElementById('modal-action-buttons');
            const conditionalMessage = document.getElementById('modal-conditional-message');

            // Kosongkan kontainer tombol
            actionButtonsDiv.innerHTML = '';
            comparisonDiv.classList.add('hidden');
            conditionalMessage.classList.add('hidden');

            if (attempt.type === 'pre-test') {
                // --- LOGIKA UNTUK PRE-TEST ---
                const continueButton = document.createElement('a');
                continueButton.href = data.nextRoute;
                continueButton.textContent = 'Mulai Membaca Buku';
                continueButton.className = 'inline-block w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition-transform transform hover:scale-105';
                actionButtonsDiv.appendChild(continueButton);

            } else {
                // --- LOGIKA UNTUK POST-TEST ---
                // Tampilkan perbandingan skor jika ada
                if (data.preTestScore !== null) {
                    document.getElementById('modal-pre-test-score').textContent = `${data.preTestScore}%`;
                    document.getElementById('modal-post-test-score').textContent = `${attempt.score}%`;
                    const difference = attempt.score - data.preTestScore;
                    const diffElement = document.getElementById('modal-score-difference');

                    diffElement.textContent = `${difference >= 0 ? '+' : ''}${difference}%`;
                    if (difference > 0) {
                        diffElement.className = 'font-bold text-xl text-green-500';
                    } else if (difference < 0) {
                        diffElement.className = 'font-bold text-xl text-red-500';
                    } else {
                        diffElement.className = 'font-bold text-xl text-gray-500';
                    }

                    comparisonDiv.classList.remove('hidden');
                }

                // Buat tombol "Ulangi Baca Buku"
                const repeatButton = document.createElement('a');
                repeatButton.href = data.repeatReadingRoute;
                repeatButton.textContent = 'Ulangi Baca Buku';
                repeatButton.className = 'inline-block w-full bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-4 rounded-lg transition-transform transform hover:scale-105';
                actionButtonsDiv.appendChild(repeatButton);

                // Buat tombol "Selesai & Kembali ke Katalog"
                const finishButton = document.createElement('a');
                finishButton.href = data.nextRoute;
                finishButton.textContent = 'Selesai & Kembali ke Katalog';
                finishButton.className = 'inline-block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition-transform transform hover:scale-105';

                // PERBAIKAN: Logika kondisional diubah menjadi '<' (lebih kecil dari)
                if (data.preTestScore !== null && attempt.score < data.preTestScore) {
                    finishButton.classList.add('opacity-50', 'cursor-not-allowed');
                    finishButton.setAttribute('title', 'Anda harus mendapatkan skor minimal sama dengan pre-test untuk menyelesaikan buku ini.');
                    finishButton.onclick = (e) => e.preventDefault(); // Mencegah klik
                    conditionalMessage.textContent = 'Nilai Anda harus minimal sama dengan pre-test untuk dapat menyelesaikan buku ini. Silakan ulangi baca buku untuk meningkatkan pemahaman.';
                    conditionalMessage.classList.remove('hidden');
                }
                actionButtonsDiv.appendChild(finishButton);
            }

            // Tampilkan modal
            resultModal.classList.remove('hidden');
        }
    </script>
    @endpush
</x-app-layout>
