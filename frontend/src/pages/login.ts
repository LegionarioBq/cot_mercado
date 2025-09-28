import api from "../api";
import Swal from "sweetalert2";
import { carregarProdutos } from "./produtos";

export function carregarLogin() {
  const app = document.getElementById("app");
  if (!app) return;

  document.body.style.margin = "0";
  document.body.style.height = "100vh";
  document.body.style.width = "100vw";
  document.body.style.background = "#f5f5f5";

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

        <form id="form-login" style="display:flex; flex-direction:column; gap:1rem;">
          <div style="display:flex; align-items:center; border:1px solid #ccc; border-radius:5px; padding:0.5rem; background:#f8f9fa;">
            <span style="margin-right:0.5rem;">👤</span>
            <input 
              type="email" 
              id="usuario" 
              placeholder="E-mail" 
              required 
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
            Login
          </button>
        </form>
      </div>
    </div>
  `;

  const form = document.getElementById("form-login") as HTMLFormElement;
  form.onsubmit = async (e) => {
    e.preventDefault();

    const email = (document.getElementById("usuario") as HTMLInputElement).value;
    const password = (document.getElementById("senha") as HTMLInputElement).value;

    try {
      const response = await api.post("/login", { email, password });

      const token = response.data.token;
      const user = response.data.user;

      // 🔐 Salva token e tipo de usuário no localStorage
      localStorage.setItem("auth_token", token);
      localStorage.setItem("user_type", user.type); // 👈 salva se é admin/editor/viewer

      // 🔑 Configura Axios com o token
      api.defaults.headers.common["Authorization"] = `Bearer ${token}`;

      // 🔍 Debug no console
      console.log("🔑 Usuário logado:", user);
      console.log("👤 Tipo de usuário:", user.type);

      Swal.fire({
        icon: "success",
        title: "Login realizado!",
        text: `Bem-vindo ${user.name} 🚀`,
        timer: 2000,
        showConfirmButton: false,
      });

      carregarProdutos(); // troca para tela de produtos
    } catch (error: any) {
      Swal.fire({
        icon: "error",
        title: "Erro no login",
        text: error.response?.data?.message || "Usuário ou senha inválidos.",
      });
    }
  };
}
