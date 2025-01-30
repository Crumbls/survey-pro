@props(['title','subtitle','updateUrl'])
<!-- Header -->
<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-900 flex items-center space-x-4">
        <span>
        @if(isset($title))
            {{ $title }}
        @else
            define title in view.
        @endif
            </span>
        @if(isset($updateUrl) && $updateUrl)
                <a href="{{ $updateUrl }}">
                    <svg class="h-5 w-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/>
                    </svg>
                </a>
        @endif
    </h1>
    <p class="text-slate-600">
        @if(isset($subtitle))
            {{ $subtitle }}
        @else
            define subtitle in view.
        @endif
    </p>
</div>
