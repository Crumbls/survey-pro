<x-layout>
    <div
        x-data="surveyCreatorApp({{ Js::from($record) }})"
        x-init="initSurveyCreator()"
        class="py-8 md:pt-18"
    >

        {{-- Survey Creator Container --}}
        <div id="surveyCreatorContainer" style="height: 100vh;" class=""></div>

        {{-- Save Status Indicator --}}

    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script>
        <!-- ... -->
        <!-- SurveyJS Form Library resources -->
        <link  href="https://unpkg.com/survey-core/defaultV2.min.css" type="text/css" rel="stylesheet">
        <script src="https://unpkg.com/survey-core/survey.core.min.js"></script>
        <script src="https://unpkg.com/survey-js-ui/survey-js-ui.min.js"></script>

        <!-- (Optional) Predefined theme configurations -->
        <script src="https://unpkg.com/survey-core/themes/index.min.js"></script>

        <!-- Survey Creator resources -->
        <link  href="https://unpkg.com/survey-creator-core/survey-creator-core.min.css" type="text/css" rel="stylesheet">
        <script src="https://unpkg.com/survey-creator-core/survey-creator-core.min.js"></script>
        <script src="https://unpkg.com/survey-creator-js/survey-creator-js.min.js"></script>        <script>
            function surveyCreatorApp(initialSurvey) {
                return {
                    creator: null,
                    saveStatus: '',
                    survey: initialSurvey,

                    initSurveyCreator() {
                        // Initialize SurveyJS Creator
                        const options = {
                            showLogicTab: true,
                            showTranslationTab: true
                        };
                        this.creator = new SurveyCreator.SurveyCreator(options);



// Method 3: Using events
                        this.creator.onNotify.add((sender, options) => {
                            console.log(sender);
                            console.log(options);
                            // Prevent default notification
                            options.cancel = true;

                            // Custom notification handling
                            var message = options.message;
                            const type = options.type;

                            switch(message) {
                                case "Modified":
                                    message = 'Saved';
                                    break;
                                default:
                                    console.log(message);

                            }


                            new FilamentNotification()
                                .title(message)
                                .icon('heroicon-o-document-text')
                                .iconColor(type)
                                .send()
                            return;

                            // Your custom notification logic here
                            // Example: using a custom notification library
                            customNotify(message, {
                                type: type,
                                duration: 3000,
                                position: 'top-right'
                            });
                        });


                        // Render the creator
                        this.creator.render('surveyCreatorContainer');

                        // Load existing survey JSON if available
                        if (this.survey && this.survey.questions) {
                            this.creator.JSON = JSON.parse(this.survey.questions);
                        }

                        // Auto-save functionality
                        this.creator.onModified.add(() => {
                            this.debouncedSave();
                        });
                    },

                    debouncedSave: _.debounce(function() {
                        this.saveSurvey();
                    }, 1000),

                    async saveSurvey() {
                        this.saveStatus = 'Saving...';

                        try {
                            const surveyDefinition = JSON.stringify(this.creator.JSON);

                            const response = await fetch(`{{ route('surveys.update', $record) }}`, {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({ definition: surveyDefinition })
                            });

                            if (!response.ok) {
                                throw new Error('Failed to save survey');
                            }

                            this.saveStatus = 'Saved';

                            // Reset save status after 3 seconds
                            setTimeout(() => {
                                this.saveStatus = '';
                            }, 3000);
                        } catch (error) {
                            this.saveStatus = 'Error';
                            console.error('Save failed:', error);
                        }
                    }
                };
            }
        </script>
        <style>
            .svc-creator__banner {
                display: none !important;
            }
        </style>
    @endpush
</x-layout>
