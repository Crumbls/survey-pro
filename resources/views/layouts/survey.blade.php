<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Manufacturing Survey Software</title>
    <meta name="description" content="{{ config('app.name') }} - Manufacturing Survey Software">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-analytics />
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
        window.addEventListener('survey-progress', (event) => {
            this.scrollProgress = event.detail.percentage;
        });
        }
 }">
    <!-- Progress Bar -->
    <div class="fixed top-0 left-0 right-0 h-1 bg-primary-500 origin-left z-[51]"
         :style="`transform: scaleX(${scrollProgress / 100})`">
    </div>


    {{ $slot }}
</div>

@stack('scripts')

@livewire('notifications')
@filamentScripts
@vite('resources/js/app.js')
</body>
</html>
