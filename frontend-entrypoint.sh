#!/bin/sh
set -e

echo "📦 Instalando dependências do frontend..."
npm install --silent

echo "⚡ Gerando build de produção..."
npm run build

echo "✅ Iniciando servidor na porta 3000..."
exec serve . -l 3000
