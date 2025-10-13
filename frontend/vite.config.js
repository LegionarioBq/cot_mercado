import { defineConfig } from "vite";

export default defineConfig({
  root: "src", // diretório de entrada
  build: {
    outDir: "../dist", // saída da build
    emptyOutDir: true
  },
  server: {
    port: 5173, // frontend roda em http://localhost:5173
  },
});
