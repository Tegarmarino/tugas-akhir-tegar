<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Assign Pre-Test untuk Buku: ') . $book->title }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ isset($test) ? route('admin.tests.pre.update', $book->id) : route('admin.tests.pre.store', $book->id) }}">
                @csrf
                <div class="mb-4">
                    <label class="block font-semibold text-gray-700">Judul Tes</label>
                    @if(isset($test))
                        <p class="text-sm text-gray-600 mb-2">
                            Mode: <span class="font-semibold text-blue-700">Edit Pre-Test</span>
                        </p>
                    @endif

                    <div class="mb-4">
                        <label class="block font-semibold text-gray-700">Judul Tes</label>
                        <input
                            type="text"
                            name="title"
                            class="w-full border-gray-300 rounded-lg"
                            placeholder="Contoh: Pre-Test Bab 1"
                            value="{{ old('title', isset($test) ? $test->title : '') }}"
                            required
                        >
                    </div>

                </div>

                {{-- <h3 class="text-lg font-semibold mb-2">Pilih Soal:</h3>
                <div class="max-h-96 overflow-y-auto border p-3 rounded-lg">
                    @foreach ($questions as $question)
                        <div class="flex items-start mb-2">
                            <input type="checkbox" name="questions[]" value="{{ $question->id }}" class="mt-1">
                            <p class="ml-2">{{ $question->question_text }}</p>
                        </div>
                    @endforeach
                </div>
                 --}}
                 @php
                    $editMode = isset($test);
                @endphp

                <div class="mb-4 flex justify-between items-center">
                    <h2 class="text-lg font-semibold">
                        {{ $editMode ? 'Edit Pre-Test' : 'Buat Pre-Test Baru' }}
                    </h2>
                    <input type="text" id="searchQuestion" placeholder="Cari soal..." class="border rounded-lg p-2 w-1/3">
                </div>

                <div class="max-h-96 overflow-y-auto border p-3 rounded-lg" id="questionList">
                    @foreach ($questions as $question)
                        <div class="flex items-start mb-2">
                            <input type="checkbox" name="questions[]" value="{{ $question->id }}"
                                class="mt-1 question-checkbox"
                                @if(isset($selectedIds) && in_array($question->id, $selectedIds)) checked @endif>
                            <p class="ml-2">{{ $question->question_text }}</p>
                        </div>
                    @endforeach
                </div>

                <script>
                    // Search filter
                    document.getElementById('searchQuestion').addEventListener('keyup', function() {
                        const value = this.value.toLowerCase();
                        document.querySelectorAll('#questionList div').forEach(div => {
                            const text = div.textContent.toLowerCase();
                            div.style.display = text.includes(value) ? '' : 'none';
                        });
                    });
                </script>


                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('admin.books.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Kembali
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Simpan Tes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
