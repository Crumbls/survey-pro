<!-- Text Block Widget -->
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
        <h3 class="text-xs text-gray-400 font-medium">Text Block</h3>
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
        <div class="space-y-4" x-data="{ content: '' }">
            <input type="text" placeholder="Heading"
                   class="w-full p-2 border border-gray-200 rounded-md text-lg font-semibold">
            <textarea placeholder="Enter your content here..."
                      class="w-full p-2 border border-gray-200 rounded-md min-h-32"
                      x-model="content"></textarea>
            <div class="flex gap-2">
                <button class="px-3 py-1 text-sm bg-gray-100 rounded hover:bg-gray-200 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="w-4 h-4 mr-1">
                        <path d="M4 7V4h16v3"></path>
                        <path d="M9 20h6"></path>
                        <path d="M12 4v16"></path>
                    </svg>
                    Format
                </button>
            </div>
        </div>
    </div>
</div>
