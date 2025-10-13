import axios, { AxiosError, AxiosRequestConfig, AxiosResponse } from "axios";

/**
 * Configuração base do Axios
 * - Usa variável de ambiente VITE_API_URL (do .env do Vite)
 * - Suporte total a Bearer Token (sem cookies nem CSRF)
 * - Compatível com Laravel Sanctum via Authorization header
 */

// console.log("🧩 VITE_API_URL:", import.meta.env.VITE_API_URL);

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || "http://localhost:8000/api",
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
  // true = envia cookies (para modo SPA com Sanctum + CSRF)
  // false = modo API via token Bearer (seu caso)
  withCredentials: true,
});

/**
 * Interceptor de requisição
 * - Adiciona o token Bearer armazenado no localStorage
 * - Pode exibir logs de requisições para debug
 */
api.interceptors.request.use(
  (config: AxiosRequestConfig) => {
    const token = localStorage.getItem("auth_token");

    if (token && config.headers) {
      config.headers.Authorization = `Bearer ${token}`;
    }

    // Log opcional (útil durante o desenvolvimento)
    // const fullUrl = `${config.baseURL ?? ""}${config.url ?? ""}`;
    // console.log(`[API] ${config.method?.toUpperCase()} → ${fullUrl}`);

    return config;
  },
  (error: AxiosError) => Promise.reject(error)
);

/**
 * Interceptor de resposta
 * - Trata erros de autenticação (401)
 * - Limpa token e redireciona para login
 */
api.interceptors.response.use(
  (response: AxiosResponse) => response,
  (error: AxiosError) => {
    if (error.response?.status === 401) {
      // Remove token inválido do localStorage
      localStorage.removeItem("auth_token");
      localStorage.removeItem("user_type");

      // Evita loop de redirecionamento
      if (!window.location.pathname.includes("login")) {
        // Recarrega ou redireciona para a tela de login
        window.location.href = "/login";
      }
    }

    return Promise.reject(error);
  }
);

export default api;
