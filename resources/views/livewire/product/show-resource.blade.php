<div class="container mx-auto px-4 py-8">
    @if(isset($breadcrumbs))
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    @endif
    <x-leadin :title="$title" :subtitle="$subtitle" :updateUrl="$updateUrl"/>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="space-y-4 gap-4">
            @if($media = $record->getFirstMedia('logo'))
                {{ $media }}
                @endif
            <h2 class="text-2xl">
                {{ $record->name }}
            </h2>
            <p>
                {{ $record->description }}
            </p>
        </div>

    </div>

</div>
