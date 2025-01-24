@props(['breadcrumbs'])
@if(isset($breadcrumbs))
    <nav aria-label="breadcrumb" class="w-max mb-4">
        <ol class="flex w-full flex-wrap items-center">
            @foreach($breadcrumbs as $breadcrumb)
                <li class="flex cursor-pointer items-center text-sm text-slate-500 transition-colors duration-300 hover:text-slate-800">
                    @if($breadcrumb['url'])
                        <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] }}</a>
                    @else
                        {{ $breadcrumb['label'] }}
                    @endif

                    @if(!$loop->last)
                    <span class="pointer-events-none mx-2 text-slate-800">
                        /
                    </span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
