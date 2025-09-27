 Desenvolver uma aplicação web para o gerenciamento de "Produtos" utilizando Laravel e Docker, com foco em boas práticas, arquitetura limpa e qualidade de…

 ```
 APP_PRODUTOS/
 ├── .github/workflows/ci-cd.yml
 ├── backend/
 │    └── .env
 ├── frontend/
 │    └── .env
 ├── .gitignore
 ├── Dockerfile.backend
 ├── Dockerfile.frontend
 └── README.md

 
 ```


 🔎 Analisando a estrutura:

backend/ → Onde ficará o Laravel (API REST, autenticação, migrations).

frontend/ → Onde ficará o app em TypeScript (pode ser React, Angular ou Vue com TS).

Dockerfile.backend → Container do Laravel.

Dockerfile.frontend → Container do frontend.

.github/workflows/ci-cd.yml → Pipeline CI/CD (build + testes + deploy).

README.md → Documentação inicial.

.gitignore → Para ignorar vendor/, node_modules/, arquivos temporários etc.



```
frontend/
 ├── src/
 │    ├── index.html
 │    ├── index.ts
 │    ├── api.ts
 │    ├── style.css
 ├── tsconfig.json
 ├── vite.config.js
 ├── package.json



```

Rodar a migratio e seed juntos

```
php artisan migrate --seed

```



📌 Benefícios dessa separação

Controller → apenas orquestra requisições e respostas.

Service → concentra toda a regra de negócio (CRUD).

Requests → isolam a validação.

Código fica mais limpo, reutilizável e testável.





📖 Comandos para Gerenciar o Banco de Dados de Teste

Este projeto possui comandos Artisan personalizados para facilitar a criação, reset e migração do banco de dados de testes (mercprodutos_test).

🔹 1. Criar o banco de teste

```
php artisan db:create mercprodutos_test


```
Cria o banco de dados mercprodutos_test no MySQL (caso não exista).

Útil ao configurar o projeto pela primeira vez em um servidor ou ambiente local.


🔹 2. Resetar o banco de teste

```
php artisan db:reset-test

```
Dropa (remove) o banco de dados mercprodutos_test.

Recria o banco de dados.

Executa todas as migrations no ambiente de teste (.env.testing).

Garante que o banco de teste esteja sempre limpo e sincronizado com as migrations atuais.


🔹 3. Rodar migrations manualmente no ambiente de teste

```
php artisan migrate --env=testing

```

Executa as migrations no banco mercprodutos_test.

Usado normalmente quando você já criou o banco e quer apenas aplicar novas migrations.

⚠️ Observações importantes

O banco de desenvolvimento e de teste são separados:

mercprodutos → uso normal da aplicação.

mercprodutos_test → usado apenas nos testes (php artisan test).

O arquivo .env configura o banco principal.

O arquivo .env.testing configura o banco de testes.