<div class="container mx-auto px-4 py-8">
    @if(isset($breadcrumbs))
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    @endif
        <x-leadin :title="$title" :subtitle="$subtitle" />

        <form wire:submit="create">
        {{ $this->form }}

        <!-- Form Actions -->
        <div class="flex items-center justify-end space-x-4 pt-4 border-t border-slate-200">
            @if(isset($cancelUrl))
            <a
                href="{{ $cancelUrl }}"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 hover:text-slate-900"
            >
                {{ isset($cancelText) ? $cancelText : __('Cancel') }}
            </a>
            @endif
            <button
                :disabled="!$isFormValid"
                type="submit"
                class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-medium text-sm text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
            >
                {{ isset($createText) ? $createText : __('Create') }}
            </button>
        </div>
    </form>
</div>
