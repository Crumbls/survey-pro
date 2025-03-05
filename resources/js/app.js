import './bootstrap';

// Instead of importing Alpine and starting it
// import Alpine from 'alpinejs'
// window.Alpine = Alpine
// Alpine.start()
if (!window.Alpine) {
    import('alpinejs').then((Alpine) => {
        window.Alpine = Alpine.default;

        console.log(window.Alpine);


        // Define our Alpine components after Alpine is loaded
        defineAlpineComponents();
        window.Alpine.start();
    });
} else {
    // If Alpine is already loaded by Filament, just define our components
    defineAlpineComponents();
}

// Just add your custom Alpine data/components
function defineAlpineComponents() {

    document.addEventListener('alpine:init', () => {
        Alpine.data('counter', (endValue, duration = 2000) => ({
            current: 0,
            endValue: endValue,
            isNumeric: !isNaN(parseFloat(endValue)) && isFinite(endValue),
            init() {
                // Your existing counter logic
            }
        }));

        // Dashboard stats data
        Alpine.data('dashboardStats', () => ({
            loading: true,
            selectedCard: null,
            stats: null,
            async loadStats() {
                await new Promise(resolve => setTimeout(resolve, 1500));
                this.stats = {
                    dailyOutput: {value: '2,543', change: '12', trend: 'up'},
                    activeWorkers: {value: '128', details: 'Across 3 shifts'},
                    uptime: {value: '98.2', status: 'above'},
                    efficiency: {value: '94.8', period: 'Last 24 hours'}
                };
                this.loading = false;
            }
        }));

        // Add any other Alpine.data components you need
    });
}
