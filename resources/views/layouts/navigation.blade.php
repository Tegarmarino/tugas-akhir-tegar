<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                {{-- Logo --}}
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        {{-- ✅ IKON BARU DARI FONT AWESOME --}}
                        <svg class="h-9 w-auto text-indigo-600" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                            <path d="M480 576L192 576C139 576 96 533 96 480L96 160C96 107 139 64 192 64L496 64C522.5 64 544 85.5 544 112L544 400C544 420.9 530.6 438.7 512 445.3L512 512C529.7 512 544 526.3 544 544C544 561.7 529.7 576 512 576L480 576zM192 448C174.3 448 160 462.3 160 480C160 497.7 174.3 512 192 512L448 512L448 448L192 448zM224 216C224 229.3 234.7 240 248 240L424 240C437.3 240 448 229.3 448 216C448 202.7 437.3 192 424 192L248 192C234.7 192 224 202.7 224 216zM248 288C234.7 288 224 298.7 224 312C224 325.3 234.7 336 248 336L424 336C437.3 336 448 325.3 448 312C448 298.7 437.3 288 424 288L248 288z"/>
                        </svg>
                    </a>
                </div>

                {{-- Desktop Navigation Links (Redesigned) --}}
                <div class="hidden space-x-4 sm:ms-10 sm:flex">

                    {{-- Style link "Pill" minimalis (bukan x-nav-link) --}}
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center px-3 py-2 text-sm font-medium transition rounded-md
                              {{ request()->routeIs('dashboard')
                                 ? 'bg-indigo-50 text-indigo-700'
                                 : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                        {{ __('Dashboard') }}
                    </a>

                    <a href="{{ route('books.index') }}"
                       class="inline-flex items-center px-3 py-2 text-sm font-medium transition rounded-md
                              {{ request()->routeIs('books.index') || request()->routeIs('books.show')
                                 ? 'bg-indigo-50 text-indigo-700'
                                 : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                        {{ __('Katalog Buku') }}
                    </a>

                    <a href="{{ route('favorites.index') }}"
                       class="inline-flex items-center px-3 py-2 text-sm font-medium transition rounded-md
                              {{ request()->routeIs('favorites.index')
                                 ? 'bg-indigo-50 text-indigo-700'
                                 : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                        {{ __('Favorit Saya') }}
                    </a>

                    {{-- ✅ LINK BANTUAN (BARU) --}}
                    <a href="{{ route('help.index') }}"
                       class="inline-flex items-center px-3 py-2 text-sm font-medium transition rounded-md
                              {{ request()->routeIs('help.index')
                                 ? 'bg-indigo-50 text-indigo-700'
                                 : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.732-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                        </svg>
                        {{ __('Bantuan') }}
                    </a>

                    {{-- ADMIN NAVIGATION LINKS --}}
                    @if(Auth::check() && Auth::user()->role == 'admin')
                        <div class="border-l border-gray-200 h-6 my-auto"></div> {{-- Divider --}}

                        <a href="{{ route('admin.dashboard') }}"
                           class="inline-flex items-center px-3 py-2 text-sm font-medium transition rounded-md
                                  {{ request()->routeIs('admin.dashboard')
                                     ? 'bg-red-50 text-red-700'
                                     : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                            Admin
                        </a>
                        <a href="{{ route('admin.books.index') }}"
                           class="inline-flex items-center px-3 py-2 text-sm font-medium transition rounded-md
                                  {{ request()->routeIs('admin.books.*')
                                     ? 'bg-red-50 text-red-700'
                                     : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                            Manajemen Buku
                        </a>
                        <a href="{{ route('admin.questions.index') }}"
                           class="inline-flex items-center px-3 py-2 text-sm font-medium transition rounded-md
                                  {{ request()->routeIs('admin.questions.*')
                                     ? 'bg-red-50 text-red-700'
                                     : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                            Bank Soal
                        </a>
                    @endif
                </div>
            </div>

            {{-- Dropdown User Profile (Kembali ke style light) --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Mobile Burger Button (Kembali ke style light) --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Responsive Navigation Menu (Mobile) (Redesigned) --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="space-y-1 px-2 py-3">
            <a href="{{ route('dashboard') }}"
               class="block rounded-md px-3 py-2 text-base font-medium transition
                      {{ request()->routeIs('dashboard')
                         ? 'bg-indigo-50 text-indigo-700'
                         : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                {{ __('Dashboard') }}
            </a>
            <a href="{{ route('books.index') }}"
               class="block rounded-md px-3 py-2 text-base font-medium transition
                      {{ request()->routeIs('books.index') || request()->routeIs('books.show')
                         ? 'bg-indigo-50 text-indigo-700'
                         : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                {{ __('Katalog Buku') }}
            </a>
            <a href="{{ route('favorites.index') }}"
               class="block rounded-md px-3 py-2 text-base font-medium transition
                      {{ request()->routeIs('favorites.index')
                         ? 'bg-indigo-50 text-indigo-700'
                         : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                {{ __('Favorit Saya') }}
            </a>
            <a href="{{ route('help.index') }}"
               class="block rounded-md px-3 py-2 text-base font-medium transition
                      {{ request()->routeIs('help.index')
                         ? 'bg-indigo-50 text-indigo-700'
                         : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                {{ __('Bantuan') }}
            </a>
        </div>

        {{-- ADMIN RESPONSIVE LINKS --}}
        @if(Auth::check() && Auth::user()->role == 'admin')
            <div class="border-t border-gray-200 pt-3 mt-3 space-y-1 px-2">
                 <a href="{{ route('admin.dashboard') }}"
                    class="block rounded-md px-3 py-2 text-base font-medium transition
                           {{ request()->routeIs('admin.dashboard')
                              ? 'bg-red-50 text-red-700'
                              : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                    Admin Dashboard
                </a>
                <a href="{{ route('admin.books.index') }}"
                   class="block rounded-md px-3 py-2 text-base font-medium transition
                          {{ request()->routeIs('admin.books.*')
                             ? 'bg-red-50 text-red-700'
                             : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                    Manajemen Buku
                </a>
                <a href="{{ route('admin.questions.index') }}"
                   class="block rounded-md px-3 py-2 text-base font-medium transition
                          {{ request()->routeIs('admin.questions.*')
                             ? 'bg-red-50 text-red-700'
                             : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                    Bank Soal
                </a>
            </div>
        @endif

        {{-- Responsive Settings Options (User) --}}
        <div class="border-t border-gray-200 pb-3 pt-4">
            <div class="flex items-center px-4">
                <div class="shrink-0">
                    {{-- Ganti dengan avatar user jika ada --}}
                    <svg class="h-10 w-10 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ms-3">
                    <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <div class="mt-3 space-y-1 px-2">
                <a href="{{ route('profile.edit') }}"
                   class="block rounded-md px-3 py-2 text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 transition">
                    {{ __('Profile') }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();"
                            class="block rounded-md px-3 py-2 text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 transition">
                        {{ __('Log Out') }}
                    </a>
                </form>
            </div>
        </div>
    </div>
</nav>
