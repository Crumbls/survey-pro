<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Manufacturing Survey Software</title>
    <meta name="description" content="{{ config('app.name') }} - Manufacturing Survey Software">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <script async src="https://www.googletagmanager.com/gtag/js?id=G-KQ4W49HMZF"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-KQ4W49HMZF');
    </script>

    @filamentStyles
    @vite('resources/css/app.css')
</head>
<body>

<div x-data="{ isOpen: false,
mobileMenuOpen: false,
 scrollProgress: 0,
        init() {
            window.addEventListener('scroll', () => {
                // Calculate scroll progress
                const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
                const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
                this.scrollProgress = (winScroll / height) * 100;
            });
        }
 }">
    <!-- Progress Bar -->
    <div class="fixed top-0 left-0 right-0 h-1 bg-primary-500 origin-left z-[51]"
         :style="`transform: scaleX(${scrollProgress / 100})`">
    </div>

    <x-top-bar />

    <div x-data="{
        init() {
            let debounceTimer;
            const checkScreenSize = () => {
                if (window.innerWidth >= 768) { // Tailwind's md breakpoint
                    const navHeight = $refs.navbar.offsetHeight;
                    $el.style.paddingTop = `${navHeight}px`;
                } else {
                    $el.style.paddingTop = '0px';
                }
            };

            // Initial check
            checkScreenSize();

            // Debounced resize handler
            window.addEventListener('resize', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    checkScreenSize();
                }, 250); // 250ms debounce delay
            });
        }
    }"
         class="min-h-screen"
    >
        <x-notifications />

        {{ $slot }}
    </div>
</div>

@stack('scripts')
<footer class="bg-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center flex-grow">
                <div class="flex-grow">
                    <div class="ml-10 items-baseline space-x-4 w-100 text-center w-100 text-white ">
                        <p class=""><small>© Copyright {{ date('Y') }}, {{ config('app.name') }}. All rights reserved.</small></p>
                        <p class="text-xs">
                            Version: {{ \App\Helpers\VersionHelper::getVersion() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
@livewire('notifications')
@filamentScripts
@vite('resources/js/app.js')
</body>
</html>
