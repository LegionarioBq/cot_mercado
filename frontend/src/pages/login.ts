import api from "../api";
import Swal from "sweetalert2";
import { carregarProdutos } from "./produtos";

/**
 * Exibe o formulário de login e realiza autenticação via token Bearer (Sanctum API)
 */
export function carregarLogin() {
  const app = document.getElementById("app");
  if (!app) return;

  // Estilos básicos da página
  document.body.style.margin = "0";
  document.body.style.height = "100vh";
  document.body.style.width = "100vw";
  document.body.style.background = "#f5f5f5";

  // Layout do formulário
  app.innerHTML = `
    <div style="
      display:flex;
      justify-content:center;
      align-items:center;
      height:100vh;
      width:100vw;
    ">
      <div style="
        background:white;
        padding:2rem;
        border-radius:10px;
        width:350px;
        text-align:center;
      ">
        <h2 style="margin-bottom:1.5rem; font-family:sans-serif;">ERP - Produtos</h2>

        <form id="form-login" style="
          display:flex;
          flex-direction:column;
          gap:1rem;
          background:transparent;
          border:none;
          box-shadow:none;
          padding:0;
        ">
          <div style="display:flex; align-items:center; border:1px solid #ccc; border-radius:5px; padding:0.5rem; background:#f8f9fa;">
            <span style="margin-right:0.5rem;">👤</span>
            <input 
              type="email" 
              id="usuario" 
              placeholder="E-mail" 
              required 
              autocomplete="username"
              style="flex:1; border:none; outline:none; background:transparent;"
            />
          </div>

          <div style="display:flex; align-items:center; border:1px solid #ccc; border-radius:5px; padding:0.5rem; background:#f8f9fa;">
            <span style="margin-right:0.5rem;">🔒</span>
            <input 
              type="password" 
              id="senha" 
              placeholder="Senha" 
              required 
              autocomplete="current-password"
              style="flex:1; border:none; outline:none; background:transparent;"
            />
          </div>

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
            Entrar
          </button>
        </form>
      </div>
    </div>
  `;

  /**
   * Manipulação do envio do formulário
   */
  const form = document.getElementById("form-login") as HTMLFormElement;
  form.onsubmit = async (e) => {
    e.preventDefault();

    const email = (document.getElementById("usuario") as HTMLInputElement).value.trim();
    const password = (document.getElementById("senha") as HTMLInputElement).value;

    if (!email || !password) {
      Swal.fire({
        icon: "warning",
        title: "Campos obrigatórios",
        text: "Informe o e-mail e a senha para continuar.",
      });
      return;
    }

    try {
      const response = await api.post("/login", { email, password });
      const { token, user } = response.data;

      // 🔐 Salva o token e tipo de usuário no localStorage
      localStorage.setItem("auth_token", token);
      localStorage.setItem("user_type", user.type);

      // Configura o Axios para usar o token em todas as próximas requisições
      api.defaults.headers.common["Authorization"] = `Bearer ${token}`;

      // Feedback visual
      await Swal.fire({
        icon: "success",
        title: "Login realizado!",
        text: `Bem-vindo, ${user.name}!`,
        timer: 2000,
        showConfirmButton: false,
      });

      // Carrega tela de produtos
      carregarProdutos();
    } catch (error: any) {
      console.error("Erro no login:", error);
      Swal.fire({
        icon: "error",
        title: "Erro ao autenticar",
        text: error.response?.data?.message || "Usuário ou senha inválidos.",
      });
    }
  };
}
