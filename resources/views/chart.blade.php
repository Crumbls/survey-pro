<!DOCTYPE html>
<html>
<head>
    <title>Network Architecture Demo</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.css" rel="stylesheet">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
        }
        #mynetwork {
            width: 100%;
            height: 100vh;
        }
    </style>
</head>
<body>
<div id="mynetwork"></div>
<script>
    const SITE_COUNT = 20;
    const SERVER_COUNT = 5;
    const DATABASE_COUNT = 3;

    const ERROR_MESSAGES = [
        'Error 503: Service Unavailable',
        'SSL Certificate Expired',
        'Error 504: Gateway Timeout',
        'DNS Resolution Failed',
        'Connection Refused'
    ];

    const nodes = new vis.DataSet();
    const edges = new vis.DataSet();

    // Add sites
    for (let i = 0; i < SITE_COUNT; i++) {
        const status = Math.random() < 0.9 ? 'green' : (Math.random() < 0.5 ? 'red' : 'yellow');
        nodes.add({
            id: `site_${i}`,
            label: `Site ${i}`,
            group: 'sites',
            color: status === 'green' ? '#00ff00' : status === 'red' ? '#ff0000' : '#ffff00',
            status: status,
            title: status !== 'green' ? ERROR_MESSAGES[Math.floor(Math.random() * ERROR_MESSAGES.length)] : 'Status: Operational'
        });
    }

    // Add servers and databases
    for (let i = 0; i < SERVER_COUNT; i++) {
        nodes.add({
            id: `server_${i}`,
            label: `Server ${i}`,
            group: 'servers',
            title: 'Server Status: Online'
        });
    }

    for (let i = 0; i < DATABASE_COUNT; i++) {
        nodes.add({
            id: `db_${i}`,
            label: `Database ${i}`,
            group: 'databases',
            title: 'Database Status: Connected'
        });
    }

    const allNodes = nodes.get();
    const sites = allNodes.filter(node => node.group === 'sites');
    const servers = allNodes.filter(node => node.group === 'servers');
    const databases = allNodes.filter(node => node.group === 'databases');

    const connectionColors = {};
    servers.forEach(server => connectionColors[server.id] = new Set());
    databases.forEach(db => connectionColors[db.id] = new Set());

    sites.forEach(site => {
        const server = servers[Math.floor(Math.random() * servers.length)];
        const database = databases[Math.floor(Math.random() * databases.length)];

        edges.add({
            from: site.id,
            to: server.id,
            color: { color: site.color }
        });
        edges.add({
            from: site.id,
            to: database.id,
            color: { color: site.color }
        });

        connectionColors[server.id].add(site.status);
        connectionColors[database.id].add(site.status);
    });

    [...servers, ...databases].forEach(node => {
        const colors = connectionColors[node.id];
        let nodeColor = '#00ff00';
        let status = 'Operational';
        if (colors.has('red')) {
            nodeColor = '#ff0000';
            status = 'Critical Issues Detected';
        }
        else if (colors.has('yellow')) {
            nodeColor = '#ffff00';
            status = 'Warning: Performance Issues';
        }
        nodes.update({
            id: node.id,
            color: nodeColor,
            title: `${node.group === 'servers' ? 'Server' : 'Database'} Status: ${status}`
        });
    });

    const container = document.getElementById("mynetwork");
    const data = { nodes, edges };
    const options = {
        nodes: {
            shape: "dot",
            size: 16
        },
        physics: {
            enabled: true,
            stabilization: false,
            barnesHut: {
                gravitationalConstant: -2000,
                springLength: 150,
                springConstant: 0.04,
                damping: 0.09
            }
        },
        groups: {
            sites: { shape: 'dot' },
            servers: { shape: 'box', size: 25 },
            databases: { shape: 'database', size: 25 }
        }
    };

    new vis.Network(container, data, options);
</script>
</body>
</html>
