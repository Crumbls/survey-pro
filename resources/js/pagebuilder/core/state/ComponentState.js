// core/state/ComponentState.js
export class ComponentState {
    constructor(initialState = {}) {
        this.local = reactive(initialState.local || {});
        this.inherited = reactive(initialState.inherited || {});
        this.shared = useSharedStore();
        this.watchers = new Set();
    }

    connect(parentState) {
        const watcher = watch(
            () => parentState.inherited,
            (newValue) => {
                this.inherited = {...this.inherited, ...newValue};
            },
            { deep: true }
        );
        this.watchers.add(watcher);
    }

    expose(key, value) {
        this.inherited[key] = value;
    }

    isolate() {
        this.inherited = {};
        this.watchers.forEach(unwatch => unwatch());
        this.watchers.clear();
    }

    dispose() {
        this.isolate();
        this.local = null;
        this.shared = null;
    }
}

// State Store Implementation
export const createComponentStore = () => {
    const state = reactive({
        components: new Map(),
        shared: {}
    });

    const actions = {
        setState(componentId, path, value) {
            const component = state.components.get(componentId);
            if (!component) return;
            set(component, path, value);
        },

        getState(componentId, path) {
            const component = state.components.get(componentId);
            if (!component) return undefined;
            return get(component, path);
        },

        setShared(path, value) {
            set(state.shared, path, value);
        },

        registerComponent(component) {
            state.components.set(component.id, component.state);
        },

        unregisterComponent(componentId) {
            state.components.delete(componentId);
        }
    };

    // Computed properties
    const getters = {
        getConnectedComponents(componentId) {
            return Array.from(state.components.entries())
                .filter(([id, state]) => state.inherited && id !== componentId);
        }
    };

    return {
        state,
        actions,
        getters
    };
};
