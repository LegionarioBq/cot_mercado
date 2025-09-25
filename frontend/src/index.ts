import api from "./api";

async function carregarProdutos() {
  try {
    const response = await api.get("/produtos");
    const produtos = response.data;

    const app = document.getElementById("app");
    if (app) {
      app.innerHTML = `
        <h2>Lista de Produtos</h2>
        <ul>
          ${produtos.data
            .map((p: any) => `<li>${p.nome} - R$ ${p.preco}</li>`)
            .join("")}
        </ul>
      `;
    }
  } catch (error) {
    console.error("Erro ao buscar produtos:", error);
    const app = document.getElementById("app");
    if (app) {
      app.innerHTML = `<p style="color:red;">Erro ao carregar produtos</p>`;
    }
  }
}

// Carregar ao iniciar
carregarProdutos();
