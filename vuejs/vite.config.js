import { fileURLToPath, URL } from 'node:url'
import liveReload from 'vite-plugin-live-reload';
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: 
  [
    vue(),
    liveReload(`${__dirname}/**/*\.php`),
  ],
  
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  },

  build: {
    rollupOptions: {
      output: {
        assetFileNames: '[name].[ext]',
        chunkFileNames: '[name].js',
        entryFileNames: '[name].js'
      }
    },
    // sourcemap: true,
    // target: 'esnext'
  }


 


})
