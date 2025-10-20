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
                        <div class="flex items-center">
                            <button id="prev-page" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm">Prev</button>
                            <span class="mx-2 text-gray-700 dark:text-gray-300 text-sm">Halaman: <span id="page-num">1</span> / <span id="page-count">?</span></span>
                            <button id="next-page" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm">Next</button>
                        </div>
                        <div class="flex items-center">
                            <button id="zoom-out-btn" class="px-2 py-1 bg-gray-500 text-white rounded hover:bg-gray-600">-</button>
                            <span id="zoom-level" class="text-sm text-gray-700 dark:text-gray-300 mx-2">150%</span>
                            <button id="zoom-in-btn" class="px-2 py-1 bg-gray-500 text-white rounded hover:bg-gray-600">+</button>
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
                ==================== --}}
                <div class="w-full h-[40%] lg:h-[90vh] lg:w-[40%] bg-white dark:bg-gray-800 p-4 shadow-lg flex flex-col rounded-lg overflow-hidden">
                    <h2 class="text-xl font-semibold mb-3 text-gray-900 dark:text-gray-100">💬 Chat dengan Asisten AI</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Buku: <span class="font-medium">{{ $book->title }}</span></p>

                    {{-- Chat Output --}}
                    <div id="chat-output" class="flex-grow overflow-y-auto border dark:border-gray-600 p-2 mb-3 rounded bg-gray-50 dark:bg-gray-700 text-sm prose dark:prose-invert max-w-none"></div>

                    {{-- Form Chat --}}
                    <form id="chat-form">
                        <textarea id="chat-input" rows="3"
                            class="w-full p-2 border dark:border-gray-600 rounded mb-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 text-sm"
                            placeholder="Tanyakan sesuatu tentang halaman atau bab ini..."></textarea>

                        <div class="flex gap-2">
                            <button type="submit" id="send-chat-btn"
                                class="flex-1 bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded text-sm">
                                💬 Tanya Halaman Ini
                            </button>

                            <button type="button" id="send-chapter-btn"
                                class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-sm">
                                🔍 Tanya Bab Ini
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================
         SCRIPT & LOGIC
    ==================== --}}
    @push('styles')
        <style>
        /* ========== CHAT BUBBLES ========== */
        #chat-output {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .chat-bubble {
            max-width: 85%;
            padding: 0.75rem 1rem;
            border-radius: 1rem;
            line-height: 1.4;
            word-wrap: break-word;
            font-size: 0.875rem;
            animation: fadeIn 0.25s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* USER CHAT */
        .user-message {
            align-self: flex-end;
            background: linear-gradient(135deg, #4F46E5, #3B82F6);
            color: #fff;
            border-bottom-right-radius: 0.25rem;
            text-align: right;
        }

        /* AI CHAT */
        .ai-message {
            align-self: flex-start;
            background-color: #F3F4F6;
            color: #1F2937;
            border-bottom-left-radius: 0.25rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .dark .ai-message {
            background-color: #374151;
            color: #E5E7EB;
        }

        /* SYSTEM/TIPS MESSAGE */
        .system-message {
            align-self: center;
            font-size: 0.75rem;
            color: #6B7280;
            background: transparent;
            padding: 0.25rem;
            font-style: italic;
        }
        </style>
        @endpush

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <script>
    // ===== PDF.js CONFIG =====
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    const pdfPath = "{{ Storage::url($book->file_path) }}";
    const bookId = {{ $book->id }};
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const chapterList = @json($book->chapters); // kirim data bab dari Laravel

    const canvas = document.getElementById('pdf-canvas');
    const ctx = canvas.getContext('2d');
    const pageNumDisplay = document.getElementById('page-num');
    const pageCountDisplay = document.getElementById('page-count');
    const prevPageBtn = document.getElementById('prev-page');
    const nextPageBtn = document.getElementById('next-page');
    const zoomInBtn = document.getElementById('zoom-in-btn');
    const zoomOutBtn = document.getElementById('zoom-out-btn');
    const zoomLevel = document.getElementById('zoom-level');

    let pdfDoc = null, currentPageNum = {{ $progress->last_page_number ?? 1 }}, scale = 1.5, ZOOM_STEP = 0.25;

    async function renderPage(num) {
        const page = await pdfDoc.getPage(num);
        const viewport = page.getViewport({ scale: scale });
        canvas.height = viewport.height;
        canvas.width = viewport.width;
        await page.render({ canvasContext: ctx, viewport: viewport }).promise;
        pageNumDisplay.textContent = num;
        zoomLevel.textContent = Math.round(scale * 100) + '%';
    }

    pdfjsLib.getDocument(pdfPath).promise.then(doc => {
        pdfDoc = doc;
        pageCountDisplay.textContent = pdfDoc.numPages;
        renderPage(currentPageNum);
    });

    prevPageBtn.addEventListener('click', () => { if (currentPageNum > 1) renderPage(--currentPageNum); });
    nextPageBtn.addEventListener('click', () => { if (currentPageNum < pdfDoc.numPages) renderPage(++currentPageNum); });
    zoomInBtn.addEventListener('click', () => { scale += ZOOM_STEP; renderPage(currentPageNum); });
    zoomOutBtn.addEventListener('click', () => { if (scale > 0.5) { scale -= ZOOM_STEP; renderPage(currentPageNum); } });

    // ===== CHAT LOGIC =====
    const chatOutput = document.getElementById('chat-output');
    const chatInput = document.getElementById('chat-input');
    const chatForm = document.getElementById('chat-form');
    const sendChatBtn = document.getElementById('send-chat-btn');
    const sendChapterBtn = document.getElementById('send-chapter-btn');

    function addChatMessage(message, sender) {
        const div = document.createElement('div');
        div.classList.add('chat-bubble');
        div.classList.add(sender === 'user' ? 'user-message' :
                        sender === 'ai' ? 'ai-message' :
                        'system-message');
        div.innerHTML = marked.parse(message);
        chatOutput.appendChild(div);
        chatOutput.scrollTop = chatOutput.scrollHeight;
    }


    // Cari ID bab berdasarkan halaman aktif
    function getCurrentChapterId(pageNum) {
        if (!chapterList || chapterList.length === 0) return null;
        for (const c of chapterList) {
            if (pageNum >= c.start_page && pageNum <= c.end_page) {
                return c.id;
            }
        }
        return null;
    }

    // ===== PER HALAMAN =====
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

    // ===== PER BAB =====
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
    </script>
    @endpush
</x-app-layout>
