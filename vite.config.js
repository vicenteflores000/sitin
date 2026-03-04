import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@fullcalendar/core/index.global.js': path.resolve(__dirname, 'node_modules/@fullcalendar/core/index.global.js'),
            '@fullcalendar/daygrid/index.global.js': path.resolve(__dirname, 'node_modules/@fullcalendar/daygrid/index.global.js'),
            '@fullcalendar/timegrid/index.global.js': path.resolve(__dirname, 'node_modules/@fullcalendar/timegrid/index.global.js'),
            '@fullcalendar/interaction/index.global.js': path.resolve(__dirname, 'node_modules/@fullcalendar/interaction/index.global.js'),
        },
    },
});
