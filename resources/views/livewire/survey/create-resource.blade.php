<div class="container mx-auto px-4 py-8">
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Survey</h1>
        <p class="text-slate-600">Start by giving your survey a title and optional description</p>
    </div>

    <div class="max-w-2xl mx-auto">

        <!-- Form Card -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200">
            <form wire:submit="create" class="p-6 space-y-6">
                {{ $this->form }}
                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4">

                    <a
                        href="{{ route('collectors.index') }}"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 hover:text-slate-900"
                    >
                        Cancel
                    </a>
                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-medium text-sm text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                    >
                        Create Survey
                    </button>
                </div>
            </form>


        </div>
    </div>
    <x-filament-actions::modals />
</div>
