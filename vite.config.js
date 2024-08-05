import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    // base: process.env.APP_URL,
    // server: {
    //     proxy: {
    //       '/api': {
    //         target: 'https://192.168.60.35:5173',
    //         changeOrigin: true,
    //         secure: false,
    //       },
    //     },
    //   },
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
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
    server: {
        // https: {
        //     key: fs.readFileSync(path.resolve(__dirname, 'storage/app/ssl/privat_key.key')),
        //     cert: fs.readFileSync(path.resolve(__dirname, 'storage/app/ssl/certificate.crt')),
        // },
        host: '192.168.60.35',
        port: 5173,
    },
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
});
