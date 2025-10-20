<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pre-Test Buku: ') . $book->title }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            {{-- ✅ Notifikasi --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                    ✅ {{ session('success') }}
                </div>
            @endif

            {{-- ❌ Error --}}
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

            {{-- 🧠 Form --}}
            <form method="POST" action="{{ route('admin.tests.pre.save', $book->id) }}">
                @csrf

                {{-- Judul Tes --}}
                <div class="mb-6">
                    <label class="block font-semibold text-gray-700">Judul Tes</label>
                    <input
                        type="text"
                        name="title"
                        class="w-full border-gray-300 rounded-lg"
                        placeholder="Contoh: Pre-Test Buku {{ $book->title }}"
                        value="{{ old('title', isset($test) ? $test->title : '') }}"
                        required
                    >
                </div>

                {{-- Search + Filter --}}
                <div class="mb-4 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800">Pilih Soal</h3>

                    <div class="flex space-x-3 w-1/2 justify-end">
                        {{-- Filter Tag --}}
                        <select id="filterTag"
                            class="border border-gray-300 rounded-lg px-2 py-2 text-sm text-gray-700 focus:ring focus:ring-indigo-100 focus:border-indigo-500">
                            <option value="">📚 Semua Topik</option>
                            @foreach ($tags as $tag)
                                <option value="{{ strtolower($tag) }}">{{ $tag }}</option>
                            @endforeach
                        </select>

                        {{-- Search --}}
                        <div class="relative w-2/3">
                            <input type="text" id="searchQuestion" placeholder="🔍 Cari soal..."
                                class="border border-gray-300 rounded-lg pl-8 pr-3 py-2 w-full focus:ring focus:ring-indigo-100 focus:border-indigo-500 text-sm">
                            <svg class="w-4 h-4 text-gray-500 absolute left-2.5 top-2.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.2-5.2M16.8 10.4A6.4 6.4 0 1110.4 4a6.4 6.4 0 016.4 6.4z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Daftar Soal --}}
                @php
                    $sortedQuestions = $questions->sortByDesc(function($q) use ($selectedIds) {
                        return in_array($q->id, $selectedIds ?? []);
                    });
                @endphp

                <div class="max-h-[480px] overflow-y-auto border border-gray-200 rounded-lg divide-y divide-gray-100 bg-gray-50" id="questionList">
                    @foreach ($sortedQuestions as $question)
                        <label class="flex items-start gap-3 p-3 cursor-pointer hover:bg-gray-100 transition question-item"
                               data-tag="{{ strtolower($question->tag) }}">
                            <input
                                type="checkbox"
                                name="questions[]"
                                value="{{ $question->id }}"
                                class="mt-1 text-indigo-600 focus:ring-indigo-500"
                                @if(isset($selectedIds) && in_array($question->id, $selectedIds)) checked @endif
                            >
                            <div>
                                <p class="text-sm text-gray-800 leading-relaxed">{{ $question->question_text }}</p>
                                <span class="text-xs text-gray-500 italic">Tag: {{ $question->tag ?? 'Tanpa Tag' }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>

                {{-- Script Filter & Search --}}
                <script>
                const searchInput = document.getElementById('searchQuestion');
                const tagSelect = document.getElementById('filterTag');
                const questionItems = document.querySelectorAll('.question-item');

                function filterQuestions() {
                    const searchValue = searchInput.value.toLowerCase();
                    const tagValue = tagSelect.value;

                    questionItems.forEach(item => {
                        const text = item.textContent.toLowerCase();
                        const tag = item.getAttribute('data-tag');
                        const matchesSearch = text.includes(searchValue);
                        const matchesTag = tagValue === '' || tag === tagValue;
                        item.style.display = (matchesSearch && matchesTag) ? '' : 'none';
                    });
                }

                searchInput.addEventListener('keyup', filterQuestions);
                tagSelect.addEventListener('change', filterQuestions);
                </script>

                {{-- Tombol --}}
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
