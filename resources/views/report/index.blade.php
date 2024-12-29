<!-- resources/views/reports.blade.php -->
<x-layout>
    <div class="container mx-auto px-4 py-8 md:pt-28">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-900">Reports</h1>
            <p class="text-slate-600">Production insights and analysis</p>
        </div>

        <!-- Coming Soon Card -->
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg p-8 border border-slate-200 text-center">
                <!-- Coming Soon Icon -->
                <div class="mb-6 flex justify-center">
                    <img src="{{ asset('images/icons/reports.svg') }}"
                         class="h-16 w-16 text-teal-500"
                         alt="Reports">
                </div>

                <h2 class="text-2xl font-bold text-slate-900 mb-3">Reports Coming Soon</h2>
                <p class="text-slate-600 mb-8 max-w-md mx-auto">
                    We're building powerful reporting tools to help you analyze and optimize your production processes. Stay tuned for updates!
                </p>

                <!-- Feature Preview List -->
                <div class="text-left max-w-md mx-auto mb-8">
                    <h3 class="font-medium text-slate-900 mb-3">Upcoming Features:</h3>
                    <ul class="space-y-2">
                        <li class="flex items-center text-slate-600">
                            <svg class="h-5 w-5 mr-3 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Production Efficiency Reports
                        </li>
                        <li class="flex items-center text-slate-600">
                            <svg class="h-5 w-5 mr-3 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Worker Performance Analytics
                        </li>
                        <li class="flex items-center text-slate-600">
                            <svg class="h-5 w-5 mr-3 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Equipment Utilization Insights
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</x-layout>
