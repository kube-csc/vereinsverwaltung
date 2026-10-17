import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { createRequire } from 'node:module';

// Tailwind 3 läuft nur für das Backend (Vite). Das Frontend nutzt weiterhin
// Laravel Mix mit Tailwind 2 (Paket "tailwindcss").
const require = createRequire(import.meta.url);

// Die Tailwind-3-Plugins greifen intern auf 	ailwindcss zu und würden sonst das
// Tailwind-2-Paket des Frontends laden. Diese Zugriffe auf Tailwind 3 umleiten.
const Module = require('node:module');
const originalResolve = Module._resolveFilename;
Module._resolveFilename = function (request, parent, ...rest) {
    if (/^tailwindcss(\/|$)/.test(request) && /tailwind-(forms|typography)-v3/.test(parent?.filename ?? '')) {
        request = request.replace(/^tailwindcss/, 'tailwindcss-v3');
    }
    return originalResolve.call(this, request, parent, ...rest);
};
const tailwindcss = require('tailwindcss-v3');
const autoprefixer = require('autoprefixer');

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/admin.css', 'resources/js/admin.js'],
            refresh: true,
        }),
    ],
    css: {
        postcss: {
            plugins: [
                tailwindcss({ config: './tailwind.admin.config.cjs' }),
                autoprefixer(),
            ],
        },
    },
});