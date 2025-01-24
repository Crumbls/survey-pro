<x-layout>
    <div class="container mx-auto px-4 py-8">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-900">Survey Dashboard: {{ $record->title }}</h1>

            <p class="text-slate-600">
                Collect data for insights and analysis
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Daily Output -->
            <div class="bg-white rounded-lg p-6 border border-slate-200">
                <div class="flex justify-between items-start mb-4">
                    <span class="text-slate-600 text-sm">Daily Output</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="space-y-2">
                    <div class="text-3xl font-bold text-slate-900">2,543</div>
                    <div class="flex items-center text-sm text-primary-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"></path>
                        </svg>
                        12% from yesterday
                    </div>
                </div>
            </div>

            <!-- Active Workers -->
            <div class="bg-white rounded-lg p-6 border border-slate-200">
                <div class="flex justify-between items-start mb-4">
                    <span class="text-slate-600 text-sm">Active Workers</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                    </svg>
                </div>
                <div class="space-y-2">
                    <div class="text-3xl font-bold text-slate-900">128</div>
                    <div class="text-sm text-slate-600">Across 3 shifts</div>
                </div>
            </div>

            <!-- Uptime -->
            <div class="bg-white rounded-lg p-6 border border-slate-200">
                <div class="flex justify-between items-start mb-4">
                    <span class="text-slate-600 text-sm">Uptime</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="space-y-2">
                    <div class="text-3xl font-bold text-slate-900">98.2%</div>
                    <div class="text-sm text-primary-500">Above target</div>
                </div>
            </div>

            <!-- Efficiency Rate -->
            <div class="bg-white rounded-lg p-6 border border-slate-200">
                <div class="flex justify-between items-start mb-4">
                    <span class="text-slate-600 text-sm">Efficiency Rate</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                    </svg>
                </div>
                <div class="space-y-2">
                    <div class="text-3xl font-bold text-slate-900">94.8%</div>
                    <div class="text-sm text-slate-600">Last 24 hours</div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
