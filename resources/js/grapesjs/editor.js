import grapesjs from 'grapesjs';
const escapeName = (name) => `${name}`.trim().replace(/([^a-z0-9\w-:/]+)/gi, '-');
const editor = grapesjs.init({
    container: '#gjs',
    height: '100%',
    width: 'auto',
    fromElement: true,
    storageManager: false,
    selectorManager: { escapeName },
    plugins: ['grapesjs-tailwind'],
/*
    selectorManager: { componentFirst: true },

    // Text editing configuration
    canvas: {
        styles: [
            'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap',
        ],
    },

    // Component configuration
    domComponents: {
        defaultTypes: {
            text: {
                updateOnChange: true,
                keepUnlocked: true,
                // Preserve content on blur/deselect
                onBlur(e) {
                    const content = component.get('content');
                    console.log(content);
                    this.set('content', content);
                }
            }
        },
        defaultOptions: {
            type: 'text',
            style: { padding: '10px' },
            activable: true,
            highlightable: true,
            resizable: true,
            editable: true,
        }
    },

    // Rich text editor configuration
    richTextEditor: {
        actions: ['bold', 'italic', 'underline', 'strikethrough', 'link'],
        keepUnlocked: true,
    },

    // Additional configuration to prevent text clearing
    canvas: {
        scripts: [],
        styles: ['https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap'],
    },

    // Storage to prevent content loss
    storageManager: {
        type: 'local',
        autosave: true,
        autoload: true,
        stepsBeforeSave: 1
    },

    // Panel Configuration
    panels: {
        defaults: [
            {
                id: 'basic-actions',
                buttons: [
                    {
                        id: 'visibility',
                        active: true,
                        className: 'btn-toggle-borders',
                        label: '<i class="fa fa-square-o"></i>',
                        command: 'sw-visibility'
                    },
                    {
                        id: 'export',
                        className: 'btn-open-export',
                        label: 'Exp',
                        command: 'export-template',
                        context: 'export-template'
                    },
                    {
                        id: 'show-json',
                        className: 'btn-show-json',
                        label: 'JSON',
                        context: 'show-json',
                        command(editor) {
                            editor.Modal.setTitle('Components JSON')
                                .setContent(`<textarea style="width:100%; height: 250px;">
                                    ${JSON.stringify(editor.getComponents(), null, 2)}
                                </textarea>`)
                                .open();
                        }
                    }
                ]
            },
            {
                id: 'panel-devices',
                buttons: [
                    {
                        id: 'device-desktop',
                        label: '<i class="fa fa-television"></i>',
                        command: 'set-device-desktop',
                        active: true,
                        togglable: false
                    },
                    {
                        id: 'device-tablet',
                        label: '<i class="fa fa-tablet"></i>',
                        command: 'set-device-tablet'
                    },
                    {
                        id: 'device-mobile',
                        label: '<i class="fa fa-mobile"></i>',
                        command: 'set-device-mobile'
                    }
                ]
            }
        ]
    },

    // Device Manager Configuration
    deviceManager: {
        devices: [{
            name: 'Desktop',
            width: '', // Set default width
        }, {
            name: 'Tablet',
            width: '768px',
            widthMedia: '992px',
        }, {
            name: 'Mobile',
            width: '320px',
            widthMedia: '480px',
        }]
    },

    // Block Manager Configuration
    blockManager: {
        appendTo: '#blocks',
        blocks: [
            {
                id: 'section',
                label: '<b>Section</b>',
                attributes: { class: 'gjs-block-section' },
                content: `<section>
                    <h1>This is a section</h1>
                    <div>This is a div element inside section</div>
                </section>`
            },
            {
                id: 'text',
                label: 'Text',
                content: '<div data-gjs-type="text">Insert your text here</div>'
            },
            {
                id: 'image',
                label: 'Image',
                select: true,
                content: { type: 'image' },
                activate: true
            }
        ]
    },

    // Style Manager Configuration
    styleManager: {
        sectors: [
            {
                name: 'Dimension',
                open: false,
                buildProps: ['width', 'height', 'min-width', 'min-height', 'padding', 'margin']
            },
            {
                name: 'Typography',
                open: false,
                buildProps: ['font-family', 'font-size', 'font-weight', 'letter-spacing', 'color', 'line-height', 'text-align']
            },
            {
                name: 'Decorations',
                open: false,
                buildProps: ['background-color', 'border', 'border-radius', 'box-shadow']
            },
            {
                name: 'Extra',
                open: false,
                buildProps: ['opacity', 'transition']
            }
        ]
    },

    // Layer Manager Configuration
    layerManager: {
        appendTo: '.layers-container'
    },

    // Storage Configuration
    storageManager: {
        type: 'local',
        autosave: true,
        autoload: true,
        stepsBeforeSave: 1
    },

    // Asset Manager Configuration
    assetManager: {
        upload: window.grapesJsConfig.uploadUrl,
        headers: {
            'X-CSRF-TOKEN': window.grapesJsConfig.csrfToken
        }
    },

    textEditMode: 'inline',
    */
});

// Register components from PHP config
Object.entries(window.grapesJsConfig.components).forEach(([name, config]) => {
    // Register component type
    editor.DomComponents.addType(name, {
        isComponent: el => el.tagName === config.tagName,
        model: {
            defaults: {
                ...config,
                traits: config.traits,
                // Ensure text content is preserved
                content: config.content || '',
                // Add text-specific options
                textable: true,
                highlightable: true,
                resizable: true,
                editable: true,
                // Prevent content from being cleared
                removable: false,
                draggable: true,
                droppable: true,
                // Keep component active after deselection
                activeOnRender: true
            }
        }
    });

    // Add block for the component
    editor.BlockManager.add(name, {
        label: config.label,
        category: config.category,
        content: config.content,
        attributes: config.attributes || {}
    });
});

// Add custom category to block manager
editor.BlockManager.add('my-category', {});

if (false) {
// Add event listeners for text preservation
    editor.on('component:selected', component => {
        if (component.get('type') === 'text') {
            const content = component.get('content');
            if (content) {
                component.set('content', content);
            }
        }
    });

    editor.on('component:update', component => {
        if (component.get('type') === 'text') {
            const content = component.get('content');
            if (content) {
                component.set('content', content);
            }
        }
    });
}
