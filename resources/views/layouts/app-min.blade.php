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
        {{ $slot }}
</div>

@stack('scripts')
@livewire('notifications')
@filamentScripts
@vite('resources/js/app.js')
@if(false)
<!--Start of Tawk.to Script-->
<script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/5a74d1a8d7591465c70755c1/default';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);
    })();
</script>
<!--End of Tawk.to Script-->
@else
@endif
</body>
</html>
