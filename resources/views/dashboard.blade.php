<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Mahasiswa') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">

            {{-- ===================================
                 📊 STATISTIK UTAMA
            ==================================== --}}
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-4">📈 Ringkasan Aktivitas Belajar</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-blue-50 p-6 rounded-lg shadow">
                        <h3 class="text-sm font-semibold text-blue-800">📚 Buku Dibaca</h3>
                        <p class="text-3xl font-bold text-blue-600 mt-1">{{ $booksReadCount }}</p>
                    </div>
                    <div class="bg-green-50 p-6 rounded-lg shadow">
                        <h3 class="text-sm font-semibold text-green-800">💖 Buku Favorit</h3>
                        <p class="text-3xl font-bold text-green-600 mt-1">{{ $favoriteCount }}</p>
                    </div>
                    <div class="bg-yellow-50 p-6 rounded-lg shadow">
                        <h3 class="text-sm font-semibold text-yellow-800">🧪 Tes Selesai</h3>
                        <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $testsDone }}</p>
                    </div>
                    <div class="bg-purple-50 p-6 rounded-lg shadow">
                        <h3 class="text-sm font-semibold text-purple-800">✅ Tes Lulus</h3>
                        <p class="text-3xl font-bold text-purple-600 mt-1">{{ $testsPassed }}</p>
                    </div>
                </div>
            </div>

            {{-- ===================================
                 🎯 STATISTIK ATTEMPT
            ==================================== --}}
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-4">🧩 Statistik Percobaan Tes</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-pink-50 p-6 rounded-lg shadow">
                        <h3 class="text-sm font-semibold text-pink-800">🧮 Total Attempt</h3>
                        <p class="text-3xl font-bold text-pink-600 mt-1">{{ $totalAttempts }}</p>
                    </div>
                    <div class="bg-orange-50 p-6 rounded-lg shadow">
                        <h3 class="text-sm font-semibold text-orange-800">📊 Rata-rata Nilai Attempt</h3>
                        <p class="text-3xl font-bold text-orange-600 mt-1">{{ round($avgAttemptScore, 1) }}%</p>
                    </div>
                    <div class="bg-indigo-50 p-6 rounded-lg shadow">
                        <h3 class="text-sm font-semibold text-indigo-800">🧠 Pre-Test Attempt</h3>
                        <p class="text-3xl font-bold text-indigo-600 mt-1">{{ $preAttempts }}</p>
                    </div>
                    <div class="bg-teal-50 p-6 rounded-lg shadow">
                        <h3 class="text-sm font-semibold text-teal-800">📘 Post-Test Attempt</h3>
                        <p class="text-3xl font-bold text-teal-600 mt-1">{{ $postAttempts }}</p>
                    </div>
                </div>
            </div>

            {{-- ===================================
                 📖 GRAFIK PROGRES MEMBACA
            ==================================== --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">📚 Progres Membaca per Buku</h3>
                <canvas id="progressChart" height="120"></canvas>
            </div>

            {{-- ===================================
                 ⚠️ TES BELUM LULUS
            ==================================== --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">⚠️ Tes Belum Lulus / Belum Dikerjakan</h3>
                @if(count($unpassedTests) === 0)
                    <p class="text-gray-500 italic">Belum ada tes yang perlu dikerjakan. Semua sudah selesai 🎉</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm border border-gray-200 rounded-lg">
                            <thead class="bg-gray-100 text-gray-700 font-semibold">
                                <tr>
                                    <th class="px-4 py-2 border">Buku</th>
                                    <th class="px-4 py-2 border">Bab</th>
                                    <th class="px-4 py-2 border">Nilai</th>
                                    <th class="px-4 py-2 border text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($unpassedTests as $test)
                                    <tr class="border-t hover:bg-gray-50">
                                        <td class="px-4 py-2 border">{{ $test['book'] }}</td>
                                        <td class="px-4 py-2 border">{{ $test['chapter'] }}</td>
                                        <td class="px-4 py-2 border text-center">
                                            {{ $test['score'] ?? 'Belum Dikerjakan' }}
                                        </td>
                                        <td class="px-4 py-2 border text-center">
                                            <a href="{{ route('quiz.show', $test['test_id']) }}"
                                               class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs px-3 py-1 rounded-md">
                                                Kerjakan Ulang
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- ===================================
                 📘 DETAIL PER BUKU
            ==================================== --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">📘 Detail Belajar Anda</h3>

                @forelse($progressData as $item)
                    @php $book = $item['book']; @endphp
                    <div class="border border-gray-200 rounded-lg shadow-sm mb-6">
                        <div class="flex justify-between items-center p-5 border-b bg-gray-50 rounded-t-lg">
                            <div>
                                <h4 class="font-semibold text-lg text-gray-800">{{ $book->title }}</h4>
                                <p class="text-sm text-gray-500">Oleh {{ $book->author ?? 'Penulis tidak diketahui' }}</p>
                            </div>
                            <a href="{{ route('books.read', $book->id) }}"
                               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                               📖 Lanjutkan Membaca
                            </a>
                        </div>

                        <div class="p-5">
                            <p class="text-sm text-gray-600 mb-1">Progres Membaca</p>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 mb-3">
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $item['pageProgress'] }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mb-3">
                                {{ $item['pageProgress'] }}% dari {{ $book->total_pages }} halaman
                            </p>

                            <h5 class="text-sm font-semibold text-gray-700 mb-2">🧩 Hasil Post-Test</h5>
                            @forelse ($item['postTests'] as $test)
                                @php
                                    $chapter = $book->chapters->firstWhere('id', $test['chapter_id']);
                                    $chapterTest = $book->tests->firstWhere('chapter_id', $chapter->id ?? null);
                                    $attemptCount = $chapterTest
                                        ? \App\Models\UserQuizAttempt::where('user_id', auth()->id())
                                            ->where('test_id', $chapterTest->id)
                                            ->count()
                                        : 0;
                                @endphp

                                <div class="flex justify-between items-center border border-gray-100 bg-gray-50 rounded-md p-2 mb-2">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:gap-2">
                                        <div class="flex items-center gap-2">
                                            @if($test['status'] === 'Lulus')
                                                <span class="text-green-600 font-bold text-lg leading-none">✅</span>
                                            @elseif($test['status'] === 'Belum Lulus')
                                                <span class="text-yellow-500 font-bold text-lg leading-none">⚠️</span>
                                            @else
                                                <span class="text-red-500 font-bold text-lg leading-none">❌</span>
                                            @endif
                                            <span class="text-sm text-gray-800">
                                                {{ $chapter->title ?? 'Bab Tidak Dikenal' }}
                                                <span class="text-xs text-gray-500">({{ $test['status'] }})</span>
                                            </span>
                                        </div>

                                        <p class="text-xs text-gray-500 ml-6 sm:ml-0">
                                            🧮 {{ $attemptCount }}x attempt
                                        </p>
                                    </div>

                                    @if($test['status'] !== 'Lulus')
                                        <a href="{{ route('quiz.show', $chapterTest->id ?? '#') }}"
                                        class="px-2 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-xs rounded-md transition">
                                        Kerjakan
                                        </a>
                                    @endif
                                </div>
                            @empty
                                <p class="text-xs text-gray-500 italic">Belum ada post-test untuk buku ini.</p>
                            @endforelse

                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 italic text-center">Belum ada data progres belajar.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- =========================
         CHART.JS SCRIPT
    ========================== --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('progressChart');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Progres Membaca (%)',
                    data: @json($chartProgress),
                    backgroundColor: '#4F46E5'
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true, max: 100 }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
