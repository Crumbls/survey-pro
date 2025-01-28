<!-- resources/views/livewire/edit-address.blade.php -->
<div>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="flex items-center justify-end space-x-4 mt-4">
            @can('view', $record)
                <a
                    href="{{ route('tenants.show', $record) }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 hover:text-slate-900"
                >
                    Cancel
                </a>
            @endcan

            <button
                type="submit"
                class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-medium text-sm text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
            >
                {{ __('tenants.save') }}
            </button>
        </div>
    </form>
</div>
