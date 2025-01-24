
<!-- Navbar with mobile menu -->
<div x-ref="navbar" class="fixed top-0 left-0 right-0 bg-white/80 backdrop-blur-md border-b z-50">
    <div class="container mx-auto px-4 py-3">
        <div class="flex items-center justify-between">
            <!-- Left side with logo -->
            <div class="flex items-center gap-8">
                <a href="{{ route('home') }}" class="text-xl font-semibold text-primary-600">
                    {{ config('app.name', 'SurveyPro') }}
                </a>

                <!-- Desktop Navigation -->
                {!! App\Menus\TopBar::render() !!}
            </div>

            <!-- Right side with auth buttons/menu -->
            <div class="flex items-center gap-4">
                @auth
                    <!-- Desktop Notifications & Profile -->
                    <div class="hidden md:flex items-center gap-4">

                        @php($count = auth()->user()->notifications()->where('type', \App\Notifications\SystemAlert::class)->count())
                        <!-- Notifications -->
                        <div class="relative">
                            <a href="{{ route('notifications.index') }}" class="relative p-2 text-slate-600 hover:text-slate-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                @if($count)
                                <span class="absolute top-0 right-0 -mt-1 -mr-1 px-2 py-0.5 text-xs font-bold text-white bg-primary-500 rounded-full">
                                    {{ $count > 10 ? '10+' : $count }}
                                </span>
                                @endif
                            </a>
                        </div>

                        <!-- User Menu Dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open"
                                    class="flex items-center gap-2 p-2 text-sm font-medium text-slate-600 hover:text-slate-900">
                                <img src="{{ auth()->user()->getAvatar() }}"
                                     alt="Avatar"
                                     class="w-8 h-8 rounded-full">
                                <span>{{ auth()->user()->name }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Desktop Dropdown Menu -->
                            <div x-show="open"
                                 @click.away="open = false"
                                 x-transition
                                 x-cloak
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-slate-200">
                                <div x-show="open"
                                     @click.away="open = false"
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-slate-200">
                                    <div class="py-1">
                                        <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                            Profile
                                        </a>
                                        @if(false)
                                        <a href="{{ route('company.settings') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                            Company Settings
                                        </a>
                                        <a href="{{ route('billing') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                            Billing
                                        </a>
                                        <a href="{{ route('team') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                            Team Management
                                        </a>
                                        <a href="{{ route('support') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                            Support
                                        </a>
                                        @endif
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                                Logout
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Menu Button -->
                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="md:hidden p-2 text-slate-600 hover:text-slate-900"
                    >
                        <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                @else
                    <!-- Guest Auth Buttons -->
                    <div class="hidden md:flex items-center gap-4">
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium transition-colors rounded-md hover:bg-gray-100">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-md hover:bg-primary-700 transition-colors">
                            Get Started
                        </a>
                    </div>

                    <!-- Mobile Menu Button for Guests -->
                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="md:hidden p-2 text-slate-600 hover:text-slate-900"
                    >
                        <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                @endauth
            </div>
        </div>

        <!-- Mobile Menu Panel -->
        <div
            x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4"
            class="md:hidden"
        >
            <div class="pt-4 pb-3 border-t border-slate-200">
                @auth
                    <!-- Authenticated Mobile Menu -->
                    <div class="flex items-center px-4 mb-4">
                        <div class="flex-shrink-0">
                            <img class="h-10 w-10 rounded-full" src="{{ auth()->user()->avatar }}" alt="">
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium text-slate-800">{{ auth()->user()->name }}</div>
                            <div class="text-sm font-medium text-slate-500">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
@endauth


                    {!! App\Menus\MobileTopBar::render() !!}

            </div>
        </div>
    </div>
</div>
