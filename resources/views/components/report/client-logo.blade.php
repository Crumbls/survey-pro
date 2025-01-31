@props(['data','record'])
@php($media = $record->client->getFirstMedia('logo'))
<div class="text-center">
    @if($media)
        <img src="{{ $media->getUrl() }}"  class="w-auto h-auto mx-auto max-h-[200px]" />
@endif
</div>
