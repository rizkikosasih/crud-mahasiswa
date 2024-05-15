import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
// import { resolve } from 'path';

export default defineConfig({
  // root: resolve(__dirname, 'resources/assets'),
  plugins: [
    laravel({
      input: ['resources/assets/js/app.js'],
      refresh: true
    })
  ]
});
