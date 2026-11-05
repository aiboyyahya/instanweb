import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/css/filament/admin.css",
                "resources/js/app.js",
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: "0.0.0.0",
        port: "5173",
        strictPort: true,

        hmr: {
            host: "172.14.10.208",
        },

        watch: {
            usePolling: true,
        },
    },
});
