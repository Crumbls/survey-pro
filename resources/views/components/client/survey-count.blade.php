<!-- Clients -->
<div class="bg-white rounded-lg p-6 border border-slate-200">
    <div class="flex justify-between items-start mb-4">
                    <span class="text-slate-600 text-sm">
                        {{ __('surveys.plural') }}
                    </span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-500" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
    </div>
    <div class="space-y-2">
        <div class="text-3xl font-bold text-slate-900">
            @if($url)
                <a href="{{ $url }}">{{ number_format($count) }}</a>
            @else
                {{ number_fomrat($count) }}
            @endif
        </div>

    </div>
</div>
