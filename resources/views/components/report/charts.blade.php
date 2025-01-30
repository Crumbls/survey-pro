@props(['data','record'])
@php($cols = array_key_exists('charts', $data) ? count($data['charts']) : 0)
<div class="container mx-auto px-4">
    <div class="grid grid-cols-1 md:grid-cols-{{ $cols }} gap-4 justify-center max-w-2xl mx-auto">
        @if(in_array('weighted', $data['charts']))
            <div class="bg-gray-100 p-4 rounded">
                Weighted Chart
            </div>
        @endif
            @if(in_array('alignment', $data['charts']))
                <div class="bg-gray-100 p-4 rounded">
                    Alignment Chart
                </div>
            @endif
    </div>
</div>
