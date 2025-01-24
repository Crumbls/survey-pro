<x-layout>
        <div class="min-h-svh">
            <section class="relative flex items-center justify-center overflow-hidden bg-gradient-to-b from-slate-50 to-white py-24">
                <div class="container px-4 mx-auto">
                    <div class="max-w-lg mx-auto">
                        <!-- Header -->
                        <div class="text-center mb-8">
                    <span class="inline-block px-4 py-1.5 mb-6 text-sm font-semibold bg-primary-50 text-primary-600 rounded-full">
                        Password Reset
                    </span>
                            <h1 class="text-3xl font-bold mb-2 text-slate-900">
                                Forgot your password?
                            </h1>
                            <p class="text-slate-600">
                                No worries! Enter your email and we'll send you reset instructions.
                            </p>
                        </div>

                        <!-- Reset Form -->
                        <div class="bg-white p-8 rounded-2xl shadow-lg border border-slate-200">
                            @if (session('status'))
                                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-600 rounded-md">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                                @csrf

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-slate-900 mb-1">
                                        Email Address
                                    </label>
                                    <input
                                        type="email"
                                        name="email"
                                        id="email"
                                        value="{{ old('email') }}"
                                        class="w-full px-4 py-2 border border-slate-200 rounded-md focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                        required
                                        autofocus
                                    />
                                    @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Submit Button -->
                                <button
                                    type="submit"
                                    class="w-full bg-primary-600 text-white px-4 py-2.5 rounded-md hover:bg-primary-700 transition-colors duration-200 font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                                >
                                    Send Reset Link
                                </button>
                            </form>

                            <!-- Back to Login -->
                            <p class="mt-6 text-center text-sm text-slate-600">
                                Remember your password?
                                <a href="{{ route('login') }}" class="font-medium text-primary-600 hover:text-primary-700">
                                    Back to login
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
</x-layout>
