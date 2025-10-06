<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Katalog Buku') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Form Pencarian --}}
            <form method="GET" action="{{ route('books.index') }}" class="mb-6">
                <div class="flex">
                    <input type="text" name="search" placeholder="Cari judul atau penulis..."
                           class="w-full px-4 py-2 border rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           value="{{ request('search') }}">
                    <button type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded-r-md hover:bg-blue-600">
                        Cari
                    </button>
                </div>
            </form>

            @if($books->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <p class="text-gray-600">Tidak ada buku yang ditemukan.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach ($books as $book)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            @if($book->cover_image_path)
                                <a href="{{ route('books.show', $book) }}">
                                <img src="{{ Storage::url($book->cover_image_path) }}" alt="{{ $book->title }}" class="w-full h-64 object-cover">
                                </a>
                            @else
                                <a href="{{ route('books.show', $book ) }}">
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
                                <div class="mt-4">
                                    <a href="{{ route('books.show', $book) }}" class="text-blue-500 hover:text-blue-700 font-medium">
                                        Lihat Detail &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-8">
                    {{ $books->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
