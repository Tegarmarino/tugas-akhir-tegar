{{-- Ini adalah file edit buku untuk admin --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Buku: ') }} <span class="italic">{{ $book->title }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    {{-- MENAMPILKAN ERROR UMUM DARI SESSION --}}
                    @if (session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">Oops! Terjadi Kesalahan:</strong>
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    {{-- MENAMPILKAN SEMUA ERROR VALIDASI JIKA ADA --}}
                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                            <p class="font-bold">Harap perbaiki error berikut:</p>
                            <ul class="mt-2 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.books.update', $book) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Judul Buku --}}
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700">Judul Buku <span class="text-red-500">*</span></label>
                            <input type="text" name="title" id="title" value="{{ old('title', $book->title) }}"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('title') border-red-500 @enderror" required>
                            @error('title')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Ganti File PDF --}}
                        <div class="mb-4">
                            <label for="pdf_file" class="block text-sm font-medium text-gray-700">Ganti File PDF (Opsional)</label>
                            <input type="file" name="pdf_file" id="pdf_file" accept=".pdf"
                                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('pdf_file') border-red-500 @enderror">
                            @if($book->file_path)
                                <p class="mt-1 text-xs text-gray-500">
                                    File saat ini:
                                    <a href="{{ Storage::url($book->file_path) }}" target="_blank" class="text-blue-500 hover:underline">
                                        {{ basename($book->file_path) }}
                                    </a>
                                </p>
                            @endif
                            @error('pdf_file')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Ganti Cover --}}
                        <div class="mb-6">
                            <label for="cover_image" class="block text-sm font-medium text-gray-700">Ganti Gambar Cover (Opsional)</label>
                            <input type="file" name="cover_image" id="cover_image" accept="image/*"
                                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 @error('cover_image') border-red-500 @enderror">
                            @if($book->cover_image_path)
                                <div class="mt-2">
                                    <img src="{{ Storage::url($book->cover_image_path) }}" alt="Current Cover" class="h-24 w-auto rounded object-cover shadow">
                                    <p class="text-xs text-gray-500">Cover saat ini</p>
                                </div>
                            @endif
                            @error('cover_image')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <hr class="my-6">
                        <p class="text-sm text-gray-600 mb-4">
                            Detail berikut digenerate oleh AI saat upload PDF. Anda bisa mengubahnya jika perlu.
                        </p>

                        {{-- Penulis --}}
                        <div class="mb-4">
                            <label for="author" class="block text-sm font-medium text-gray-700">Penulis</label>
                            <input type="text" name="author" id="author" value="{{ old('author', $book->author) }}"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>

                        {{-- Tanggal Terbit --}}
                        <div class="mb-4">
                            <label for="publication_date" class="block text-sm font-medium text-gray-700">Tanggal Terbit</label>
                            <input type="date" name="publication_date" id="publication_date"
                                   value="{{ old('publication_date', $book->publication_date ? $book->publication_date->format('Y-m-d') : '') }}"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>

                        {{-- Overview --}}
                        <div class="mb-4">
                            <label for="overview" class="block text-sm font-medium text-gray-700">Overview</label>
                            <textarea name="overview" id="overview" rows="5"
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('overview', $book->overview) }}</textarea>
                        </div>

                        {{-- Jumlah Halaman --}}
                        <div class="mb-6">
                            <label for="total_pages" class="block text-sm font-medium text-gray-700">
                                Jumlah Halaman
                            </label>
                            <input type="number" name="total_pages" id="total_pages"
                                value="{{ old('total_pages', $book->total_pages) }}"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                                        focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-gray-100 text-gray-600 cursor-not-allowed"
                                readonly disabled>

                            <p class="mt-2 text-xs text-gray-500 italic">
                                📘 Jumlah halaman terdeteksi otomatis dari file PDF buku yang diunggah.
                                Anda tidak dapat mengubahnya secara manual.
                            </p>
                        </div>



                        <div class="flex items-center justify-end space-x-3 mt-6">
                            <a href="{{ route('admin.books.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Batal
                            </a>
                            <a href="{{ route('admin.chapters.index', $book->id) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                Kelola Bab / Sub-Bab
                            </a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-md">
                                Update Buku
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
