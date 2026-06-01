#!/bin/bash
set -e

echo "🚀 Installation de Laravel..."
# docker compose run --rm -u root app composer create-project laravel/laravel . --prefer-dist

echo "⚙️  Configuration du .env..."
docker compose run --rm -u root app bash -c "
cat > /var/www/html/.env << 'EOF'
APP_NAME=Tiny Soccer Predictions
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8080

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=tinysp
DB_USERNAME=tinysp
DB_PASSWORD=tinysp

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=noreply@tinysp.local
MAIL_FROM_NAME=Tiny Soccer Predictions
EOF
"

echo "🔑 Génération de la clé app..."
docker compose run --rm app php artisan key:generate

echo "📦 Installation des dépendances front..."
docker compose run --rm -u root app bash -c "npm install && npm run build" 2>/dev/null || true

echo "🗄️  Migration base de données..."
docker compose run --rm app php artisan migrate

echo "✅ Installation terminée !"
echo ""
echo "👉 App       : http://localhost:8080"
echo "👉 Mailpit   : http://localhost:8025"
