<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Penandaan Bab / Sub-Bab Buku: ') . $book->title }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Tambah Bab/Sub-Bab</h3>
            <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700">
                💡 <strong>Catatan:</strong> Anda tidak perlu menambahkan nomor bab secara manual.
                Sistem akan memberikan nomor urut otomatis berdasarkan urutan bab yang anda tambahkan.
            </div>

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

            @if (session('warning'))
                <div class="mb-4 p-4 bg-yellow-100 text-yellow-700 rounded-lg">
                    {{ session('warning') }}
                </div>
            @endif

            <form action="{{ route('admin.chapters.store', $book->id) }}" method="POST">
                @csrf
                <div id="chapter-container">
                    <div class="chapter-item mb-4 border border-gray-200 p-4 rounded-lg">
                        <label class="block text-gray-700 font-semibold">Judul Bab/Sub-Bab</label>
                        <input type="text" name="chapters[0][title]" class="w-full border-gray-300 rounded-lg mb-2" required>

                        <div class="flex space-x-2">
                            <div class="w-1/2">
                                <label class="block text-gray-700 font-semibold">Halaman Awal</label>
                                <input type="number" name="chapters[0][start_page]" class="w-full border-gray-300 rounded-lg" required>
                            </div>
                            <div class="w-1/2">
                                <label class="block text-gray-700 font-semibold">Halaman Akhir</label>
                                <input type="number" name="chapters[0][end_page]" class="w-full border-gray-300 rounded-lg" required>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" id="add-chapter" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300">
                    + Tambah Bab Lagi
                </button>

                <div class="mt-4 flex justify-end">
                    <a href="{{ route('admin.books.edit', $book->id) }}"
                        class="me-5 px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Kembali
                    </a>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Simpan Semua</button>
                </div>
            </form>
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Daftar Bab/Sub-Bab Saat Ini</h3>
            @if($chapters->count())
                <table class="w-full border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-3 border-b">Judul</th>
                            <th class="p-3 border-b">Halaman</th>
                            <th class="p-3 border-b text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($chapters as $chapter)
                            <tr class="hover:bg-gray-50">
                                <td class="p-3 border-b">{{ $chapter->title }}</td>
                                <td class="p-3 border-b">{{ $chapter->start_page }} - {{ $chapter->end_page }}</td>
                                <td class="p-3 border-b text-center">
                                    <div class="flex justify-center space-x-2">
                                        <a href="{{ route('admin.chapters.edit', [$book->id, $chapter->id]) }}"
                                        class="text-blue-600 hover:underline">Edit</a>
                                        <form action="{{ route('admin.chapters.destroy', [$book->id, $chapter->id]) }}"
                                            method="POST"
                                            onsubmit="return confirm('Hapus bab ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-600">Belum ada bab/sub-bab untuk buku ini.</p>
            @endif
        </div>
    </div>

    <script>
        let chapterIndex = 1;
        const container = document.getElementById('chapter-container');

        document.getElementById('add-chapter').addEventListener('click', () => {
            const template = `
                <div class="chapter-item mb-4 border border-gray-200 p-4 rounded-lg relative">
                    <button type="button" class="remove-chapter absolute top-2 right-2 text-red-600 hover:text-red-800 text-sm">✕</button>

                    <label class="block text-gray-700 font-semibold">Judul Bab/Sub-Bab</label>
                    <input type="text" name="chapters[${chapterIndex}][title]" class="w-full border-gray-300 rounded-lg mb-2" required>

                    <div class="flex space-x-2">
                        <div class="w-1/2">
                            <label class="block text-gray-700 font-semibold">Halaman Awal</label>
                            <input type="number" name="chapters[${chapterIndex}][start_page]" class="w-full border-gray-300 rounded-lg" required>
                        </div>
                        <div class="w-1/2">
                            <label class="block text-gray-700 font-semibold">Halaman Akhir</label>
                            <input type="number" name="chapters[${chapterIndex}][end_page]" class="w-full border-gray-300 rounded-lg" required>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', template);
            chapterIndex++;
        });

        // event delegation untuk hapus input
        container.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-chapter')) {
                e.target.closest('.chapter-item').remove();
            }
        });
    </script>

</x-app-layout>
