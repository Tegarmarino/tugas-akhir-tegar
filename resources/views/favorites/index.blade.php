<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buku Favorit Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($favoriteBooks->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <p class="text-gray-600">Anda belum memiliki buku favorit.</p>
                    <a href="{{ route('books.index') }}" class="mt-4 inline-block text-blue-500 hover:underline">Jelajahi Katalog Buku</a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach ($favoriteBooks as $book)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            @if($book->cover_image_path)
                             <a href="{{ route('books.show', $book) }}">
                                <img src="{{ Storage::url($book->cover_image_path) }}" alt="{{ $book->title }}" class="w-full h-64 object-cover">
                             </a>
                            @else
                             <a href="{{ route('books.show', $book) }}">
                                <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-500">No Cover</span>
                                </div>
                             </a>
                            @endif
                            <div class="p-6">
                                <h3 class="font-semibold text-lg text-gray-900 truncate">
                                    <a href="{{ route('books.show', $book) }}" class="hover:underline">{{ $book->title }}</a>
                                </h3>
                                <p class="text-sm text-gray-600 mt-1">{{ $book->author ?? 'Penulis tidak diketahui' }}</p>
                                <div class="mt-4 flex justify-between items-center">
                                    <a href="{{ route('books.read', $book) }}" class="text-sm text-blue-500 hover:text-blue-700 font-medium">
                                        Baca Buku
                                    </a>
                                    {{-- Tombol Hapus Favorit jika diperlukan --}}
                                    <form action="{{ route('books.favorite', $book) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs text-red-500 hover:text-red-700">Hapus Favorit</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-8">
                    {{ $favoriteBooks->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
