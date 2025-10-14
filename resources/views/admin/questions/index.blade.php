<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bank Soal') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-end mb-4">
            <a href="{{ route('admin.questions.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                + Tambah Soal
            </a>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                @if($questions->count() > 0)
                    <table class="w-full border border-gray-200">
                        <thead class="bg-gray-100 text-left">
                            <tr>
                                <th class="p-3 border-b">#</th>
                                <th class="p-3 border-b">Pertanyaan</th>
                                <th class="p-3 border-b">Tag</th>
                                <th class="p-3 border-b text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($questions as $question)
                                <tr class="hover:bg-gray-50">
                                    <td class="p-3 border-b">{{ $loop->iteration }}</td>
                                    <td class="p-3 border-b">{{ Str::limit($question->question_text, 80) }}</td>
                                    <td class="p-3 border-b">{{ $question->tag ?? '-' }}</td>
                                    <td class="p-3 border-b text-center">
                                        <a href="{{ route('admin.questions.edit', $question->id) }}" class="text-blue-600 hover:underline">Edit</a> |
                                        <form action="{{ route('admin.questions.destroy', $question->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Yakin ingin menghapus soal ini?')" class="text-red-600 hover:underline">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $questions->links() }}
                    </div>
                @else
                    <p class="text-gray-600">Belum ada soal di database.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
