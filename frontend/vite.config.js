import { defineConfig } from "vite";

export default defineConfig({
  root: "src", // diretório de entrada
  build: {
    outDir: "../dist", // saída da build
    emptyOutDir: true
  },
  server: {
    port: 3000, // frontend roda em http://localhost:3000
  },
});
