<!-- resources/views/auth/verify-email.blade.php -->
<x-layout>
    <section class="relative flex items-center justify-center overflow-hidden bg-gradient-to-b from-slate-50 to-white py-24">
        <div class="container px-4 mx-auto">
            <div class="max-w-lg mx-auto">
                <!-- Header -->
                <div class="text-center mb-8">
                    <span class="inline-block px-4 py-1.5 mb-6 text-sm font-semibold bg-primary-50 text-primary-600 rounded-full">
                        Verify Email
                    </span>
                    <h1 class="text-3xl font-bold mb-2 text-slate-900">
                        Check your email
                    </h1>
                    <p class="text-slate-600">
                        We've sent you a verification link to secure your account
                    </p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-lg border border-slate-200">
                    @if (session('status') == 'verification-link-sent')
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-600 rounded-md">
                            A new verification link has been sent to your email address.
                        </div>
                    @endif

                    <div class="text-center space-y-6">
                        <p class="text-slate-600">
                            Please check your email and click the verification link to continue.
                            If you didn't receive the email, we can send another.
                        </p>

                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="w-full sm:w-auto bg-primary-600 text-white px-6 py-2.5 rounded-md hover:bg-primary-700 transition-colors duration-200 font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                                >
                                    Resend Verification Email
                                </button>
                            </form>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="w-full sm:w-auto px-6 py-2.5 border border-slate-200 rounded-md hover:bg-slate-50 transition-colors duration-200 font-medium"
                                >
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout>
