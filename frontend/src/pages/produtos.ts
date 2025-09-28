import api from "../api";
import Swal from "sweetalert2";
import { carregarLogin } from "./login";

interface Produto {
  id: number;
  nome: string;
  preco: number | string;
  descricao?: string;
}

let paginaAtual = 1;
const itensPorPagina = 5;
let termoBusca = "";
let filtroCampo = "nome"; // padrão

// ✅ Aplica o token do localStorage (se existir)
const token = localStorage.getItem("auth_token");
if (token) {
  api.defaults.headers.common["Authorization"] = `Bearer ${token}`;
}

export async function carregarProdutos(page: number = 1) {
  try {
    let response;

    if (termoBusca) {
      response = await api.get(`/produtos/search`, {
        params: {
          page,
          per_page: itensPorPagina,
          search: termoBusca,
          filter: filtroCampo,
        },
      });
    } else {
      response = await api.get(`/produtos`, {
        params: {
          page,
          per_page: itensPorPagina,
        },
      });
    }

    const data = response.data;
    const produtos: Produto[] = data.data;
    const totalPaginas = data.last_page;

    const app = document.getElementById("app");
    if (!app) return;

    let html = `
      <!-- 🏷️ Cabeçalho -->
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
        <h1 style="margin:0;">Gestão de Produtos</h1>
        <button id="btn-logout" style="background:#dc3545; color:white; border:none; padding:0.5rem 1rem; border-radius:5px; cursor:pointer;">
          🚪 Logout
        </button>
      </div>

      <!-- 🔍 Título da lista e filtro -->
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
        <h2 style="margin:0;">Lista de Produtos</h2>
        <div style="display:flex; gap:0.5rem;">
          <input type="text" id="campo-busca" placeholder="Buscar..." value="${termoBusca}" />
          <select id="campo-filtro">
            <option value="id" ${filtroCampo === "id" ? "selected" : ""}>ID</option>
            <option value="nome" ${filtroCampo === "nome" ? "selected" : ""}>Nome</option>
            <option value="preco" ${filtroCampo === "preco" ? "selected" : ""}>Preço</option>
            <option value="descricao" ${filtroCampo === "descricao" ? "selected" : ""}>Descrição</option>
          </select>
          <button id="btn-buscar">🔍 Buscar</button>
          <button id="btn-limpar">❌ Limpar</button>
          <button id="btn-adicionar">➕ Adicionar</button>
        </div>
      </div>

      <!-- 📋 Tabela -->
      <table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse:collapse;">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Preço</th>
            <th>Descrição</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
    `;

    produtos.forEach((p) => {
      const preco = typeof p.preco === "string" ? parseFloat(p.preco) : p.preco;

      html += `
        <tr>
          <td>${p.id}</td>
          <td>${p.nome}</td>
          <td>R$ ${preco.toFixed(2)}</td>
          <td>${p.descricao || ""}</td>
          <td>
            <button onclick="editarProduto(${p.id}, '${p.nome}', ${preco}, '${p.descricao || ""}')">✏️</button>
            <button onclick="excluirProduto(${p.id})">🗑️</button>
          </td>
        </tr>
      `;
    });

    html += `</tbody></table>`;

    // 📄 Paginação
    html += `<div class="paginacao" style="margin-top:1rem; text-align:center;">`;

    if (page > 1) {
      html += `<button onclick="carregarProdutos(${page - 1})">⬅️ Anterior</button>`;
    }

    const maxExibir = 5;
    let inicio = Math.max(1, page - 2);
    let fim = Math.min(totalPaginas, inicio + maxExibir - 1);

    if (fim - inicio < maxExibir - 1) {
      inicio = Math.max(1, fim - maxExibir + 1);
    }

    for (let i = inicio; i <= fim; i++) {
      if (i === page) {
        html += `<strong>[${i}]</strong> `;
      } else {
        html += `<button onclick="carregarProdutos(${i})">${i}</button> `;
      }
    }

    if (page < totalPaginas) {
      html += `<button onclick="carregarProdutos(${page + 1})">Próxima ➡️</button>`;
    }

    html += `</div>`;

    // ➕ Modal oculto
    html += `
      <div id="modal-produto" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; 
           background:rgba(0,0,0,0.5); justify-content:center; align-items:center;">
        <div style="background:#fff; padding:20px; border-radius:8px; width:300px;">
          <h3 id="modal-titulo">Adicionar Produto</h3>
          <form id="form-produto">
            <input type="hidden" id="produto-id" />
            <input type="text" id="nome" placeholder="Nome" required /><br/><br/>
            <input type="number" id="preco" placeholder="Preço" required step="0.01" /><br/><br/>
            <input type="text" id="descricao" placeholder="Descrição" /><br/><br/>
            <button type="submit">Salvar</button>
            <button type="button" id="fechar-modal">Cancelar</button>
          </form>
        </div>
      </div>
    `;

    app.innerHTML = html;

    // Bind dos botões
    document.getElementById("btn-buscar")?.addEventListener("click", async () => {
      termoBusca = (document.getElementById("campo-busca") as HTMLInputElement).value.trim();
      filtroCampo = (document.getElementById("campo-filtro") as HTMLSelectElement).value;
      await carregarProdutos(1);
    });

    document.getElementById("btn-limpar")?.addEventListener("click", async () => {
      termoBusca = "";
      filtroCampo = "nome";
      await carregarProdutos(1);
    });

    document.getElementById("btn-adicionar")?.addEventListener("click", () => {
      abrirModal();
    });

    document.getElementById("fechar-modal")?.addEventListener("click", () => {
      fecharModal();
    });

    // ✅ Logout
    document.getElementById("btn-logout")?.addEventListener("click", async () => {
      try {
        await api.post("/logout");
      } catch (e) {
        console.warn("Erro ao chamar logout na API:", e);
      }
      localStorage.removeItem("auth_token");
      Swal.fire("Até logo!", "Você saiu da aplicação.", "success").then(() => {
        carregarLogin();
      });
    });

    const form = document.getElementById("form-produto") as HTMLFormElement;
    form.onsubmit = async (e) => {
      e.preventDefault();
      await salvarProduto();
    };

    paginaAtual = page;
  } catch (error: any) {
    // ⚠️ Se deu erro 401, redireciona para login
    if (error.response?.status === 401) {
      Swal.fire({
        icon: "warning",
        title: "Sessão expirada",
        text: "Faça login novamente para continuar.",
      }).then(() => {
        localStorage.removeItem("auth_token");
        carregarLogin();
      });
    } else if (error.response?.status === 500) {
      Swal.fire({
        icon: "error",
        title: "Erro interno no servidor",
        text: "Ocorreu um erro no backend (500). Verifique os logs do Laravel.",
      }).then(() => {
        console.error("❌ Erro 500 no backend:", error.response?.data);
      });
    } else {
      Swal.fire({
        icon: "error",
        title: "Erro ao carregar produtos",
        text: error?.message || "Tente novamente mais tarde.",
      });
    }
  }
}

function abrirModal(id?: number, nome?: string, preco?: number, descricao?: string) {
  const modal = document.getElementById("modal-produto")!;
  (document.getElementById("produto-id") as HTMLInputElement).value = id ? id.toString() : "";
  (document.getElementById("nome") as HTMLInputElement).value = nome || "";
  (document.getElementById("preco") as HTMLInputElement).value = preco ? preco.toString() : "";
  (document.getElementById("descricao") as HTMLInputElement).value = descricao || "";

  document.getElementById("modal-titulo")!.textContent = id ? "Editar Produto" : "Adicionar Produto";
  modal.style.display = "flex";
}

function fecharModal() {
  const modal = document.getElementById("modal-produto")!;
  modal.style.display = "none";
}

async function salvarProduto() {
  const id = (document.getElementById("produto-id") as HTMLInputElement).value;
  const nome = (document.getElementById("nome") as HTMLInputElement).value;
  const preco = parseFloat((document.getElementById("preco") as HTMLInputElement).value);
  const descricao = (document.getElementById("descricao") as HTMLInputElement).value;

  try {
    if (id) {
      await api.put(`produtos/${id}`, { nome, preco, descricao });
      Swal.fire("✅ Sucesso", "Produto atualizado com sucesso!", "success");
    } else {
      await api.post("produtos", { nome, preco, descricao });
      Swal.fire("✅ Sucesso", "Produto criado com sucesso!", "success");
    }
    fecharModal();
    await carregarProdutos(paginaAtual);
  } catch (error: any) {
    Swal.fire({
      icon: "error",
      title: "Erro ao salvar produto",
      text: error?.response?.data?.message || "Tente novamente.",
    });
  }
}

;(window as any).editarProduto = (id: number, nome: string, preco: number, descricao: string) => {
  abrirModal(id, nome, preco, descricao);
};

;(window as any).excluirProduto = async (id: number) => {
  const confirmacao = await Swal.fire({
    title: "Tem certeza?",
    text: "Essa ação não poderá ser desfeita.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sim, excluir",
    cancelButtonText: "Cancelar",
  });

  if (!confirmacao.isConfirmed) return;

  try {
    await api.delete(`produtos/${id}`);
    Swal.fire("✅ Sucesso", "Produto excluído com sucesso!", "success");
    await carregarProdutos(paginaAtual);
  } catch (error: any) {
    Swal.fire({
      icon: "error",
      title: "Erro ao excluir produto",
      text: error?.response?.data?.message || "Tente novamente.",
    });
  }
};

;(window as any).carregarProdutos = carregarProdutos;
