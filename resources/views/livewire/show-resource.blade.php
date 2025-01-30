<div class="container mx-auto px-4 py-8">
    @if(isset($breadcrumbs))
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    @endif

        <x-leadin :title="$title" :subtitle="$subtitle" />
aEYYO
</div>
