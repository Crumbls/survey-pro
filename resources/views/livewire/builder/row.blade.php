<div
    x-data="{
        showChildControls: false,
        availableChildren: @entangle('availableChildTypes')
    }"
    class="column"
    :class="'w-' + @entangle('width')"
>
    <div class="controls" x-show="showChildControls">
        <template x-for="type in availableChildren">
            <button
                @click="$wire.addChild(type)"
                x-show="!@entangle('isAtMaxChildren')"
            >
                Add x-text="type"
            </button>
        </template>
    </div>

    @if(isset($slot))
    {{ $slot }}
    @else
    no slot

        @endif
</div>
