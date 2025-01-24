@foreach($messages as $type => $message)
    <div x-data="{ show: true }"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         class="
        @class([
       'text-center py-4 lg:px-4 cursor-none',
//       'bg-green-50 border-green-200 text-green-800' => $type == 'success',
  //     'bg-red-50 border-red-200 text-red-800' => $type == 'error',
    //   'bg-yellow-50 border-yellow-200 text-yellow-800' => $type == 'warning',
      // 'bg-blue-50 border-blue-200 text-blue-800' => $type == 'status'
   ])">
        <div class="bg-primary-900 text-center py-4 lg:px-4">
            <div class="p-2 bg-primary-500 items-center text-indigo-100 leading-none lg:rounded-full flex lg:inline-flex" role="alert">
                <span class="flex rounded-full bg-primary-600  uppercase px-2 py-1 text-xs font-bold mr-3">
                    {{ \Str::title($type) }}
                </span>
                <span class="font-semibold mr-2 text-left flex-auto">
                    {{ $message }}
                </span>
            </div>
        </div>
    </div>
@endforeach
