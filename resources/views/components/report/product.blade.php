@props(['data','record'])
@php($product = is_array($data) && array_key_exists('product_id', $data) ? \App\Models\Product::find($data['product_id']) : null)
@if($product)
<div class="container mx-auto dis-px-4 space-y-4">
    @php($media = $product->getMedia('logo'))

    @if($media->count())
        @php($width = array_key_exists('image_width', $data) && is_numeric($data['image_width']) ? $data['image_width'] : null)
        <div class="space-y-4">
            @foreach($media as $m)
                <div class="text-center">
                    <img src="{{ $m->getUrl() }}"
                    class="h-auto w-auto mx-auto"
                    style="{{ $width ? 'max-width: '.$width.'px' : '' }}"
                    />
                </div>
            @endforeach
        </div>
    @endif

    <h1 class="text-center">
        {{ $product->name }}
    </h1>
    @if($product->description)
        {{ $product->description }}
    @endif
</div>
@endif
