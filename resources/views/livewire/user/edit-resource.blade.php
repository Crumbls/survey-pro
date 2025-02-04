<div class="container mx-auto px-4 py-8">
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <!-- Header -->
    <x-leadin :title="$title" :subtitle="$subtitle" />


    <div class="">

        <!-- Form Card -->
        <div class="">
            @livewire('user.list-tenant-resource', ['user' => $user])

        </div>
    </div>
</div>
