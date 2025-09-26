import api from "./api";

interface Produto {
  id: number;
  nome: string;
  preco: number;
  descricao?: string;
}

async function carregarProdutos() {
  try {
    const response = await api.get("produtos");
    const produtos: Produto[] = response.data.data;

    const app = document.getElementById("app");
    if (!app) return;

    let html = `
      <h2>Lista de Produtos</h2>
      <table border="1" cellpadding="5" cellspacing="0">
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
      html += `
        <tr>
          <td>${p.id}</td>
          <td>${p.nome}</td>
          <td>R$ ${p.preco.toFixed(2)}</td>
          <td>${p.descricao || ""}</td>
          <td>
            <button onclick="editarProduto(${p.id}, '${p.nome}', ${p.preco}, '${p.descricao || ""}')">Editar</button>
            <button onclick="excluirProduto(${p.id})">Excluir</button>
          </td>
        </tr>
      `;
    });

    html += `
        </tbody>
      </table>

      <h3>Adicionar Produto</h3>
      <form id="form-produto">
        <input type="text" id="nome" placeholder="Nome" required />
        <input type="number" id="preco" placeholder="Preço" required step="0.01" />
        <input type="text" id="descricao" placeholder="Descrição" />
        <button type="submit">Adicionar</button>
      </form>
    `;

    app.innerHTML = html;

    const form = document.getElementById("form-produto") as HTMLFormElement;
    form.onsubmit = async (e) => {
      e.preventDefault();
      await adicionarProduto();
    };
  } catch (error) {
    console.error("❌ Erro ao carregar produtos:", error);
  }
}

async function adicionarProduto() {
  const nome = (document.getElementById("nome") as HTMLInputElement).value;
  const preco = parseFloat((document.getElementById("preco") as HTMLInputElement).value);
  const descricao = (document.getElementById("descricao") as HTMLInputElement).value;

  try {
    await api.post("produtos", { nome, preco, descricao });
    await carregarProdutos();
  } catch (error) {
    console.error("❌ Erro ao adicionar produto:", error);
  }
}

;(window as any).editarProduto = async (id: number, nome: string, preco: number, descricao: string) => {
  const novoNome = prompt("Novo nome:", nome);
  const novoPreco = parseFloat(prompt("Novo preço:", preco.toString()) || preco.toString());
  const novaDescricao = prompt("Nova descrição:", descricao);

  try {
    await api.put(`produtos/${id}`, {
      nome: novoNome,
      preco: novoPreco,
      descricao: novaDescricao,
    });
    await carregarProdutos();
  } catch (error) {
    console.error("❌ Erro ao editar produto:", error);
  }
};

;(window as any).excluirProduto = async (id: number) => {
  if (!confirm("Tem certeza que deseja excluir este produto?")) return;

  try {
    await api.delete(`produtos/${id}`);
    await carregarProdutos();
  } catch (error) {
    console.error("❌ Erro ao excluir produto:", error);
  }
};

carregarProdutos();
