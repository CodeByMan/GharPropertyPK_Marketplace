import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  define: {
    __APP_NAME__: JSON.stringify('GharProperty.pk'),
  },
  plugins: [
    laravel({
      input: ['resources/js/app.js', 'resources/css/app.css'],
      refresh: true,
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
  ],
})
