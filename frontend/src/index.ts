import { carregarLogin } from "./pages/login";
import { carregarProdutos } from "./pages/produtos";

document.addEventListener("DOMContentLoaded", async () => {
  console.log("🚀 Aplicação iniciada...");

  const token = localStorage.getItem("auth_token");

  if (token) {
    console.log("🔑 Token encontrado, testando no backend...");

    try {
      await carregarProdutos(); // tenta carregar com token
    } catch (error) {
      console.error("❌ Erro ao validar token, voltando para login:", error);
      localStorage.removeItem("auth_token");
      carregarLogin();
    }
  } else {
    console.log("🟡 Nenhum token encontrado, indo para login...");
    carregarLogin();
  }
});
