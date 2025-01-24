// core/relationships/ContainerComponent.js
export class ContainerComponent extends BaseComponent {
    constructor(config) {
        super(config);
        this.childConstraints = {
            allowed: config.allowedChildren || [],
            maxDepth: config.maxDepth || Infinity,
            maxChildren: config.maxChildren || Infinity
        };
        this.children = new Map();
    }

    validateChild(child) {
        if (!this.childConstraints.allowed.includes(child.type)) {
            throw new Error(`Child type ${child.type} not allowed in ${this.type}`);
        }
        if (this.children.size >= this.childConstraints.maxChildren) {
            throw new Error(`Maximum children limit reached for ${this.type}`);
        }
        return true;
    }

    addChild(child, position) {
        this.validateChild(child);
        this.children.set(child.id, { component: child, position });
        this.notifyChildrenUpdated();
    }

    removeChild(childId) {
        this.children.delete(childId);
        this.notifyChildrenUpdated();
    }

    moveChild(childId, newPosition) {
        const child = this.children.get(childId);
        if (child) {
            child.position = newPosition;
            this.notifyChildrenUpdated();
        }
    }

    getChildren() {
        return Array.from(this.children.values())
            .sort((a, b) => a.position - b.position)
            .map(child => child.component);
    }

    notifyChildrenUpdated() {
        this.emit('children:updated', {
            componentId: this.id,
            children: this.getChildren()
        });
    }
}
