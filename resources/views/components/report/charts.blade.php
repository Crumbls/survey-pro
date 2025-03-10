@props(['data','record'])
@php($cols = array_key_exists('charts', $data) ? min(count($data['charts']), 2) : 0)
<div class="container mx-auto px-4">
    <div class="grid grid-cols-1 md:grid-cols-{{ $cols }} gap-4 justify-center max-w-2xl mx-auto">
        @if(in_array('weighted', $data['charts']))
            <div class="">
                <img src="/images/weighted-score.png" class="mx-auto" />
            </div>
        @endif
            @if(in_array('alignment', $data['charts']))
                <div class="">
                    <img src="/images/alignment-factor.png" class="mx-auto" />
                </div>
            @endif
    </div>
</div>
