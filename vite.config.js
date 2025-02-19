import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/report.css',
                'resources/js/app.js',
                'resources/js/survey-builder.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: "127.0.0.1",
        port: 3000,
    }
});
