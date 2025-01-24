<x-layout>
    <div class="pt-16">
        <!-- Hero Section -->
        <section class="relative min-h-[80vh] flex items-center justify-center overflow-hidden bg-gradient-to-b from-slate-50 to-white">
            <div class="container px-4 mx-auto">
                <div class="max-w-4xl mx-auto text-center">
                <span class="inline-block px-4 py-1.5 mb-6 text-sm font-semibold bg-primary-50 text-primary-600 rounded-full">
                    Production Excellence Through Data
                </span>

                    <h1 class="text-4xl md:text-6xl font-bold mb-8 text-slate-900">
                        Optimize Production Performance with Smart Surveys
                    </h1>

                    <p class="text-xl text-slate-600 mb-10 leading-relaxed">
                        Transform your manufacturing processes with data-driven insights. Identify bottlenecks, improve efficiency, and boost production output through targeted surveys and real-time analytics.
                    </p>

                    <div class="flex flex-wrap justify-center gap-4">
                        <a href="#" class="inline-flex items-center justify-center gap-2 px-8 py-3 text-sm font-medium text-white bg-primary-600 rounded-md hover:bg-primary-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                            Boost Production Now
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>

                        <a href="#" class="inline-flex items-center justify-center px-8 py-3 text-sm font-medium border-2 border-slate-200 rounded-md hover:border-slate-300 transition-all duration-200">
                            View Success Stories
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-24 bg-white">
            <div class="container px-4 mx-auto">
                <div class="max-w-3xl mx-auto text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold mb-6 text-slate-900">
                        Maximize Production Performance
                    </h2>
                    <p class="text-xl text-slate-600">
                        Comprehensive tools to analyze, optimize, and enhance your manufacturing output
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach($features as $feature)
                        <div class="p-8 rounded-2xl bg-slate-50 hover:bg-slate-100 transition-all duration-200">
                            <div class="mb-6">
                                <img src="{{ $feature['icon'] }}"
                                     class="h-16 w-16"
                                     alt="{{ $feature['title'] }}">
                            </div>
                            <h3 class="text-xl font-semibold mb-4 text-slate-900">
                                {{ $feature['title'] }}
                            </h3>
                            <p class="text-slate-600 leading-relaxed">
                                {{ $feature['description'] }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <!-- Stats Section -->
        <section class="py-24 bg-gradient-to-b from-white to-slate-50">
            <div class="container px-4 mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 text-center">
                    <div class="p-8">
                <span x-data="counter('35')"
                      class="text-4xl md:text-5xl font-bold text-slate-900">
                    <span x-text="current"></span>%
                </span>
                        <p class="mt-4 text-lg text-slate-600">Production Increase</p>
                    </div>

                    <div class="p-8">
                <span x-data="counter('45000')"
                      class="text-4xl md:text-5xl font-bold text-slate-900">
                    <span x-text="current"></span>+
                </span>
                        <p class="mt-4 text-lg text-slate-600">Production Hours Saved</p>
                    </div>

                    <div class="p-8">
                <span x-data="counter('250')"
                      class="text-4xl md:text-5xl font-bold text-slate-900">
                    <span x-text="current"></span>+
                </span>
                        <p class="mt-4 text-lg text-slate-600">Manufacturing Plants</p>
                    </div>

                    <div class="p-8">
                <span x-data="counter('40')"
                      class="text-4xl md:text-5xl font-bold text-slate-900">
                    <span x-text="current"></span>%
                </span>
                        <p class="mt-4 text-lg text-slate-600">Efficiency Gain</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layout>
