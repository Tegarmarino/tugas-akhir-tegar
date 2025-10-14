<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Status Pre-Test Buku: ') . $book->title }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if (!$hasTest)
                <p class="text-gray-700 mb-4">Belum ada pre-test untuk buku ini.</p>
                <a href="{{ route('admin.tests.pre.create', $book->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    + Buat Pre-Test Baru
                </a>
            @else
                <h3 class="text-lg font-semibold mb-2">Judul Tes: {{ $test->title }}</h3>

                <div class="overflow-x-auto mb-6">
                    <table class="min-w-full border border-gray-200 rounded-lg">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="py-2 px-4 text-left border-b">#</th>
                                <th class="py-2 px-4 text-left border-b">Pertanyaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($test->questions as $index => $question)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2 px-4 border-b">{{ $index + 1 }}</td>
                                    <td class="py-2 px-4 border-b">{{ $question->question_text }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.tests.pre.edit', $book->id) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600">
                        ✏️ Edit Pre-Test
                    </a>
                    <a href="{{ route('admin.books.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Kembali
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
