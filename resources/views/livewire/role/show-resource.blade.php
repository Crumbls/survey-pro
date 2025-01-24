<div class="container mx-auto px-4 py-8">
    @if(isset($breadcrumbs))
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    @endif
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Centers</h1>
        <p class="text-slate-600">
            Organizational units that allow you to group users, manage assessments, and maintain data separation between different departments, locations, or distinct user groups.
        </p>
    </div>

    {{ $record }}
</div>
