import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/activity.css',
                'resources/css/app.css',
                'resources/css/auth-card.css',
                'resources/css/auth.css',
                'resources/css/forget.css',
                'resources/css/homepage.css',
                'resources/css/login.css',
                'resources/css/main.css',
                'resources/js/app.js',
                'resources/js/bootstrap.js',
                'resources/js/searchfunction.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
