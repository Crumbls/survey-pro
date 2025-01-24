<div class="container mx-auto px-4 py-8">
    @if(isset($breadcrumbs))
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    @endif
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Title</h1>
        <p class="text-slate-600">
            Subtitle
        </p>
    </div>

    {{ $this->table }}
</div>
