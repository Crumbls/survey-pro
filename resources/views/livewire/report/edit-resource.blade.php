<div class="container mx-auto px-4 py-8">
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <!-- Header -->
    <x-leadin :title="$title" :subtitle="$subtitle" />


    <div class="">

        <!-- Form Card -->
        <div class="">
            <form wire:submit="save" class="space-y-6">
                {{ $this->form }}

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4 pt-4">

                    <a
                        href="{{ route('surveys.reports.index', $record->survey) }}"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 hover:text-slate-900"
                    >
                        {{ __('Cancel') }}
                    </a>
                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-medium text-sm text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                    >
                        {{ __('reports.singular_update') }}
                    </button>
                </div>
            </form>


        </div>
    </div>
    <x-filament-actions::modals />
</div>
