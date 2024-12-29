<x-layout>
    <section class="relative flex items-center justify-center overflow-hidden bg-gradient-to-b from-slate-50 to-white py-24">
        <div class="container px-4 mx-auto">
            <div class="max-w-lg mx-auto">
                <!-- Header -->
                <div class="text-center mb-8">
                    <span class="inline-block px-4 py-1.5 mb-6 text-sm font-semibold bg-teal-50 text-teal-600 rounded-full">
                        Reset Password
                    </span>
                    <h1 class="text-3xl font-bold mb-2 text-slate-900">
                        Set your new password
                    </h1>
                    <p class="text-slate-600">
                        Please create a strong password for your account
                    </p>
                </div>

                <!-- Reset Password Form -->
                <div class="bg-white p-8 rounded-2xl shadow-lg border border-slate-200">
                    <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
                        @csrf
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-900 mb-1">
                                Email Address
                            </label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                value="{{ old('email', $request->email) }}"
                                class="w-full px-4 py-2 border border-slate-200 rounded-md focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                required
                                readonly
                            />
                        </div>

                        <!-- New Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-900 mb-1">
                                New Password
                            </label>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="w-full px-4 py-2 border border-slate-200 rounded-md focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                required
                            />
                            @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm New Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-slate-900 mb-1">
                                Confirm New Password
                            </label>
                            <input
                                type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                class="w-full px-4 py-2 border border-slate-200 rounded-md focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                required
                            />
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            class="w-full bg-teal-600 text-white px-4 py-2.5 rounded-md hover:bg-teal-700 transition-colors duration-200 font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                        >
                            Reset Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</x-layout>
