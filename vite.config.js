import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    server: {
        host: '0.0.0.0', // Listen on all interfaces
        hmr: { host: 'localhost' },
        //watch: { usePolling: true },
    },
    build: {
        rollupOptions: {
            // disable HTMX warnings
            onwarn(warning, warn) {
                if (warning.code === 'EVAL' && /[\\/]node_modules[\\/]htmx\.org[\\/]/.test(warning.id)) {
                    return;
                }
                warn(warning);
            }
        }
    },
    resolve: {
        alias: {
            '@tk-base': path.resolve(__dirname, 'packages/ttek/tk-base/resources'),
            //'$': 'jQuery',
        },
    },
    plugins: [
        laravel({
            refresh: true,
            input: [
                'resources/scss/app.scss',
                'resources/js/app.js',
            ],
        }),
    ],
});
