import axios from "axios";

const api = axios.create({
  baseURL: (window as any)._env_?.API_URL || "http://127.0.0.1:8000/api",
});

console.log("➡️ Axios configurado com baseURL:", api.defaults.baseURL);

api.interceptors.request.use((config) => {
  const fullUrl = `${config.baseURL ?? ""}${config.url ?? ""}`;
  console.log("➡️ Requisição feita para:", fullUrl);
  return config;
});

export default api;
