<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-t">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Asisten Baca AI') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen bg-gray-50">

        {{-- =======================
             HEADER & NAVIGASI
        ======================== --}}
        <header class="absolute top-0 left-0 right-0 z-10">
            <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex justify-between items-center">
                    {{-- Logo Project Kamu --}}
                    <div class="flex items-center">
                       {{-- ✅ IKON BARU DARI FONT AWESOME --}}
                        <svg class="h-9 w-auto text-indigo-600" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                            <path d="M480 576L192 576C139 576 96 533 96 480L96 160C96 107 139 64 192 64L496 64C522.5 64 544 85.5 544 112L544 400C544 420.9 530.6 438.7 512 445.3L512 512C529.7 512 544 526.3 544 544C544 561.7 529.7 576 512 576L480 576zM192 448C174.3 448 160 462.3 160 480C160 497.7 174.3 512 192 512L448 512L448 448L192 448zM224 216C224 229.3 234.7 240 248 240L424 240C437.3 240 448 229.3 448 216C448 202.7 437.3 192 424 192L248 192C234.7 192 224 202.7 224 216zM248 288C234.7 288 224 298.7 224 312C224 325.3 234.7 336 248 336L424 336C437.3 336 448 325.3 448 312C448 298.7 437.3 288 424 288L248 288z"/>
                        </svg>
                        <span class="text-xl font-bold text-gray-800 ml-2">Asisten Baca AI</span>
                    </div>

                    {{-- Tombol Login/Register --}}
                    <div class="flex items-center space-x-4">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition">Log in</a>

                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 transition">
                                        Register
                                    </a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
            </nav>
        </header>

        {{-- =======================
             HERO SECTION
        ======================== --}}
        <main>
            <div class="relative pt-16 sm:pt-24 lg:pt-32">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl md:text-6xl">
                        <span class="block xl:inline">Tingkatkan Pemahaman</span>
                        <span class="block text-indigo-600 xl:inline">Membaca Anda.</span>
                    </h1>
                    <p class="mt-3 max-w-md mx-auto text-base text-gray-600 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                        Ubah cara Anda belajar. Gunakan Asisten Baca AI untuk memahami dokumen akademik,
                        bertanya secara kontekstual, dan melacak progres belajar Anda.
                    </p>
                    <div class="mt-5 max-w-md mx-auto sm:flex sm:justify-center md:mt-8">
                        <div class="rounded-md shadow">
                            <a href="{{ route('register') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10 transition">
                                Mulai Belajar (Gratis)
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        {{-- =======================
             FITUR-FITUR
        ======================== --}}
        <div class="bg-white mt-16 sm:mt-24 lg:mt-32 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-base font-semibold text-indigo-600 tracking-wide uppercase">Fitur Kami</h2>
                    <p class="mt-2 text-3xl font-bold text-gray-900 sm:text-4xl">
                        Dirancang untuk Mahasiswa
                    </p>
                </div>

                <div class="mt-12 grid gap-10 md:grid-cols-3">
                    {{-- Fitur 1 --}}
                    <div class="flex flex-col items-center text-center p-6">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-50 text-indigo-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="mt-5 text-lg font-medium text-gray-900">Tanya Jawab AI Kontekstual</h3>
                        <p class="mt-2 text-base text-gray-600">
                            Dapatkan jawaban instan dari AI (Gemini) berdasarkan halaman atau bab yang sedang Anda baca.
                        </p>
                    </div>
                    {{-- Fitur 2 --}}
                    <div class="flex flex-col items-center text-center p-6">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-50 text-indigo-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="mt-5 text-lg font-medium text-gray-900">Pre-Test & Post-Test</h3>
                        <p class="mt-2 text-base text-gray-600">
                            Ukur pemahaman Anda sebelum (Pre-Test) dan sesudah (Post-Test) membaca dengan kuis terstruktur.
                        </p>
                    </div>
                    {{-- Fitur 3 --}}
                    <div class="flex flex-col items-center text-center p-6">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-50 text-indigo-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="mt-5 text-lg font-medium text-gray-900">Lacak Progres Belajar</h3>
                        <p class="mt-2 text-base text-gray-600">
                            Pantau progres membaca dan riwayat nilai tes Anda melalui dashboard yang intuitif dan personal.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- =======================
             FOOTER
        ======================== --}}
        <footer class="bg-gray-50">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <p class="text-center text-base text-gray-500">
                    &copy; {{ date('Y') }} Asisten Baca AI. Dibuat untuk Tugas Akhir.
                </p>
            </div>
        </footer>
    </div>
</body>
</html>
