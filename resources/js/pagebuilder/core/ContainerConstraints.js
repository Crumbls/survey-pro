import {ContainerTypes} from "./ContainerTypes.js";

export const ContainerConstraints = {
    [ContainerTypes.GRID]: {
        maxChildren: 12,
        allowedChildren: ['*'],
        childProperties: {
            colSpan: { type: 'number', min: 1, max: 12 },
            rowSpan: { type: 'number', min: 1, max: 12 }
        }
    },
    [ContainerTypes.FLEX]: {
        maxChildren: 20,
        allowedChildren: ['*'],
        childProperties: {
            grow: { type: 'number', min: 0 },
            shrink: { type: 'number', min: 0 },
            basis: { type: 'string' }
        }
    }
    // ... other container type definitions
};
