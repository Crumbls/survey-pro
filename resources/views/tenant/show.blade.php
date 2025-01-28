<!-- resources/views/dashboard.blade.php -->
<x-layout>
    <div class="container mx-auto px-4 py-8">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-900 flex items-center space-x-4">
                <span>
                {{ __('tenants.singular') }}: {{ $record->name }}
                    </span>
                @can('update', $record)
                    <a href="{{ route('tenants.edit', $record) }}">
                    <svg class="h-5 w-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/>
                    </svg>
                    </a>
                    @endcan
            </h1>
            <p class="text-slate-600">
                {{ __('tenants.description') }}
            </p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Daily Output -->
            <div class="bg-white rounded-lg p-6 border border-slate-200">
                <div class="flex justify-between items-start mb-4">
                    <span class="text-slate-600 text-sm">Surveys</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="space-y-2">
                    <div class="text-3xl font-bold text-slate-900">
                        <a href="{{ route('tenants.surveys.index', $record) }}">{{ number_format($record->surveys->count())     }}</a>
                    </div>
                    <div class="flex items-center text-sm text-primary-500 hidden">
                        @if($record->surveys->count())
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                            </svg>
                            12% from yesterday
                        @else
                            <a href="{{ route('tenants.surveys.create', $record) }}">Get Started</a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Collectors -->
            <div class="bg-white rounded-lg p-6 border border-slate-200">
                <div class="flex justify-between items-start mb-4">
                    <span class="text-slate-600 text-sm">
                        {{ __('collectors.plural') }}
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                    </svg>
                </div>
                <div class="space-y-2">
                    @php($count = \App\Models\Collector::whereRaw('1=1')
                ->whereIn('survey_id',
                    $record
                        ->surveys()
                        ->select('surveys.id')
                )->count())
                    <div class="text-3xl font-bold text-slate-900">
                        <a href="{{ route('tenants.collectors.index', $record) }}">{{ number_format($count)     }}</a>
                    </div>
                    <div class="flex items-center text-sm text-primary-500 hidden">
                        @if($count <= 1)
                            <a href="{{ route('collectors.create') }}">Get Started</a>
                        @else
                            <a href="{{ route('collectors.create') }}">Create a new User</a>
                        @endif

                    </div>
                </div>
            </div>

            <!-- Uptime -->
            <div class="bg-white rounded-lg p-6 border border-slate-200">
                @php($count = \App\Models\Report::whereRaw('1=1')
                                    ->whereIn('survey_id',
                                        $record
                                            ->surveys()
                                            ->select('surveys.id')
                                    )->count())
                <div class="flex justify-between items-start mb-4">
                    <span class="text-slate-600 text-sm">{{ __('reports.plural') }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="space-y-2">
                    <div class="text-3xl font-bold text-slate-900">
                        @if($count)
                            <a href="{{ route('tenants.reports.index', $record) }}">
                            {{ number_format($count) }}
                            </a>

                        @else
                            0
                        @endif
                    </div>
                </div>
            </div>

            <!-- Efficiency Rate -->
            <div class="bg-white rounded-lg p-6 border border-slate-200 hidden">
                <div class="flex justify-between items-start mb-4">
                    <span class="text-slate-600 text-sm">Efficiency Rate</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
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
