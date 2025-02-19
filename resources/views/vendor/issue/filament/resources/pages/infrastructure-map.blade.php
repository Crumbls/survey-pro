{{-- resources/views/vendor/filament/resources/pages/infrastructure-map.blade.php --}}
<x-filament::page>
    @pushOnce('styles')
        <link href="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.css" rel="stylesheet">
        <style>
            #infrastructure-map {
                width: 100%;
                height: {{ config('infrastructure.visualization.height') }};
                border: {{ config('infrastructure.visualization.border') }};
                border-radius: {{ config('infrastructure.visualization.borderRadius') }};
            }
        </style>
    @endPushOnce

    <div id="infrastructure-map"></div>

    @pushOnce('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const container = document.getElementById('infrastructure-map');
                const nodes = new vis.DataSet(@js($this->nodes));
                const edges = new vis.DataSet(@js($this->edges));

                const options = {
                    nodes: @js(config('infrastructure.visualization.nodes')),
                    edges: @js(config('infrastructure.visualization.edges')),
                    groups: @js(config('infrastructure.nodes.types')),
                    physics: false,
                    layout: {
                        hierarchical: {
                            enabled: true,
                            direction: 'LR',
                            sortMethod: 'directed',
                            levelSeparation: 250,
                            nodeSpacing: 100
                        }
                    },
                    interaction: @js(config('infrastructure.visualization.interaction'))
                };

                new vis.Network(container, { nodes, edges }, options);
            });
        </script>
    @endPushOnce
</x-filament::page>
