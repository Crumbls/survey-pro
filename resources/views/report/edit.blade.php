<!DOCTYPE html>
<html>
<head>
    <title>GrapesJS Editor</title>
    <link rel="stylesheet" href="https://unpkg.com/grapesjs/dist/css/grapes.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <script src="https://unpkg.com/grapesjs"></script>
    @vite(['resources/js/grapesjs/editor.js'])
    <style>
        body {
            margin: 0;
            padding: 0;
        }
        .editor-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        .panel__top {
            padding: 0;
            width: 100%;
            display: flex;
            position: initial;
            justify-content: center;
            justify-content: space-between;
        }
        .panel__basic-actions {
            position: initial;
        }
        .editor-row {
            display: flex;
            justify-content: flex-start;
            align-items: stretch;
            flex-grow: 1;
        }
        .panel__left {
            flex-basis: 230px;
            position: relative;
            overflow-y: auto;
            background-color: #f5f5f5;
        }
        .panel__right {
            flex-basis: 230px;
            position: relative;
            overflow-y: auto;
            background-color: #f5f5f5;
        }
        .panel__switcher {
            position: initial;
        }
        .editor-canvas {
            flex-grow: 1;
        }
        #gjs {
            border: none;
        }
        /* Reset some GrapesJS default styles */
        .gjs-cv-canvas {
            width: 100%;
            height: 100%;
            top: 0;
        }
        .gjs-block {
            width: auto;
            height: auto;
            min-height: auto;
        }
        .gjs-blocks-cs {
            border: none;
            height: 100%;
        }
    </style>
</head>
<body>
<div class="editor-container">
    <div class="panel__top">
        <div class="panel__basic-actions"></div>
        <div class="panel__devices"></div>
        <div class="panel__switcher"></div>
    </div>
    <div class="editor-row">
        <div class="panel__left">
            <div id="blocks"></div>
        </div>
        <div class="editor-canvas">
            <div id="gjs">
                <h1>Welcome to GrapesJS</h1>
                <p>This is the default content</p>
            </div>
        </div>
        <div class="panel__right">
            <div class="styles-container"></div>
            <div class="traits-container"></div>
            <div class="layers-container"></div>
        </div>
    </div>
</div>
<script>
    window.grapesJsConfig = {!! json_encode([
            'components' => app(App\Services\ComponentRegistry::class)->all(),
            'uploadUrl' => route('grapesjs.upload'),
            'csrfToken' => csrf_token(),
        ]) !!};
</script>
</body>
</html>
