/**
 * Standalone Alpine.js Plugin for Laravel/Filament
 *
 * This is a self-initializing plugin that doesn't require explicit importing
 * in your app.js file. It will automatically register with Alpine when loaded.
 */
(function() {
    // Check if Alpine is already available
    if (window.Alpine) {
        // Alpine is already loaded, register immediately
        console.log('a');
        registerPlugin(window.Alpine);
    } else {
        // Alpine isn't loaded yet, wait for the init event
        document.addEventListener('alpine:init', () => {
            console.log('b');
            registerPlugin(window.Alpine);
        });
    }

    /**
     * Register all plugin features with the Alpine instance
     */
    function registerPlugin(Alpine) {
        console.log('alive');
        // Register a new directive
        Alpine.directive('chat', (el, { value, modifiers, expression }, { evaluate }) => {
            // This runs when the directive is encountered
            el.innerHTML = `Directive value: ${value || evaluate(expression)}`;
        });

        // Register a new magic property
        Alpine.magic('chat', (el) => {
            return {
                greet(name) {
                    return `Hello, ${name}!`;
                },

                get count() {
                    return 42;
                }
            };
        });

        // Register a store for state management
        Alpine.store('myStore', {
            // State
            items: [],

            // Getters
            get count() {
                return this.items.length;
            },

            // Actions
            addItem(item) {
                this.items.push(item);
            },

            removeItem(index) {
                this.items.splice(index, 1);
            }
        });

        // Register a custom component
        Alpine.data('chat', () => ({
            open: false,

            toggle() {
                this.open = !this.open;
            },

            init() {
                console.log('My component initialized');
            }
        }));
    }
})();
