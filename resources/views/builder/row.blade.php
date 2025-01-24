<div
    class="relative group relative bg-builder-section border border-builder-border rounded-lg mb-8 transition-all duration-300">
    <div
        class="absolute -left-12 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
        <button class="p-2 hover:bg-builder-hover rounded transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 class="lucide lucide-grip-vertical w-4 h-4 text-gray-400"


            >
                <circle cx="9" cy="12" r="1"></circle>
                <circle cx="9" cy="5" r="1"></circle>
                <circle cx="9" cy="19" r="1"></circle>
                <circle cx="15" cy="12" r="1"></circle>
                <circle cx="15" cy="5" r="1"></circle>
                <circle cx="15" cy="19" r="1"></circle>
            </svg>
        </button>
    </div>
    <!-- Modal header -->
    <div class="
                flex items-center justify-between p-2 bg-gray-50 rounded-t-lg border-b border-builder-border
                ">
        @include('builder.header-title', ['text' => 'Row'])

        <div class="flex items-center gap-1">
            <button class="p-1 hover:bg-builder-hover rounded transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                     stroke-linejoin="round" class="lucide lucide-square-pen w-3 h-3 text-gray-400"


                >
                    <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path
                        d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"></path>
                </svg>
            </button>
            <button class="p-1 hover:bg-builder-hover rounded transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                     stroke-linejoin="round" class="lucide lucide-x w-3 h-3 text-gray-400"


                >
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                </svg>
            </button>
        </div>
    </div>
    <!-- Modal body -->
    <div class="px-2 space-y-4">

        @include('builder.button-add')

        <div class="flex flex-col md:flex-row gap-4">
            @if(rand(0,1))
                @include('builder.column', ['width' => 'w-full'])
            @else
                @include('builder.column', ['width' => 'w-full md:w-1/3'])
                @include('builder.column', ['width' => 'w-full md:w-2/3'])
            @endif
        </div>

        <!-- End of Widget Interior -->

        @include('builder.button-add')

    </div>
</div>
