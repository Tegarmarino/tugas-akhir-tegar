<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Bab/Sub-Bab Buku: ') . $book->title }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="list-disc pl-6 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('admin.chapters.update', [$book->id, $chapter->id]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold">Judul Bab/Sub-Bab</label>
                    <input type="text" name="title" value="{{ $chapter->title }}" class="w-full border-gray-300 rounded-lg" required>
                </div>

                <div class="flex space-x-2">
                    <div class="w-1/2">
                        <label class="block text-gray-700 font-semibold">Halaman Awal</label>
                        <input type="number" name="start_page" value="{{ $chapter->start_page }}" class="w-full border-gray-300 rounded-lg" required>
                    </div>
                    <div class="w-1/2">
                        <label class="block text-gray-700 font-semibold">Halaman Akhir</label>
                        <input type="number" name="end_page" value="{{ $chapter->end_page }}" class="w-full border-gray-300 rounded-lg" required>
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <a href="{{ route('admin.chapters.index', $book->id) }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md mr-2">Batal</a>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
