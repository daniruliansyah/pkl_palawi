<header
    x-data="{
        sidebarToggle: false,
        menuToggle: false,
        nDrop: false,
        nData: [],
        notifying: false,
        csrfToken: '',
        initCsrf() {
            const meta = document.querySelector('meta[name=\'csrf-token\']');
            this.csrfToken = meta ? meta.content : '';
        },
        init() { this.initCsrf(); }
    }"
    class="sticky top-0 z-50 flex w-full border-b border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"
>
    <div class="flex grow flex-col lg:flex-row items-center justify-between px-4 lg:px-6 py-2">
        
        {{-- KIRI: LOGO / MENU TOGGLE --}}
        <div class="flex w-full lg:w-auto items-center justify-between">
            <a href="/" class="text-lg font-bold text-gray-800 dark:text-white">
                LOGO
            </a>

            {{-- tombol hamburger muncul di mobile --}}
            <button
                @click="menuToggle = !menuToggle"
                class="lg:hidden p-2 rounded-md text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
            >
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        {{-- KANAN: NOTIFIKASI & PROFIL --}}
        <div
            :class="menuToggle ? 'flex' : 'hidden'"
            class="w-full lg:flex lg:w-auto flex-col lg:flex-row items-center gap-4 mt-2 lg:mt-0"
        >
            {{-- BLOK NOTIFIKASI --}}
            <div class="relative">
                <button
                    class="relative flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 hover:bg-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400"
                    @click.prevent="nDrop = !nDrop"
                >
                    {{-- badge --}}
                    <template x-if="notifying">
                        <span id="notif-badge"
                            class="absolute top-1 right-1 h-2 w-2 rounded-full bg-orange-400">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-orange-400 opacity-75"></span>
                        </span>
                    </template>

                    {{-- icon lonceng --}}
                    <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M10.75 2.3c0-.4-.34-.8-.75-.8s-.75.4-.75.8v.5C6.08 3.2 3.62 5.9 3.62 9.2v5.2h-.3c-.4 0-.75.3-.75.7s.35.8.75.8h12.7c.4 0 .75-.3.75-.8s-.35-.7-.75-.7h-.3V9.2c0-3.3-2.46-6-5.63-6.4v-.5zM15 14.4V9.2c0-2.7-2.2-4.9-5-4.9s-5 2.2-5 4.9v5.2h10zM8 17.7c0 .4.34.7.75.7h2.5c.4 0 .75-.3.75-.7s-.35-.8-.75-.8h-2.5c-.41 0-.75.4-.75.8z"/>
                    </svg>
                </button>

                {{-- dropdown --}}
                <div
                    x-show="nDrop"
                    x-cloak
                    x-transition
                    class="absolute right-0 mt-3 w-80 max-w-[90vw] rounded-lg bg-white shadow-xl ring-1 ring-black ring-opacity-5 dark:bg-gray-800"
                >
                    <div class="p-2 text-xs text-center border-b border-gray-200 dark:border-gray-700">
                        <p class="text-gray-900 dark:text-white">Notifikasi</p>
                    </div>

                    <div class="max-h-72 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700">
                        <template x-for="n in (nData || [])" :key="n.id">
                            <a href="javascript:void(0)"
                               class="block px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <div class="flex items-start">
                                    <img :src="n.data.user_image || '{{ asset('images/default.jpg') }}'"
                                         class="w-8 h-8 rounded-full mr-3 object-cover">
                                    <div class="flex-1 overflow-hidden">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white"
                                           x-text="n.data.sender || 'System'"></p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1"
                                           x-html="n.data.message"></p>
                                        <span class="text-[10px] text-gray-400" x-text="n.created_at"></span>
                                    </div>
                                </div>
                            </a>
                        </template>
                        <template x-if="(nData || []).length === 0">
                            <p class="p-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                Tidak ada notifikasi baru.
                            </p>
                        </template>
                    </div>
                </div>
            </div>

            {{-- PROFIL USER --}}
            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                <button @click="open = !open" class="flex items-center gap-2">
                    <img src="{{ asset('storage/' . Auth::user()->foto) }}"
                         class="w-8 h-8 rounded-full object-cover">
                    <span class="hidden lg:block text-gray-700 dark:text-gray-300">
                        {{ Auth::user()->nama_lengkap }}
                    </span>
                </button>

                <div
                    x-show="open"
                    x-transition
                    class="absolute right-0 mt-2 w-56 max-w-[90vw] rounded-lg bg-white shadow-xl ring-1 ring-black ring-opacity-5 dark:bg-gray-800"
                >
                    <a href="{{ route('profile.show') }}"
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                        Edit Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>