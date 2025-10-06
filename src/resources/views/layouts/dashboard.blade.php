<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"
    />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />

    <title>{{ config('app.name', 'Laravel') }} - @yield('title')</title>

    {{-- CSS eksternal --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    {{-- Vite untuk Tailwind & JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>

  <body
    x-data="{
        page: 'ecommerce',
        loaded: true,
        darkMode: JSON.parse(localStorage.getItem('darkMode')) || false,
        stickyMenu: false,
        sidebarToggle: false,
        scrollTop: false
    }"
    x-init="$watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))"
    :class="{'dark bg-gray-900': darkMode === true}"
  >

    <!-- ============================================== -->
    <!-- NOTIFIKASI FLASH DARI SESSION (Error, Success, dan Status) -->
    <!-- ============================================== -->

    {{-- Pesan Error --}}
    @if (session('error'))
      <div
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 7000)"
        x-show="show"
        x-transition
        class="fixed top-4 right-4 z-[1000] p-4 rounded-xl shadow-lg bg-red-600 text-white"
        role="alert"
      >
        <div class="flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" viewBox="0 0 24 24" fill="currentColor">
            <path fill-rule="evenodd"
              d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM12.75 6a.75.75 0 00-1.5 0v6a.75.75 0 001.5 0V6zM12 16.5a.75.75 0 100-1.5.75.75 0 000 1.5z"
              clip-rule="evenodd" />
          </svg>
          <span class="font-medium">Akses Ditolak:</span> {{ session('error') }}
        </div>
      </div>
    @endif

    {{-- Pesan Sukses --}}
    @if (session('success') || session('status'))
      <div
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 5000)"
        x-show="show"
        x-transition
        class="fixed top-4 right-4 z-[1000] p-4 rounded-xl shadow-lg bg-green-500 text-white"
        role="alert"
      >
        <div class="flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
              clip-rule="evenodd" />
          </svg>
          <span class="font-medium">Informasi:</span> {{ session('success') ?? session('status') }}
        </div>
      </div>
    @endif

    <!-- ============================================== -->

    {{-- Preloader --}}
    @include('partials.preloader')

    <!-- ===== Page Wrapper Start ===== -->
    <div class="flex h-screen overflow-hidden">

      {{-- Sidebar --}}
      @include('partials.sidebar-admin')

      {{-- Content Area --}}
      <div class="relative flex flex-col flex-1 overflow-y-auto">

        {{-- Overlay kecil (untuk mobile sidebar) --}}
        @include('partials.overlay')

        {{-- Header --}}
        <div
          class="relative flex flex-col  transition-all duration-300"
          :class="sidebarToggle ? "
        >
          @include('partials.header')

          {{-- Main Content --}}
          <main class="p-4 md:p-6 2xl:p-10 ">
            @yield('content')
          </main>
        </div>
      </div>
    </div>
    <!-- ===== Page Wrapper End ===== -->

    {{-- Scripts eksternal --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- Inisialisasi Flatpickr --}}
    <script>
      document.addEventListener("DOMContentLoaded", function() {
          flatpickr(".datepicker", {
              dateFormat: "Y-m-d",
              allowInput: true,
          });
      });
    </script>

    {{-- Stack untuk script tambahan tiap halaman --}}
    @stack('scripts')
  </body>
</html>
