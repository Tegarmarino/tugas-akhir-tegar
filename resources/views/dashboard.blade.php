<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("Selamat datang kembali!") }}
                    <p class="mt-4">Jelajahi <a href="{{ route('books.index') }}" class="text-blue-500 hover:underline">katalog buku kami</a> atau lihat <a href="{{ route('favorites.index') }}" class="text-blue-500 hover:underline">buku favorit Anda</a>.</p>
                    {{-- Tampilkan beberapa buku terbaru atau rekomendasi di sini --}}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
