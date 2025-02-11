import { Model } from 'survey-core/survey.core';
import { SurveyCreator } from 'survey-creator-core/survey-creator-core';

export default () => ({
    creator: null,
    init() {
        const creatorOptions = {
            showLogicTab: true,
            isAutoSave: true
        };

        this.creator = new SurveyCreator(creatorOptions);
        this.creator.render(this.$el);

        // Optional: Load an existing survey
        // this.creator.text = JSON.stringify(yourSurveyJSON);

        // Optional: Save survey
        this.creator.saveSurveyFunc = (saveNo, callback) => {
            // Implement your save logic here
            const json = this.creator.text;
            // Example: Send to your Laravel backend
            fetch('/api/surveys', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: json
            })
                .then(response => response.json())
                .then(data => {
                    callback(saveNo, data.success);
                })
                .catch(error => {
                    callback(saveNo, false);
                    console.error('Error saving survey:', error);
                });
        };
    }
});
