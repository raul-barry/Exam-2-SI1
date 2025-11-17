import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import fs from 'fs';
import path from 'path';

export default defineConfig({
    plugins: [
        react({
            // Optimizaciones para React sin dependencias adicionales
            fastRefresh: true,
        }),
        {
            name: 'copy-manifest',
            writeBundle() {
                const manifestPath = path.resolve('./public/build/.vite/manifest.json');
                const destPath = path.resolve('./public/build/manifest.json');
                
                if (fs.existsSync(manifestPath)) {
                    fs.copyFileSync(manifestPath, destPath);
                    console.log('✓ Manifest copiado a build/manifest.json');
                }
            }
        }
    ],

    build: {
        manifest: true,
        outDir: 'public/build',
        emptyOutDir: true,
        // Optimizaciones de build
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true
            }
        },
        // Optimizar chunk splitting
        chunkSizeWarningLimit: 1000,
        rollupOptions: {
            input: {
                main: 'resources/js/app.jsx'
            },
            output: {
                manualChunks: {
                    // Separar vendor chunks para mejor caching
                    'react-vendor': ['react', 'react-dom', 'react-router-dom'],
                    'axios-vendor': ['axios']
                }
            }
        }
    },
    
    server: {
        host: '127.0.0.1',
        port: 5173,
        strictPort: false,
        cors: true,
        hmr: {
            host: 'localhost',
            port: 5173,
            protocol: 'ws'
        }
    },
    
    // Optimizaciones adicionales
    optimizeDeps: {
        include: ['react', 'react-dom', 'react-router-dom', 'axios']
    }
});