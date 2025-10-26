<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Post-Test Buku: ') . $book->title }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            {{-- ✅ Notifikasi --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                    ✅ {{ session('success') }}
                </div>
            @endif

            {{-- 🧩 Form --}}
            <form method="POST" action="{{ route('admin.tests.post.store', $book->id) }}">
                @csrf

                @foreach ($chapters as $chapter)
                    <div class="mb-8 border border-gray-200 rounded-lg shadow-sm p-5 bg-gray-50">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-lg font-semibold text-gray-800">
                                📖 {{ $chapter->title }}
                                <span class="text-sm text-gray-500">(Halaman {{ $chapter->start_page }} - {{ $chapter->end_page }})</span>
                            </h3>
                        </div>

                        {{-- Filter + Search --}}
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="text-sm font-medium text-gray-700">Pilih Soal</h4>

                            <div class="flex space-x-3 w-1/2 justify-end">
                                {{-- Filter Tag --}}
                                <select class="border border-gray-300 rounded-lg px-2 py-2 text-sm text-gray-700 tagFilter"
                                        data-chapter="{{ $chapter->id }}">
                                    <option value="">📚 Semua Topik</option>
                                    @foreach ($tags as $tag)
                                        <option value="{{ strtolower($tag) }}">{{ $tag }}</option>
                                    @endforeach
                                </select>

                                {{-- Search Bar --}}
                                <div class="relative w-2/3">
                                    <input type="text"
                                           placeholder="🔍 Cari soal..."
                                           class="border border-gray-300 rounded-lg pl-8 pr-3 py-2 w-full text-sm searchQuestion"
                                           data-chapter="{{ $chapter->id }}">
                                    <svg class="w-4 h-4 text-gray-500 absolute left-2.5 top-2.5"
                                         xmlns="http://www.w3.org/2000/svg" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M21 21l-5.2-5.2M16.8 10.4A6.4 6.4 0 1110.4 4a6.4 6.4 0 016.4 6.4z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Hidden Chapter ID --}}
                        <input type="hidden" name="tests[{{ $chapter->id }}][chapter_id]" value="{{ $chapter->id }}">

                        {{-- Daftar Soal --}}
                        @php
                            // Sort pertanyaan: yang sudah dipilih tampil paling atas
                            $sortedQuestions = $questions->sortByDesc(function($q) use ($selected, $chapter) {
                                return isset($selected[$chapter->id]) && in_array($q->id, $selected[$chapter->id]);
                            });
                        @endphp

                        <div class="max-h-[480px] overflow-y-auto border border-gray-200 rounded-lg divide-y divide-gray-100 bg-white" id="chapter-{{ $chapter->id }}">
                            @foreach ($sortedQuestions as $question)
                                <label class="flex items-start gap-3 p-3 cursor-pointer hover:bg-gray-50 transition question-item-{{ $chapter->id }}"
                                    data-tag="{{ strtolower($question->tag) }}">
                                    <input
                                        type="checkbox"
                                        name="tests[{{ $chapter->id }}][questions][]"
                                        value="{{ $question->id }}"
                                        class="mt-1 text-indigo-600 focus:ring-indigo-500"
                                        @if(isset($selected[$chapter->id]) && in_array($question->id, $selected[$chapter->id])) checked @endif
                                    >
                                    <div>
                                        <p class="text-sm text-gray-800 leading-relaxed">{{ $question->question_text }}</p>
                                        <span class="text-xs text-gray-500 italic">Tag: {{ $question->tag ?? 'Tanpa Tag' }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                    </div>
                @endforeach

                {{-- Script: Search + Filter per Bab --}}
                <script>
                    document.querySelectorAll('.searchQuestion').forEach(input => {
                        input.addEventListener('keyup', function() {
                            const chapterId = this.dataset.chapter;
                            const searchValue = this.value.toLowerCase();
                            const tagValue = document.querySelector(`.tagFilter[data-chapter='${chapterId}']`).value;
                            document.querySelectorAll('.question-item-' + chapterId).forEach(div => {
                                const text = div.textContent.toLowerCase();
                                const tag = div.getAttribute('data-tag');
                                const matchesSearch = text.includes(searchValue);
                                const matchesTag = tagValue === '' || tag === tagValue;
                                div.style.display = (matchesSearch && matchesTag) ? '' : 'none';
                            });
                        });
                    });

                    document.querySelectorAll('.tagFilter').forEach(select => {
                        select.addEventListener('change', function() {
                            const chapterId = this.dataset.chapter;
                            const tagValue = this.value;
                            const searchValue = document.querySelector(`.searchQuestion[data-chapter='${chapterId}']`).value.toLowerCase();
                            document.querySelectorAll('.question-item-' + chapterId).forEach(div => {
                                const text = div.textContent.toLowerCase();
                                const tag = div.getAttribute('data-tag');
                                const matchesSearch = text.includes(searchValue);
                                const matchesTag = tagValue === '' || tag === tagValue;
                                div.style.display = (matchesSearch && matchesTag) ? '' : 'none';
                            });
                        });
                    });
                </script>

                {{-- Tombol --}}
                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('admin.books.index') }}"
                       class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Kembali
                    </a>
                    <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        💾 Simpan Semua Post-Test
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
