@php
    // Get the parent state path
       $parentStatePath = substr($getStatePath(), 0, strrpos($getStatePath(), '.'));


@endphp
<div x-data="{ image: $wire.$entangle('{{ $parentStatePath }}.image_url'),
preset: $wire.$entangle('{{ $parentStatePath }}.image_size_preset'),
width: $wire.$entangle('{{ $parentStatePath }}.image_width') }">
    <div x-show="image && preset == 'custom'" class="text-center">
        <img :src="image" :width="width" class="mx-auto" />
    </div>
</div>
