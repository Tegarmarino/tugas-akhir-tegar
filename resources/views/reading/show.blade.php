<x-app-layout>
    <x-slot name="title">
        Membaca: {{ $book->title }}
    </x-slot>

    {{-- Mengatur tinggi halaman agar tidak ada scroll di body utama --}}
    <div class="h-screen flex flex-col overflow-hidden">
        {{-- Kontainer utama yang akan mengisi sisa layar --}}
        <div class="flex-grow max-w-full mx-auto sm:px-6 lg:px-8 py-6 overflow-hidden">
            <div class="flex flex-col lg:flex-row gap-4 h-full">

                {{-- Kolom PDF Viewer (60% di desktop) --}}
                <div class="w-full h-[60%] lg:h-auto lg:w-[60%] flex flex-col bg-gray-200 dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                    {{-- Kontrol Bar di Atas --}}
                    <div class="p-2 bg-gray-100 dark:bg-gray-700 flex items-center justify-between z-10 gap-2 rounded-t-lg border-b dark:border-gray-600 flex-shrink-0">
                        {{-- Kontrol Navigasi Halaman (selalu terlihat) --}}
                        <div class="flex items-center">
                            <button id="prev-page" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm disabled:opacity-50 disabled:cursor-not-allowed">Prev</button>
                            <span class="mx-2 text-gray-700 dark:text-gray-300 text-sm">Halaman: <span id="page-num">1</span> / <span id="page-count">?</span></span>
                            <button id="next-page" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm disabled:opacity-50 disabled:cursor-not-allowed">Next</button>
                        </div>

                        {{-- Dropdown untuk Aksi Lainnya (menggunakan Alpine.js) --}}
                        <div x-data="{ open: false }" class="relative">
                            {{-- Tombol Pemicu Dropdown --}}
                            <button @click="open = !open" class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700 text-sm flex items-center">
                                Aksi
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 ml-1">
                                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            {{-- Konten Dropdown --}}
                            <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-md shadow-lg z-20 p-4 border dark:border-gray-600">
                                {{-- Kontrol Zoom --}}
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Zoom</label>
                                    <div class="flex items-center justify-center">
                                        <button id="zoom-out-btn" class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">-</button>
                                        <span id="zoom-level" class="mx-2 text-gray-700 dark:text-gray-300 text-sm w-12 text-center">150%</span>
                                        <button id="zoom-in-btn" class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">+</button>
                                    </div>
                                </div>
                                {{-- Kontrol Aksi Buku --}}
                                <div class="border-t dark:border-gray-600 pt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status Baca</label>
                                    <div class="flex flex-col items-center space-y-2">
                                        @if(isset($progress) && $progress->is_finished)
                                            <span class="w-full text-center px-3 py-1 bg-green-600 text-white rounded text-sm flex items-center justify-center cursor-default" title="Anda telah menyelesaikan buku ini">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 mr-1"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" /></svg>
                                                Selesai
                                            </span>
                                            <a href="{{ route('quiz.show', ['quiz' => $book->quiz, 'type' => 'post-test']) }}" class="w-full text-center px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm" title="Ulangi post-test">
                                                Latihan Kuis
                                            </a>
                                        @else
                                            <a href="{{ route('quiz.show', ['quiz' => $book->quiz, 'type' => 'post-test']) }}" id="finish-reading-btn" class="w-full text-center px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-sm">
                                                Selesai Baca & Post-test
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Container ini akan menangani scrolling saat di-zoom --}}
                    <div id="pdf-render-container" class="overflow-auto relative flex-grow bg-gray-300 dark:bg-gray-700 rounded-b-lg">
                        <div class="relative mx-auto" id="pdf-content-wrapper">
                            <canvas id="pdf-canvas" class="shadow-lg"></canvas>
                            <div id="text-layer" class="textLayer absolute top-0 left-0" style="pointer-events: auto;"></div>
                        </div>
                    </div>
                </div>

                {{-- Kolom Chat AI (40% di desktop) --}}
                <div class="w-full h-[40%] lg:h-[90vh] lg:w-[40%] lg:flex-shrink-0 bg-white dark:bg-gray-800 p-4 shadow-lg flex flex-col rounded-lg overflow-hidden">
                    <h2 class="hidden lg:block text-xl font-semibold mb-3 text-gray-900 dark:text-gray-100">Chat dengan Asisten AI</h2>
                    <p class="hidden lg:block text-xs text-gray-500 dark:text-gray-400 mb-1">Buku: <span class="font-medium">{{ $book->title }}</span></p>
                    <p class="hidden lg:flex text-xs text-gray-500 dark:text-gray-400 mb-3 items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 mr-1 flex-shrink-0"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.75 15h.5a.75.75 0 0 0 0-1.5h-.5a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.25 9H9Z" clip-rule="evenodd" /></svg>
                        <span>Tips: Untuk pertanyaan tentang topik/kata spesifik, pastikan Anda berada di halaman yang memuat topik/kata tersebut.</span>
                    </p>
                    <div id="chat-output" class="flex-grow overflow-y-auto border dark:border-gray-600 p-2 mb-3 rounded bg-gray-50 dark:bg-gray-700 text-sm prose dark:prose-invert max-w-none">
                        {{-- Pesan chat akan muncul di sini --}}
                    </div>
                    <div x-data="{ open: false }" class="mb-3 relative">
                        {{-- Tombol Pemicu Dropdown --}}
                        <button @click="open = !open" class="w-full text-left text-sm text-gray-600 dark:text-gray-400 mb-1 p-2 border dark:border-gray-600 rounded-md flex justify-between items-center">
                            <span>Contoh Pertanyaan...</span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                            <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        {{-- Konten Dropdown --}}
                        <div x-show="open" @click.outside="open = false" x-transition class="absolute top-full mt-2 w-full bg-white dark:bg-gray-800 rounded-md shadow-lg z-20 p-2 border dark:border-gray-600 space-y-1 max-h-48 overflow-y-auto">
                            <button class="template-question w-full text-left text-xs bg-gray-100 dark:bg-gray-700 dark:text-gray-200 text-gray-800 px-2 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600" data-question="Simpulkan halaman ini secara detail.">Simpulkan halaman ini secara detail.</button>
                            <button class="template-question w-full text-left text-xs bg-gray-100 dark:bg-gray-700 dark:text-gray-200 text-gray-800 px-2 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600" data-question="Jelaskan konsep-konsep utama yang dibahas di halaman ini.">Jelaskan konsep utama halaman ini.</button>
                            <button class="template-question w-full text-left text-xs bg-gray-100 dark:bg-gray-700 dark:text-gray-200 text-gray-800 px-2 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600" data-question="Apa argumen sentral yang disampaikan penulis di halaman ini?">Apa argumen sentral halaman ini?</button>
                            <button class="template-question w-full text-left text-xs bg-gray-100 dark:bg-gray-700 dark:text-gray-200 text-gray-800 px-2 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600" data-question="Berikan ringkasan singkat keseluruhan buku ini.">Simpulkan seluruh buku ini.</button>
                        </div>
                    </div>
                    <form id="chat-form">
                        <textarea id="chat-input" class="w-full p-2 border dark:border-gray-600 rounded mb-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 text-sm" rows="3" placeholder="Tanyakan sesuatu tentang buku ini atau halaman saat ini..."></textarea>
                        <button type="submit" id="send-chat-btn" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded text-sm">
                            Kirim
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal untuk definisi --}}
    <div id="definition-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
        <div class="relative p-5 border w-full max-w-md shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="definition-title">Definisi</h3>
                <div class="mt-2 px-7 py-3 text-left">
                    <div class="text-sm text-gray-600 dark:text-gray-300 prose dark:prose-invert max-w-none" id="definition-content-html"></div>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="close-modal-btn" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf_viewer.css">
    <style>
        #pdf-render-container { position: relative; }
        #pdf-canvas { display: block; }
        .textLayer { position: absolute; left: 0; top: 0; right: 0; bottom: 0; overflow: hidden; opacity: 0.2; line-height: 1.0; pointer-events: auto; }
        .textLayer > span { color: transparent; position: absolute; white-space: pre; cursor: text; transform-origin: 0% 0%; }
        .textLayer .highlight { margin: -1px; padding: 1px; background-color: rgba(180, 0, 180, 0.2); border-radius: 4px; }
        .textLayer .highlight.appended { position: initial; }
        .textLayer .highlight.begin { border-radius: 4px 0px 0px 4px; }
        .textLayer .highlight.end { border-radius: 0px 4px 4px 0px; }
        .textLayer .highlight.middle { border-radius: 0px; }
        .textLayer .highlight.selected { background-color: rgba(0, 0, 180, 0.2); }

        #chat-output div { padding: 0.5rem; margin-bottom: 0.25rem; border-radius: 0.5rem; word-wrap: break-word; max-width: 90%; display: flex; flex-direction: column; }
        #chat-output div.user-message { background-color: #DBEAFE; color: #1E3A8A; text-align: right; margin-left: auto; align-items: flex-end;}
        #chat-output div.ai-message { background-color: #F3F4F6; color: #1F2937; text-align: left; margin-right: auto; align-items: flex-start;}
        .dark #chat-output div.user-message { background-color: #1E40AF; color: #DBEAFE; }
        .dark #chat-output div.ai-message { background-color: #374151; color: #F3F4F6; }

        .speak-button { background: none; border: none; cursor: pointer; padding: 0.25rem; margin-top: 0.25rem; align-self: flex-start; }
        .speak-button svg { width: 1rem; height: 1rem; fill: currentColor; }
        .speak-button.speaking svg { fill: #2563eb; }
        .dark .speak-button.speaking svg { fill: #3b82f6; }

        #chat-output .ai-message .prose-rendered-content h1,
        #chat-output .ai-message .prose-rendered-content h2,
        #chat-output .ai-message .prose-rendered-content h3,
        #definition-content-html h1,
        #definition-content-html h2,
        #definition-content-html h3 { margin-top: 0.75em; margin-bottom: 0.25em; line-height: 1.2; font-weight: 600; }
        #chat-output .ai-message .prose-rendered-content h1, #definition-content-html h1 { font-size: 1.25em; }
        #chat-output .ai-message .prose-rendered-content h2, #definition-content-html h2 { font-size: 1.125em; }
        #chat-output .ai-message .prose-rendered-content h3, #definition-content-html h3 { font-size: 1.05em; }
        #chat-output .ai-message .prose-rendered-content p, #definition-content-html p { margin-bottom: 0.5em; }
        #chat-output .ai-message .prose-rendered-content ul,
        #chat-output .ai-message .prose-rendered-content ol,
        #definition-content-html ul,
        #definition-content-html ol { margin-left: 1.5em; margin-bottom: 0.5em; list-style-position: outside; }
        #chat-output .ai-message .prose-rendered-content ul, #definition-content-html ul { list-style-type: disc; }
        #chat-output .ai-message .prose-rendered-content ol, #definition-content-html ol { list-style-type: decimal; }
        #chat-output .ai-message .prose-rendered-content li, #definition-content-html li { margin-bottom: 0.2em; }
        #chat-output .ai-message .prose-rendered-content code, #definition-content-html code { background-color: #e5e7eb; padding: 0.1em 0.3em; border-radius: 0.25em; font-size: 0.9em; font-family: monospace; }
        .dark #chat-output .ai-message .prose-rendered-content code, .dark #definition-content-html code { background-color: #4b5563; }
        #chat-output .ai-message .prose-rendered-content pre, #definition-content-html pre { background-color: #e5e7eb; padding: 0.5em; border-radius: 0.25em; overflow-x: auto; margin-bottom: 0.5em; font-size: 0.9em;}
        .dark #chat-output .ai-message .prose-rendered-content pre, .dark #definition-content-html pre { background-color: #4b5563; }
        #chat-output .ai-message .prose-rendered-content blockquote, #definition-content-html blockquote { border-left: 3px solid #d1d5db; padding-left: 0.75em; margin-left: 0; margin-bottom: 0.5em; color: #4b5563; font-style: italic; }
        .dark #chat-output .ai-message .prose-rendered-content blockquote, .dark #definition-content-html blockquote { border-left-color: #4b5563; color: #9ca3af; }
        #chat-output .ai-message .prose-rendered-content a, #definition-content-html a { color: #2563eb; text-decoration: underline; }
        .dark #chat-output .ai-message .prose-rendered-content a, .dark #definition-content-html a { color: #3b82f6; }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        const pdfPath = "{{ Storage::url($book->file_path) }}";
        const bookId = {{ $book->id }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const pdfContentWrapper = document.getElementById('pdf-content-wrapper');
        const canvas = document.getElementById('pdf-canvas');
        const ctx = canvas.getContext('2d');
        const textLayerDiv = document.getElementById('text-layer');
        const pageNumDisplay = document.getElementById('page-num');
        const pageCountDisplay = document.getElementById('page-count');
        const prevPageBtn = document.getElementById('prev-page');
        const nextPageBtn = document.getElementById('next-page');
        const zoomInBtn = document.getElementById('zoom-in-btn');
        const zoomOutBtn = document.getElementById('zoom-out-btn');
        const zoomLevelSpan = document.getElementById('zoom-level');
        const chatOutput = document.getElementById('chat-output');
        const chatInput = document.getElementById('chat-input');
        const chatForm = document.getElementById('chat-form');
        const sendChatBtn = document.getElementById('send-chat-btn');
        const templateQuestionButtons = document.querySelectorAll('.template-question');
        const definitionModal = document.getElementById('definition-modal');
        const definitionTitle = document.getElementById('definition-title');
        const definitionContentHtml = document.getElementById('definition-content-html');
        const closeModalBtn = document.getElementById('close-modal-btn');

        let pdfDoc = null;
        let currentPageNum = {{ $progress->last_page_number ?? 1 }};
        let pageRendering = false;
        let pageNumPending = null;
        let scale = 1.5;
        const ZOOM_STEP = 0.25;
        let typingSpeed = 10;
        let currentSpokenUtterance = null;
        let activeSpeakButton = null;

        const speakerIconSVG = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M7.126 7.46A6.961 6.961 0 0 0 5.002 10a6.961 6.961 0 0 0 2.124 2.54c.465.425.89.892 1.279 1.393a.75.75 0 0 0 1.19-.036c.19-.26.37-.528.538-.803.13-.21.243-.422.338-.633a4.5 4.5 0 0 1 1.13-2.037.75.75 0 0 0-1.042-1.074 3.001 3.001 0 0 0-1.007 1.585.75.75 0 0 0 .023.699c-.15.22-.306.435-.465.646a.754.754 0 0 1-.211.216c-.223-.32-.47-.629-.734-.922A5.46 5.46 0 0 1 5.002 10c0-.66.122-1.292.35-1.874.062-.163.13-.323.207-.478a3 3 0 0 1 3.11-1.79.75.75 0 0 0 .694-1.204A4.5 4.5 0 0 0 7.126 7.46ZM12.9 4.097a.75.75 0 0 1 1.04-1.08l.003.002A9.2 9.2 0 0 1 15.5 10a9.198 9.198 0 0 1-1.556 6.98c-.001.002-.002.003-.002.004a.75.75 0 1 1-1.04-1.08c.001-.001.001-.002.002-.003A7.699 7.699 0 0 0 14 10a7.697 7.697 0 0 0-1.1-3.843Z"/></svg>`;
        const stopIconSVG = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M2 10a8 8 0 1 1 16 0 8 8 0 0 1-16 0Zm5-2.25A.75.75 0 0 1 7.75 7h4.5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-.75.75h-4.5a.75.75 0 0 1-.75-.75v-4.5Z" clip-rule="evenodd" /></svg>`;

        function debounce(func, delay) { let timeout; return function(...args) { clearTimeout(timeout); timeout = setTimeout(() => func.apply(this, args), delay); }; }
        async function saveProgress(page) { try { await fetch("{{ route('books.progress.update', $book) }}", { method: 'PATCH', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', }, body: JSON.stringify({ last_page_number: page }) }); } catch (error) { console.error('[Progress] Failed to save progress:', error); } }
        const debouncedSaveProgress = debounce(saveProgress, 2000);

        function updateZoomDisplay() {
            zoomLevelSpan.textContent = `${Math.round(scale * 100)}%`;
        }

        async function renderPage(num) {
            pageRendering = true;
            pageNumDisplay.textContent = num;
            updateZoomDisplay();
            try {
                const page = await pdfDoc.getPage(num);
                const viewport = page.getViewport({ scale: scale });

                pdfContentWrapper.style.width = viewport.width + 'px';
                pdfContentWrapper.style.height = viewport.height + 'px';
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                textLayerDiv.style.width = viewport.width + 'px';
                textLayerDiv.style.height = viewport.height + 'px';
                textLayerDiv.innerHTML = '';

                const renderContext = { canvasContext: ctx, viewport: viewport };
                await page.render(renderContext).promise;

                const textContent = await page.getTextContent();
                const textLayer = new pdfjsLib.TextLayerBuilder({
                    textLayerDiv: textLayerDiv,
                    pageIndex: page.pageIndex,
                    viewport: viewport,
                    eventBus: null,
                });
                textLayer.setTextContentSource(textContent);
                textLayer.render();

                pageRendering = false;
                if (pageNumPending !== null) {
                    renderPage(pageNumPending);
                    pageNumPending = null;
                }
            } catch (error) { console.error('Error rendering page ' + num + ':', error); pageRendering = false; }
        }

        function queueRenderPage(num) {
            if (pageRendering) {
                pageNumPending = num;
            } else {
                renderPage(num);
            }
            debouncedSaveProgress(num);

            prevPageBtn.disabled = (num <= 1);
            prevPageBtn.classList.toggle('opacity-50', num <= 1);
            prevPageBtn.classList.toggle('cursor-not-allowed', num <= 1);

            if (pdfDoc) {
                nextPageBtn.disabled = (num >= pdfDoc.numPages);
                nextPageBtn.classList.toggle('opacity-50', num >= pdfDoc.numPages);
                nextPageBtn.classList.toggle('cursor-not-allowed', num >= pdfDoc.numPages);
            }
        }

        prevPageBtn.addEventListener('click', function() { if (currentPageNum <= 1) return; currentPageNum--; queueRenderPage(currentPageNum); });
        nextPageBtn.addEventListener('click', function() { if (!pdfDoc || currentPageNum >= pdfDoc.numPages) return; currentPageNum++; queueRenderPage(currentPageNum); });

        zoomInBtn.addEventListener('click', function() {
            scale += ZOOM_STEP;
            queueRenderPage(currentPageNum);
        });

        zoomOutBtn.addEventListener('click', function() {
            if (scale - ZOOM_STEP >= 0.25) {
                scale -= ZOOM_STEP;
                queueRenderPage(currentPageNum);
            }
        });

        pdfjsLib.getDocument(pdfPath).promise.then(function(doc) {
            pdfDoc = doc;
            pageCountDisplay.textContent = pdfDoc.numPages;
            queueRenderPage(currentPageNum);
        }).catch(function(error) {
            console.error('Error loading PDF: ', error);
            const pdfContainer = document.getElementById('pdf-render-container');
            if(pdfContainer){
                 pdfContainer.innerHTML = '<p class="text-red-500 p-4">Gagal memuat file PDF. Pastikan file valid dan dapat diakses.</p>';
            }
        });

        window.addEventListener('beforeunload', (event) => {
            if (navigator.sendBeacon) {
                const url = "{{ route('books.progress.update', $book) }}";
                const data = new Blob([JSON.stringify({last_page_number: currentPageNum, _token: csrfToken})], { type: 'application/json' });
                navigator.sendBeacon(url, data);
            } else {
                saveProgress(currentPageNum);
            }
        });

        textLayerDiv.addEventListener('mouseup', async function() {
            const selectedText = window.getSelection().toString().trim();
            if (selectedText.length > 1 && selectedText.length < 150) {
                try {
                    definitionContentHtml.innerHTML = '<p class="animate-pulse text-gray-500 dark:text-gray-400">Memproses...</p>';
                    definitionTitle.textContent = `Definisi: "${selectedText}"`;
                    definitionModal.classList.remove('hidden');

                    const response = await fetch("{{ route('books.highlight.define', $book) }}", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body: JSON.stringify({ text: selectedText })
                    });
                    if (!response.ok) {
                        const errorData = await response.json().catch(() => ({ definition: "Error: Respons server tidak valid."}));
                        throw new Error(errorData.definition || `Server error: ${response.status}`);
                    }
                    const data = await response.json();
                    if (data.definition) {
                        definitionContentHtml.innerHTML = marked.parse(data.definition, { breaks: true, gfm: true });
                    } else {
                        definitionContentHtml.textContent = 'Definisi tidak ditemukan.';
                    }
                } catch (error) {
                    console.error('Error getting definition:', error);
                    definitionContentHtml.textContent = error.message || 'Gagal mengambil definisi.';
                }
            }
        });
        closeModalBtn.addEventListener('click', () => definitionModal.classList.add('hidden'));

        templateQuestionButtons.forEach(button => {
            button.addEventListener('click', function() {
                const question = this.dataset.question;
                chatInput.value = question;
                const submitEvent = new Event('submit', { bubbles: true, cancelable: true });
                chatForm.dispatchEvent(submitEvent);
            });
        });

        function addChatMessage(message, sender, isTyping = false) {
            const messageContainer = document.createElement('div');
            messageContainer.classList.add(sender === 'user' ? 'user-message' : 'ai-message');

            const textDiv = document.createElement('div');
            textDiv.classList.add('prose-rendered-content');

            if (sender === 'ai' && isTyping) {
                messageContainer.classList.add('flex', 'items-center', 'space-x-1', 'p-2');
                for (let i = 0; i < 3; i++) {
                    const dot = document.createElement('span');
                    dot.classList.add('h-2', 'w-2', 'bg-gray-400', 'dark:bg-gray-600', 'rounded-full', 'animate-pulse');
                    if (i === 0) dot.style.animationDelay = '0s';
                    if (i === 1) dot.style.animationDelay = '0.2s';
                    if (i === 2) dot.style.animationDelay = '0.4s';
                    messageContainer.appendChild(dot);
                }
            } else if (sender === 'ai') {
                textDiv.innerHTML = marked.parse(message, { breaks: true, gfm: true });
                messageContainer.appendChild(textDiv);

                const speakButton = document.createElement('button');
                speakButton.classList.add('speak-button', 'text-gray-500', 'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-200');
                speakButton.innerHTML = speakerIconSVG;
                speakButton.setAttribute('aria-label', 'Bacakan pesan AI');

                const textToSpeak = textDiv.textContent || textDiv.innerText || "";

                speakButton.onclick = function() {
                    if (!textToSpeak || !('speechSynthesis' in window)) {
                        alert('Maaf, browser Anda tidak mendukung fitur pembacaan teks.');
                        return;
                    }
                    if (window.speechSynthesis.speaking) {
                        window.speechSynthesis.cancel();
                        if (activeSpeakButton === this && currentSpokenUtterance && currentSpokenUtterance.text === textToSpeak) {
                            this.innerHTML = speakerIconSVG;
                            activeSpeakButton = null;
                            currentSpokenUtterance = null;
                            return;
                        }
                        if(activeSpeakButton) activeSpeakButton.innerHTML = speakerIconSVG;
                    }

                    const utterance = new SpeechSynthesisUtterance(textToSpeak);
                    utterance.lang = 'id-ID';

                    utterance.onstart = () => {
                        this.innerHTML = stopIconSVG;
                        this.classList.add('speaking');
                        activeSpeakButton = this;
                    };
                    utterance.onend = () => {
                        this.innerHTML = speakerIconSVG;
                        this.classList.remove('speaking');
                        if (activeSpeakButton === this) activeSpeakButton = null;
                        currentSpokenUtterance = null;
                    };
                    utterance.onerror = (event) => {
                        console.error('SpeechSynthesisUtterance.onerror', event);
                        this.innerHTML = speakerIconSVG;
                        this.classList.remove('speaking');
                        if (activeSpeakButton === this) activeSpeakButton = null;
                        currentSpokenUtterance = null;
                        addChatMessage('Maaf, terjadi kesalahan saat mencoba membacakan teks.', 'ai', false);
                    }
                    window.speechSynthesis.speak(utterance);
                    currentSpokenUtterance = utterance;
                };
                messageContainer.appendChild(speakButton);

            } else {
                textDiv.textContent = message;
                messageContainer.appendChild(textDiv);
            }

            chatOutput.appendChild(messageContainer);
            chatOutput.scrollTop = chatOutput.scrollHeight;
            return messageContainer;
        }

        let typingIndicatorElement = null;

        chatForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const userQuestion = chatInput.value.trim();
            if (!userQuestion) return;

            addChatMessage(userQuestion, 'user');
            chatInput.value = '';
            sendChatBtn.disabled = true;
            sendChatBtn.classList.add('opacity-50', 'cursor-not-allowed');

            if (typingIndicatorElement) typingIndicatorElement.remove();
            typingIndicatorElement = addChatMessage('', 'ai', true);

            try {
                const response = await fetch("{{ route('books.chat', $book) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        question: userQuestion,
                        page_number: currentPageNum
                    })
                });

                if (typingIndicatorElement) {
                    typingIndicatorElement.remove();
                    typingIndicatorElement = null;
                }

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({ reply: "Error: Respons server tidak valid."}));
                    throw new Error(errorData.reply || `Server error: ${response.status}`);
                }
                const data = await response.json();
                if (data.reply) {
                    addChatMessage(data.reply, 'ai');
                } else if (data.error) {
                    addChatMessage("Error: " + data.error, 'ai', false);
                }
            } catch (error) {
                console.error('Error sending chat message:', error);
                if (typingIndicatorElement) {
                    typingIndicatorElement.remove();
                    typingIndicatorElement = null;
                }
                addChatMessage(error.message || "Error: Tidak dapat terhubung ke server.", 'ai', false);
            } finally {
                sendChatBtn.disabled = false;
                sendChatBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        });
    </script>
    @endpush
</x-app-layout>
