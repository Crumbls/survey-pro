<div class="page-builder">
    {{-- Component Controls --}}
    <div class="controls mb-4 p-4 bg-gray-100">
        {{ var_export(\App\Services\PageBuilderRegistry::getRegisteredTypes()) }}
        {{var_export($availableRootComponents) }}
        @foreach($availableRootComponents as $type => $componentClass)
            <button
                wire:click="addComponent('{{ $type }}')"
                class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
            >
                Add {{ Str::title($type) }}
            </button>
        @endforeach
    </div>

    {{-- Component Tree --}}
    <div class="builder-canvas">
        @foreach($structure as $component)
            {{ var_export($component) }}
            @livewire(\App\Services\PageBuilderRegistry::getClass($component['type']), [
            'componentData' => $component,
            'depth' => 0,
            'parentType' => null
            ], key($component['id']))
        @endforeach
    </div>
</div>
