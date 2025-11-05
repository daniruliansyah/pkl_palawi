<aside
  :class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : '-translate-x-full'"
  class="sidebar fixed left-0 top-0 z-50 flex h-screen w-[290px] flex-col overflow-y-hidden border-r border-gray-300 bg-white px-5 transition-transform duration-300 ease-linear lg:static lg:translate-x-0"
>
  @php
      use Illuminate\Support\Facades\Auth;

        $user = Auth::user();
        $isSeniorSDM = false;
        $isStaffSDM = false;
        $isGM = false; // <-- Deklarasi variabel baru

        if ($user && $user->jabatanTerbaru && $user->jabatanTerbaru->jabatan) {
            $jabatan = $user->jabatanTerbaru->jabatan->nama_jabatan;

            // --- Pengecekan SDM ---
            $hasSDM = stripos($jabatan, 'sdm') !== false;
            $hasSenior = stripos($jabatan, 'senior') !== false;
            $hasStaff = stripos($jabatan, 'staff') !== false;
            $hasGM = stripos($jabatan, 'general manager') !== false; // <-- Cek 'General Manager'
            // ---------------------

            if ($hasSenior && $hasSDM) $isSeniorSDM = true;
            if ($hasStaff && $hasSDM) $isStaffSDM = true;
            if ($hasGM) $isGM = true; // <-- Set $isGM menjadi true jika nama jabatan mengandung 'General Manager'
        }
  @endphp

  {{-- Header Sidebar --}}
  <div :class="sidebarToggle ? 'justify-center' : 'justify-between'" class="flex items-center gap-2 pt-8 pb-7">
  <a href="{{ route('dashboard') }}" class="flex items-center">
    <span class="logo" :class="sidebarToggle ? 'hidden' : 'block'">
      <img class="w-40 mx-auto translate-x-8 dark:hidden" src="{{ asset('images/logo2.jpg') }}" alt="Logo" />
      <img class="w-40 hidden mx-auto translate-x-8 dark:block" src="./images/logo.jpg" alt="Logo" />
    </span>
    <img :class="sidebarToggle ? 'block' : 'hidden'" class="logo-icon w-8 h-8" src="./images/logo2.jpg" alt="Logo" />
  </a>
</div>

  {{-- Menu Sidebar --}}
  <div class="no-scrollbar flex flex-col overflow-y-auto duration-300 ease-linear">
    <nav x-data="{ selected: '' }">
      <h3 class="mb-4 text-xs uppercase text-gray-400">Menu</h3>
      <ul class="flex flex-col gap-4">

        {{-- Menu Karyawan (Staff & Senior SDM) --}}
        @if ($isStaffSDM || $isSeniorSDM)
          <li>
            <a
              href="{{ route('karyawan.index') }}"
              @click="selected = 'Karyawan'"
              class="menu-item flex items-center gap-3 p-2 rounded hover:bg-green-50 transition-colors"
              :class="selected === 'Karyawan' ? 'bg-green-50 font-semibold text-gray-800' : 'bg-white text-gray-700'"
            >
              <svg class="w-6 h-6 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 12c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5zm0 2c-3.866 0-7 2.015-7 4.5V21h14v-2.5c0-2.485-3.134-4.5-7-4.5z"/>
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'hidden' : 'block'">Karyawan</span>
            </a>
          </li>
        @endif

        {{-- Menu Pengajuan Izin --}}
        <li>
          <a
            href="#"
            @click.prevent="selected = (selected === 'Forms' ? '' : 'Forms')"
            class="menu-item flex items-center gap-3 p-2 rounded hover:bg-green-50 transition-colors"
            :class="selected === 'Forms' ? 'bg-green-50 font-semibold text-gray-800' : 'bg-white text-gray-700'"
          >
            <svg class="w-6 h-6 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
              <path fill-rule="evenodd" clip-rule="evenodd"
                d="M5.5 3.25C4.257 3.25 3.25 4.257 3.25 5.5v13c0 1.243 1.007 2.25 2.25 2.25h13c1.243 0 2.25-1.007 2.25-2.25v-13C20.75 4.257 19.743 3.25 18.5 3.25H5.5ZM6.25 9.714c0-.414.336-.75.75-.75H17a.75.75 0 1 1 0 1.5H7a.75.75 0 0 1-.75-.75Zm0 4.572c0-.414.336-.75.75-.75H17a.75.75 0 1 1 0 1.5H7a.75.75 0 0 1-.75-.75Z" />
            </svg>
            <span class="menu-item-text" :class="sidebarToggle ? 'hidden' : 'block'">Pengajuan Izin</span>
            <svg class="w-4 h-4 ml-auto text-gray-500" :class="selected === 'Forms' ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
          </a>
          <ul :class="selected === 'Forms' ? 'block' : 'hidden'" class="pl-9 mt-2 flex flex-col gap-1">
            @if (!$isGM) {{-- <-- TAMBAHKAN BARIS INI --}}
            <li><a href="{{ route('cuti.index') }}" class="p-2 rounded hover:bg-green-50 transition-colors text-gray-700 hover:text-gray-900 block">Pengajuan Izin</a></li>
            @endif {{-- <-- TAMBAHKAN BARIS INI --}}
            <li><a href="{{ route('approvals.index') }}" class="p-2 rounded hover:bg-green-50 transition-colors text-gray-700 hover:text-gray-900 block">Daftar Surat Cuti</a></li>
          </ul>
        </li>

        {{-- Menu Peringatan (Hanya Senior SDM) --}}
        @if ($isSeniorSDM || $isGM)
          <li>
            <a
              href="#"
              @click.prevent="selected = (selected === 'Tables' ? '' : 'Tables')"
              class="menu-item flex items-center gap-3 p-2 rounded hover:bg-green-50 transition-colors"
              :class="selected === 'Tables' ? 'bg-green-50 font-semibold text-gray-800' : 'bg-white text-gray-700'"
            >
              <svg class="w-6 h-6 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2.25 1.75 21.75h20.5L12 2.25ZM12 4.75l8.125 15.5H3.875L12 4.75Zm-1 5.5h2v5h-2v-5Zm0 6.5h2v2h-2v-2Z" />
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'hidden' : 'block'">Peringatan</span>
              <svg class="w-4 h-4 ml-auto text-gray-500" :class="selected === 'Tables' ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
              </svg>
            </a>
            <ul :class="selected === 'Tables' ? 'block' : 'hidden'" class="pl-9 mt-2 flex flex-col gap-1">
              <li><a href="{{ route('sp.index') }}" class="p-2 rounded hover:bg-green-50 transition-colors text-gray-700 hover:text-gray-900 block">Buat Peringatan</a></li>
              <li><a href="{{ route('sp.approvals.index') }}" class="p-2 rounded hover:bg-green-50 transition-colors text-gray-700 hover:text-gray-900 block">Daftar SP</a></li>
            </ul>
          </li>
        @endif

        {{-- Menu Perjalanan Dinas --}}
        <li>
          <a
            href="#"
            @click.prevent="selected = (selected === 'Pages' ? '' : 'Pages')"
            class="menu-item flex items-center gap-3 p-2 rounded hover:bg-green-50 transition-colors"
            :class="selected === 'Pages' ? 'bg-green-50 font-semibold text-gray-800' : 'bg-white text-gray-700'"
          >
            <svg class="w-6 h-6 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
              <path d="M14 6V5a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v1H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6h-6Z" />
            </svg>
            <span class="menu-item-text" :class="sidebarToggle ? 'hidden' : 'block'">Perjalanan Dinas</span>
            <svg class="w-4 h-4 ml-auto text-gray-500" :class="selected === 'Pages' ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
          </a>
          <ul :class="selected === 'Pages' ? 'block' : 'hidden'" class="pl-9 mt-2 flex flex-col gap-1">
            <li><a href="{{ route('sppd.index') }}" class="p-2 rounded hover:bg-green-50 transition-colors text-gray-700 hover:text-gray-900 block">Pengajuan SPPD</a></li>
            <li><a href="{{ route('sppd.approvals.index') }}" class="p-2 rounded hover:bg-green-50 transition-colors text-gray-700 hover:text-gray-900 block">Daftar SPPD</a></li>
          </ul>
        </li>

        <li>
          <a
            href="{{ route('calendar.index') }}"
            class="menu-item flex items-center gap-3 p-2 rounded hover:bg-green-50 transition-colors bg-white text-gray-700"
          >
            <svg class="w-6 h-6 flex-shrink-0 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>

            <span class="menu-item-text" :class="sidebarToggle ? 'hidden' : 'block'">Calendar</span>
          </a>
        </li>
      </ul>
    </nav>
  </div>
</aside>
