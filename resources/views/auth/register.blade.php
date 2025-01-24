<x-layout>
    <div class="min-h-svh">
        <section class="relative flex items-center justify-center overflow-hidden bg-gradient-to-b from-slate-50 to-white py-24">
            <div class="container px-4 mx-auto">
                <div class="max-w-lg mx-auto">
                    <!-- Header -->
                    <div class="text-center mb-8">
                    <span class="inline-block px-4 py-1.5 mb-6 text-sm font-semibold bg-primary-50 text-primary-600 rounded-full">
                        Get Started
                    </span>
                        <h1 class="text-3xl font-bold mb-2 text-slate-900">
                            Create your account
                        </h1>
                        <p class="text-slate-600">
                            Start optimizing your production process today
                        </p>
                    </div>

                    <!-- Registration Form -->
                    <div class="bg-white p-8 rounded-2xl shadow-lg border border-slate-200">
                        <form method="POST" action="{{ route('register') }}" class="space-y-6">
                            @csrf

                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-slate-900 mb-1">
                                    Full Name
                                </label>
                                <input
                                    type="text"
                                    name="name"
                                    id="name"
                                    value="{{ old('name') }}"
                                    class="w-full px-4 py-2 border border-slate-200 rounded-md focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                    required
                                    autofocus
                                />
                                @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-slate-900 mb-1">
                                    Work Email
                                </label>
                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    value="{{ old('email') }}"
                                    class="w-full px-4 py-2 border border-slate-200 rounded-md focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                    required
                                />
                                @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-slate-900 mb-1">
                                    Password
                                </label>
                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    class="w-full px-4 py-2 border border-slate-200 rounded-md focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                    required
                                />
                                @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-slate-900 mb-1">
                                    Confirm Password
                                </label>
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    id="password_confirmation"
                                    class="w-full px-4 py-2 border border-slate-200 rounded-md focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                    required
                                />
                            </div>

                            <!-- Terms & Conditions -->
                            <div class="flex items-start">
                                <input
                                    type="checkbox"
                                    name="terms"
                                    id="terms"
                                    class="mt-1 h-4 w-4 text-primary-600 border-slate-300 rounded focus:ring-primary-500"
                                    required
                                />
                                <label for="terms" class="ml-2 block text-sm text-slate-600">
                                    I agree to the
                                    <a href="#" class="text-primary-600 hover:text-primary-700">Terms of Service</a>
                                    and
                                    <a href="#" class="text-primary-600 hover:text-primary-700">Privacy Policy</a>
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <button
                                type="submit"
                                class="w-full bg-primary-600 text-white px-4 py-2.5 rounded-md hover:bg-primary-700 transition-colors duration-200 font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                            >
                                Create Account
                            </button>
                        </form>
@if(false)
                        <!-- Social Registration -->
                        <div class="relative my-6">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-slate-200"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-white text-slate-500">Or register with</span>
                            </div>
                        </div>
                        <!-- Social Login Buttons -->
                        <div class="grid grid-cols-2 gap-4">
                            <button class="flex items-center justify-center px-4 py-2 border border-slate-200 rounded-md hover:bg-slate-50 transition-colors duration-200">
                                <svg class="h-5 w-5 mr-2" viewBox="0 0 24 24">
                                    <!-- Google icon -->
                                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                                </svg>
                                Google
                            </button>
                            <button class="flex items-center justify-center px-4 py-2 border border-slate-200 rounded-md hover:bg-slate-50 transition-colors duration-200">
                                <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <!-- GitHub icon -->
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.477 2 12c0 4.42 2.87 8.17 6.84 9.5.5.08.66-.23.66-.5v-1.69c-2.77.6-3.36-1.34-3.36-1.34-.46-1.16-1.11-1.47-1.11-1.47-.91-.62.07-.6.07-.6 1 .07 1.53 1.03 1.53 1.03.87 1.52 2.34 1.07 2.91.83.09-.65.35-1.09.63-1.34-2.22-.25-4.55-1.11-4.55-4.92 0-1.11.38-2 1.03-2.71-.1-.25-.45-1.29.1-2.64 0 0 .84-.27 2.75 1.02.79-.22 1.65-.33 2.5-.33.85 0 1.71.11 2.5.33 1.91-1.29 2.75-1.02 2.75-1.02.55 1.35.2 2.39.1 2.64.65.71 1.03 1.6 1.03 2.71 0 3.82-2.34 4.66-4.57 4.91.36.31.69.92.69 1.85V21c0 .27.16.59.67.5C19.14 20.16 22 16.42 22 12A10 10 0 0012 2z"/>
                                </svg>
                                GitHub
                            </button>
                        </div>
                        @endif
                        <!-- Login Link -->
                        <p class="mt-6 text-center text-sm text-slate-600">
                            Already have an account?
                            <a href="{{ route('login') }}" class="font-medium text-primary-600 hover:text-primary-700">
                                Sign in
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layout>
