<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $test->title }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        {{-- ✅ Alert Hasil Test --}}
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg font-medium">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg font-medium">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white p-6 shadow-sm sm:rounded-lg">

            {{-- Jika belum ada soal sama sekali --}}
            @if ($test->questions->isEmpty())
                <div class="text-center py-12">
                    <p class="text-gray-600 text-lg mb-2">📭 Belum ada soal untuk tes ini.</p>
                    <p class="text-sm text-gray-500">
                        Silakan kembali lagi nanti setelah dosen atau admin menambahkan soal.
                    </p>

                    <a href="{{ url()->previous() }}"
                        class="inline-block mt-5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-md transition">
                        ← Kembali
                    </a>
                </div>
            @else
                {{-- Jika ada soal --}}
                <form method="POST" action="{{ route('quiz.store', $test->id) }}">
                    @csrf

                    @foreach ($test->questions as $index => $question)
                        <div class="mb-6 border-b border-gray-200 pb-4">
                            <h3 class="font-semibold text-gray-800 mb-2">
                                {{ $index + 1 }}. {{ $question->question_text }}
                            </h3>

                            @foreach ($question->options as $key => $option)
                                <label class="block mb-1">
                                    <input
                                        type="radio"
                                        name="answers[{{ $question->id }}]"
                                        value="{{ $key }}"
                                        class="mr-2 text-indigo-600 focus:ring-indigo-500"
                                        required
                                    >
                                    {{ strtoupper($key) }}. {{ $option }}
                                </label>
                            @endforeach
                        </div>
                    @endforeach

                    <div class="mt-6 flex justify-end">
                        <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                            Selesai Tes
                        </button>
                    </div>
                </form>
            @endif

        </div>
    </div>
</x-app-layout>
