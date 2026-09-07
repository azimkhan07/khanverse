import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import path from 'path'
import { fileURLToPath } from 'url'

const __dirname = path.dirname(fileURLToPath(import.meta.url))

export default defineConfig({
  plugins: [react()],
  base: '/build/',
  server: {
    proxy: {
      '/api': 'http://127.0.0.1:8000',
    },
  },
  css: {
    postcss: {
      plugins: [],
    },
  },
  resolve: {
    alias: {
      '@shared': path.resolve(__dirname, 'src/shared'),
    },
  },
  build: {
    outDir: '../../public/build',
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: {
        main: path.resolve(__dirname, 'src/main.jsx'),
        admin: path.resolve(__dirname, 'src/admin/main.jsx'),
        seller: path.resolve(__dirname, 'src/seller/main.jsx'),
        buyer: path.resolve(__dirname, 'src/buyer/main.jsx'),
      },
      output: {
        manualChunks(id) {
          if (id.includes('node_modules')) {
            if (id.includes('react-dom')) return 'vendor-react-dom';
            if (id.includes('react')) return 'vendor-react';
            if (id.includes('react-router')) return 'vendor-router';
            return 'vendor';
          }
        },
      },
    },
  },
})
