<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Soal Baru') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <form action="{{ route('admin.questions.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold">Pertanyaan</label>
                    <textarea name="question_text" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm" required></textarea>
                </div>

                @foreach(['a', 'b', 'c', 'd'] as $opt)
                    <div class="mb-3">
                        <label class="block text-gray-700 font-semibold">Pilihan {{ strtoupper($opt) }}</label>
                        <input type="text" name="option_{{ $opt }}" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                    </div>
                @endforeach

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold">Jawaban Benar</label>
                    <select name="correct_answer" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                        <option value="">-- Pilih Jawaban --</option>
                        @foreach(['a','b','c','d'] as $ans)
                            <option value="{{ $ans }}">Pilihan {{ strtoupper($ans) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold">Tag (opsional)</label>
                    <input type="text" name="tag" class="w-full border-gray-300 rounded-lg shadow-sm">
                </div>

                <div class="flex justify-end">
                    <a href="{{ route('admin.questions.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg mr-2">Batal</a>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
