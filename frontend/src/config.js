// Garante que window._env_ exista
const env = window._env_;
export const API_URL = env?.API_URL || "http://localhost:8000/api";
