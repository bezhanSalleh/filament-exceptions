import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';
export default defineConfig({
    plugins: [tailwindcss()],
    build: {
        outDir: 'resources/dist',
        rollupOptions: {
            input: ['resources/css/styles.css', 'resources/js/scripts.js'],
            output: {
                assetFileNames: '[name][extname]',
                entryFileNames: '[name].js',
            },
        },
    },
});
