<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Dotenv\Dotenv;

class ResetTestDatabase extends Command
{
    /**
     * Nome e assinatura do comando Artisan.
     *
     * @var string
     */
    protected $signature = 'db:reset-test {--force : Força reset sem pedir confirmação}';

    /**
     * Descrição do comando.
     *
     * @var string
     */
    protected $description = 'Reseta o banco de testes (mercprodutos_test), recria, roda migrations e seeders';

    /**
     * Executa o comando.
     */
    public function handle(): int
    {
        // 🔄 Carregar variáveis do .env.testing
        $this->loadEnvTesting();
        $this->info("🔄 Variáveis de ambiente carregadas do .env.testing");

        $dbName = trim(env('DB_DATABASE', 'mercprodutos_test'), '"');

        if ($dbName !== 'mercprodutos_test') {
            $this->error("❌ O banco configurado não é de teste. Verifique seu .env.testing. Valor atual: {$dbName}");
            return Command::FAILURE;
        }

        if (
            !$this->option('force') &&
            !$this->confirm(" ⚠️ Isso vai apagar todo o banco `{$dbName}`. Deseja continuar?", false)
        ) {
            $this->warn("Operação cancelada.");
            return Command::SUCCESS;
        }

        try {
            // Dropar o banco de teste
            DB::statement("DROP DATABASE IF EXISTS `{$dbName}`");
            $this->warn("🗑️ Banco `{$dbName}` removido.");

            // Criar novamente
            DB::statement("CREATE DATABASE `{$dbName}`");
            $this->info("✅ Banco `{$dbName}` recriado.");

            // 🔄 Forçar o Laravel a usar o banco recém-criado
            config(['database.connections.mysql.database' => $dbName]);
            DB::purge('mysql');
            DB::reconnect('mysql');

            // Rodar migrations + seeders no banco de teste
            $this->call('migrate:fresh', [
                '--seed' => true,
                '--env' => 'testing',
            ]);

            $this->info("📦 Migrations + Seeders rodados no banco `{$dbName}`.");
        } catch (\Throwable $e) {
            $this->error("❌ Erro: " . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Carrega variáveis do arquivo .env.testing
     */
    private function loadEnvTesting(): void
    {
        $path = base_path();
        if (file_exists($path . '/.env.testing')) {
            $dotenv = Dotenv::createImmutable($path, '.env.testing');
            $dotenv->load();
        }
    }
}
