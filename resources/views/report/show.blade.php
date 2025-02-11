        <x-layout>
            <div class="container mx-auto px-4 py-8">
                <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
                <x-leadin :title="$title" :subtitle="$subtitle" />

                @vite('resources/css/report.css')

                <section class="report flex flex-col gap-y-4" id="report-{{ $record->getKey() }}">
                @if(Auth::id())
                    @php((array)$record->data)
                    @foreach($record->data as $idx => $chunk)
                        @php($componentName = 'report.'.$chunk['type'])
                    @if(View::exists('components.'.$componentName))

                    <x-dynamic-component
                        :component="$componentName"
                        :data="array_key_exists('data', $chunk) ? $chunk['data'] : []"
                        :record="$record"
                    />
                        @else
                        <div>
                            <p>
                                Component does not exist: {{ $componentName  }}

                            </p>
                        </div>

                        @endif
                    @endforeach
                </section>
                    @else
                <!-- Coming Soon Card -->
                <div class="max-w-2xl mx-auto">
                    <div class="bg-white rounded-lg p-8 border border-slate-200 text-center">
                        <!-- Report Icon -->
                        <div class="mb-6 flex justify-center">
                            <img src="{{ asset('images/icons/reports.svg') }}"
                                 class="h-16 w-16 text-primary-500"
                                 alt="Reports">
                        </div>

                        <h2 class="text-2xl font-bold text-slate-900 mb-3">Report Coming Soon</h2>
                        <p class="text-slate-600 mb-8 max-w-md mx-auto">
                            We're developing powerful Report tools to provide deep insights into your manufacturing processes and help drive data-informed decisions.
                        </p>

                        <!-- Feature Preview List -->
                        <div class="text-left max-w-md mx-auto">
                            <h3 class="font-medium text-slate-900 mb-3">Upcoming Report Features:</h3>
                            <ul class="space-y-3">
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 mr-3 mt-1 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <span class="font-medium text-slate-900">Real-time Performance Metrics</span>
                                        <p class="text-sm text-slate-600">Monitor production efficiency and performance in real-time</p>
                                    </div>
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 mr-3 mt-1 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <span class="font-medium text-slate-900">Predictive Report</span>
                                        <p class="text-sm text-slate-600">AI-powered insights to forecast production trends and maintenance needs</p>
                                    </div>
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 mr-3 mt-1 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <span class="font-medium text-slate-900">Custom Dashboards</span>
                                        <p class="text-sm text-slate-600">Build and customize Report dashboards for your specific needs</p>
                                    </div>
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 mr-3 mt-1 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <span class="font-medium text-slate-900">Advanced Data Visualization</span>
                                        <p class="text-sm text-slate-600">Interactive charts and graphs for deeper understanding of your data</p>
                                    </div>
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 mr-3 mt-1 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <span class="font-medium text-slate-900">Export & Integration</span>
                                        <p class="text-sm text-slate-600">Easy data export and integration with other business tools</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                    @endif
            </div>
        </x-layout>

