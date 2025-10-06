<x-app-layout> {{-- Atau x-admin-layout jika Anda buat layout terpisah --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("Ini adalah dashboard admin.") }}
                    {{-- Statistik: Jumlah Buku, Jumlah User, dll. --}}
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="bg-blue-100 p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-blue-800">Total Buku</h3>
                            <p class="text-3xl font-bold text-blue-600">{{ \App\Models\Book::count() }}</p>
                        </div>
                        <div class="bg-green-100 p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-green-800">Total User</h3>
                            <p class="text-3xl font-bold text-green-600">{{ \App\Models\User::count() }}</p>
                        </div>
                        {{-- Tambahkan card statistik lainnya --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
