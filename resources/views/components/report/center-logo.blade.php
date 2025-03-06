@props(['data','record'])
@php($media = $record->client->tenant->getFirstMedia('logo'))
HERE
<div class="text-center">
    CENTER!
    @if($media)
        <img src="{{ $media->getUrl() }}" class="w-auto h-auto mx-auto"/>
    @endif
</div>
