<div class="container mx-auto px-4 py-8">
    @if(isset($breadcrumbs))
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    @endif
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Title</h1>
        <p class="text-slate-600">
            Subtitle
        </p>
    </div>

        <form wire:submit="save">
            {{ $this->form }}

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-slate-200">
                <a
                    href="{{ route('collectors.index') }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 hover:text-slate-900"
                >
                    Cancel
                </a>
                <button

                    :disabled="!$isFormValid"
                    type="submit"
                    class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-medium text-sm text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                >
                    Create User
                </button>
            </div>
        </form>

</div>
