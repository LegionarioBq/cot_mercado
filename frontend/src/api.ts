import axios from "axios";

const api = axios.create({
  baseURL: (window as any)._env_?.API_URL || "http://127.0.0.1:8000/api",
});

//console.log("Axios configurado com baseURL:", api.defaults.baseURL);

// Interceptor de requisição (adiciona token e loga URL)
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem("auth_token");
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }

    const fullUrl = `${config.baseURL ?? ""}${config.url ?? ""}`;
    //console.log("Requisição feita para:", fullUrl);

    return config;
  },
  (error) => Promise.reject(error)
);

// Interceptor de resposta (captura erro 401 e redireciona)
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      //console.warn("Sessão expirada. Redirecionando para login...");
      localStorage.removeItem("auth_token");

      // só recarrega se não estiver na tela de login
      if (!window.location.pathname.includes("login")) {
        window.location.reload();
      }
    }
    return Promise.reject(error);
  }
);

export default api;
