<div class="container mx-auto px-4 py-8">
    @if(isset($breadcrumbs))
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    @endif
        <x-leadin :title="$title" :subtitle="$subtitle" :updateUrl="$updateUrl"/>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <x-client.survey-count :record="$record" />
            <x-client.collector-count :record="$record" />
            <x-client.report-count :record="$record" />

        </div>

</div>
