// core/registry/ComponentRegistry.js
export class ComponentRegistry {
    constructor() {
        this.components = new Map();
        this.middleware = [];
        this.extensions = new Set();
    }

    register(component) {
        this.validateComponent(component);
        this.applyMiddleware(component);
        this.components.set(component.type, component);
    }

    registerMiddleware(middleware) {
        this.middleware.push(middleware);
    }

    registerExtension(extension) {
        this.validateExtension(extension);
        this.extensions.add(extension);
    }

    create(type, config) {
        const Component = this.components.get(type);
        if (!Component) throw new Error(`Component type ${type} not found`);
        return new Component(config);
    }

    validateComponent(component) {
        // Validate component implements required interface
        const required = ['init', 'mount', 'unmount', 'update', 'validate', 'render'];
        for (const method of required) {
            if (typeof component[method] !== 'function') {
                throw new Error(`Component ${component.type} missing required method: ${method}`);
            }
        }
    }

    applyMiddleware(component) {
        return this.middleware.reduce(
            (wrapped, middleware) => middleware(wrapped),
            component
        );
    }
}
