<div class="relative group relative bg-builder-section border border-builder-border rounded-lg mb-8 transition-all duration-300">
    <div class="absolute -left-12 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
        <button class="p-2 hover:bg-builder-hover rounded transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 class="lucide lucide-grip-vertical w-4 h-4 text-gray-400">
                <circle cx="9" cy="12" r="1"></circle>
                <circle cx="9" cy="5" r="1"></circle>
                <circle cx="9" cy="19" r="1"></circle>
                <circle cx="15" cy="12" r="1"></circle>
                <circle cx="15" cy="5" r="1"></circle>
                <circle cx="15" cy="19" r="1"></circle>
            </svg>
        </button>
    </div>

    <div class="flex items-center justify-between p-2 bg-gray-50 rounded-t-lg border-b border-builder-border">
        <h3 class="text-xs text-gray-400 font-medium">Image Gallery</h3>
        <div class="flex items-center gap-1">
            <button class="p-1 hover:bg-builder-hover rounded transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                     stroke-linejoin="round" class="w-3 h-3 text-gray-400">
                    <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"></path>
                </svg>
            </button>
            <button class="p-1 hover:bg-builder-hover rounded transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                     stroke-linejoin="round" class="w-3 h-3 text-gray-400">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    <div class="p-4">
        <div class="space-y-4" x-data="{ images: [] }">
            <div class="grid grid-cols-2 gap-4">
                <template x-for="(image, index) in 4" :key="index">
                    <div class="aspect-square bg-gray-100 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-200 hover:border-gray-300 transition-colors cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="w-8 h-8 text-gray-400">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <path d="m21 15-5-5L5 21"></path>
                        </svg>
                    </div>
                </template>
            </div>
            <div class="flex justify-between items-center">
                <select class="text-sm border border-gray-200 rounded-md p-1">
                    <option>Grid Layout</option>
                    <option>Masonry</option>
                    <option>Carousel</option>
                </select>
                <button class="px-3 py-1 text-sm bg-gray-100 rounded hover:bg-gray-200">
                    Add Images
                </button>
            </div>
        </div>
    </div>
</div>
