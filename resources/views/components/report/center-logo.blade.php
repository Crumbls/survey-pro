@props(['data','record'])
@php($media = $record->client->tenant->getFirstMedia('logo'))
<div class="text-center">
    @if($media)
        <img src="{{ $media->getUrl() }}" class="w-auto h-auto mx-auto"/>
    @endif
</div>
