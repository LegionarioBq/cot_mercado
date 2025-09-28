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

// URL base do backend (APP_URL do Laravel)
const API_URL = "http://127.0.0.1:8000";

// Aplica o token do localStorage (se existir)
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
      <!-- Cabeçalho -->
      <div class="header">
        <h1>Gestão de Produtos</h1>
        <button id="btn-logout" class="btn-logout">🚪 Logout</button>
      </div>

      <!-- Título da lista e filtro -->
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

    produtos.forEach((p) => {
      const preco = typeof p.preco === "string" ? parseFloat(p.preco) : p.preco;
      const imgUrl = p.imagens && p.imagens.length > 0 ? p.imagens[0].url : "";

      html += `
        <tr>
          <td class="center">${p.id}</td>
          <td class="center">
            ${
              imgUrl
                ? `<img src="${imgUrl}" alt="produto" class="thumb"/>`
                : "—"
            }
          </td>
          <td>${p.nome}</td>
          <td>${new Intl.NumberFormat("pt-BR", { style: "currency", currency: "BRL" }).format(preco)}</td>
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

    // Paginação
    html += `<div class="paginacao">`;
    if (page > 1) {
      html += `<button onclick="carregarProdutos(${page - 1})">⬅️ Anterior</button>`;
    }
    for (let i = 1; i <= totalPaginas; i++) {
      html +=
        i === page
          ? `<strong>[${i}]</strong> `
          : `<button onclick="carregarProdutos(${i})">${i}</button> `;
    }
    if (page < totalPaginas) {
      html += `<button onclick="carregarProdutos(${page + 1})">Próxima ➡️</button>`;
    }
    html += `</div>`;

    // Modal de adicionar/editar produto
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

            <!-- Upload customizado -->
            <div class="file-upload">
              <input type="file" id="imagem" accept="image/png,image/jpeg,image/webp" hidden />
              <label for="imagem" class="file-label">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M.5 9.9a.5.5 0 0 1 .5-.5h4.5V1.5a.5.5 0 0 1 1 0v7.9H12a.5.5 0 0 1 .354.854l-5 5a.5.5 0 0 1-.708 0l-5-5A.5.5 0 0 1 .5 9.9z"/>
                </svg>
                Enviar imagem
              </label>
              <span id="file-name">Nenhum arquivo escolhido</span>
            </div>

            <div class="modal-actions">
              <button type="submit">Salvar</button>
              <button type="button" id="fechar-modal">Cancelar</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Modal de visualização -->
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

    document.getElementById("fechar-visualizar")?.addEventListener("click", () => {
      document.getElementById("modal-visualizar")!.style.display = "none";
    });

    // Logout
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

    // Mostrar nome do arquivo selecionado
    const fileInput = document.getElementById("imagem") as HTMLInputElement;
    const fileNameSpan = document.getElementById("file-name") as HTMLElement;
    if (fileInput) {
      fileInput.addEventListener("change", () => {
        if (fileInput.files && fileInput.files.length > 0) {
          fileNameSpan.textContent = fileInput.files[0].name;
        } else {
          fileNameSpan.textContent = "Nenhum arquivo escolhido";
        }
      });
    }

    // Máscara de preço
    const precoInput = document.getElementById("preco") as HTMLInputElement;
    if (precoInput) {
      precoInput.addEventListener("input", () => {
        let value = precoInput.value.replace(/\D/g, "");
        if (value) {
          const numberValue = parseFloat(value) / 100;
          precoInput.value = new Intl.NumberFormat("pt-BR", {
            style: "currency",
            currency: "BRL",
          }).format(numberValue);
        } else {
          precoInput.value = "";
        }
      });
    }

    // Contador descrição
    const descInput = document.getElementById("descricao") as HTMLInputElement;
    const contador = document.getElementById("desc-contador") as HTMLElement;
    if (descInput && contador) {
      descInput.addEventListener("input", () => {
        contador.textContent = `${descInput.value.length} / 255`;
      });
    }

    const form = document.getElementById("form-produto") as HTMLFormElement;
    form.onsubmit = async (e) => {
      e.preventDefault();
      await salvarProduto();
    };

    paginaAtual = page;
  } catch (error: any) {
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
  const preco = precoStr
    ? parseFloat(precoStr.replace(/[R$\s.]/g, "").replace(",", "."))
    : 0;

  const quantidade = parseInt((document.getElementById("quantidade") as HTMLInputElement).value);
  const descricao = (document.getElementById("descricao") as HTMLInputElement).value;
  const imagem = (document.getElementById("imagem") as HTMLInputElement).files?.[0];

  if (preco < 0 || quantidade < 0) {
    Swal.fire("❌ Erro", "Preço e quantidade não podem ser negativos.", "error");
    return;
  }
  if (imagem) {
    if (imagem.size > 10 * 1024 * 1024) {
      Swal.fire("❌ Erro", "A imagem não pode ultrapassar 10MB.", "error");
      return;
    }
    if (!["image/jpeg", "image/png", "image/webp"].includes(imagem.type)) {
      Swal.fire("❌ Erro", "Formato inválido. Permitidos: jpeg, png, webp.", "error");
      return;
    }
  }

  const formData = new FormData();
  formData.append("nome", nome);
  formData.append("preco", preco.toString());
  formData.append("quantidade", quantidade.toString());
  formData.append("descricao", descricao);
  if (imagem) formData.append("imagem", imagem);

  try {
    if (id) {
      await api.post(`produtos/${id}?_method=PUT`, formData, {
        headers: { "Content-Type": "multipart/form-data" },
      });
      Swal.fire("✅ Sucesso", "Produto atualizado com sucesso!", "success");
    } else {
      await api.post("produtos", formData, {
        headers: { "Content-Type": "multipart/form-data" },
      });
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

// Visualizar produto
;(window as any).visualizarProduto = async (id: number) => {
  try {
    const resp = await api.get(`/produtos/${id}`);
    const produto = resp.data;

    const modal = document.getElementById("modal-visualizar")!;
    const imgUrl =
      produto.imagens && produto.imagens.length > 0
        ? produto.imagens[0].url
        : "";
    (document.getElementById("view-img") as HTMLImageElement).src = imgUrl;
    (document.getElementById("view-desc") as HTMLParagraphElement).textContent =
      produto.descricao || "Sem descrição.";

    modal.style.display = "flex";
  } catch (e) {
    Swal.fire("Erro", "Não foi possível carregar o produto.", "error");
  }
};

;(window as any).editarProduto = (
  id: number,
  nome: string,
  preco: number,
  quantidade: number,
  descricao: string
) => {
  abrirModal(id, nome, preco, quantidade, descricao);
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
