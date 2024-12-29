<x-layout>
    <div class="container mx-auto px-4 py-8 md:pt-28">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-900">Create a Survey Collector</h1>
            <p class="text-slate-600">Set up your survey distribution goals</p>
        </div>
        <div class="max-w-2xl mx-auto">

            <!-- Form Card -->
            <div class="bg-white rounded-lg shadow-sm border border-slate-200">
                <form action="{{ route('collectors.store') }}" method="POST" class="p-6 space-y-6">
                    @csrf

                    <!-- Tenant Selection (only if multiple tenants) -->
                    @if($surveys->count() > 1)
                        <div>
                            <label for="survey_id" class="block text-sm font-medium text-slate-900 mb-1">
                                Select Organization
                            </label>
                            <select
                                name="survey_id"
                                id="survey_id"
                                class="w-full rounded-md border-slate-200 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                required
                            >
                                <option value="">Select an organization...</option>
                                @foreach($surveys as $survey)
                                    <option value="{{ $survey->getKey() }}" {{ old('survey_id') == $survey->getKey() ? 'selected' : '' }}>
                                        {{ $survey->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('survey_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @else
                        <input type="hidden" name="survey_id" value="{{ $surveys->first()->getKey() }}" />
                    @endif

                    <!-- Reference Code with Prefix -->
                    <div>
                        <label for="reference" class="block text-sm font-medium text-slate-900 mb-1">
                            Reference Code
                        </label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-slate-200 bg-slate-50 text-slate-500 text-sm">
                                /r/
                            </span>
                            @php($defaultReference = 'a')
                            <input
                                type="text"
                                name="reference"
                                id="reference"
                                value="{{ old('reference', $defaultReference) }}"
                                class="flex-1 min-w-0 block rounded-none rounded-r-md border-slate-200 focus:border-teal-500 focus:ring-teal-500"
                                pattern="[a-zA-Z0-9]+"
                                maxlength="250"
                                required
                            >
                        </div>
                        <p class="mt-1 text-sm text-slate-500">Only letters and numbers allowed, no spaces or special characters</p>
                        @error('reference')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Survey Goal -->
                    <div>
                        <label for="goal" class="block text-sm font-medium text-slate-900 mb-1">
                            Target Response Count
                        </label>
                        <input
                            type="number"
                            name="goal"
                            id="goal"
                            value="{{ old('goal') }}"
                            class="w-full rounded-md border-slate-200 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                            min="1"
                            required
                            placeholder="Enter target number of responses"
                        >
                        @error('goal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end space-x-4 pt-4 border-t border-slate-200">
                        <a
                            href="{{ route('collectors.index') }}"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 hover:text-slate-900"
                        >
                            Cancel
                        </a>
                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-md font-medium text-sm text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                        >
                            Create Target
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>
