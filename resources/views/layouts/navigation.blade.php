<nav x-data="{ open: false }" class="bg-brand-red shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                        <x-application-logo class="block h-8 w-auto fill-current text-white" />
                        <span class="text-white font-extrabold text-lg hidden sm:block">LMS Matematika</span>
                    </a>
                </div>
                <div class="hidden space-x-1 sm:-my-px sm:ms-8 sm:flex">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 pt-1 text-sm font-semibold leading-5 transition duration-150 ease-in-out {{ request()->routeIs('dashboard', 'beranda') && ! request()->routeIs('guru.*', 'siswa.*') ? 'text-white border-b-2 border-white' : 'text-red-100 hover:text-white hover:border-b-2 hover:border-red-200' }}">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        {{ __('Dashboard') }}
                    </a>
                    @auth
                        @if(Auth::user()->role === 'guru')
                        <a href="{{ route('guru.kelas.index') }}" class="inline-flex items-center px-4 pt-1 text-sm font-semibold leading-5 transition duration-150 ease-in-out {{ request()->routeIs('guru.*') ? 'text-white border-b-2 border-white' : 'text-red-100 hover:text-white hover:border-b-2 hover:border-red-200' }}">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            Kelas
                        </a>
                        @endif
                        @if(Auth::user()->role === 'siswa')
                        <a href="{{ route('siswa.ujian.index') }}" class="inline-flex items-center px-4 pt-1 text-sm font-semibold leading-5 transition duration-150 ease-in-out {{ request()->routeIs('siswa.ujian.index') || request()->routeIs('siswa.gabung-kelas.*') || request()->routeIs('siswa.ujian.kelas.tampil') ? 'text-white border-b-2 border-white' : 'text-red-100 hover:text-white hover:border-b-2 hover:border-red-200' }}">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            Kelas
                        </a>
                        <a href="{{ route('siswa.ujian.riwayat') }}" class="inline-flex items-center px-4 pt-1 text-sm font-semibold leading-5 transition duration-150 ease-in-out {{ request()->routeIs('siswa.ujian.riwayat') ? 'text-white border-b-2 border-white' : 'text-red-100 hover:text-white hover:border-b-2 hover:border-red-200' }}">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Riwayat Ujian
                        </a>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-white/20 text-sm leading-4 font-semibold rounded-lg text-white bg-white/10 hover:bg-white/20 focus:outline-none transition ease-in-out duration-150">
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
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-red-100 hover:text-white hover:bg-brand-red-dark focus:outline-none focus:bg-brand-red-dark focus:text-white transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-brand-red-dark border-t border-red-700">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard', 'beranda') && ! request()->routeIs('guru.*', 'siswa.*')" class="text-white hover:text-red-100">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            @auth
                @if(Auth::user()->role === 'guru')
                    <x-responsive-nav-link :href="route('guru.kelas.index')" :active="request()->routeIs('guru.*')" class="text-white hover:text-red-100">
                        Kelas
                    </x-responsive-nav-link>
                @endif
                @if(Auth::user()->role === 'siswa')
                    <x-responsive-nav-link :href="route('siswa.ujian.index')" :active="request()->routeIs('siswa.ujian.index') || request()->routeIs('siswa.gabung-kelas.*') || request()->routeIs('siswa.ujian.kelas.tampil')" class="text-white hover:text-red-100">
                        Kelas
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('siswa.ujian.riwayat')" :active="request()->routeIs('siswa.ujian.riwayat')" class="text-white hover:text-red-100">
                        Riwayat Ujian
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>
        <div class="pt-4 pb-1 border-t border-red-700">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-red-200">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-red-100 hover:text-white">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" class="text-red-100 hover:text-white"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
