<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Demo Modal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    x-data="{
        page: 'dashboard',
        darkMode: false,
        sidebarToggle: false,
        isProfileAddressModal: false
    }"
    x-init="
        darkMode = JSON.parse(localStorage.getItem('darkMode'));
        $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))
    "
    :class="{'dark bg-gray-900': darkMode}"
>
    <!-- Sidebar -->
    <aside class="w-64 h-screen bg-gray-100 dark:bg-gray-800 p-4">
        <ul class="flex flex-col gap-2">
            <li>
                <button
                    @click="isProfileAddressModal = true"
                    class="group flex items-center gap-2 px-3 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 w-full"
                >
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 3.5C7.30558 3.5 3.5 7.30558 3.5 12C3.5 14.1526 4.3002 16.1184 5.61936 17.616C6.17279 15.3096 8.24852 13.5955 10.7246 13.5955H13.2746C15.7509 13.5955 17.8268 15.31 18.38 17.6167C19.6996 16.119 20.5 14.153 20.5 12C20.5 7.30558 16.6944 3.5 12 3.5Z"/>
                    </svg>
                    Edit Profile
                </button>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="ml-64 p-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Dashboard</h1>
        <p class="text-gray-600 dark:text-gray-300 mt-2">Click "Edit Profile" to open modal.</p>
    </main>

    <!-- Profile Address Modal -->
    <div
        x-show="isProfileAddressModal"
        x-transition
        class="fixed inset-0 flex items-center justify-center p-5 z-50"
    >
        <!-- Overlay -->
        <div
            @click="isProfileAddressModal = false"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm"
        ></div>

        <!-- Modal Box -->
        <div
            @click.outside="isProfileAddressModal = false"
            class="relative w-full max-w-lg bg-white dark:bg-gray-900 rounded-3xl p-6 lg:p-10 shadow-lg"
        >
            <!-- Close Button -->
            <button
                @click="isProfileAddressModal = false"
                class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
            >
                ✕
            </button>

            <!-- Modal Content -->
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Edit Address</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Update your details to keep your profile up-to-date.</p>

            <form class="flex flex-col gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">Country</label>
                    <input type="text" value="United States" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-gray-800 dark:text-white"/>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">City/State</label>
                    <input type="text" value="Arizona, United States" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-gray-800 dark:text-white"/>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">Postal Code</label>
                    <input type="text" value="ERT 2489" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-gray-800 dark:text-white"/>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" @click="isProfileAddressModal = false" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50">Close</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
