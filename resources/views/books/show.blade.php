{{-- resources/views/books/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $book->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded-md shadow-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 px-4 py-3 bg-red-100 text-red-800 rounded-md shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            {{-- ===========================
                 CARD: DETAIL BUKU
            ============================ --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="md:flex">
                    {{-- Gambar Cover --}}
                    <div class="md:w-1/3 p-6 border-r border-gray-100">
                        @if($book->cover_image_path)
                            <img src="{{ Storage::url($book->cover_image_path) }}"
                                 alt="{{ $book->title }}"
                                 class="w-full h-auto object-cover rounded-md shadow-lg">
                        @else
                            <div class="w-full h-96 bg-gray-200 flex items-center justify-center rounded-md">
                                <span class="text-gray-500 text-xl">No Cover Available</span>
                            </div>
                        @endif
                    </div>

                    {{-- Detail Buku --}}
                    <div class="md:w-2/3 p-6">
                        <h1 class="text-3xl font-bold text-gray-900">{{ $book->title }}</h1>
                        <p class="text-lg text-gray-700 mt-2">Oleh: {{ $book->author ?? 'Penulis tidak diketahui' }}</p>
                        <p class="text-sm text-gray-500 mt-1">
                            Tanggal Terbit:
                            {{ $book->publication_date ? $book->publication_date->format('d F Y') : 'Tidak diketahui' }}
                        </p>

                        {{-- Overview --}}
                        <div class="mt-6">
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">📘 Ringkasan Buku</h3>
                            <p class="text-gray-600 leading-relaxed prose">
                                {{ $book->overview ?? 'Ringkasan belum tersedia untuk buku ini.' }}
                            </p>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="mt-8 flex flex-wrap gap-3">
                            <button id="openReadModal"
                                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition duration-150 ease-in-out">
                                📖 Baca Buku
                            </button>

                            <form id="favoriteForm" action="{{ route('books.favorite', $book) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="px-6 py-3 font-semibold rounded-lg shadow-md transition duration-150 ease-in-out
                                        {{ $isFavorited ? 'bg-red-100 text-red-600 hover:bg-red-200' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                    {{ $isFavorited ? 'Hapus dari Favorit' : 'Tambah ke Favorit' }}
                                </button>
                            </form>

                            <form action="{{ route('books.progress.reset', $book->id) }}" method="POST"
                                onsubmit="return confirm('Yakin reset progres dan hasil tes untuk buku ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-md transition duration-150 ease-in-out">
                                    🔄 Reset Progress
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===========================
                CARD: STATUS BELAJAR
            ============================ --}}
            <div class="mt-8 bg-white border border-gray-200 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">📊 Status Belajar Mahasiswa</h3>
                <p class="text-sm text-gray-600 mb-5 leading-relaxed">
                    Status ini menampilkan progres belajar Anda pada buku ini.
                    <span class="font-medium text-gray-700">Pre-Test</span> digunakan untuk menilai pemahaman awal terhadap isi buku,
                    sementara <span class="font-medium text-gray-700">Post-Test</span> disusun secara spesifik per bab
                    untuk mengukur sejauh mana Anda memahami materi setelah membaca.
                </p>

                {{-- =========================
                    PRE-TEST
                ========================== --}}
                @php
                    $preTestExists = isset($preTest) && $preTest !== null;
                @endphp

                <div class="p-4 mb-4 bg-gray-50 rounded-md border border-gray-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <strong class="text-gray-800">🧩 Pre-Test Umum</strong>
                            <p class="text-sm text-gray-500 mt-1">
                                Mengukur pemahaman umum Anda terhadap konsep dan topik utama buku sebelum mulai membaca.
                            </p>
                        </div>

                        @if (!$preTestExists)
                            <span class="text-gray-400 italic">Belum tersedia</span>
                        @elseif ($hasPreTestDone)
                            <span class="text-green-600 font-semibold">
                                ✅ Selesai (Nilai: {{ $userScore ?? '—' }})
                            </span>
                        @else
                            <span class="text-gray-500 italic">Belum dikerjakan</span>
                        @endif
                    </div>
                </div>

                {{-- =========================
                    POST-TEST PER BAB
                ========================== --}}
                <h4 class="text-sm font-semibold text-gray-700 mb-3 mt-6">📖 Post-Test per Bab</h4>

                @php
                    $hasAnyPostTest = $book->tests()->where('type', 'post')->exists();
                @endphp

                @if (!$hasAnyPostTest)
                    <div class="p-4 bg-gray-50 border border-gray-100 rounded-md text-sm text-gray-500 italic">
                        Belum ada post-test yang dibuat untuk bab-bab di buku ini.
                        Silakan tunggu hingga dosen atau admin menambahkannya.
                    </div>
                @else
                    @foreach ($book->chapters->sortBy('start_page') as $chapter)
                        @php
                            $chapterTest = $book->tests()
                                ->where('type', 'post')
                                ->where('chapter_id', $chapter->id)
                                ->first();

                            $result = $chapterTest
                                ? \App\Models\Result::where('user_id', auth()->id())
                                    ->where('test_id', $chapterTest->id)
                                    ->first()
                                : null;
                        @endphp

                        <div class="border border-gray-100 bg-gray-50 rounded-md p-3 mb-3 flex justify-between items-start text-sm">
                            <div>
                                <strong class="text-gray-800">{{ $chapter->title }}</strong>
                                <p class="text-xs text-gray-500 mt-1">
                                    Post-test ini mengevaluasi pemahaman Anda terhadap isi dan konsep yang dibahas dalam bab ini.
                                </p>
                            </div>

                            @if (!$chapterTest)
                                <span class="text-gray-400 italic">Belum tersedia</span>
                            @elseif ($result)
                                <span class="text-green-600 font-semibold whitespace-nowrap">
                                    ✅ Selesai (Nilai: {{ $result->score ?? '—' }})
                                </span>
                            @else
                                <span class="text-gray-500 italic">Belum dikerjakan</span>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>


        </div>
    </div>

    {{-- =========================
         MODAL OPSI BACA / PRE-TEST
    ========================== --}}
    @if ($preTest)
        <div id="readModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg w-96 p-6 text-center">
                <h2 class="text-lg font-semibold mb-3">Pre-Test Tersedia</h2>
                <p class="text-gray-600 mb-6">
                    Buku ini memiliki pre-test untuk menilai pemahaman awal Anda.
                    Ingin mengerjakannya sekarang sebelum mulai membaca?
                </p>
                <div class="flex justify-center space-x-3">
                    <a href="{{ route('quiz.show', $preTest->id) }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                       Kerjakan Pre-Test
                    </a>
                    <a href="{{ route('books.read', $book) }}"
                       class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-md">
                       Langsung Baca
                    </a>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            const openReadModal = document.getElementById('openReadModal');
            const readModal = document.getElementById('readModal');
            if (openReadModal && readModal) {
                openReadModal.addEventListener('click', () => {
                    @if($hasPreTestDone)
                        // Kalau sudah pretest, langsung ke halaman baca
                        window.location.href = "{{ route('books.read', $book) }}";
                    @else
                        // Kalau belum, tampilkan modal pilihan
                        readModal.classList.remove('hidden');
                    @endif
                });
            }

            if (readModal) {
                readModal.addEventListener('click', e => {
                    if (e.target === readModal) readModal.classList.add('hidden');
                });
            }
        </script>
    @endpush
</x-app-layout>
