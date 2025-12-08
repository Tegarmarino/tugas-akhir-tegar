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
                        <p class="text-3xl font-bold text-orange-600 mt-1">{{ round($avgAttemptScore, 1) }}</p>
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
                 📘 DETAIL PER BUKU (DIHAPUS)
            ==================================== --}}

            {{-- Bagian 'Detail Belajar Anda' (looping $progressData)
                 sudah dihapus dari sini agar tampilan minimalis --}}

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
