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
        registerPlugin(window.Alpine);
    } else {
        // Alpine isn't loaded yet, wait for the init event
        document.addEventListener('alpine:init', () => {
            registerPlugin(window.Alpine);
        });
    }

    /**
     * Register all plugin features with the Alpine instance
     */
    function registerPlugin(Alpine) {

        /**
         * Initialize our Event Bus
         */
        var eventBus = {
            events: {},
            wildcardEvents: {},

            /**
             * Subscribe to an event
             * @param {string} event - Event name
             * @param {Function} callback - Function to call when event is triggered
             * @return {Function} - Unsubscribe function
             */
            on: function(event, callback) {
                // Handle wildcard subscriptions
                if (event.includes('*')) {
                    var patternStr = '^' + event.replace(/\*/g, '.*') + '$';
                    var pattern = new RegExp(patternStr);

                    if (!this.wildcardEvents[event]) {
                        this.wildcardEvents[event] = {
                            pattern: pattern,
                            callbacks: []
                        };
                    }

                    this.wildcardEvents[event].callbacks.push(callback);

                    // Return unsubscribe function
                    var self = this;
                    return function() {
                        self.wildcardEvents[event].callbacks =
                            self.wildcardEvents[event].callbacks.filter(function(cb) {
                                return cb !== callback;
                            });
                    };
                } else {
                    // Standard event subscription
                    if (!this.events[event]) {
                        this.events[event] = [];
                    }

                    this.events[event].push(callback);

                    // Return unsubscribe function
                    var self = this;
                    return function() {
                        self.events[event] = self.events[event].filter(function(cb) {
                            return cb !== callback;
                        });
                    };
                }
            },

            /**
             * Subscribe to an event but only trigger once
             * @param {string} event - Event name
             * @param {Function} callback - Function to call when event is triggered
             */
            once: function(event, callback) {
                var self = this;
                var unsubscribe = this.on(event, function(payload) {
                    unsubscribe();
                    callback(payload);
                });

                return unsubscribe;
            },

            /**
             * Emit an event
             * @param {string} event - Event name
             * @param {any} payload - Data to pass to subscribers
             * @returns {boolean} - Whether any handlers were called
             */
            emit: function(event, payload) {
                var handled = false;

                // Call exact event matches
                if (this.events[event]) {
                    var callbacks = this.events[event];
                    for (var i = 0; i < callbacks.length; i++) {
                        callbacks[i](payload);
                        handled = true;
                    }
                }

                // Call matching wildcard handlers
                var wildcardKeys = Object.keys(this.wildcardEvents);
                for (var j = 0; j < wildcardKeys.length; j++) {
                    var key = wildcardKeys[j];
                    var wildcardEvent = this.wildcardEvents[key];
                    var pattern = wildcardEvent.pattern;
                    var wildcardCallbacks = wildcardEvent.callbacks;

                    if (pattern.test(event)) {
                        for (var k = 0; k < wildcardCallbacks.length; k++) {
                            wildcardCallbacks[k](payload, event);
                            handled = true;
                        }
                    }
                }

                return handled;
            },

            /**
             * Remove all subscriptions for an event
             * @param {string} event - Event name
             */
            off: function(event) {
                if (event.includes('*')) {
                    delete this.wildcardEvents[event];
                } else {
                    delete this.events[event];
                }
            },

            /**
             * Remove all event subscriptions
             */
            clear: function() {
                this.events = {};
                this.wildcardEvents = {};
            }
        };

        // Register the event bus as a magic property so it's accessible anywhere
        Alpine.magic('bus', function() {
            return {
                // Subscribe to an event
                on: function(event, callback) {
                    return eventBus.on(event, callback);
                },

                // Subscribe once
                once: function(event, callback) {
                    return eventBus.once(event, callback);
                },

                // Emit an event
                emit: function(event, payload) {
                    return eventBus.emit(event, payload);
                },

                // Remove all handlers for an event
                off: function(event) {
                    eventBus.off(event);
                },

                // Remove all event handlers
                clear: function() {
                    eventBus.clear();
                },

                // Chain events (call second event after first one fires)
                chain: function(triggerEvent, nextEvent) {
                    var busRef = eventBus;
                    return eventBus.on(triggerEvent, function(payload) {
                        busRef.emit(nextEvent, payload);
                    });
                },

                // Create a channel for namespaced events
                channel: function(namespace) {
                    var ns = namespace;
                    var busRef = eventBus;
                    return {
                        on: function(event, callback) {
                            return busRef.on(ns + '.' + event, callback);
                        },
                        once: function(event, callback) {
                            return busRef.once(ns + '.' + event, callback);
                        },
                        emit: function(event, payload) {
                            return busRef.emit(ns + '.' + event, payload);
                        },
                        off: function(event) {
                            busRef.off(ns + '.' + event);
                        },
                        clear: function() {
                            // Use wildcard to clear all events in this namespace
                            busRef.off(ns + '.*');
                        }
                    };
                }
            };
        });

        // Register a custom component
        Alpine.data('chat', (config = {}) => ({
            open: false,
            messages: [],
            selectedTab: null,
            agent: {
                enabled: false
            },
            ticket: {
                enabled: false
            },
            knowledgebase: {
                enabled: true,
                query: ''
            },

            init() {
                // Define default config inside init
                const defaults = {
                    open: false,
                    agent: {
                        enabled: false
                    },
                    ticket: {
                        enabled: false
                    },
                    knowledgebase: {
                        enabled: false
                    },
                };

                // Simple merge function for configuration
                const mergeConfig = (defaults, config) => {
                    const result = { ...defaults };

                    // Handle first level properties
                    for (const key in config) {
                        // For objects, do a shallow merge
                        if (typeof config[key] === 'object' && config[key] !== null &&
                            typeof result[key] === 'object' && result[key] !== null) {
                            result[key] = { ...result[key], ...config[key] };
                        } else {
                            // For simple values, just override
                            result[key] = config[key];
                        }
                    }

                    return result;
                };

                const settings = mergeConfig(defaults, config);


                console.log(this.ticket);
                console.log(settings.ticket);

                this.applyConfig(this, settings);

                console.log(this.ticket);

                if (!this.selectedTab) {
                    this.selectedTab = this.firstServiceEnabled();
                }

                this.$bus.on('load', function() {
                    console.log('test');
                });

                // You can also emit events
                this.$bus.emit('load', {
                    chat: this
                });
            },

            /**
             * Knowledgebase Methods
             */
            search() {

            },


            /**
             * Chat Window Methods
             */
            chatOpen() {
                if (!this.open) {
                    this.$bus.emit('open');
                }
                this.open = true;

            },
            chatClose() {
                if (this.open) {
                    this.$bus.emit('hidden');
                }
                this.open = false;
            },
            chatToggle() {
                if (this.open) {
                    this.$bus.emit('hidden');
                } else {
                    this.$bus.emit('open');
                }
                this.open = !this.open;
            },
            applyConfig(target, source) {
                Object.keys(source).forEach(key => {
                    // Check if the property exists in the target (whether own property or not)
                    if (key in target) {
                        // For objects, merge recursively
                        if (typeof source[key] === 'object' && source[key] !== null &&
                            typeof target[key] === 'object' && target[key] !== null) {

                            // Recursive call for nested objects
                            this.applyConfig(target[key], source[key]);
                        } else {
                            target[key] = source[key];
                        }
                    }
                });
            },
            firstServiceEnabled() {
                // Define the order to check
                const itemsToCheck = ['agent', 'ticket', 'knowledgebase'];

                // Find the first enabled item
                return itemsToCheck.find(key => this[key] && this[key].enabled === true);
            },


        }));
    }
})();
