#!/bin/sh
set -e

# Esperar o banco ficar pronto (MySQL/Postgres)
echo "⏳ Aguardando banco de dados..."
until nc -z -v -w30 $DB_HOST $DB_PORT
do
  echo "Aguardando banco ($DB_HOST:$DB_PORT)..."
  sleep 5
done

# Executar migrations
echo "🚀 Rodando migrations..."
php artisan migrate --force

# Iniciar o servidor Laravel
echo "✅ Subindo Laravel na porta 8000..."
exec php artisan serve --host=0.0.0.0 --port=8000
