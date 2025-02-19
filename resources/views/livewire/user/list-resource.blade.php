<div class="container mx-auto px-4 py-8">
    @if(isset($breadcrumbs))
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    @endif

    <x-leadin :title="$title" :subtitle="$subtitle" />

        @if(!$this->tenant)
            @php($tenants = \Illuminate\Support\Facades\Auth::user()->tenants)
            @if($tenants->isEmpty())
                <p>test</p>
                @else
                <h3 class="text-xl font-bold text-slate-900 flex items-center space-x-4 mb-4">
                    Select a {{ __('tenants.singular') }} to view it's users.
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @if($tenants->isEmpty())
                        <p>test</p>
                    @else
                        @foreach($tenants as $tenant)

                            <!-- Clients -->
                            <div class="bg-white rounded-lg p-6 border border-slate-200">
                                <a href="{{ route('tenants.users.index', $tenant) }}" class="block text-slate-600 text-sm block text-center">
                        {{ $tenant->name }}
                                </a>

                            </div>
                        @endforeach
                    @endif

                </div>
            @endif
        @else
    {{ $this->table }}
        @endif
</div>
