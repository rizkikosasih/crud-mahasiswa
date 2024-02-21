import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/assets/js/app.js', 'resources/assets/js/custom.js'],
      refresh: true
    })
  ]
});
