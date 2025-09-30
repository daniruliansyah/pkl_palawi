<x-guest-layout>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">

    <div id="particles-js" class="absolute inset-0 z-0 h-screen w-screen bg-gradient-to-b from-green-200 via-white to-green-300"></div>

    <div class="relative z-10 w-full max-w-md bg-white/80 rounded-xl shadow-lg p-6 flex flex-col justify-center mx-4 sm:mx-auto my-auto h-auto min-h-fit">
        <div class="flex flex-col items-center mb-6 text-center">
            <img src="{{ asset('images/econique.jpg') }}" alt="Econique Logo" class="w-24 mb-4">
            <h1 class="text-2xl md:text-2xl font-semibold drop-shadow-md" style="font-family: 'Poppins', sans-serif;">
                Selamat Datang
            </h1>
            <h2 class="text-3xl md:text-3xl font-bold drop-shadow-sm" style="font-family: 'Poppins', sans-serif;">
                ECONIQUE-yey
            </h2>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4">
            @csrf

            <x-input-label for="email" :value="__('Email')" class="text-[#1A4314]" />
            <x-text-input id="email" class="block mt-1 w-full border-[#1A4314] focus:ring-[#1A4314] focus:border-[#1A4314]"
                          type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />

            <x-input-label for="password" :value="__('Password')" class="text-[#1A4314]" />
            <x-text-input id="password" class="block mt-1 w-full border-[#1A4314] focus:ring-[#1A4314] focus:border-[#1A4314]"
                          type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />

            <div class="flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#1A4314] shadow-sm focus:ring-[#1A4314]" name="remember">
                <label for="remember_me" class="ms-2 text-sm text-gray-600">Ingat saya</label>
            </div>

            <div class="flex flex-col md:flex-row items-center justify-between mt-2 gap-3">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-[#1A4314] hover:text-green-900 w-full text-center md:w-auto md:text-left" href="{{ route('password.request') }}">
                        Lupa password?
                    </a>
                @endif

                <x-primary-button class="bg-[#1A4314] hover:bg-green-900 focus:bg-green-950 w-full md:w-auto">
                    {{ __('Masuk') }}
                </x-primary-button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <script>
    window.onload = function() {
        particlesJS('particles-js', {
            "particles": {
                "number": { "value": 50, "density": { "enable": true, "value_area": 800 } },
                "shape": {
                    "type": "image",
                    "image": {
                        "src": @json(asset('images/daun.png')),
                        "width": 50,
                        "height": 50
                    }
                },
                "opacity": { "value": 0.8, "random": true, "anim": { "enable": true, "speed": 0.5, "opacity_min": 0.3, "sync": false } },
                "size": { "value": 12, "random": true },
                "move": {
                    "enable": true,
                    "speed": 1.5,
                    "direction": "bottom",
                    "random": true,
                    "straight": false,
                    "out_mode": "out",
                    "bounce": false,
                    "attract": { "enable": false, "rotateX": 300, "rotateY": 1200 }
                },
                "rotate": {
                    "value": 0,
                    "random": true,
                    "direction": "clockwise",
                    "animation": { "enable": true, "speed": 3, "sync": false }
                }
            },
            "interactivity": {
                "detect_on": "canvas",
                "events": {
                    "onhover": { "enable": true, "mode": "repulse" },
                    "onclick": { "enable": true, "mode": "push" }
                },
                "modes": {
                    "repulse": { "distance": 100 },
                    "push": { "particles_nb": 4 }
                }
            },
            "retina_detect": true
        });
    };
    </script>
</x-guest-layout>
