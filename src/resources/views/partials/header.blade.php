<header
    x-data="{
        // PROPERTI HEADER ORIGINAL
        sidebarToggle: false,
        menuToggle: false,

        // PROPERTI NOTIFIKASI
        nDrop: false,
        nData: [],
        notifying: false, // State Alpine.js untuk badge
        // NOTE: Pastikan rute ini benar dan dapat diakses (status 200)
        markSingleUrl: '{{ route('notifikasi.mark-single-read', ['notification' => 'PLACEHOLDER_ID']) }}'.replace('PLACEHOLDER_ID', ''),
        markAllUrl: '{{ route('notifikasi.mark-all-read') }}', // URL untuk Mark All
        csrfToken: '',
        dropdownOpen: false, // Tambahkan properti untuk dropdown profil

        // METHOD 1: Mark Single
        markAsRead(id) {
            const url = this.markSingleUrl + id;
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                },
            })
            .then(response => {
                if (response.ok) {
                    this.nData = this.nData.map(n =>
                        n.id === id ? { ...n, read_at: new Date().toISOString() } : n
                    );
                    // Hitung ulang status notifying
                    this.notifying = this.nData.some(n => n.read_at === null);
                }
            })
            .catch(error => console.error('Gagal menandai notifikasi tunggal:', error));
            // Lanjutkan jika ada link
        },

        // METHOD 2: Mark All
        markAllAsRead() {
            // Cek apakah ada yang belum dibaca sebelum fetch
            if (this.notifying) {
                // SET STATE DULU agar badge segera hilang (optimistic update)
                this.notifying = false;

                fetch(this.markAllUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    },
                    body: JSON.stringify({})
                })
                .then(response => {
                    if (!response.ok) {
                        this.notifying = true; // Rollback jika gagal
                        throw new Error('Mark-read failed: ' + response.statusText);
                    }
                    // Tandai semua notifikasi di state lokal sebagai sudah dibaca
                    this.nData = this.nData.map(n => ({
                        ...n,
                        read_at: n.read_at === null ? new Date().toISOString() : n.read_at
                    }));
                })
                .catch(error => {
                    console.error('Gagal menandai semua notifikasi:', error);
                    this.notifying = true; // Rollback jika error fetch
                });
            }
        },

        // METHOD KRITIS: Untuk inisialisasi CSRF dan Fetch data
        initCsrf() {
            const meta = document.querySelector('meta[name=\'csrf-token\']');
            this.csrfToken = meta ? meta.content : '';
        },

        // PANGGILAN X-INIT DI FUNCTION init()
        init() {
            this.initCsrf(); // Panggil CSRF dulu

            // Panggil data notifikasi
            fetch('{{ route('notifikasi.index') }}')
                // Pastikan struktur JSON dari controller sesuai
                .then(response => response.ok ? response.json() : Promise.reject('Fetch failed: ' + response.statusText))
                .then(data => {
                    // Cek struktur data: menggunakan `notifications`, `nData`, atau langsung array
                    const notifications = data.notifications || data.nData || [];
                    this.nData = notifications;

                    // Set status notifying berdasarkan data yang baru dimuat
                    this.notifying = notifications.some(n => n.read_at === null);

                    console.log('Notif Loaded:', this.nData.length, 'Unread Check:', this.notifying);
                })
                .catch(error => console.error('Error fetching notifications:', error));
        }
        // <<< FIX: Tidak ada koma setelah method 'init' jika ini adalah yang terakhir!
    }"
    x-init="init()"
    class="sticky top-0 z-50 flex w-full border-gray-200 lg:border-b dark:border-gray-800 bg-white dark:bg-gray-900"
>
    <div class="flex grow items-center justify-between px-4 py-3 md:px-6 2xl:px-11">

        {{-- TEKS SINEGRITAS, SOLIDITAS, KUALITAS --}}
        <div class="hidden lg:block">
            <h1 class="text-2xl font-serif italic font-semibold text-green-700 dark:text-green-500">
                Sinegritas, Soliditas, dan Kualitas
            </h1>
        </div>
        {{-- AKHIR TEKS HEADER --}}

        <div class="flex flex-col items-center justify-between lg:flex-row lg:px-6">
            <div
                :class="menuToggle ? 'flex' : 'hidden'"
                class="shadow-theme-md w-full items-center justify-between gap-4 px-5 py-4 lg:flex lg:justify-end lg:px-0 lg:shadow-none"
            >
                {{-- PERUBAHAN UTAMA: gap-4 untuk jarak notif dan profil --}}
                <div class="2xsm:gap-3 flex items-center gap-4">

                    {{-- 1. BLOK NOTIFIKASI --}}
                    <div class="relative" @click.outside="nDrop = false">
                        <button
                            class="hover:text-dark-900 relative flex h-11 w-11 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
                            @click.prevent="
                                nDrop = !nDrop;
                                if (nDrop) {
                                    markAllAsRead();
                                }
                            "
                        >
                            {{-- badge orange - Menggunakan properti notifying Alpine.js --}}
                            <span
                                x-show="notifying"
                                id="notif-badge"
                                class="absolute top-0.5 right-0 z-1 h-2 w-2 rounded-full bg-orange-400"
                                x-cloak
                            >
                                <span class="absolute -z-1 inline-flex h-full w-full animate-ping rounded-full bg-orange-400 opacity-75"></span>
                            </span>

                            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M10.75 2.29248C10.75 1.87827 10.4143 1.54248 10 1.54248C9.58583 1.54248 9.25004 1.87827 9.25004 2.29248V2.83613C6.08266 3.20733 3.62504 5.9004 3.62504 9.16748V14.4591H3.33337C2.91916 14.4591 2.58337 14.7949 2.58337 15.2091C2.58337 15.6234 2.91916 15.9591 3.33337 15.9591H4.37504H15.625H16.6667C17.0809 15.9591 17.4167 15.6234 17.4167 15.2091C17.4167 14.7949 17.0809 14.4591 16.6667 14.4591H16.375V9.16748C16.375 5.9004 13.9174 3.20733 10.75 2.83613V2.29248ZM14.875 14.4591V9.16748C14.875 6.47509 12.6924 4.29248 10 4.29248C7.30765 4.29248 5.12504 6.47509 5.12504 9.16748V14.4591H14.875ZM8.00004 17.7085C8.00004 18.1228 8.33583 18.4585 8.75004 18.4585H11.25C11.6643 18.4585 12 18.1228 12 17.7085C12 17.2943 11.6643 16.9585 11.25 16.9585H8.75004C8.33583 16.9585 8.00004 17.2943 8.00004 17.7085Z"/>
                            </svg>
                        </button>

                        {{-- dropdown notifikasi --}}
                        <div
                            x-show="nDrop"
                            x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95"
                            class="absolute right-0 mt-3 w-80 rounded-lg bg-white shadow-xl ring-1 ring-black ring-opacity-5 dark:bg-gray-800"
                        >
                            <div class="p-1 text-xs text-center border-b border-gray-200 dark:border-gray-700">
                                <p class="text-gray-900 dark:text-white">Notifikasi (<span x-text="nData.filter(n => n.read_at === null).length"></span>)</p>
                            </div>

                            <div class="py-1 divide-y divide-gray-100 dark:divide-gray-700 max-h-96 overflow-y-auto">
                                <template x-for="n in (nData || [])" :key="n.id">
                                    <a
                                        :href="n.data.link || 'javascript:void(0)'"
                                        @click.prevent="markAsRead(n.id); if (n.data.link) window.location.href = n.data.link;"
                                        :class="{'bg-gray-50 dark:bg-gray-700': n.read_at === null}"
                                        class="block px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer"
                                    >
                                        <div class="flex items-start">
                                            <img
                                                :src="n.data.user_image || '{{ asset('images/default.jpg') }}'"
                                                class="w-8 h-8 rounded-full mr-3 object-cover"
                                            >
                                            <div class="flex-1 overflow-hidden">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white"
                                                   x-text="n.data.sender || 'System'"></p>
                                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1"
                                                   x-html="n.data.message"></p>
                                                <span class="text-[10px] text-gray-400"
                                                   x-text="n.created_at"></span>
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
                    {{-- END BLOK NOTIFIKASI --}}

                    {{-- 2. BLOK PROFIL PENGGUNA --}}
                    @php
                        // Ambil data user dari Auth
                        $user = Auth::user();
                        // Fallback untuk foto jika null
                        $userFoto = $user->foto ? asset('storage/' . $user->foto) : asset('images/default.jpg');
                    @endphp

                    <div
                        class="relative"
                        @click.outside="dropdownOpen = false"
                    >
                        <a
                            class="flex items-center text-gray-700 dark:text-gray-400"
                            href="#"
                            @click.prevent="dropdownOpen = ! dropdownOpen"
                        >
                            <img
                                src="{{ $userFoto }}"
                                alt="User"
                                class="w-8 h-8 rounded-full mr-3 object-cover"
                            >

                            <span class="text-theme-sm mr-1 hidden font-medium lg:block text-gray-700 dark:text-gray-200">
                                {{ $user->nama_lengkap }}
                            </span>

                            <svg
                                :class="dropdownOpen && 'rotate-180'"
                                class="stroke-gray-700 transition-transform duration-300 dark:stroke-gray-400 hidden lg:block"
                                width="18"
                                height="20"
                                viewBox="0 0 18 20"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M4.3125 8.65625L9 13.3437L13.6875 8.65625"
                                    stroke=""
                                    stroke-width="1.5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </a>

                        <div
                            x-show="dropdownOpen"
                            x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95"
                            class="absolute right-0 z-50 mt-2 w-60 rounded-lg bg-white shadow-xl ring-1 ring-black ring-opacity-5 dark:bg-gray-800"
                        >
                            {{-- Edit Profile --}}
                            <ul class="flex flex-col gap-1 border-b border-gray-200 pt-4 pb-3 dark:border-gray-800">
                                <li>
                                    <a
                                        href="{{ route('profile.show') }}"
                                        class="group text-theme-sm flex items-center gap-3 rounded-lg px-3 py-2 font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300"
                                    >
                                        <svg class="w-6 h-6 flex-shrink-0 text-gray-500 group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.964 0a12.903 12.903 0 01-11.964 0M12 7.5a3.75 3.75 0 100 7.5 3.75 3.75 0 000-7.5z" />
                                        </svg>
                                        Edit profile
                                    </a>
                                </li>
                            </ul>

                            {{-- Sign Out --}}
                            <ul class="flex flex-col gap-1 py-3">
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="group text-theme-sm flex w-full items-center gap-3 rounded-lg px-3 py-2 font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300"
                                        >
                                            <svg class="w-6 h-6 flex-shrink-0 text-gray-500 group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-2.25a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 0011.25 21h2.25a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h7.5" />
                                            </svg>
                                            Sign out
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                    {{-- END BLOK PROFIL PENGGUNA --}}
                </div>
            </div>
        </div>
    </div>
</header>
