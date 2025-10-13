import api from "../api";
import Swal from "sweetalert2";
import { carregarLogin } from "./login";

interface ProdutoImage {
  path: string;
  url: string;
}

interface Produto {
  id: number;
  nome: string;
  preco: number | string;
  quantidade: number;
  descricao?: string;
  imagens?: ProdutoImage[];
}

let paginaAtual = 1;
const itensPorPagina = 5;
let termoBusca = "";
let filtroCampo = "nome";

// Usa URL vinda do .env do Vite
const API_URL = import.meta.env.VITE_API_URL || "http://localhost:8000/api";

//Aplica token salvo no localStorage (para manter sessão)
const token = localStorage.getItem("auth_token");
if (token) {
  api.defaults.headers.common["Authorization"] = `Bearer ${token}`;
}

export async function carregarProdutos(page: number = 1) {
  try {
    let response;

    // Se tiver busca, usa endpoint /search
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

    // ====== Layout da página ======
    let html = `
      <!-- Cabeçalho -->
      <div class="header">
        <h1>Gestão de Produtos</h1>
        <button id="btn-logout" class="btn-logout">🚪 Logout</button>
      </div>

      <!-- Barra de ações -->
      <div class="toolbar">
        <h2>Lista de Produtos</h2>
        <div class="actions">
          <input type="text" id="campo-busca" placeholder="Buscar..." value="${termoBusca}" />
          <select id="campo-filtro">
            <option value="id" ${filtroCampo === "id" ? "selected" : ""}>ID</option>
            <option value="nome" ${filtroCampo === "nome" ? "selected" : ""}>Nome</option>
            <option value="preco" ${filtroCampo === "preco" ? "selected" : ""}>Preço</option>
            <option value="quantidade" ${filtroCampo === "quantidade" ? "selected" : ""}>Quantidade</option>
            <option value="descricao" ${filtroCampo === "descricao" ? "selected" : ""}>Descrição</option>
          </select>
          <button id="btn-buscar">🔍 Buscar</button>
          <button id="btn-limpar">❌ Limpar</button>
          <button id="btn-adicionar">➕ Adicionar</button>
        </div>
      </div>

      <!-- Tabela -->
      <table>
        <thead>
          <tr>
            <th class="center">ID</th>
            <th class="center">Imagem</th>
            <th>Nome</th>
            <th>Preço</th>
            <th class="center">Quantidade</th>
            <th>Descrição</th>
            <th class="center">Ações</th>
          </tr>
        </thead>
        <tbody>
    `;

    // ====== Linhas da tabela ======
    produtos.forEach((p) => {
      const preco = typeof p.preco === "string" ? parseFloat(p.preco) : p.preco;
      const imgUrl = p.imagens && p.imagens.length > 0 ? p.imagens[0].url : "";

      html += `
        <tr>
          <td class="center">${p.id}</td>
          <td class="center">
            ${imgUrl ? `<img src="${imgUrl}" alt="produto" class="thumb"/>` : "—"}
          </td>
          <td>${p.nome}</td>
          <td>${new Intl.NumberFormat("pt-BR", {
            style: "currency",
            currency: "BRL",
          }).format(preco)}</td>
          <td class="center">${p.quantidade}</td>
          <td>${p.descricao || ""}</td>
          <td class="center">
            <button onclick="visualizarProduto(${p.id})">👁️</button>
            <button onclick="editarProduto(${p.id}, '${p.nome}', ${preco}, ${p.quantidade}, '${p.descricao || ""}')">✏️</button>
            <button onclick="excluirProduto(${p.id})">🗑️</button>
          </td>
        </tr>
      `;
    });

    html += `</tbody></table>`;

    // ====== Paginação ======
    html += `<div class="paginacao">`;
    if (page > 1) html += `<button onclick="carregarProdutos(${page - 1})">⬅️ Anterior</button>`;
    for (let i = 1; i <= totalPaginas; i++) {
      html += i === page
        ? `<strong>[${i}]</strong> `
        : `<button onclick="carregarProdutos(${i})">${i}</button> `;
    }
    if (page < totalPaginas) html += `<button onclick="carregarProdutos(${page + 1})">Próxima ➡️</button>`;
    html += `</div>`;

    // ====== Modais ======
    html += `
      <div id="modal-produto" class="modal">
        <div class="modal-content">
          <h3 id="modal-titulo">Adicionar Produto</h3>
          <form id="form-produto" enctype="multipart/form-data" class="modal-form">
            <input type="hidden" id="produto-id" />
            <input type="text" id="nome" placeholder="Nome" required />
            <input type="text" id="preco" placeholder="Preço" required />
            <input type="number" id="quantidade" placeholder="Quantidade" required min="0"/>
            <input type="text" id="descricao" placeholder="Descrição" maxlength="255"/>
            <small id="desc-contador">0 / 255</small>

            <div class="file-upload">
              <input type="file" id="imagem" accept="image/png,image/jpeg,image/webp" hidden />
              <label for="imagem" class="file-label">📁 Enviar imagem</label>
              <span id="file-name">Nenhum arquivo escolhido</span>
            </div>

            <div class="modal-actions">
              <button type="submit">Salvar</button>
              <button type="button" id="fechar-modal">Cancelar</button>
            </div>
          </form>
        </div>
      </div>

      <div id="modal-visualizar" class="modal">
        <div class="modal-content">
          <h3>Detalhes do Produto</h3>
          <img id="view-img" src="" class="thumb"/>
          <p id="view-desc"></p>
          <button id="fechar-visualizar">Fechar</button>
        </div>
      </div>
    `;

    app.innerHTML = html;

    // ====== Eventos ======
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

    document.getElementById("btn-adicionar")?.addEventListener("click", () => abrirModal());
    document.getElementById("fechar-modal")?.addEventListener("click", () => fecharModal());
    document.getElementById("fechar-visualizar")?.addEventListener("click", () => {
      document.getElementById("modal-visualizar")!.style.display = "none";
    });

    // ====== Logout ======
    document.getElementById("btn-logout")?.addEventListener("click", async () => {
      try {
        await api.post("/logout");
        localStorage.removeItem("auth_token");
        localStorage.removeItem("user_type");
        delete api.defaults.headers.common["Authorization"];
        await Swal.fire({
          icon: "success",
          title: "Sessão encerrada",
          text: "Você saiu da aplicação com sucesso.",
          timer: 1800,
          showConfirmButton: false,
        });
        carregarLogin();
      } catch {
        localStorage.removeItem("auth_token");
        delete api.defaults.headers.common["Authorization"];
        carregarLogin();
      }
    });

    // Upload: mostrar nome do arquivo
    const fileInput = document.getElementById("imagem") as HTMLInputElement;
    const fileNameSpan = document.getElementById("file-name") as HTMLElement;
    fileInput?.addEventListener("change", () => {
      fileNameSpan.textContent = fileInput.files?.[0]?.name || "Nenhum arquivo escolhido";
    });

    // Máscara de preço
    const precoInput = document.getElementById("preco") as HTMLInputElement;
    precoInput?.addEventListener("input", () => {
      let value = precoInput.value.replace(/\D/g, "");
      precoInput.value = value
        ? new Intl.NumberFormat("pt-BR", { style: "currency", currency: "BRL" }).format(parseFloat(value) / 100)
        : "";
    });

    // Contador de descrição
    const descInput = document.getElementById("descricao") as HTMLInputElement;
    const contador = document.getElementById("desc-contador") as HTMLElement;
    descInput?.addEventListener("input", () => {
      contador.textContent = `${descInput.value.length} / 255`;
    });

    // Submissão do formulário
    const form = document.getElementById("form-produto") as HTMLFormElement;
    form.onsubmit = async (e) => {
      e.preventDefault();
      await salvarProduto();
    };

    paginaAtual = page;
  } catch (error: any) {
    if (error.response?.status === 401) {
      await Swal.fire({
        icon: "warning",
        title: "Sessão expirada",
        text: "Faça login novamente para continuar.",
      });
      localStorage.removeItem("auth_token");
      delete api.defaults.headers.common["Authorization"];
      carregarLogin();
    } else if (error.response?.status === 500) {
      Swal.fire({
        icon: "error",
        title: "Erro interno no servidor",
        text: "Ocorreu um erro no backend (500). Verifique os logs do Laravel.",
      });
      console.error("Erro 500:", error.response?.data);
    } else {
      Swal.fire({
        icon: "error",
        title: "Erro ao carregar produtos",
        text: error?.message || "Tente novamente mais tarde.",
      });
    }
  }
}

/** ===== Funções auxiliares ===== */

function abrirModal(id?: number, nome?: string, preco?: number, quantidade?: number, descricao?: string) {
  const modal = document.getElementById("modal-produto")!;
  (document.getElementById("produto-id") as HTMLInputElement).value = id ? id.toString() : "";
  (document.getElementById("nome") as HTMLInputElement).value = nome || "";
  (document.getElementById("preco") as HTMLInputElement).value = preco
    ? new Intl.NumberFormat("pt-BR", { style: "currency", currency: "BRL" }).format(preco)
    : "";
  (document.getElementById("quantidade") as HTMLInputElement).value = quantidade ? quantidade.toString() : "";
  (document.getElementById("descricao") as HTMLInputElement).value = descricao || "";
  document.getElementById("modal-titulo")!.textContent = id ? "Editar Produto" : "Adicionar Produto";
  modal.style.display = "flex";
}

function fecharModal() {
  document.getElementById("modal-produto")!.style.display = "none";
}

async function salvarProduto() {
  const id = (document.getElementById("produto-id") as HTMLInputElement).value;
  const nome = (document.getElementById("nome") as HTMLInputElement).value;
  const precoStr = (document.getElementById("preco") as HTMLInputElement).value;
  const preco = precoStr ? parseFloat(precoStr.replace(/[R$\s.]/g, "").replace(",", ".")) : 0;
  const quantidade = parseInt((document.getElementById("quantidade") as HTMLInputElement).value);
  const descricao = (document.getElementById("descricao") as HTMLInputElement).value;
  const imagem = (document.getElementById("imagem") as HTMLInputElement).files?.[0];

  if (preco < 0 || quantidade < 0) {
    Swal.fire("Erro", "Preço e quantidade não podem ser negativos.", "error");
    return;
  }

  const formData = new FormData();
  formData.append("nome", nome);
  formData.append("preco", preco.toString());
  formData.append("quantidade", quantidade.toString());
  formData.append("descricao", descricao);
  if (imagem) formData.append("imagem", imagem);

  try {
    if (id) {
      await api.post(`produtos/${id}?_method=PUT`, formData, { headers: { "Content-Type": "multipart/form-data" } });
      Swal.fire("Sucesso", "Produto atualizado com sucesso!", "success");
    } else {
      await api.post("produtos", formData, { headers: { "Content-Type": "multipart/form-data" } });
      Swal.fire("Sucesso", "Produto criado com sucesso!", "success");
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

// ====== Visualização e exclusão ======
(window as any).visualizarProduto = async (id: number) => {
  try {
    const resp = await api.get(`/produtos/${id}`);
    const produto = resp.data;
    const modal = document.getElementById("modal-visualizar")!;
    const imgUrl = produto.imagens?.[0]?.url || "";
    (document.getElementById("view-img") as HTMLImageElement).src = imgUrl;
    (document.getElementById("view-desc") as HTMLParagraphElement).textContent = produto.descricao || "Sem descrição.";
    modal.style.display = "flex";
  } catch {
    Swal.fire("Erro", "Não foi possível carregar o produto.", "error");
  }
};

(window as any).editarProduto = (id: number, nome: string, preco: number, quantidade: number, descricao: string) => {
  abrirModal(id, nome, preco, quantidade, descricao);
};

(window as any).excluirProduto = async (id: number) => {
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
    Swal.fire("Sucesso", "Produto excluído com sucesso!", "success");
    await carregarProdutos(paginaAtual);
  } catch (error: any) {
    Swal.fire({
      icon: "error",
      title: "Erro ao excluir produto",
      text: error?.response?.data?.message || "Tente novamente.",
    });
  }
};

(window as any).carregarProdutos = carregarProdutos;
