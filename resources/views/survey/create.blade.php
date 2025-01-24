<x-layout>
    <div class="container mx-auto px-4 py-8">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-900">Create New Survey</h1>
            <p class="text-slate-600">Start by giving your survey a title and optional description</p>
        </div>
        <div class="max-w-2xl mx-auto">

            <!-- Form Card -->
            <div class="bg-white rounded-lg shadow-sm border border-slate-200">
                <form action="{{ route('surveys.store') }}" method="POST" class="p-6 space-y-6">
                    @csrf

                    <!-- Tenant Selection (if user has multiple tenants) -->
                    @if($tenants->count() > 1)
                        <div>
                            <label for="tenant_id" class="block text-sm font-medium text-slate-900 mb-1">
                                Select Tenant
                            </label>
                            <select
                                name="tenant_id"
                                id="tenant_id"
                                class="w-full rounded-md border-slate-200 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            >
                                <option value="">Select a tenant...</option>
                                @foreach($tenants as $tenant)
                                    <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                        {{ $tenant->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tenant_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-slate-900 mb-1">
                            Survey Title
                        </label>
                        <input
                            type="text"
                            name="title"
                            id="title"
                            value="{{ old('title') }}"
                            class="w-full rounded-md border-slate-200 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            placeholder="Enter survey title"
                            required
                        >
                        @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-900 mb-1">
                            Description (Optional)
                        </label>
                        <textarea
                            name="description"
                            id="description"
                            rows="4"
                            class="w-full rounded-md border-slate-200 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            placeholder="Enter survey description"
                        >{{ old('description') }}</textarea>
                        @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end space-x-4 pt-4">
                        <a
                            href="{{ route('surveys.index') }}"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 hover:text-slate-900"
                        >
                            Cancel
                        </a>
                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-medium text-sm text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                        >
                            Create Survey
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>
