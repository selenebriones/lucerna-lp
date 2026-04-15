import { defineConfig } from 'astro/config';
import tailwindcss from '@tailwindcss/vite';

// https://astro.build/config
export default defineConfig({
  // Se aplica la ruta del servidor solo cuando compilamos para produccion
  // En local (dev) se usa '/' para no romper el ambiente de pruebas
  base: process.env.NODE_ENV === 'production' ? '/paneles-solares-residenciales/' : '/',
  vite: {
    plugins: [tailwindcss()],
  },
});
