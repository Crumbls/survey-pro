<div class="container mx-auto px-4 py-8">
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Survey Reports</h1>
        <p class="text-slate-600">Build custom reports that drive decisions.</p>
    </div>

    {{ $this->table }}
</div>
