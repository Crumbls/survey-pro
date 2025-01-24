export const ComponentInterface = {
    // Required Properties
    id: String,
    type: String,
    label: String,
    version: String,

    // Metadata
    metadata: {
        author: String,
        description: String,
        category: String,
        tags: Array,
        icon: String,
        documentation: String
    },

    // Configuration Schema
    schema: {
        properties: Object,
        required: Array,
        validation: Object
    },

    // Required Methods
    init() {},
    mount() {},
    unmount() {},
    update(newProps) {},
    validate() {},
    render() {},

    // State Management
    state: {
        local: Object,
        inherited: Object,
        shared: Object
    },

    // Lifecycle Hooks
    hooks: {
        beforeMount: Array,
        afterMount: Array,
        beforeUpdate: Array,
        afterUpdate: Array,
        beforeUnmount: Array
    }
};

