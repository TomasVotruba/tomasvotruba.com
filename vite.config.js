import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import hljs from 'highlight.js';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.scss',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
    ],
});
