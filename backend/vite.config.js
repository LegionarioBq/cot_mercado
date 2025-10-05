import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],

    // ✅ Adicionado para funcionar corretamente dentro do Docker
    server: {
        host: '0.0.0.0',       // permite acesso externo (Docker -> navegador)
        port: 5173,            // mesma porta usada no docker-compose
        strictPort: true,      // evita fallback para outra porta
        watch: {
            usePolling: true,  // força reload mesmo com volume montado
        },
        cors: true,            // permite chamadas da API no backend
    },

    // ✅ Mantém compatibilidade para testes de build (modo preview)
    preview: {
        host: '0.0.0.0',
        port: 5173,
    },
});
