<!-- resources/views/analytics.blade.php -->
<x-layout>
    <div class="min-h-screen bg-gray-50 p-8">
        <div class="container mx-auto max-w-7xl">

            <div class="mb-4">
                <h1 class="text-2xl font-semibold text-gray-900">Page Builder</h1>
                <p class="text-gray-500">Drag and drop sections, rows, and columns to build your page.</p>
            </div>
            <div class="container mx-auto max-w-7xl gap-4 space-y-4">
            <div class="relative group">
                <div class=" transition-opacity duration-300">
                    <div class="flex items-center justify-center ">
                        <div
                            class="border-2 border-dashed border-gray-200 rounded-lg p-2 transition-all duration-300 hover:border-gray-300">
                            <button
                                class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="lucide lucide-plus w-4 h-4">
                                    <path d="M5 12h14"></path>
                                    <path d="M12 5v14"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Section content -->
            @include('builder.section')
                <div class="relative group">
                    <div class=" transition-opacity duration-300">
                        <div class="flex items-center justify-center ">
                            <div
                                class="border-2 border-dashed border-gray-200 rounded-lg p-2 transition-all duration-300 hover:border-gray-300">
                                <button
                                    class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round" class="lucide lucide-plus w-4 h-4">
                                        <path d="M5 12h14"></path>
                                        <path d="M12 5v14"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @include('builder.section')
            <div class="relative group">
                <div class=" transition-opacity duration-300">
                    <div class="flex items-center justify-center ">
                        <div
                            class="border-2 border-dashed border-gray-200 rounded-lg p-2 transition-all duration-300 hover:border-gray-300">
                            <button
                                class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="lucide lucide-plus w-4 h-4">
                                    <path d="M5 12h14"></path>
                                    <path d="M12 5v14"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-layout>
