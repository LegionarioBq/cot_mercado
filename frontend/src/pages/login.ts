import { carregarProdutos } from "./produtos";

export function carregarLogin() {
  const app = document.getElementById("app");
  if (!app) return;

  // garantir que body ocupe 100% da tela
  document.body.style.margin = "0";
  document.body.style.height = "100vh";
  document.body.style.width = "100vw";
  document.body.style.background = "#f5f5f5"; // fundo simples cinza

  app.innerHTML = `
    <div style="
      display:flex;
      justify-content:center;
      align-items:center;
      height:100vh;
      width:100vw;
      margin:0;
      background: linear-gradient(135deg, #1cb5e0, #000851);
    ">
      <div style="
        background:white;
        padding:2rem;
        border-radius:10px;
        width:350px;
        text-align:center;
      ">

        <!-- Título -->
        <h2 style="margin-bottom:1.5rem; font-family:sans-serif;">ERP - Produtos</h2>

        <!-- Formulário -->
        <form id="form-login" style="display:flex; flex-direction:column; gap:1rem;">
          
          <!-- Campo usuário -->
          <div style="display:flex; align-items:center; border:1px solid #ccc; border-radius:5px; padding:0.5rem; background:#f8f9fa;">
            <span style="margin-right:0.5rem;">👤</span>
            <input 
              type="text" 
              id="usuario" 
              placeholder="Usuário" 
              required 
              style="flex:1; border:none; outline:none; background:transparent;"
            />
          </div>

          <!-- Campo senha -->
          <div style="display:flex; align-items:center; border:1px solid #ccc; border-radius:5px; padding:0.5rem; background:#f8f9fa;">
            <span style="margin-right:0.5rem;">🔒</span>
            <input 
              type="password" 
              id="senha" 
              placeholder="Senha" 
              required 
              style="flex:1; border:none; outline:none; background:transparent;"
            />
          </div>

          <!-- Botão -->
          <button 
            type="submit" 
            style="
              padding:0.7rem;
              background:#28a745;
              color:white;
              font-weight:bold;
              border:none;
              border-radius:5px;
              cursor:pointer;
              transition: background 0.3s;
            "
          >
            Login
          </button>
        </form>
      </div>
    </div>
  `;

  const form = document.getElementById("form-login") as HTMLFormElement;
  form.onsubmit = async (e) => {
    e.preventDefault();

    const usuario = (document.getElementById("usuario") as HTMLInputElement).value;
    const senha = (document.getElementById("senha") as HTMLInputElement).value;

    console.log("Tentando login:", usuario);

    // 🚧 Aqui futuramente vai a validação real no backend
    if (usuario && senha) {
      alert("✅ Login realizado com sucesso!");
      carregarProdutos(); // troca para tela de produtos
    } else {
      alert("❌ Usuário ou senha inválidos.");
    }
  };
}
