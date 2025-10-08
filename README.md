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
COT_MERCADO/
├── .github/
│   └── workflows/
│       └── ci-cd.yml                # Pipeline CI/CD (build + push + deploy)
│
├── backend/                         # API Laravel
│   ├── app/
│   │   ├── Console/
│   │   │   ├── Commands/
│   │   │   │   ├── CreateDatabase.php
│   │   │   │   └── ResetTestDatabase.php
│   │   │   └── Kernel.php
│   │   │
│   │   ├── Exceptions/
│   │   │   └── Handler.php
│   │   │
│   │   ├── Http/
│   │   │   ├── Controllers/         # Controllers principais
│   │   │   │   ├── ProdutoController.php
│   │   │   │   └── AuthController.php
│   │   │   ├── Middleware/          # Validações (FormRequest)
│   │   │   │   └── Authenticate.php
│   │   │   ├── Requests/            # Validações (FormRequest)
│   │   │   └── Kernel.php
│   │   │
│   │   ├── Models/                  # Models do Eloquent
│   │   │   ├── Produto.php
│   │   │   ├── ProdutoImage.php
│   │   │   └── User.php
│   │   │
│   │   ├── Providers/
│   │   │   ├── AppServiceProvider.php
│   │   │   ├── AuthServiceProvider.php
│   │   │   └── RouteServiceProvider.php
│   │   │
│   │   └── Services/                # Regras de negócio (camada de serviço)
│   │       └── ProdutoService.php
│   │
│   ├── bootstrap/
│   │   └── app.php
│   │
│   ├── config/
│   │   ├── app.php
│   │   ├── auth.php
│   │   ├── database.php
│   │   ├── filesystems.php
│   │   ├── logging.php
│   │   ├── sanctum.php
│   │   └── services.php
│   │
│   ├── database/
│   │   ├── factories/
│   │   │   ├── ProdutoFactory.php
│   │   │   └── UserFactory.php
│   │   │
│   │   ├── migrations/
│   │   │   ├── 0001_01_01_000000_create_users_table.php
│   │   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   │   ├── 2025_09_25_180407_create_produtos_table.php
│   │   │   ├── 2025_09_28_135927_create_produto_images_table.php
│   │   │   └── (... demais migrations ...)
│   │   │
│   │   ├── seeders/
│   │   │   ├── DatabaseSeeder.php
│   │   │   └── ProdutoSeeder.php
│   │   │
│   │   └── database.sqlite           # Banco local para testes
│   │
│   ├── public/
│   │   ├── img/
│   │   ├── index.php
│   │   └── .htaccess
│   │
│   ├── resources/
│   │   └── views/
│   │       └── welcome.blade.php
│   │
│   ├── routes/
│   │   ├── api.php                   # Rotas de API (Produto, Auth)
│   │   └── web.php                   # Rotas web básicas
│   │
│   ├── storage/
│   │   ├── app/public/img/           # Uploads de produtos
│   │   ├── framework/
│   │   └── logs/
│   │
│   ├── tests/
│   │   ├── Feature/
│   │   │   ├── ProdutoApiTest.php
│   │   │   ├── ProdutoSearchApiTest.php
│   │   │   └── ExampleTest.php
│   │   └── Unit/
│   │       └── ExampleTest.php
│   │
│   ├── artisan
│   ├── composer.json
│   ├── composer.lock
│   ├── phpunit.xml
│   ├── .env
│   ├── .env.example
│   └── README.md
│
├── frontend/                        # Frontend TypeScript (Vite)
│   ├── src/
│   │   ├── api.ts                   # Configuração do Axios + Token
│   │   ├── pages/
│   │   │   ├── produtos.ts          # CRUD de produtos
│   │   │   └── login.ts             # Tela de login e autenticação
│   │   ├── css/
│   │   │   └── style.css
│   │   ├── index.html
│   │   ├── index.ts                 # Ponto de entrada da aplicação
│   │   └── utils.ts
│   │
│   ├── tsconfig.json
│   ├── vite.config.js
│   ├── package.json
│   ├── package-lock.json
│   ├── .env
│   └── README.md
│
├── docker-compose.yml                # Orquestração (Backend + Frontend + MySQL)
├── Dockerfile.backend                # Build do container Laravel
├── Dockerfile.frontend               # Build do container Vite/TS
├── backend-entrypoint.sh             # Script de inicialização do backend
├── frontend-entrypoint.sh            # Script de inicialização do frontend
└── README.md                         # Documentação principal do projeto


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


### Suba os containers

- 🚀 Script de Instalação da Base com Docker
Este script automatiza a configuração inicial de um servidor Ubuntu Server 24.04 LTS, incluindo:

- Atualização completa do sistema
- Definição do fuso horário (America/Sao_Paulo)
- Instalação da versão mais recente do Docker e dependências
- Habilitação dos serviços Docker e containerd para inicialização automática
- Verificação automática se o Docker já está instalado (evita reinstalação)
- Geração opcional de chave SSH RSA 2048 bits para uso em pipelines de CI/CD


Executar remotamente via curl (sem baixar o arquivo)
Copie a linha abaixo e execute diretamente o comando no seu terminal:

```
  curl -sSL https://raw.githubusercontent.com/LegionarioBq/DevOps/main/install_base_docker.sh | bash

```
OU

```
  wget -qO- https://raw.githubusercontent.com/LegionarioBq/DevOps/main/install_base_docker.sh | bash

```

No terminal WSL
- abre o diretorio da aplicação.

```
  docker-compose down -v
  docker-compose build --no-cache
  docker-compose up -d

```
🧰 Comandos Docker Úteis
Acessar containers manualmente

```
  docker exec -it app_produtos_mysql bash
  docker exec -it app_produtos_back bash
  docker exec -it app_produtos_front sh

```
  Rodar testes dentro do container do backend

```
  docker exec -it app_produtos_back php artisan test

```

2. Configure o backend

 - cd backend
 - cp .env.example .env
 - php artisan key:generate
 - php artisan migrate --seed
 - php artisan storage:link

3. Configure o frontend

 - cd frontend
 - cp .env.example .env
 - npm install
 - npm run dev

4. Acesse no navegador

API Laravel → http://127.0.0.1:8000/api

Frontend TypeScript → http://127.0.0.1:3000

PhpMyAdmin → http://localhost:8082

<br>
🧪 Testes Automatizados
Criar banco de testes

```
  php artisan db:create mercprodutos_test

```

Resetar banco de testes

```
  php artisan db:reset-test

```

Rodar migrations no banco de teste

```
  php artisan migrate --env=testing

```

Executar testes

```
  php artisan test

```

Separação clara de bancos:

mercprodutos → produção/desenvolvimento.

mercprodutos_test → ambiente de testes.

<br><br>
📜 Fluxo Completo de Autenticação

Usuário acessa tela de login (frontend).

Envia email + password para /api/login.

Backend valida e gera Sanctum Token.

Frontend salva token no localStorage.

Todas as requisições API enviam Authorization: Bearer {token}.

Sanctum valida → se ok → Controller executa.

Gates verificam permissões antes de ações críticas.

<br>
🖥️ Demonstração do Frontend

- Listagem com paginação.

- Busca com filtro dinâmico.

- Modal para criar/editar produtos com upload de imagem.

- Modal de visualização com preview da imagem e descrição.

🖥️ Interface do Sistema <br>
Tela principal – Gestão de Produtos
<p align="center"> <img src="736f371a-5cc0-4470-88d9-33c44636be90.png" alt="Gestão de Produtos" width="850"> </p>
Tela de detalhes
<p align="center"> <img src="0a0af6eb-1d35-4525-9dfa-c649b154d274.png" alt="Detalhes do Produto" width="850"> </p>
Tela de edição
<p align="center"> <img src="346aad63-b32c-44be-b595-49db3973b16b.png" alt="Editar Produto" width="850"> </p>
Tela de criação
<p align="center"> <img src="5f4b53a8-0e7d-4cf4-b131-6d1d5352f16d.png" alt="Adicionar Produto" width="850"> </p>
Confirmação de exclusão
<p align="center"> <img src="1b2794dd-d6da-4bc2-962c-7c57a8e14edb.png" alt="Confirmar Exclusão" width="850"> </p>
Sucesso na atualização
<p align="center"> <img src="215ec2a4-1608-4910-8948-1a580707fb81.png" alt="Produto Atualizado" width="850"> </p>
Busca de produto específica
<p align="center"> <img src="62b92eaa-8256-4668-8ffe-21d7ef0ba0a0.png" alt="Busca de Produto" width="850"> </p>


<br>
🧪 Testes Automatizados
<p align="center"> <img src="756a1531-9434-49da-9932-53f3e43469a9.png" alt="Resultados dos testes" width="750"> </p>

Todos os 13 testes passaram com sucesso ✅
Incluindo CRUD, buscas, autenticação Sanctum e validações.

<br>
✨ Diferenciais Implementados

Uso de SOLID (separação Controller/Service/Request).

Upload de imagens com hash único.

Logs para auditoria.

Frontend TypeScript com integração direta via Axios.

Paginação e busca em tempo real.

Pipeline CI/CD pronto (.github/workflows/ci-cd.yml).


<br>
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

<br>
✅ Conclusão

O sistema APP_PRODUTOS é uma aplicação web robusta, escalável e bem estruturada, que combina as melhores práticas de Laravel, TypeScript e Docker.

Ele está pronto para ser expandido com novas funcionalidades (ex: filtros avançados, relatórios, integração com APIs externas) mantendo a arquitetura limpa e testável.


