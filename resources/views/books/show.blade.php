<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $book->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="md:flex">
                    <div class="md:w-1/3 p-6">
                        @if($book->cover_image_path)
                            <img src="{{ Storage::url($book->cover_image_path) }}" alt="{{ $book->title }}" class="w-full h-auto object-cover rounded-md shadow-lg">
                        @else
                            <div class="w-full h-96 bg-gray-200 flex items-center justify-center rounded-md">
                                <span class="text-gray-500 text-xl">No Cover Available</span>
                            </div>
                        @endif
                    </div>
                    <div class="md:w-2/3 p-6">
                        <h1 class="text-3xl font-bold text-gray-900">{{ $book->title }}</h1>
                        <p class="text-lg text-gray-700 mt-2">Oleh: {{ $book->author ?? 'Penulis tidak diketahui' }}</p>
                        <p class="text-sm text-gray-500 mt-1">Tanggal Terbit: {{ $book->publication_date ? $book->publication_date->format('d F Y') : 'Tidak diketahui' }}</p>

                        <div class="mt-6">
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">Overview</h3>
                            <p class="text-gray-600 leading-relaxed prose">{{ $book->overview ?? 'Overview tidak tersedia.' }}</p>
                        </div>

                        <div class="mt-8 flex space-x-4">
                            <a href="{{ route('books.read', $book) }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition duration-150 ease-in-out">
                                Baca Buku
                            </a>
                            <form id="favoriteForm" action="{{ route('books.favorite', $book) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="px-6 py-3 font-semibold rounded-lg shadow-md transition duration-150 ease-in-out
                                               {{ $isFavorited ? 'bg-red-100 text-red-600 hover:bg-red-200' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                    {{ $isFavorited ? 'Hapus dari Favorit' : 'Tambah ke Favorit' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Anda bisa menambahkan skrip AJAX untuk tombol favorit di sini --}}
</x-app-layout>
