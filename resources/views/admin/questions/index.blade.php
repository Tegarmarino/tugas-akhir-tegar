<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bank Soal') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{-- Flash Message --}}
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex justify-end mb-4">
            <a href="{{ route('admin.questions.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                + Tambah Soal
            </a>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                @if($questions->count() > 0)
                    <form action="{{ route('admin.questions.bulk-delete') }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <div class="flex justify-between mb-4">
                            <div>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" id="select-all" class="cursor-pointer">
                                    <span class="text-gray-700 text-sm">Pilih Semua (halaman ini)</span>
                                </label>
                            </div>

                            <div>
                                <button type="submit"
                                        onclick="return confirm('Yakin ingin menghapus semua soal yang dipilih?')"
                                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                                    Hapus Terpilih
                                </button>
                            </div>
                        </div>

                        <table class="w-full border border-gray-200">
                            <thead class="bg-gray-100 text-left">
                                <tr>
                                    <th class="p-3 border-b text-center">Pilih</th>
                                    <th class="p-3 border-b text-center">#</th>
                                    <th class="p-3 border-b">Pertanyaan</th>
                                    <th class="p-3 border-b">Tag</th>
                                    <th class="p-3 border-b text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($questions as $question)
                                    <tr class="hover:bg-gray-50">
                                        <td class="p-3 border-b text-center">
                                            <input type="checkbox" name="ids[]" value="{{ $question->id }}" class="checkbox-item cursor-pointer">
                                        </td>
                                        <td class="p-3 border-b text-center">{{ $loop->iteration + ($questions->currentPage() - 1) * $questions->perPage() }}</td>
                                        <td class="p-3 border-b">{{ Str::limit($question->question_text, 80) }}</td>
                                        <td class="p-3 border-b">{{ $question->tag ?? '-' }}</td>
                                        <td class="p-3 border-b text-center">
                                            <a href="{{ route('admin.questions.edit', $question->id) }}" class="text-blue-600 hover:underline">Edit</a> |
                                            <form action="{{ route('admin.questions.destroy', $question->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        onclick="return confirm('Yakin ingin menghapus soal ini?')"
                                                        class="text-red-600 hover:underline">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-4">
                            {{ $questions->appends(request()->query())->links() }}
                        </div>
                    </form>
                @else
                    <p class="text-gray-600">Belum ada soal di database.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Script Select All --}}
    <script>
        document.getElementById('select-all').addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('.checkbox-item');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    </script>
</x-app-layout>
