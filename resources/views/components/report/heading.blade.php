@props(['data','record'])
@php($tag = array_key_exists('level', $data) ? $data['level'] : 'h1')
<{{ $tag }} class="{{ $data['alignment'] }}">
    {{ array_key_exists('content', $data) ? $data['content'] : '' }}
</{{ $tag }}>
