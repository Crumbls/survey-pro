<!-- resources/views/dashboard.blade.php -->
<x-layout>
    <div class="container mx-auto px-4 py-8">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs"/>

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-900">
                {{ __('tenants.singular_edit') }}: {{ $record->name }}
            </h1>
            <p class="text-slate-600">
                {{ __('tenants.description') }}
            </p>
        </div>

        <div x-data="{ selectedTab: 'basic' }" class="w-full block md:flex md:flex-row space-x-4">
            <div x-on:keydown.right.prevent="$focus.wrap().next()" x-on:keydown.left.prevent="$focus.wrap().previous()"
                 class="flex flex-row md:flex-col gap-2 overflow-x-auto border-slate-300 dark:border-slate-700
                 w-full md:w-1/4"
                 role="tablist" aria-label="tab options">
                <!-- basic Section -->
                <div class="mb-8">
                    <h2 class="text-gray-900 font-medium mb-4 hidden">General</h2>

                    <!-- Basic Information Section -->
                    <div class="mb-4">

                        <button class="flex items-center px-4 py-2 rounded-md mb-2 text-sm py-1 w-full"
                                class="block text-sm py-1"
                                x-on:click="selectedTab = 'basic'"
                                x-bind:aria-selected="selectedTab === 'basic'"
                                x-bind:tabindex="selectedTab === 'basic' ? '0' : '-1'"
                                x-bind:class="selectedTab === 'basic' ? 'font-bold  border-l-2 border-primary-700 dark:border-primary-600 dark:text-primary-600 text-blue-600 bg-blue-50 ' : 'text-gray-600 hover:text-gray-900 hover:border-l-2 hover:border-b-slate-800 hover:text-black'"
                                type="button"
                                role="tab"
                                aria-controls="tabpanelBasic"
                        >
                            <span class="text-sm font-medium">Basic Information</span>
                        </button>

                        <button class="flex items-center px-4 py-2 rounded-md mb-2 text-sm py-1 w-full"
                                class="block text-sm py-1"
                                x-on:click="selectedTab = 'address'"
                                x-bind:aria-selected="selectedTab === 'address'"
                                x-bind:tabindex="selectedTab === 'address' ? '0' : '-1'"
                                x-bind:class="selectedTab === 'address' ? 'font-bold  border-l-2 border-primary-700 dark:border-primary-600 dark:text-primary-600 text-blue-600 bg-blue-50 ' : 'text-gray-600 hover:text-gray-900 hover:border-l-2 hover:border-b-slate-800 hover:text-black'"
                                type="button"
                                role="tab"
                                aria-controls="tabpanelBasic"
                        >
                        <span class="text-sm font-medium">
                            Address
                        </span>
                        </button>


                    </div>
                </div>

                <!-- Options Section -->
                <div class="hidden">
                    <h2 class="text-gray-900 font-medium mb-4">Options</h2>

                    <div class="space-y-2 pl-4">
                        <a href="#" class="block text-gray-600 hover:text-gray-900 text-sm py-1">
                            Mailing Lists
                        </a>
                        <a href="#" class="block text-gray-600 hover:text-gray-900 text-sm py-1">
                            View Mailings
                        </a>
                        <a href="#" class="block text-gray-600 hover:text-gray-900 text-sm py-1">
                            Tasks
                        </a>
                        <a href="#" class="block text-gray-600 hover:text-gray-900 text-sm py-1">
                            Notes
                        </a>
                        <a href="#" class="block text-gray-600 hover:text-gray-900 text-sm py-1">
                            Audit Log
                        </a>
                    </div>
                </div>
            </div>
            <div class="w-full md:w-3/4">
                <div class="px-2 py-4 text-slate-700 dark:text-slate-300">
                    <div x-cloak x-show="selectedTab === 'basic'" id="tabpanelBasic" role="tabpanel"
                         aria-label="basic">
                        @if(isset($record))
                        <livewire:tenant.edit-resource
                            :model-id="$record->getKey()"
                            :model-type="App\Models\Tenant::class"  />
                        @endif
                    </div>
                    <div x-cloak x-show="selectedTab === 'address'" id="tabpanelAddress" role="tabpanel"
                    @if(isset($record))
                        <livewire:tenant.edit-address
                            :model-id="$record->getKey()"
                            :model-type="App\Models\Tenant::class"  />
                    @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layout>
