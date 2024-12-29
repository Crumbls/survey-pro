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
    <div class="fixed top-0 left-0 right-0 h-1 bg-teal-500 origin-left z-[51]"
         :style="`transform: scaleX(${scrollProgress / 100})`">
    </div>

    <x-top-bar />

    {{ $slot }}
</div>

@stack('scripts')
@filamentScripts
@vite('resources/js/app.js')
</body>
</html>
