<aside
  :class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : '-translate-x-full'"
  class="sidebar fixed left-0 top-0 z-50 flex h-screen w-[290px] flex-col overflow-y-hidden border-r border-gray-200 bg-white px-5 transition-transform duration-300 ease-linear dark:border-gray-800 dark:bg-black lg:static lg:translate-x-0"
>
  <div
    :class="sidebarToggle ? 'justify-center' : 'justify-between'"
    class="flex items-center gap-2 pt-8 pb-7"
  >
    <a href="index.html">
      <span class="logo" :class="sidebarToggle ? 'hidden' : ''">
        <img class="dark:hidden" src="./images/logo/b.png" alt="Logo" />
        <img class="hidden dark:block" src="./images/logo/b.png" alt="Logo" />
      </span>
      <img
        :class="sidebarToggle ? 'lg:block' : 'hidden'"
        class="logo-icon hidden"
        src="./images/logo/logo-icon.svg"
        alt="Logo"
      />
    </a>
  </div>
  <div class="no-scrollbar flex flex-col overflow-y-auto duration-300 ease-linear">
    <nav x-data="{ selected: '' }">
      <div>
        <h3 class="mb-4 text-xs uppercase text-gray-400">
          <span :class="sidebarToggle ? 'lg:hidden' : ''">MENU</span>
          <svg
            :class="sidebarToggle ? 'lg:block' : 'hidden'"
            class="mx-auto"
            width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
          >
            <path
              d="M5.99915 10.2451C6.96564 10.2451 7.74915 11.0286 7.74915 11.9951V12.0051C7.74915 12.9716 6.96564 13.7551 5.99915 13.7551C5.03265 13.7551 4.24915 12.9716 4.24915 12.0051V11.9951C4.24915 11.0286 5.03265 10.2451 5.99915 10.2451ZM17.9991 10.2451C18.9656 10.2451 19.7491 11.0286 19.7491 11.9951V12.0051C19.7491 12.9716 18.9656 13.7551 17.9991 13.7551C17.0326 13.7551 16.2491 12.9716 16.2491 12.0051V11.9951C16.2491 11.0286 17.0326 10.2451 17.9991 10.2451ZM13.7491 11.9951C13.7491 11.0286 12.9656 10.2451 11.9991 10.2451C11.0326 10.2451 10.2491 11.0286 10.2491 11.9951V12.0051C10.2491 12.9716 11.0326 13.7551 11.9991 13.7551C12.9656 13.7551 13.7491 12.9716 13.7491 12.0051V11.9951Z"
              fill="currentColor"
            />
          </svg>
        </h3>
        <ul class="mb-6 flex flex-col gap-4">

         <!-- Menu Item Karyawan -->
<li>
  <a
    href="{{ route('karyawan') }}"
    class="menu-item group"
    :class="(selected === 'Karyawan') && (page === 'karyawan') ? 'menu-item-active' : 'menu-item-inactive'"
  >
    <svg
      :class="(selected === 'Karyawan') && (page === 'karyawan') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
      width="24"
      height="24"
      viewBox="0 0 24 24"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
    >
      <path
        d="M12 12c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5zm0 2c-3.866 0-7 2.015-7 4.5V21h14v-2.5c0-2.485-3.134-4.5-7-4.5z"
        fill=""
      />
    </svg>

    <span
      class="menu-item-text"
      :class="sidebarToggle ? 'lg:hidden' : ''"
    >
      Karyawan
    </span>
  </a>
</li>


          <!-- PENGAJUAN_IZIN -->
          <li>
            <a
              href="#"
              @click.prevent="selected = (selected === 'Forms' ? '':'Forms')"
              class="menu-item group"
              :class=" (selected === 'Forms') || (page === 'formElements' || page === 'formLayout' || page === 'proFormElements' || page === 'proFormLayout') ? 'menu-item-active' : 'menu-item-inactive'"
            >
              <svg
                :class="(selected === 'Forms') || (page === 'formElements' || page === 'formLayout' || page === 'proFormElements' || page === 'proFormLayout') ? 'menu-item-icon-active'  :'menu-item-icon-inactive'"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  fill-rule="evenodd"
                  clip-rule="evenodd"
                  d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H18.5001C19.7427 20.75 20.7501 19.7426 20.7501 18.5V5.5C20.7501 4.25736 19.7427 3.25 18.5001 3.25H5.5ZM4.75 5.5C4.75 5.08579 5.08579 4.75 5.5 4.75H18.5001C18.9143 4.75 19.2501 5.08579 19.2501 5.5V18.5C19.2501 18.9142 18.9143 19.25 18.5001 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V5.5ZM6.25005 9.7143C6.25005 9.30008 6.58583 8.9643 7.00005 8.9643L17 8.96429C17.4143 8.96429 17.75 9.30008 17.75 9.71429C17.75 10.1285 17.4143 10.4643 17 10.4643L7.00005 10.4643C6.58583 10.4643 6.25005 10.1285 6.25005 9.7143ZM6.25005 14.2857C6.25005 13.8715 6.58583 13.5357 7.00005 13.5357H17C17.4143 13.5357 17.75 13.8715 17.75 14.2857C17.75 14.6999 17.4143 15.0357 17 15.0357H7.00005C6.58583 15.0357 6.25005 14.6999 6.25005 14.2857Z"
                  fill="currentColor"
                />
              </svg>

              <span
                class="menu-item-text"
                :class="sidebarToggle ? 'lg:hidden' : ''"
              >
                Pengajuan Izin
              </span>

              <svg
                class="menu-item-arrow absolute right-2.5 top-1/2 -translate-y-1/2 stroke-current"
                :class="[(selected === 'Forms') ? 'menu-item-arrow-active' : 'menu-item-arrow-inactive', sidebarToggle ? 'lg:hidden' : '' ]"
                width="20"
                height="20"
                viewBox="0 0 20 20"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585"
                  stroke=""
                  stroke-width="1.5"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
            </a>

            <!-- Dropdown Menu Pengajuan Izin -->
            <div
              class="overflow-hidden transform translate"
              :class="(selected === 'Forms') ? 'block' :'hidden'"
            >
              <ul
                :class="sidebarToggle ? 'lg:hidden' : 'flex'"
                class="flex flex-col gap-1 mt-2 menu-dropdown pl-9"
              >
                <li>
                  <a
                    href="form-elements.html"
                    class="menu-dropdown-item group"
                    :class="page === 'formElements' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'"
                  >
                    List Pengajuan
                  </a>
                </li>
                <li>
                  <a
                    href="form-elements.html"
                    class="menu-dropdown-item group"
                    :class="page === 'formElements' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'"
                  >
                    History Pengajuan
                  </a>
                </li>
              </ul>
            </div>
            <!-- Dropdown Menu Pengajuan_Izin End -->
          </li>
          <!-- END PENGAJUAN_IZIN -->

          <!-- PERINGATAN -->
          <li>
            <a
              href="#"
              @click.prevent="selected = (selected === 'Tables' ? '':'Tables')"
              class="menu-item group"
              :class="(selected === 'Tables') || (page === 'basicTables' || page === 'dataTables') ? 'menu-item-active' : 'menu-item-inactive'"
            >
              <svg
                :class="(selected === 'Tables') || (page === 'basicTables' || page === 'dataTables') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
              <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M12 2.25L1.75 21.75H22.25L12 2.25ZM12 4.75L20.125 20.25H3.875L12 4.75ZM11 10.25H13V15.25H11V10.25ZM11 16.75H13V18.75H11V16.75Z"
                fill="currentColor"
              />
              </svg>

              <span
                class="menu-item-text"
                :class="sidebarToggle ? 'lg:hidden' : ''"
              >
                Peringatan
              </span>

              <svg
                class="menu-item-arrow absolute right-2.5 top-1/2 -translate-y-1/2 stroke-current"
                :class="[(selected === 'Tables') ? 'menu-item-arrow-active' : 'menu-item-arrow-inactive', sidebarToggle ? 'lg:hidden' : '' ]"
                width="20"
                height="20"
                viewBox="0 0 20 20"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585"
                  stroke=""
                  stroke-width="1.5"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
            </a>

            <!-- Dropdown Menu Start -->
            <div
              class="overflow-hidden transform translate"
              :class="(selected === 'Tables') ? 'block' :'hidden'"
            >
              <ul
                :class="sidebarToggle ? 'lg:hidden' : 'flex'"
                class="flex flex-col gap-1 mt-2 menu-dropdown pl-9"
              >
                <li>
                  <a
                    href="basic-tables.html"
                    class="menu-dropdown-item group"
                    :class="page === 'basicTables' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'"
                  >
                    List Peringatan
                  </a>
                </li>
                <li>
                  <a
                    href="basic-tables.html"
                    class="menu-dropdown-item group"
                    :class="page === 'basicTables' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'"
                  >
                    Riwayat Peringatan
                  </a>
                </li>
              </ul>
            </div>
            <!-- Dropdown Menu End -->
          </li>
          <!-- END PERINGATAN -->

          <!-- SPPD -->
          <li>
            <a
              href="#"
              @click.prevent="selected = (selected === 'Pages' ? '':'Pages')"
              class="menu-item group"
              :class="(selected === 'Pages') || (page === 'fileManager' || page === 'pricingTables' || page === 'blank' || page === 'page404' || page === 'page500' || page === 'page503' || page === 'success' || page === 'faq' || page === 'comingSoon' || page === 'maintenance') ? 'menu-item-active' : 'menu-item-inactive'"
            >
              <svg
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
              >
              <path
                d="M14 6V5C14 4.44772 13.5523 4 13 4H11C10.4477 4 10 4.44772 10 5V6M14 6H20V18C20 19.1046 19.1046 20 18 20H6C4.89543 20 4 19.1046 4 18V8C4 6.89543 4.89543 6 6 6H10M14 6H10"
                stroke="currentColor"
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
              </svg>

              <span
                class="menu-item-text"
                :class="sidebarToggle ? 'lg:hidden' : ''"
              >
                Perjalanan Dinas
              </span>

              <svg
                class="menu-item-arrow absolute right-2.5 top-1/2 -translate-y-1/2 stroke-current"
                :class="[(selected === 'Pages') ? 'menu-item-arrow-active' : 'menu-item-arrow-inactive', sidebarToggle ? 'lg:hidden' : '' ]"
                width="20"
                height="20"
                viewBox="0 0 20 20"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585"
                  stroke=""
                  stroke-width="1.5"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
            </a>

            <!-- Dropdown Menu Start -->
            <div
              class="overflow-hidden transform translate"
              :class="(selected === 'Pages') ? 'block' :'hidden'"
            >
              <ul
                :class="sidebarToggle ? 'lg:hidden' : 'flex'"
                class="flex flex-col gap-1 mt-2 menu-dropdown pl-9"
              >
                <li>
                  <a
                    href="blank.html"
                    class="menu-dropdown-item group"
                    :class="page === 'blank' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'"
                  >
                    List Pengajuan SPPD
                  </a>
                </li>
                <li>
                  <a
                    href="404.html"
                    class="menu-dropdown-item group"
                    :class="page === 'page404' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'"
                  >
                    Riwayat SPPD
                  </a>
                </li>
              </ul>
            </div>
            <!-- Dropdown Menu End -->
          </li>
          <!-- Menu Item Pages -->
          </ul>

          <!-- Others Group -->
          <div>
            <h3 class="mb-4 text-xs uppercase leading-[20px] text-gray-400">
              <span
                class="menu-group-title"
                :class="sidebarToggle ? 'lg:hidden' : ''"
              >
                others
              </span>

              <svg
                :class="sidebarToggle ? 'lg:block hidden' : 'hidden'"
                class="mx-auto fill-current menu-group-icon"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  fill-rule="evenodd"
                  clip-rule="evenodd"
                  d="M5.99915 10.2451C6.96564 10.2451 7.74915 11.0286 7.74915 11.9951V12.0051C7.74915 12.9716 6.96564 13.7551 5.99915 13.7551C5.03265 13.7551 4.24915 12.9716 4.24915 12.0051V11.9951C4.24915 11.0286 5.03265 10.2451 5.99915 10.2451ZM17.9991 10.2451C18.9656 10.2451 19.7491 11.0286 19.7491 11.9951V12.0051C19.7491 12.9716 18.9656 13.7551 17.9991 13.7551C17.0326 13.7551 16.2491 12.9716 16.2491 12.0051V11.9951C16.2491 11.0286 17.0326 10.2451 17.9991 10.2451ZM13.7491 11.9951C13.7491 11.0286 12.9656 10.2451 11.9991 10.2451C11.0326 10.2451 10.2491 11.0286 10.2491 11.9951V12.0051C10.2491 12.9716 11.0326 13.7551 11.9991 13.7551C12.9656 13.7551 13.7491 12.9716 13.7491 12.0051V11.9951Z"
                  fill="currentColor"
                />
              </svg>
            </h3>

            <ul class="flex flex-col gap-4 mb-6">
            </ul>
          </div>
        </nav>
</aside>
