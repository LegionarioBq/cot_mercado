# 📦 APP_PRODUTOS – Sistema de Gestão de Produtos (Laravel + TypeScript + Docker)

Este projeto implementa um **sistema completo de gerenciamento de produtos**, desenvolvido em **Laravel (backend)** e **TypeScript (frontend)**, utilizando **Docker** para orquestração de ambiente.  

O objetivo principal foi atender a um **teste técnico** com foco em:  
- Boas práticas de código.  
- Arquitetura limpa (Controller → Service → Request).  
- Validações robustas no frontend e backend.  
- Autenticação e autorização segura (Laravel Sanctum + Gates).  
- Upload de imagens com hash, restrições de formato e tamanho.  

---

## 🚀 Estrutura do Projeto

```
APP_PRODUTOS/
├── .github/workflows/ci-cd.yml # Pipeline CI/CD
├── backend/ # API em Laravel
│ ├── app/Http/Controllers # Controllers (Produto, Auth)
│ ├── app/Http/Requests # Validações (Store/Update)
│ ├── app/Models # Models (Produto, ProdutoImage, User)
│ ├── app/Services # Services (ProdutoService)
│ ├── database/migrations # Migrations do banco
│ └── .env # Configurações (APP_URL, DB, etc.)
├── frontend/ # Frontend em TypeScript (Vite + TS)
│ ├── src/
│ │ ├── api.ts # Configuração do Axios + Token
│ │ ├── produtos.ts # CRUD com modal, listagem, busca
│ │ ├── login.ts # Tela e fluxo de autenticação
│ │ ├── style.css # Estilos básicos
│ │ └── index.html / index.ts # Entrada da aplicação
│ └── .env
├── Dockerfile.backend # Build do container Laravel
├── Dockerfile.frontend # Build do container frontend
├── docker-compose.yml # Orquestração (Laravel + MySQL + Frontend)
└── README.md # Este documento

```
---

## 🔑 Funcionalidades Implementadas

### ✅ CRUD de Produtos
- Listagem paginada com busca e filtro (ID, nome, preço, quantidade, descrição).  
- Criar, editar e excluir produtos.  
- Validações robustas (preço não pode ser negativo, quantidade não pode ser negativa).  
- Upload de imagem por produto:
  - Hash único gerado no backend.
  - Restrição de 10MB.
  - Apenas formatos **JPEG, PNG, WEBP**.
  - Armazenamento em `storage/app/public/img/`.
  - Exibição via `/img/{hash.ext}` (Laravel `storage:link`).  

### 🔐 Autenticação e Autorização
- Login com email/senha via **Laravel Sanctum**.  
- Token salvo no `localStorage` (frontend).  
- Todas as rotas de produtos protegidas.  
- Permissões via **Gates**:
  - `manage-produtos`: apenas **admin/editor** podem criar/editar.  
  - `delete-produtos`: apenas **admin** pode excluir.  
- Logs detalhados de todas as operações (quem criou, atualizou, tentou excluir sem permissão).  

### ⚙️ Arquitetura Limpa
- **Controller**: apenas orquestra requests/responses.  
- **Service**: concentra regra de negócio (CRUD + upload).  
- **Request**: validação centralizada (`ProdutoStoreRequest` / `ProdutoUpdateRequest`).  
- **Model**: `Produto` (com relação `hasMany` para `ProdutoImage`).  

### 🖼️ Imagens
- Relacionamento **1 Produto → N Imagens**.  
- Evita duplicação com hash (`sha1_file`).  
- Se tentar enviar a mesma imagem, retorna:  


2. Suba os containers

docker-compose up -d

3. Configure o backend

cd backend
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link

4. Configure o frontend

cd frontend
cp .env.example .env
npm install
npm run dev

5. Acesse no navegador

API Laravel → http://127.0.0.1:8000/api

Frontend TypeScript → http://127.0.0.1:5173

🧪 Testes Automatizados
Criar banco de testes

php artisan db:create mercprodutos_test


Resetar banco de testes

php artisan db:reset-test

Rodar migrations no banco de teste

php artisan migrate --env=testing

Executar testes

php artisan test


Separação clara de bancos:

mercprodutos → produção/desenvolvimento.

mercprodutos_test → ambiente de testes.

📜 Fluxo Completo de Autenticação

Usuário acessa tela de login (frontend).

Envia email + password para /api/login.

Backend valida e gera Sanctum Token.

Frontend salva token no localStorage.

Todas as requisições API enviam Authorization: Bearer {token}.

Sanctum valida → se ok → Controller executa.

Gates verificam permissões antes de ações críticas.

🖥️ Demonstração do Frontend

Listagem com paginação.

Busca com filtro dinâmico.

Modal para criar/editar produtos com upload de imagem.

Modal de visualização com preview da imagem e descrição.

✨ Diferenciais Implementados

Uso de SOLID (separação Controller/Service/Request).

Upload de imagens com hash único.

Logs para auditoria.

Frontend TypeScript com integração direta via Axios.

Paginação e busca em tempo real.

Pipeline CI/CD pronto (.github/workflows/ci-cd.yml).

📖 Requisitos Originais do Teste Técnico

Este projeto foi desenvolvido seguindo integralmente os requisitos funcionais, diferenciais e extras do teste técnico, incluindo:

Docker

CRUD + Autenticação

API RESTful protegida

Validações (frontend + backend)

Upload de imagens

Arquitetura limpa + SOLID

Testes automatizados

Controle de permissões

✅ Conclusão

O sistema APP_PRODUTOS é uma aplicação web robusta, escalável e bem estruturada, que combina as melhores práticas de Laravel, TypeScript e Docker.

Ele está pronto para ser expandido com novas funcionalidades (ex: filtros avançados, relatórios, integração com APIs externas) mantendo a arquitetura limpa e testável.


