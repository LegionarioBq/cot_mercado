<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateDatabase extends Command
{
    protected $signature = 'db:create {name?}';
    protected $description = 'Cria um banco de dados MySQL (usa .env.testing se for banco de testes)';

    public function handle()
    {
        $dbName = $this->argument('name') ?? env('DB_DATABASE');

        if ($dbName === 'mercprodutos_test') {
            $this->loadEnvTesting();
            $dbName = env('DB_DATABASE', 'mercprodutos_test');
        }

        try {
            DB::statement("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
            $this->info("✅ Banco de dados `$dbName` criado com sucesso!");
        } catch (\Exception $e) {
            $this->error("❌ Erro ao criar banco `$dbName`: " . $e->getMessage());
        }
    }

    private function loadEnvTesting(): void
    {
        $path = base_path('.env.testing');
        if (!file_exists($path)) {
            $this->warn("⚠️ Arquivo .env.testing não encontrado.");
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) {
                continue; // ignora comentários
            }
            [$name, $value] = array_pad(explode('=', $line, 2), 2, null);
            if ($name && $value !== null) {
                $name = trim($name);
                $value = trim($value, " \t\n\r\0\x0B\"'");
                putenv("$name=$value");
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }

        $this->info("🔄 Variáveis de ambiente carregadas do .env.testing");
    }
}
