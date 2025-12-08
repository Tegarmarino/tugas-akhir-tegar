<x-app-layout>
    <x-slot name="title">
        Membaca: {{ $book->title }}
    </x-slot>

    {{-- =========================
         PDF VIEWER + CHAT PANEL
    ============================ --}}
    <div class="h-screen flex flex-col overflow-hidden">
        <div class="flex-grow max-w-full mx-auto sm:px-6 lg:px-8 py-6 overflow-hidden">
            <div class="flex flex-col lg:flex-row gap-4 h-full">

                {{-- ===================
                     PDF VIEWER AREA (FIXED SIZING)
                ==================== --}}
                <div class="w-full lg:w-[60%] flex flex-col bg-gray-200 dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden lg:h-[90vh]">
                    {{-- Toolbar --}}
                    <div class="p-2 bg-gray-100 dark:bg-gray-700 flex items-center justify-between border-b dark:border-gray-600">
                        {{-- Navigasi Halaman --}}
                        <div class="flex items-center space-x-2">
                            <button id="prev-page" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm">Prev</button>
                            <span class="text-gray-700 dark:text-gray-300 text-sm">
                                Halaman: <span id="page-num">1</span> / <span id="page-count">?</span>
                            </span>
                            <button id="next-page" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm">Next</button>
                        </div>

                        {{-- Dropdown Aksi (Zoom, Jump, Test) --}}
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open"
                                class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700 text-sm flex items-center">
                                ⚙️ Aksi
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-4 h-4 ml-1">
                                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 011.06 0L10 11.94l3.72-3.72a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.22 9.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            {{-- Isi Dropdown --}}
                            <div x-show="open" @click.outside="open = false" x-transition
                                class="absolute right-0 mt-2 w-72 bg-white dark:bg-gray-800 rounded-md shadow-lg z-20 p-4 border dark:border-gray-600 space-y-3">

                                {{-- Zoom Controls --}}
                                <div class="border-b pb-3 dark:border-gray-600">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">🔍 Zoom</label>
                                    <div class="flex justify-center items-center space-x-3">
                                        <button id="zoom-out-btn" class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">-</button>
                                        <span id="zoom-level" class="text-sm text-gray-700 dark:text-gray-300 w-12 text-center">150%</span>
                                        <button id="zoom-in-btn" class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">+</button>
                                    </div>
                                </div>

                                {{-- Lompat Bab (Chapter Jump) --}}
                                <div class="border-b pb-3 dark:border-gray-600">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">📖 Lompat ke Bab</label>
                                    <select id="chapter-jump"
                                        class="w-full text-sm border-gray-300 dark:border-gray-600 rounded-md p-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                        <option value="">-- Pilih Bab --</option>
                                        @foreach ($book->chapters->sortBy('start_page') as $chapter)
                                            <option value="{{ $chapter->start_page }}">
                                                {{ $chapter->title }} (hal {{ $chapter->start_page }}–{{ $chapter->end_page }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- ===========================
                                     POST-TEST STATUS & AKSI (RESTORED)
                                 =========================== --}}
                                <div class="border-b pb-3 dark:border-gray-600">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        🧪 Post-Test Bab
                                    </label>

                                    @foreach ($book->chapters->sortBy('start_page') as $chapter)
                                        @php
                                            $chapterTest = $book->tests()
                                                ->where('type', 'post')
                                                ->where('chapter_id', $chapter->id)
                                                ->first();

                                            $result = $chapterTest
                                                ? \App\Models\UserQuizAttempt::where('user_id', auth()->id())
                                                    ->where('test_id', $chapterTest->id)
                                                    ->latest()
                                                    ->first()
                                                : null;
                                        @endphp

                                        <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md p-2 mb-2">
                                            <div class="flex items-center gap-2">
                                                @if (!$chapterTest)
                                                    <span class="text-gray-400 font-bold text-lg leading-none">–</span>
                                                    <span class="text-gray-500 text-xs italic">Belum ada test</span>
                                                @elseif ($result && $result->score >= 80)
                                                    <span class="text-green-600 font-bold text-lg leading-none">✅</span>
                                                    <span class="text-green-700 text-xs">Lulus ({{ $result->score }}%)</span>
                                                @elseif ($result)
                                                    <span class="text-yellow-500 font-bold text-lg leading-none">⚠️</span>
                                                    <span class="text-red-600 text-xs">Belum Lulus ({{ $result->score }}%)</span>
                                                @else
                                                    <span class="text-red-500 font-bold text-lg leading-none">❌</span>
                                                    <span class="text-gray-500 text-xs">Belum Dikerjakan</span>
                                                @endif
                                            </div>

                                            {{-- Tombol --}}
                                            @if ($chapterTest)
                                                @if (!$result || ($result && $result->score < 80))
                                                    <a href="{{ route('quiz.show', $chapterTest->id) }}"
                                                    class="px-2 py-1 {{ $result ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-indigo-600 hover:bg-indigo-700' }} text-white text-xs rounded-md transition">
                                                        {{ $result ? 'Ulangi' : 'Kerjakan' }}
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    @endforeach
                                </div>


                                {{-- Tombol Kembali ke Detail --}}
                                <div>
                                    <a href="{{ route('books.show', $book->id) }}"
                                        class="block text-center bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 py-2 rounded-md text-sm font-medium transition">
                                        🔙 Kembali ke Detail Buku
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- PDF Render Container --}}
                    <div id="pdf-render-container"
                        class="overflow-auto relative flex-grow bg-gray-300 dark:bg-gray-700 rounded-b-lg">
                        {{-- PDF Content Wrapper (size ditentukan oleh JS/Zoom) --}}
                        <div class="relative mx-auto" id="pdf-content-wrapper">
                            <canvas id="pdf-canvas" class="shadow-lg mx-auto"></canvas>
                            <div id="text-layer" class="textLayer absolute top-0 left-0"></div>
                        </div>
                    </div>
                </div>

                {{-- ===================
                     CHAT AI AREA (40%)
                ==================== --}}
                <div class="w-full lg:w-[40%] bg-white dark:bg-gray-900 p-4 shadow-xl flex flex-col rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 lg:h-[90vh]">
                    <div class="flex items-center mb-3">
                        <div class="h-2 w-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Chat Asisten AI 📘</h2>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Buku: <span class="font-medium">{{ $book->title }}</span></p>

                    {{-- CHAT OUTPUT AREA --}}
                    <div id="chat-output" class="flex-grow overflow-y-auto p-3 mb-3 rounded-lg bg-gray-50 dark:bg-gray-800 space-y-3">
                        <div class="text-center text-gray-400 text-xs italic mt-10">✨ Tanyakan sesuatu tentang halaman atau bab ini...</div>
                    </div>

                    {{-- Quick Prompt Buttons (RESTORED) --}}
                    <div x-data="{ open: false }" class="mb-3 relative">
                        <button @click="open = !open"
                            class="w-full text-left text-sm text-gray-600 dark:text-gray-400 mb-1 p-2 border border-gray-300 dark:border-gray-700 rounded-lg flex justify-between items-center transition">
                            <span>Contoh Pertanyaan Cepat... (Quick Prompt)</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-4 h-4 ml-1">
                                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 011.06 0L10 11.94l3.72-3.72a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.22 9.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition
                            class="absolute bottom-full mb-1 w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg z-20 p-2 border dark:border-gray-700 space-y-1 max-h-48 overflow-y-auto">
                            <button class="template-question w-full text-left text-xs bg-gray-100 dark:bg-gray-700 dark:text-gray-200 text-gray-800 px-2 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600" data-question="Simpulkan bab ini secara detail.">Simpulkan bab ini secara detail.</button>
                            <button class="template-question w-full text-left text-xs bg-gray-100 dark:bg-gray-700 dark:text-gray-200 text-gray-800 px-2 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600" data-question="Jelaskan konsep-konsep utama yang dibahas di bab ini.">Jelaskan konsep utama bab ini.</button>
                            <button class="template-question w-full text-left text-xs bg-gray-100 dark:bg-gray-700 dark:text-gray-200 text-gray-800 px-2 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600" data-question="Apa argumen sentral yang disampaikan penulis di bab ini?">Apa argumen sentral bab ini?</button>
                        </div>
                    </div>

                    {{-- Form Chat --}}
                    <form id="chat-form" class="mt-auto">
                        <textarea id="chat-input" rows="2"
                            class="w-full p-2 border border-gray-300 dark:border-gray-700 rounded-lg mb-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 text-sm focus:ring-2 focus:ring-indigo-500 outline-none resize-none"
                            placeholder="Tanyakan sesuatu tentang halaman atau bab ini..."></textarea>
                        <div class="flex gap-2">
                            <button type="button" id="send-page-btn"
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg text-sm transition-all duration-150 ease-in-out">
                                💬 Tanya Halaman Ini
                            </button>
                            <button type="button" id="send-chapter-btn"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg text-sm transition-all duration-150 ease-in-out">
                                🔍 Tanya Bab Ini
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf_viewer.css">
        <style>
            /* Fix Canvas to allow natural scrolling */
            #pdf-canvas {
                display: block;
                margin: 0 auto;
                box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            }
            /* Styling for chat bubbles */
            .chat-bubble {
                padding: 0.9rem 1.1rem;
                border-radius: 1rem;
                max-width: 90%;
                animation: fadeIn 0.25s ease-in-out;
                transition: all 0.3s ease;
                align-self: flex-start;
            }
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .user-message {
                align-self: flex-end;
                background: linear-gradient(135deg, #4f46e5, #3b82f6);
                color: #fff;
                border-bottom-right-radius: 0.3rem;
                text-align: right;
                box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            }
            /* AI MESSAGE */
            .ai-message {
                align-self: flex-start;
                background: linear-gradient(to bottom right, #f9fafb, #f3f4f6);
                border: 1px solid #e5e7eb;
                color: #111827;
                border-bottom-left-radius: 0.3rem;
                box-shadow: 0 2px 4px rgba(0,0,0,0.08);
                font-size: 0.95rem;
            }
            .dark .ai-message {
                background: linear-gradient(to bottom right, #1f2937, #111827);
                border-color: #374151;
                color: #e5e7eb;
            }
        </style>
    @endpush

    @push('scripts')
        {{-- MathJax Config --}}
        <script>
            window.MathJax = {
                tex: { inlineMath: [['$', '$'], ['\\(', '\\)']], displayMath: [['$$', '$$'], ['\\[', '\\]']], processEscapes: true },
                svg: { fontCache: 'global', scale: 0.9 },
                options: { renderActions: { addMenu: [0, '', ''] } }
            };
        </script>
        <script async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

        <script>
            /* ============================
             KONFIGURASI PDF.JS & VARIABEL
            ============================ */
            pdfjsLib.GlobalWorkerOptions.workerSrc =
                'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

            const pdfPath = "{{ Storage::url($book->file_path) }}";
            const bookId = {{ $book->id }};
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const chapterList = @json($book->chapters);

            const canvas = document.getElementById('pdf-canvas');
            const ctx = canvas.getContext('2d');
            const pdfContentWrapper = document.getElementById('pdf-content-wrapper');

            const pageNumDisplay = document.getElementById('page-num');
            const pageCountDisplay = document.getElementById('page-count');
            const prevPageBtn = document.getElementById('prev-page');
            const nextPageBtn = document.getElementById('next-page');
            const zoomInBtn = document.getElementById('zoom-in-btn');
            const zoomOutBtn = document.getElementById('zoom-out-btn');
            const zoomLevel = document.getElementById('zoom-level');
            const chapterJumpSelect = document.getElementById('chapter-jump');

            let pdfDoc = null;
            let currentPageNum = {{ $progress->last_page_number ?? 1 }};
            let scale = 1.5;
            const ZOOM_STEP = 0.25;

            /* ============================
             FUNGSI RENDER PDF (FIXED SIZING LOGIC)
            ============================ */
            async function renderPage(num) {
                const page = await pdfDoc.getPage(num);
                const viewport = page.getViewport({ scale: scale });

                // Set wrapper size based on viewport/zoom (important for scrolling)
                pdfContentWrapper.style.width = viewport.width + 'px';
                pdfContentWrapper.style.height = viewport.height + 'px';

                canvas.height = viewport.height;
                canvas.width = viewport.width;

                await page.render({ canvasContext: ctx, viewport: viewport }).promise;

                // Update UI elements
                pageNumDisplay.textContent = num;
                zoomLevel.textContent = Math.round(scale * 100) + '%';

                // Save progress asynchronously
                debouncedSaveProgress(num);
            }

            /* ============================
             LOAD PDF
            ============================ */
            pdfjsLib.getDocument(pdfPath).promise.then(doc => {
                pdfDoc = doc;
                pageCountDisplay.textContent = pdfDoc.numPages;
                renderPage(currentPageNum);
            });

            /* ============================
             AUTO-SAVE PROGRESS LOGIC (DEBOUNCE)
            ============================ */
            function debounce(func, delay) {
                let timeout;
                return function (...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), delay);
                };
            }
            async function saveProgress(page) {
                try {
                    await fetch("{{ route('books.progress.update', $book) }}", {
                        method: 'PATCH',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ last_page_number: page })
                    });
                } catch (error) {
                    console.error('[Progress] Gagal menyimpan progres:', error);
                }
            }
            const debouncedSaveProgress = debounce(saveProgress, 2000);

            /* ============================
             NAVIGASI PDF
            ============================ */
            prevPageBtn.addEventListener('click', () => {
                if (currentPageNum > 1) {
                    currentPageNum--;
                    renderPage(currentPageNum);
                }
            });
            nextPageBtn.addEventListener('click', () => {
                if (currentPageNum < pdfDoc.numPages) {
                    currentPageNum++;
                    renderPage(currentPageNum);
                }
            });
            zoomInBtn.addEventListener('click', () => {
                scale += ZOOM_STEP;
                renderPage(currentPageNum);
            });
            zoomOutBtn.addEventListener('click', () => {
                if (scale > 0.5) {
                    scale -= ZOOM_STEP;
                    renderPage(currentPageNum);
                }
            });

            /* ============================
             LOMPAT KE BAB (CHAPTER JUMP)
            ============================ */
            chapterJumpSelect?.addEventListener('change', async function () {
                const targetPage = parseInt(this.value);
                if (!isNaN(targetPage) && pdfDoc) {
                    currentPageNum = targetPage;
                    // Reset scroll position and render new page
                    document.getElementById('pdf-render-container').scrollTo({ top: 0, behavior: 'smooth' });
                    renderPage(currentPageNum);
                }
            });

            /* ============================
             CHAT AI LOGIC (WITH QUICK PROMPT)
            ============================ */
            const chatOutput = document.getElementById('chat-output');
            const chatInput = document.getElementById('chat-input');
            const chatForm = document.getElementById('chat-form');
            const sendPageBtn = document.getElementById('send-page-btn'); // Renamed to send-page-btn
            const sendChapterBtn = document.getElementById('send-chapter-btn');
            const templateQuestionButtons = document.querySelectorAll('.template-question');

            /* Fungsi nambah bubble chat */
            function addChatMessage(message, sender, isTemplate = false) {
                const div = document.createElement('div');
                div.classList.add('chat-bubble', sender === 'user' ? 'user-message' : 'ai-message');

                if (sender === 'ai') {
                    const content = document.createElement('div');
                    content.classList.add('prose', 'dark:prose-invert', 'leading-relaxed');
                    content.innerHTML = marked.parse(message);
                    div.appendChild(content);
                    chatOutput.appendChild(div);
                    // Render LaTeX setelah DOM update
                    if (window.MathJax) {
                        setTimeout(() => {
                            MathJax.typesetPromise([content]);
                        }, 150);
                    }
                } else {
                    // User message, potentially from template
                    const prefix = isTemplate ? '⚡ ' : ''; // Ubah penanda Quick Prompt
                    div.innerHTML = `<span class="font-medium">${prefix}${message}</span>`;
                    chatOutput.appendChild(div);
                }
                chatOutput.scrollTop = chatOutput.scrollHeight;
            }

            /* Fungsi cari bab aktif */
            function getCurrentChapterId(pageNum) {
                if (!chapterList || chapterList.length === 0) return null;
                for (const c of chapterList) {
                    if (pageNum >= c.start_page && pageNum <= c.end_page) {
                        return c.id;
                    }
                }
                return null;
            }

            /* ===== ASYNC FUNCTION: HANDLE CHAT PER BAB/CHAPTER (Shared Handler) ===== */
            async function handleChapterChat(question, isQuickPrompt = false) {
                const chapterId = getCurrentChapterId(currentPageNum);
                if (!chapterId) return addChatMessage("⚠️ Tidak ada bab yang cocok dengan halaman ini.", 'ai');

                addChatMessage(`(Bab) ${question}`, 'user', isQuickPrompt); // Use isQuickPrompt flag for user bubble
                chatInput.value = '';
                sendPageBtn.disabled = true;
                sendChapterBtn.disabled = true;

                try {
                    const res = await fetch(`/books/${bookId}/chapters/${chapterId}/chat`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ question })
                    });
                    const data = await res.json();
                    addChatMessage(data.reply || 'Tidak ada jawaban AI.', 'ai');
                } catch (e) {
                    console.error(e);
                    addChatMessage('❌ Gagal memuat jawaban bab.', 'ai');
                } finally {
                    sendPageBtn.disabled = false;
                    sendChapterBtn.disabled = false;
                }
            }

            /* ===== ASYNC FUNCTION: HANDLE CHAT PER HALAMAN (Shared Handler) ===== */
            async function handlePageChat(question) {
                addChatMessage(`(Hal ${currentPageNum}) ${question}`, 'user');
                chatInput.value = '';
                sendPageBtn.disabled = true;
                sendChapterBtn.disabled = true;

                try {
                    const response = await fetch("{{ route('books.chat', $book) }}", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ question, page_number: currentPageNum })
                    });
                    const data = await response.json();
                    addChatMessage(data.reply || 'Tidak ada jawaban AI.', 'ai');
                } catch (e) {
                    console.error(e);
                    addChatMessage('❌ Gagal terhubung ke AI.', 'ai');
                } finally {
                    sendPageBtn.disabled = false;
                    sendChapterBtn.disabled = false;
                }
            }


            /* ===== QUICK PROMPT LISTENER (FIXED: Triggers Chapter Mode) ===== */
            templateQuestionButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const question = this.dataset.question;
                    chatInput.value = question;

                    // Trigger the specific asynchronous function directly
                    handleChapterChat(question, true);
                });
            });


            /* ===== BUTTON LISTENERS ===== */
            // 1. Tanya Halaman Ini (Green Button)
            sendPageBtn.addEventListener('click', (e) => {
                const question = chatInput.value.trim();
                if (!question) return;
                handlePageChat(question);
            });

            // 2. Tanya Bab Ini (Blue Button)
            sendChapterBtn.addEventListener('click', (e) => {
                const question = chatInput.value.trim();
                if (!question) return addChatMessage("⚠️ Tulis pertanyaan di kolom chat dulu sebelum kirim.", 'ai');
                handleChapterChat(question, false);
            });

            /* ===== FORM ENTER LISTENER (Handles Enter Key) ===== */
            chatForm.addEventListener('submit', (e) => {
                e.preventDefault();
                // Default submit action: Tanya Halaman Ini
                sendPageBtn.click();
            });

        </script>
    @endpush
</x-app-layout>
