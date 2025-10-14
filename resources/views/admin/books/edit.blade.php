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
                    <form action="{{ route('admin.books.update', $book) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700">Judul Buku <span class="text-red-500">*</span></label>
                            <input type="text" name="title" id="title" value="{{ old('title', $book->title) }}"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('title') border-red-500 @enderror" required>
                            @error('title')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="pdf_file" class="block text-sm font-medium text-gray-700">Ganti File PDF (Opsional)</label>
                            <input type="file" name="pdf_file" id="pdf_file" accept=".pdf"
                                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('pdf_file') border-red-500 @enderror">
                            @if($book->file_path)
                                <p class="mt-1 text-xs text-gray-500">File saat ini: <a href="{{ Storage::url($book->file_path) }}" target="_blank" class="text-blue-500 hover:underline">{{ basename($book->file_path) }}</a></p>
                            @endif
                            @error('pdf_file')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="cover_image" class="block text-sm font-medium text-gray-700">Ganti Gambar Cover (Opsional)</label>
                            <input type="file" name="cover_image" id="cover_image" accept="image/*"
                                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 @error('cover_image') border-red-500 @enderror">
                            @if($book->cover_image_path)
                                <div class="mt-2">
                                    <img src="{{ Storage::url($book->cover_image_path) }}" alt="Current Cover" class="h-24 w-auto rounded object-cover">
                                    <p class="text-xs text-gray-500">Cover saat ini</p>
                                </div>
                            @endif
                            @error('cover_image')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <hr class="my-6">
                        <p class="text-sm text-gray-600 mb-4">Detail berikut digenerate oleh AI. Anda bisa mengubahnya jika perlu.</p>

                        <div class="mb-4">
                            <label for="author" class="block text-sm font-medium text-gray-700">Penulis</label>
                            <input type="text" name="author" id="author" value="{{ old('author', $book->author) }}"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('author') border-red-500 @enderror">
                            @error('author')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="publication_date" class="block text-sm font-medium text-gray-700">Tanggal Terbit</label>
                            <input type="date" name="publication_date" id="publication_date" value="{{ old('publication_date', $book->publication_date ? $book->publication_date->format('Y-m-d') : '') }}"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('publication_date') border-red-500 @enderror">
                            @error('publication_date')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="overview" class="block text-sm font-medium text-gray-700">Overview</label>
                            <textarea name="overview" id="overview" rows="5"
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('overview') border-red-500 @enderror">{{ old('overview', $book->overview) }}</textarea>
                            @error('overview')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('admin.books.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Batal
                            </a>
                            <a href="{{ route('admin.chapters.index', $book->id) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                Kelola Bab / Sub-Bab
                            </a>
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Update Buku
                            </button>
                            <div class="mt-6 flex justify-between items-center">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
