<aside
  :class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : '-translate-x-full'"
  class="sidebar fixed left-0 top-0 z-50 flex h-screen w-[290px] flex-col overflow-y-hidden border-r border-gray-300 bg-white px-5 transition-transform duration-300 ease-linear lg:static lg:translate-x-0"
>

@php
    // Memastikan penggunaan Facade Auth
    use Illuminate\Support\Facades\Auth;
    
    // Mendapatkan user yang sedang login
    // Asumsi: $user sudah dimuat dengan eager loading 'jabatanTerbaru.jabatan'
    $user = Auth::user(); 
    $isSeniorSDM = false;
    $isStaffSDM = false;

    // Asumsi Auth::user() tersedia di Blade
    $user = Auth::user();

    // 1. Cek apakah user dan relasinya valid
    if ($user && $user->jabatanTerbaru && $user->jabatanTerbaru->jabatan) {
        $jabatan = $user->jabatanTerbaru->jabatan->nama_jabatan;

        // Cek apakah jabatan mengandung 'sdm' (ini umum untuk kedua kondisi)
        $hasSDM = (stripos($jabatan, 'sdm') !== false);

        // 2. Tentukan status Senior SDM
        $hasSenior = (stripos($jabatan, 'senior') !== false);
        if ($hasSenior && $hasSDM) {
            $isSeniorSDM = true;
        }

        // 3. Tentukan status Staff SDM
        $hasStaff = (stripos($jabatan, 'staff') !== false);
        if ($hasStaff && $hasSDM) {
            $isStaffSDM = true;
        }
    }
@endphp

  <!-- Logo -->
  <div :class="sidebarToggle ? 'justify-center' : 'justify-between'" class="flex items-center gap-2 pt-8 pb-7">
    <a href="index.html">
      <span class="logo" :class="sidebarToggle ? 'hidden' : ''">
        <img class="dark:hidden" src="./images/logo/b.png" alt="Logo" />
        <img class="hidden dark:block" src="./images/logo/b.png" alt="Logo" />
      </span>
      <img :class="sidebarToggle ? 'lg:block' : 'hidden'" class="logo-icon hidden" src="./images/logo/logo-icon.svg" alt="Logo" />
    </a>
  </div>

  <!-- Menu -->
  <div class="no-scrollbar flex flex-col overflow-y-auto duration-300 ease-linear">
    <nav x-data="{ selected: '' }">
      <h3 class="mb-4 text-xs uppercase text-gray-400">Menu</h3>
      <ul class="flex flex-col gap-4">
        <!-- Karyawan -->
        @if ($isStaffSDM || $isSeniorSDM)
          <li>
            <a
              href="{{ route('karyawan.index') }}"
              @click="selected = 'Karyawan'"
              class="menu-item flex items-center gap-3 p-2 rounded hover:bg-green-50 transition-colors"
              :class="selected === 'Karyawan' ? 'bg-green-50 font-semibold text-gray-800' : 'bg-white text-gray-700'"
            >
              <svg class="w-6 h-6 flex-shrink-0 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 12c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5zm0 2c-3.866 0-7 2.015-7 4.5V21h14v-2.5c0-2.485-3.134-4.5-7-4.5z"/>
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'hidden' : 'block'">Karyawan</span>
            </a>
          </li>
        @endif

        <!-- Pengajuan Izin -->
        <li>
          <a
            href="#"
            @click.prevent="selected = (selected === 'Forms' ? '' : 'Forms')"
            class="menu-item flex items-center gap-3 p-2 rounded hover:bg-green-50 transition-colors"
            :class="selected === 'Forms' ? 'bg-green-50 font-semibold text-gray-800' : 'bg-white text-gray-700'"
          >
            <svg class="w-6 h-6 flex-shrink-0 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H18.5001C19.7427 20.75 20.7501 19.7426 20.7501 18.5V5.5C20.7501 4.25736 19.7427 3.25 18.5001 3.25H5.5ZM4.75 5.5C4.75 5.08579 5.08579 4.75 5.5 4.75H18.5001C18.9143 4.75 19.2501 5.08579 19.2501 5.5V18.5C19.2501 18.9142 18.9143 19.25 18.5001 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V5.5ZM6.25005 9.7143C6.25005 9.30008 6.58583 8.9643 7.00005 8.9643L17 8.96429C17.4143 8.96429 17.75 9.30008 17.75 9.71429C17.75 10.1285 17.4143 10.4643 17 10.4643L7.00005 10.4643C6.58583 10.4643 6.25005 10.1285 6.25005 9.7143ZM6.25005 14.2857C6.25005 13.8715 6.58583 13.5357 7.00005 13.5357H17C17.4143 13.5357 17.75 13.8715 17.75 14.2857C17.75 14.6999 17.4143 15.0357 17 15.0357H7.00005C6.58583 15.0357 6.25005 14.6999 6.25005 14.2857Z"/>
            </svg>
            <span class="menu-item-text" :class="sidebarToggle ? 'hidden' : 'block'">Pengajuan Izin</span>
            <svg class="w-4 h-4 ml-auto text-gray-500" :class="selected === 'Forms' ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
            </svg>
          </a>

          <!-- Dropdown -->
          <ul :class="selected === 'Forms' ? 'block' : 'hidden'" class="pl-9 mt-2 flex flex-col gap-1">
            <li>
              <a href={{ route('cuti.index') }} class="p-2 rounded hover:bg-green-50 transition-colors text-gray-700 hover:text-gray-900 block">Pengajuan Izin</a>
            </li>
            <li>
              <a href="form-elements.html" class="p-2 rounded hover:bg-green-50 transition-colors text-gray-700 hover:text-gray-900 block">Riwayat Surat Cuti</a>
            </li>
          </ul>
        </li>

        <!-- Peringatan -->
      @if ($isSeniorSDM)
        <li>
          <a
            href="#"
            @click.prevent="selected = (selected === 'Tables' ? '' : 'Tables')"
            class="menu-item flex items-center gap-3 p-2 rounded hover:bg-green-50 transition-colors"
            :class="selected === 'Tables' ? 'bg-green-50 font-semibold text-gray-800' : 'bg-white text-gray-700'"
          >
            <svg class="w-6 h-6 flex-shrink-0 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2.25L1.75 21.75H22.25L12 2.25ZM12 4.75L20.125 20.25H3.875L12 4.75ZM11 10.25H13V15.25H11V10.25ZM11 16.75H13V18.75H11V16.75Z"/>
            </svg>
            <span class="menu-item-text" :class="sidebarToggle ? 'hidden' : 'block'">Peringatan</span>
            <svg class="w-4 h-4 ml-auto text-gray-500" :class="selected === 'Tables' ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
            </svg>
          </a>

          <ul :class="selected === 'Tables' ? 'block' : 'hidden'" class="pl-9 mt-2 flex flex-col gap-1">
            <li><a href={{ route('sp.create') }} class="p-2 rounded hover:bg-green-50 transition-colors text-gray-700 hover:text-gray-900 block">Buat Peringatan</a></li>
            <li><a href={{ route('sp.index') }} class="p-2 rounded hover:bg-green-50 transition-colors text-gray-700 hover:text-gray-900 block">Daftar SP</a></li>
          </ul>
        </li>
      @endif

        <!-- Perjalanan Dinas -->
        <li>
          <a
            href="#"
            @click.prevent="selected = (selected === 'Pages' ? '' : 'Pages')"
            class="menu-item flex items-center gap-3 p-2 rounded hover:bg-green-50 transition-colors"
            :class="selected === 'Pages' ? 'bg-green-50 font-semibold text-gray-800' : 'bg-white text-gray-700'"
          >
            <svg class="w-6 h-6 flex-shrink-0 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
              <path d="M14 6V5C14 4.44772 13.5523 4 13 4H11C10.4477 4 10 4.44772 10 5V6M14 6H20V18C20 19.1046 19.1046 20 18 20H6C4.89543 20 4 19.1046 4 18V8C4 6.89543 4.89543 6 6 6H10M14 6H10"/>
            </svg>
            <span class="menu-item-text" :class="sidebarToggle ? 'hidden' : 'block'">Perjalanan Dinas</span>
            <svg class="w-4 h-4 ml-auto text-gray-500" :class="selected === 'Pages' ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
            </svg>
          </a>

          <ul :class="selected === 'Pages' ? 'block' : 'hidden'" class="pl-9 mt-2 flex flex-col gap-1">
            <li><a href={{ route('sppd.index') }} class="p-2 rounded hover:bg-green-50 transition-colors text-gray-700 hover:text-gray-900 block">Pengajuan SPPD</a></li>
            <li><a href="404.html" class="p-2 rounded hover:bg-green-50 transition-colors text-gray-700 hover:text-gray-900 block">Riwayat SPPD</a></li>
          </ul>
        </li>
      </ul>
    </nav>
  </div>
</aside>
