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
                     PDF VIEWER AREA
                ==================== --}}
                <div class="w-full h-[60%] lg:h-auto lg:w-[60%] flex flex-col bg-gray-200 dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                    {{-- Toolbar --}}
                    <div class="p-2 bg-gray-100 dark:bg-gray-700 flex items-center justify-between border-b dark:border-gray-600">
                        {{-- Navigasi Halaman --}}
                        <div class="flex items-center space-x-2">
                            <button id="prev-page"
                                class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm">Prev</button>
                            <span class="text-gray-700 dark:text-gray-300 text-sm">
                                Halaman: <span id="page-num">1</span> / <span id="page-count">?</span>
                            </span>
                            <button id="next-page"
                                class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm">Next</button>
                        </div>

                        {{-- Dropdown Aksi --}}
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open"
                                class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700 text-sm flex items-center">
                                ⚙️ Aksi
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-4 h-4 ml-1">
                                    <path fill-rule="evenodd"
                                        d="M5.22 8.22a.75.75 0 011.06 0L10 11.94l3.72-3.72a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.22 9.28a.75.75 0 010-1.06z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>

                            {{-- Isi Dropdown --}}
                            <div x-show="open" @click.outside="open = false" x-transition
                                class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-md shadow-lg z-20 p-4 border dark:border-gray-600 space-y-3">

                                {{-- Zoom Controls --}}
                                <div class="border-b pb-3 dark:border-gray-600">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">🔍 Zoom</label>
                                    <div class="flex justify-center items-center space-x-3">
                                        <button id="zoom-out-btn" class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">-</button>
                                        <span id="zoom-level" class="text-sm text-gray-700 dark:text-gray-300 w-12 text-center">150%</span>
                                        <button id="zoom-in-btn" class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">+</button>
                                    </div>
                                </div>

                                {{-- Lompat Bab --}}
                                <div>
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
                                    PROGRESS & POST-TEST STATUS
                                =========================== --}}
                                <div class="border-t pt-3 dark:border-gray-600 mt-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        📊 Progres Belajar
                                    </label>

                                    @php
                                        $progressPercent = 0;
                                        if ($book->total_pages && $progress->last_page_number) {
                                            $progressPercent = min(100, round(($progress->last_page_number / $book->total_pages) * 100));
                                        }

                                        $currentChapter = $book->chapters
                                            ->first(fn($c) => $progress->last_page_number >= $c->start_page && $progress->last_page_number <= $c->end_page);
                                    @endphp

                                    {{-- Bar Progres --}}
                                    <div class="mb-3">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            📖 Halaman: <span id="progress-page">{{ $progress->last_page_number ?? 1 }}</span> / {{ $book->total_pages ?? '?' }}
                                        </p>
                                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700 mt-1">
                                            <div id="progress-bar" class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $progressPercent }}%"></div>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1" id="progress-text">Progres Baca: {{ $progressPercent }}%</p>
                                    </div>

                                    {{-- Bab Aktif --}}
                                    <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md p-2 mb-3">
                                        <p class="text-xs text-gray-700 dark:text-gray-300">
                                            📍 Sedang membaca bab
                                            <strong>{{ $currentChapter->title ?? 'Belum ada bab aktif' }}</strong>
                                        </p>
                                    </div>

                                    {{-- Daftar Bab --}}
                                    <div class="text-xs text-gray-700 dark:text-gray-300 mt-3 border-t border-gray-200 dark:border-gray-700 pt-2 space-y-2">
                                        <p class="text-sm font-semibold mb-1">📖 Post-Test Bab:</p>

                                        @foreach ($book->chapters->sortBy('start_page') as $chapter)
                                            @php
                                                $chapterTest = $book->tests()
                                                    ->where('type', 'post')
                                                    ->where('chapter_id', $chapter->id)
                                                    ->first();

                                                $isRead = $progress->last_page_number >= $chapter->start_page;
                                                $testDone = $chapterTest
                                                    ? \App\Models\Result::where('user_id', auth()->id())
                                                        ->where('test_id', $chapterTest->id)
                                                        ->exists()
                                                    : false;
                                            @endphp

                                            <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md p-2">
                                                <div class="flex items-center gap-2">
                                                    @if ($testDone)
                                                        <span class="text-green-600 font-bold text-lg leading-none">✅</span>
                                                    @elseif($chapterTest)
                                                        <span class="text-red-500 font-bold text-lg leading-none">❌</span>
                                                    @else
                                                        <span class="text-gray-400 font-bold text-lg leading-none">–</span>
                                                    @endif

                                                    <span>{{ $chapter->title }}</span>
                                                </div>

                                                @if(!$testDone && $chapterTest)
                                                    <a href="{{ route('quiz.show', $chapterTest->id) }}"
                                                        class="px-2 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-xs rounded-md transition">
                                                        Kerjakan
                                                    </a>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>


                    {{-- PDF Render --}}
                    <div id="pdf-render-container" class="overflow-auto relative flex-grow bg-gray-300 dark:bg-gray-700 rounded-b-lg">
                        <div class="relative mx-auto" id="pdf-content-wrapper">
                            <canvas id="pdf-canvas" class="shadow-lg"></canvas>
                            <div id="text-layer" class="textLayer absolute top-0 left-0"></div>
                        </div>
                    </div>
                </div>

                {{-- ===================
                    CHAT AI AREA
                =================== --}}
                <div class="w-full h-[40%] lg:h-[90vh] lg:w-[40%] bg-white dark:bg-gray-900 p-4 shadow-xl flex flex-col rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center mb-3">
                        <div class="h-2 w-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Chat Asisten AI 📘</h2>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Buku: <span class="font-medium">{{ $book->title }}</span></p>

                    {{-- CHAT OUTPUT AREA --}}
                    <div id="chat-output" class="flex-grow overflow-y-auto p-3 mb-3 rounded-lg bg-gray-50 dark:bg-gray-800 space-y-3">
                        <div class="text-center text-gray-400 text-xs italic mt-10">✨ Tanyakan sesuatu tentang halaman atau bab ini...</div>
                    </div>

                    {{-- Form Chat --}}
                    <form id="chat-form" class="mt-auto">
                        <textarea id="chat-input" rows="2"
                            class="w-full p-2 border border-gray-300 dark:border-gray-700 rounded-lg mb-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 text-sm focus:ring-2 focus:ring-indigo-500 outline-none resize-none"
                            placeholder="Tanyakan sesuatu..."></textarea>

                        <div class="flex gap-2">
                            <button type="submit" id="send-chat-btn"
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
    <style>
    /* ========== Chat Layout Animations ========== */
    .chat-bubble {
        max-width: 90%;
        padding: 0.9rem 1.1rem;
        border-radius: 1rem;
        line-height: 1.5;
        animation: fadeIn 0.25s ease-in-out;
        transition: all 0.3s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ========== User Message ========== */
    .user-message {
        align-self: flex-end;
        background: linear-gradient(135deg, #4f46e5, #3b82f6);
        color: #fff;
        border-bottom-right-radius: 0.3rem;
        text-align: right;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    /* ========== AI Message ========== */
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

    /* ========== Markdown Formatting ========== */
    .ai-message .prose {
        max-width: 100%;
        font-family: 'Inter', system-ui, sans-serif;
        font-size: 0.93rem;
    }
    .ai-message .prose h1,
    .ai-message .prose h2,
    .ai-message .prose h3 {
        font-weight: 700;
        color: #1e40af;
        margin-top: 0.75rem;
    }
    .dark .ai-message .prose h1,
    .dark .ai-message .prose h2,
    .dark .ai-message .prose h3 {
        color: #93c5fd;
    }
    .ai-message .prose table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 0.75rem;
    }
    .ai-message .prose th,
    .ai-message .prose td {
        border: 1px solid #d1d5db;
        padding: 0.5rem 0.75rem;
        text-align: left;
    }
    .ai-message .prose blockquote {
        border-left: 3px solid #3b82f6;
        padding-left: 0.75rem;
        font-style: italic;
        color: #4b5563;
    }
    .dark .ai-message .prose blockquote {
        color: #9ca3af;
        border-color: #60a5fa;
    }

    /* ========== Scrollbar ========== */
    #chat-output::-webkit-scrollbar {
        width: 6px;
    }
    #chat-output::-webkit-scrollbar-thumb {
        background: #9ca3af;
        border-radius: 4px;
    }

    /* ====== FIX MATHJAX OVERFLOW DAN UKURAN ====== */
    .ai-message .MathJax,
    .ai-message mjx-container {
        max-width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        display: inline-block;
        vertical-align: middle;
    }

    .ai-message mjx-container[jax="SVG"] svg {
        max-width: 100% !important;
        height: auto !important;
    }

    .ai-message mjx-container[jax="SVG"][display="true"] {
        display: block;
        margin: 1rem auto;
        text-align: center;
    }

    /* Supaya teks sekitarnya tetap sejajar */
    .ai-message p {
        overflow-wrap: break-word;
        word-break: break-word;
    }

    /* Sedikit spacing tambahan antar rumus dan teks */
    .ai-message mjx-container + p,
    .ai-message p + mjx-container {
        margin-top: 0.5rem;
    }
    </style>
    </style>
    @endpush


    @push('scripts')
    <script>
    window.MathJax = {
    tex: {
        inlineMath: [['$', '$'], ['\\(', '\\)']],
        displayMath: [['$$', '$$'], ['\\[', '\\]']],
        processEscapes: true
    },
    svg: {
        fontCache: 'global',
        scale: 0.9, // sedikit dikecilin biar proporsional
    },
    options: {
        renderActions: {
        addMenu: [0, '', ''] // nonaktifkan popup MathJax menu
        }
    }
    };
    </script>
    <script async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>




    <script>
    /* ============================
    KONFIGURASI PDF.JS & VARIABEL
    ============================ */
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    const pdfPath = "{{ Storage::url($book->file_path) }}";
    const bookId = {{ $book->id }};
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const chapterList = @json($book->chapters);

    const canvas = document.getElementById('pdf-canvas');
    const ctx = canvas.getContext('2d');
    const pageNumDisplay = document.getElementById('page-num');
    const pageCountDisplay = document.getElementById('page-count');
    const prevPageBtn = document.getElementById('prev-page');
    const nextPageBtn = document.getElementById('next-page');
    const zoomInBtn = document.getElementById('zoom-in-btn');
    const zoomOutBtn = document.getElementById('zoom-out-btn');
    const zoomLevel = document.getElementById('zoom-level');

    let pdfDoc = null;
    let currentPageNum = {{ $progress->last_page_number ?? 1 }};
    let scale = 1.5;
    const ZOOM_STEP = 0.25;

    /* ============================
    FUNGSI RENDER PDF
    ============================ */
    async function renderPage(num) {
        const page = await pdfDoc.getPage(num);
        const viewport = page.getViewport({ scale: scale });
        canvas.height = viewport.height;
        canvas.width = viewport.width;
        await page.render({ canvasContext: ctx, viewport: viewport }).promise;
        pageNumDisplay.textContent = num;
        zoomLevel.textContent = Math.round(scale * 100) + '%';
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
    AUTO-SAVE PROGRESS LOGIC
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
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ last_page_number: page })
            });
            console.log(`[Progress] Disimpan: halaman ${page}`);
        } catch (error) {
            console.error('[Progress] Gagal menyimpan progres:', error);
        }
    }

    const debouncedSaveProgress = debounce(saveProgress, 2000);

    /* Simpan progres otomatis sebelum halaman ditutup */
    window.addEventListener('beforeunload', () => {
        if (navigator.sendBeacon) {
            const url = "{{ route('books.progress.update', $book) }}";
            const data = new Blob(
                [JSON.stringify({ last_page_number: currentPageNum, _token: csrfToken })],
                { type: 'application/json' }
            );
            navigator.sendBeacon(url, data);
        } else {
            saveProgress(currentPageNum);
        }
    });

    /* ============================
    NAVIGASI PDF
    ============================ */
    prevPageBtn.addEventListener('click', () => {
        if (currentPageNum > 1) {
            currentPageNum--;
            renderPage(currentPageNum);
            debouncedSaveProgress(currentPageNum);
        }
    });

    nextPageBtn.addEventListener('click', () => {
        if (currentPageNum < pdfDoc.numPages) {
            currentPageNum++;
            renderPage(currentPageNum);
            debouncedSaveProgress(currentPageNum);
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
    LOMPAT KE BAB
    ============================ */
    const chapterJumpSelect = document.getElementById('chapter-jump');
    chapterJumpSelect?.addEventListener('change', async function () {
        const targetPage = parseInt(this.value);
        if (!isNaN(targetPage) && pdfDoc) {
            currentPageNum = targetPage;
            try {
                const page = await pdfDoc.getPage(currentPageNum);
                const viewport = page.getViewport({ scale: scale });
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                await page.render({ canvasContext: ctx, viewport: viewport }).promise;
                pageNumDisplay.textContent = currentPageNum;
                zoomLevel.textContent = Math.round(scale * 100) + '%';
                document.getElementById('pdf-render-container').scrollTo({ top: 0, behavior: 'smooth' });
                debouncedSaveProgress(currentPageNum);
            } catch (err) {
                console.error('[PDF] Gagal render halaman bab:', err);
            }
        }
    });

    /* ============================
    CHAT AI LOGIC
    ============================ */
    const chatOutput = document.getElementById('chat-output');
    const chatInput = document.getElementById('chat-input');
    const chatForm = document.getElementById('chat-form');
    const sendChatBtn = document.getElementById('send-chat-btn');
    const sendChapterBtn = document.getElementById('send-chapter-btn');

    /* Fungsi nambah bubble chat */
    function addChatMessage(message, sender) {
    const div = document.createElement('div');
    div.classList.add('chat-bubble', sender === 'user' ? 'user-message' : 'ai-message');

    if (sender === 'ai') {
        const content = document.createElement('div');
        content.classList.add('prose', 'dark:prose-invert', 'leading-relaxed');
        content.innerHTML = marked.parse(message);
        div.appendChild(content);
        chatOutput.appendChild(div);
        chatOutput.scrollTop = chatOutput.scrollHeight;

        // Render LaTeX setelah DOM update
        if (window.MathJax) {
            setTimeout(() => {
                MathJax.typesetPromise([content]);
            }, 150);
        }
    } else {
        div.innerHTML = `<span class="font-medium text-white">${message}</span>`;
        chatOutput.appendChild(div);
        chatOutput.scrollTop = chatOutput.scrollHeight;
    }
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

    /* ===== CHAT PER HALAMAN ===== */
    chatForm.addEventListener('submit', async e => {
        e.preventDefault();
        const question = chatInput.value.trim();
        if (!question) return;
        addChatMessage(question, 'user');
        chatInput.value = '';
        sendChatBtn.disabled = true;
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
            sendChatBtn.disabled = false;
        }
    });

    /* ===== CHAT PER BAB ===== */
    sendChapterBtn.addEventListener('click', async () => {
        const question = chatInput.value.trim();
        if (!question) return addChatMessage("⚠️ Tulis pertanyaan di kolom chat dulu sebelum kirim.", 'ai');

        const chapterId = getCurrentChapterId(currentPageNum);
        if (!chapterId) return addChatMessage("⚠️ Tidak ada bab yang cocok dengan halaman ini.", 'ai');

        addChatMessage(`(Bab) ${question}`, 'user');
        chatInput.value = '';
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
            sendChapterBtn.disabled = false;
        }
    });


    /* ============================
    AUTO UPDATE PROGRESS BAR (REALTIME)
    ============================ */
    let lastSavedPage = currentPageNum;

    function updateProgressUI(pageNum, totalPages) {
        const percent = Math.min(100, Math.round((pageNum / totalPages) * 100));
        document.getElementById('progress-page').textContent = pageNum;
        document.getElementById('progress-bar').style.width = percent + '%';
        document.getElementById('progress-text').textContent = `Progres Baca: ${percent}%`;
    }

    async function syncProgress(page) {
        if (page === lastSavedPage) return;
        lastSavedPage = page;

        try {
            await fetch("{{ route('books.progress.update', $book) }}", {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ last_page_number: page })
            });
            updateProgressUI(page, {{ $book->total_pages ?? 1 }});
            console.log(`[AutoUpdate] Progress updated: halaman ${page}`);
        } catch (error) {
            console.error('[AutoUpdate] Failed to update progress:', error);
        }
    }

    // Hook ke navigasi halaman PDF
    prevPageBtn.addEventListener('click', () => syncProgress(currentPageNum));
    nextPageBtn.addEventListener('click', () => syncProgress(currentPageNum));
    chapterJumpSelect?.addEventListener('change', () => syncProgress(currentPageNum));

    </script>
    @endpush
</x-app-layout>
