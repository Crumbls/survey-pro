<x-layout-survey>
    <div class="container mx-auto px-4 py-8">

        <!-- Header -->
        <div class="max-w-2xl mx-auto">
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
                <style>
                    .svc-creator__banner {
                        display: none !important;
                    }
                    .sd-body.sd-completedpage:before,
                    .sd-body.sd-completedpage:after {
                        display: none !important
                    }
                    .sd-completedpage {
                        padding: 2rem 0;
                    }
                </style>

                <script>
                    document.addEventListener('alpine:init', () => {
                        Alpine.data('survey', () => ({
                            init() {
let surveyJson = {!! $surveyJson !!};


                                 console.log(surveyJson)
                                const survey = new Survey.Model(surveyJson);

                                 window.survey1 = survey;
// Track progress changes
                                survey.onValueChanged.add((sender, options) => {
                                    try {
                                        let dat = survey.getProgressInfo()
                                        let per = 100 / dat.questionCount * dat.answeredQuestionCount;

                                        const customEvent = new CustomEvent('survey-progress', {
                                            detail: {
                                                percentage: per,
                                            }
                                        });
                                        window.dispatchEvent(customEvent);

                                    } catch (e) {

                                    }
                                   // console.log(`Survey completion: ${progressValue}%`);

                                    // Optional: Update UI with progress
//                                    document.getElementById('progress-bar').value = progressValue;
                                });



                                survey.render(this.$refs.surveyContainer);


                                survey.onComplete.add((sender, options) => {
                                    console.log(survey.data);
                                });
                            }
                        }));
                    });
                </script>
            @endpush

            <div x-data="survey">
                <div x-ref="surveyContainer"></div>

            </div>
        </div>
    </div>
</x-layout-survey>
