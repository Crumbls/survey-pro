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
            <x-tenant.client-count :record="$record" />
            <x-tenant.survey-count :record="$record" />
            <x-tenant.collector-count :record="$record" />
            <x-tenant.report-count :record="$record" />
            <x-tenant.user-count :record="$record" />
            <x-tenant.product-count :record="$record" />

        </div>
    </div>
</x-layout>
