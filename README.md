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



🔑 Fluxo de Autenticação e Autorização (Laravel + Sanctum)
1. Login (/api/login)

Rota pública (POST /login)

Recebe email e password.

Usa Auth::attempt para validar credenciais.

Se correto:

Gera um token Sanctum ($user->createToken('auth_token')).

Retorna JSON com user + token.

👉 Esse token precisa ser enviado pelo frontend em todas as requisições protegidas:

Authorization: Bearer {token}

2. Proteção das rotas (routes/api.php)

As rotas de produtos e logout estão dentro do grupo Route::middleware('auth:sanctum').

Isso significa que só quem tiver um token válido consegue acessar.

Exemplo:

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/produtos', [ProdutoController::class, 'store']); // admin/editor
});

3. Controle de permissões (AuthServiceProvider)

Onde definimos quem pode criar, editar ou excluir.

Gate::define('manage-produtos', function (User $user) {
    $type = strtolower(trim($user->type));
    return in_array($type, ['admin', 'editor']);
});

Gate::define('delete-produtos', function (User $user) {
    $type = strtolower(trim($user->type));
    return in_array($type, ['admin', 'editor']);
});

4. Uso dos Gates no ProdutoController

Antes de criar/editar/excluir, chamamos Gate::allows.

Exemplo:

if (! Gate::allows('manage-produtos')) {
    return response()->json([
        'message' => '❌ Apenas administradores e editores podem criar produtos.'
    ], Response::HTTP_FORBIDDEN);
}

5. Logs para debug

Em cada operação crítica (store, update, destroy), registramos:

Log::info('👤 Tentando criar produto', [
    'id' => $user->id,
    'email' => $user->email,
    'type' => $user->type,
]);


Assim fica fácil identificar quem tentou acessar e se tinha permissão.

📊 Resumindo o fluxo

Login → gera token Sanctum.

Frontend guarda token no localStorage e envia em cada requisição.

Middleware Sanctum → valida se token é válido.

Gate (AuthServiceProvider) → valida se o tipo de usuário tem permissão.

Controller → executa ação ou retorna 403 Unauthorized.

👉 Esse padrão já está pronto para crescer. Você pode:

Criar mais Gates para diferentes permissões.

Criar Policies específicas por modelo.

Adicionar expiração de tokens.


