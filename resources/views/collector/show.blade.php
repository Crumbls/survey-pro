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
                            survey: null,
                            reference: null,
                            percentageComplete: 0,
                            saving: false,
                            savingAutomatic: false,
                            init() {
                                let surveyJson = {!! $surveyJson !!};

                                this.survey = new Survey.Model(surveyJson);

                                 window.survey1 = this.survey;
// Track progress changes
//                                survey.onCompleting.add(async (sender, options) => { console.log(options); })

                                this.survey.onValueChanged.add((sender, options) => {
                                    try {
                                        let dat = this.survey.getProgressInfo()
                                        this.percentageComplete = 100 / dat.questionCount * dat.answeredQuestionCount;
                                            const customEvent = new CustomEvent('survey-progress', {
                                            detail: {
                                                percentage: this.percentageComplete,
                                            }
                                        });
                                        window.dispatchEvent(customEvent);
                                        this.savingAutomatic = true;
                                        this.debouncedSave();
                                    } catch (e) {
                                    }
                                });

                                this.survey.render(this.$refs.surveyContainer);

                                window.test2 = this.saveSurvey;

                                this.survey.onComplete.add((sender, options) => {
                                    this.percentageComplete = 100;
                                    const customEvent = new CustomEvent('survey-progress', {
                                        detail: {
                                            percentage: this.percentageComplete,
                                        }
                                    });
                                    window.dispatchEvent(customEvent);
                                    this.savingAutomatic = false;
                                    this.saveSurvey();
                                });
                            },

                            debouncedSave: _.debounce(function() {
                                this.saveSurvey();
                            }, 2000),

                            async getReference() {
                                if (this.reference) {
                                    return this.reference;
                                }
                                try {
                                    let $this = this;
                                    let dat = await fetch('{{ route('responses.create', $record) }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                        },
                                        body: JSON.stringify({
                                            // Add your data here
                                        })
                                    });
                                    dat = await dat.json();
                                    $this.reference = await dat.id;
                                    return $this.reference;
                                } catch (e) {
                                    console.log(e);
                                }
                            },

                            async saveSurvey() {

                                let $this = this;

                                if ($this.saving) {
                                    console.log('Saving skipped, already active.');
                                    return;
                                }

                                /**
                                 * Check to see if we have a unique saving id.
                                 */
                                $this.saveStatus = 'Saving...';
                                $this.saving = true;

                                const surveyDefinition = this.survey.data;

                                this.getReference()
                                    .then(function(reference) {

                                        let response = fetch('{{ url('responses') }}/' + reference, {
                                            method: 'PATCH',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                            },
                                            body: JSON.stringify({
                                                data: surveyDefinition
                                            })
                                        }).then(function(res) {
                                            return res.json();
                                        }).then(function(res) {

                                            // Reset save status after 3 seconds
                                            setTimeout(() => {
                                                $this.saveStatus = '';
                                            }, 3000);

                                            if (res.status == 'success') {

                                                this.saveStatus = 'Saved';

                                                if (!$this.savingAutomatic) {
                                                    new FilamentNotification()
                                                        .title('Saved successfully')
                                                        .icon('heroicon-o-document-text')
                                                        .iconColor('success')
                                                        .send()
                                                }
                                            } else {
                                                console.log(res);
                                            }

                                            $this.saving = false;
                                        });



//                                        console.log(response);
                                        return;

                                        if (!response.ok) {
                                            throw new Error('Failed to save survey');
                                        }

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
