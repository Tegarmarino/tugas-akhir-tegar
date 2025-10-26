<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">

            {{-- =========================
                 📊 STATISTIK UTAMA
            ========================== --}}
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-4">📘 Statistik Sistem</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-blue-50 p-6 rounded-lg shadow text-center">
                        <div class="text-3xl mb-1">📗</div>
                        <h3 class="text-sm font-semibold text-blue-800">Total Buku</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ $totalBooks }}</p>
                    </div>
                    <div class="bg-indigo-50 p-6 rounded-lg shadow text-center">
                        <div class="text-3xl mb-1">📖</div>
                        <h3 class="text-sm font-semibold text-indigo-800">Total Bab</h3>
                        <p class="text-3xl font-bold text-indigo-600">{{ $totalChapters }}</p>
                    </div>
                    <div class="bg-yellow-50 p-6 rounded-lg shadow text-center">
                        <div class="text-3xl mb-1">🧩</div>
                        <h3 class="text-sm font-semibold text-yellow-800">Total Tes</h3>
                        <p class="text-3xl font-bold text-yellow-600">{{ $totalTests }}</p>
                    </div>
                    <div class="bg-green-50 p-6 rounded-lg shadow text-center">
                        <div class="text-3xl mb-1">👩‍🎓</div>
                        <h3 class="text-sm font-semibold text-green-800">Total Mahasiswa</h3>
                        <p class="text-3xl font-bold text-green-600">{{ $totalUsers }}</p>
                    </div>
                    <div class="bg-purple-50 p-6 rounded-lg shadow text-center">
                        <div class="text-3xl mb-1">🧾</div>
                        <h3 class="text-sm font-semibold text-purple-800">Hasil Tes Tersimpan</h3>
                        <p class="text-3xl font-bold text-purple-600">{{ $totalResults }}</p>
                    </div>
                    <div class="bg-red-50 p-6 rounded-lg shadow text-center">
                        <div class="text-3xl mb-1">📈</div>
                        <h3 class="text-sm font-semibold text-red-800">Rata-rata Nilai Global</h3>
                        <p class="text-3xl font-bold text-red-600">{{ $avgScoreGlobal }}%</p>
                    </div>
                </div>
            </div>

            {{-- =========================
                 🔁 STATISTIK ATTEMPT
            ========================== --}}
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-4">🧮 Statistik Attempt Tes</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-pink-50 p-6 rounded-lg shadow text-center">
                        <div class="text-3xl mb-1">🧮</div>
                        <h3 class="text-sm font-semibold text-pink-800">Total Attempt</h3>
                        <p class="text-3xl font-bold text-pink-600">{{ $totalAttempts }}</p>
                    </div>
                    <div class="bg-orange-50 p-6 rounded-lg shadow text-center">
                        <div class="text-3xl mb-1">📊</div>
                        <h3 class="text-sm font-semibold text-orange-800">Rata-rata Nilai Attempt</h3>
                        <p class="text-3xl font-bold text-orange-600">{{ $avgAttemptScoreGlobal }}%</p>
                    </div>
                    <div class="bg-teal-50 p-6 rounded-lg shadow text-center">
                        <div class="text-3xl mb-1">🔁</div>
                        <h3 class="text-sm font-semibold text-teal-800">Rata-rata Attempt / Mahasiswa</h3>
                        <p class="text-3xl font-bold text-teal-600">{{ $avgAttemptsPerUser }}</p>
                    </div>
                </div>
            </div>

            {{-- =========================
                 📈 GRAFIK ANALITIK
            ========================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">📊 Jumlah Tes per Buku</h3>
                    <canvas id="testsPerBookChart" height="100"></canvas>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">📈 Rata-rata Nilai per Buku</h3>
                    <canvas id="avgScoreChart" height="100"></canvas>
                </div>
            </div>

            {{-- =========================
                 🏆 PERFORMA & MAHASISWA
            ========================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Buku --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">📚 Performa Buku</h3>

                    <div class="border border-gray-100 rounded-lg p-4 mb-4">
                        <p class="text-gray-700 text-sm mb-2">
                            <strong>Buku Terbaik:</strong> {{ $topBook['title'] ?? '-' }}
                            <span class="text-green-600 font-semibold">({{ $topBook['avg_score'] ?? 0 }}%)</span>
                        </p>
                        <p class="text-gray-700 text-sm">
                            <strong>Buku Terendah:</strong> {{ $worstBook['title'] ?? '-' }}
                            <span class="text-red-600 font-semibold">({{ $worstBook['avg_score'] ?? 0 }}%)</span>
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm border border-gray-200 rounded-lg">
                            <thead class="bg-gray-100 text-gray-700 font-semibold">
                                <tr>
                                    <th class="px-4 py-2 border">Judul Buku</th>
                                    <th class="px-4 py-2 border text-center">Tes</th>
                                    <th class="px-4 py-2 border text-center">Rata-rata</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookStats as $b)
                                    <tr class="border-t hover:bg-gray-50">
                                        <td class="px-4 py-2 border">{{ $b['title'] }}</td>
                                        <td class="px-4 py-2 border text-center">{{ $b['tests_count'] }}</td>
                                        <td class="px-4 py-2 border text-center">{{ $b['avg_score'] }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Mahasiswa --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">👩‍🎓 Mahasiswa Teraktif</h3>
                    @if($topStudents->isEmpty())
                        <p class="text-gray-500 italic">Belum ada data progres mahasiswa.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm border border-gray-200 rounded-lg">
                                <thead class="bg-gray-100 text-gray-700 font-semibold">
                                    <tr>
                                        <th class="px-4 py-2 border">Nama</th>
                                        <th class="px-4 py-2 border text-center">Buku Dibaca</th>
                                        <th class="px-4 py-2 border text-center">Update Terakhir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topStudents as $s)
                                        <tr class="border-t hover:bg-gray-50">
                                            <td class="px-4 py-2 border">{{ $s['name'] }}</td>
                                            <td class="px-4 py-2 border text-center">{{ $s['books'] }}</td>
                                            <td class="px-4 py-2 border text-center">
                                                {{ \Carbon\Carbon::parse($s['last_update'])->diffForHumans() }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- =========================
         📊 CHART.JS SCRIPT
    ========================== --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // 📊 Jumlah Tes per Buku
        new Chart(document.getElementById('testsPerBookChart'), {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Jumlah Tes',
                    data: @json($chartTests),
                    backgroundColor: '#4F46E5'
                }]
            },
            options: { scales: { y: { beginAtZero: true } } }
        });

        // 📈 Rata-rata Nilai per Buku
        new Chart(document.getElementById('avgScoreChart'), {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Rata-rata Nilai (%)',
                    data: @json($chartAvgScores),
                    fill: false,
                    borderColor: '#16A34A',
                    tension: 0.3
                }]
            },
            options: { scales: { y: { beginAtZero: true, max: 100 } } }
        });
    </script>
    @endpush
</x-app-layout>
