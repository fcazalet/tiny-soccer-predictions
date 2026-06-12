#!/bin/bash
set -e

echo "⚙️  Configuration du .env..."
docker compose exec app bash -c "cp .env.dev /var/www/html/.env"

echo "🔑 Génération de la clé app..."
docker compose exec app php artisan key:generate

echo "📦 Installation des dépendances front..."
docker compose exec app bash -c "npm install && npm run build" 2>/dev/null || true

echo "🗄️  Migration base de données..."
docker compose exec app php artisan migrate

echo "✅ Installation terminée !"
echo ""
echo "👉 App       : http://localhost:8080"
echo "👉 Mailpit   : http://localhost:8025"
