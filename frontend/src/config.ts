// Tipagem para o objeto global window._env_
interface EnvConfig {
  API_URL: string;
}

// Garante que window._env_ exista
const env = (window as any)._env_ as EnvConfig | undefined;

export const API_URL = env?.API_URL || "http://localhost:8000/api";
