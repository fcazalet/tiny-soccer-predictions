# ⚽ WorldCup Pronostics

Application de pronostics pour la Coupe du Monde, avec authentification passwordless par email (OTP).

## Stack

- **Laravel 11** (PHP 8.3)
- **MariaDB 11**
- **Mailpit** (SMTP local + interface web)
- **Nginx**
- **Docker Compose**

---

## 🚀 Installation

### 1. Prérequis

- Docker + Docker Compose installés
- Ports `8080` et `8025` disponibles

### 2. Démarrer les conteneurs

```bash
docker compose up -d
```

### 3. Installer Laravel (première fois uniquement)

```bash
chmod +x setup.sh
./setup.sh
```

### 4. Copier les fichiers stubs dans le projet Laravel

```bash
# Migration
cp src_stubs/create_login_tokens_table.php src/database/migrations/$(date +%Y_%m_%d_%H%M%S)_create_login_tokens_table.php

# Controller
mkdir -p src/app/Http/Controllers/Auth
cp src_stubs/OtpController.php src/app/Http/Controllers/Auth/OtpController.php

# Notification
mkdir -p src/app/Notifications
cp src_stubs/LoginOtpNotification.php src/app/Notifications/LoginOtpNotification.php

# Vues
mkdir -p src/resources/views/auth
cp src_stubs/email.blade.php src/resources/views/auth/email.blade.php
cp src_stubs/otp.blade.php src/resources/views/auth/otp.blade.php
```

### 5. Ajouter les routes

Remplacer le contenu de `src/routes/web.php` par celui de `src_stubs/routes_web.php`.

### 6. Créer le modèle LoginToken

```bash
docker compose exec app php artisan make:model LoginToken
```

Ajouter dans `src/app/Models/LoginToken.php` :
```php
protected $fillable = ['email', 'token', 'expires_at'];
protected $casts = ['expires_at' => 'datetime'];
```

### 7. Lancer les migrations

```bash
docker compose exec app php artisan migrate
```

---

## 🌐 Accès

| Service | URL |
|---|---|
| Application | http://localhost:8080 |
| Mailpit (emails) | http://localhost:8025 |

---

## 🔄 Commandes utiles

```bash
# Démarrer
docker compose up -d

# Arrêter
docker compose down

# Logs
docker compose logs -f app

# Artisan
docker compose exec app php artisan <commande>

# Vider le cache
docker compose exec app php artisan optimize:clear
```

---

## 📁 Structure

```
worldcup/
├── docker-compose.yml
├── Dockerfile
├── setup.sh
├── docker/
│   └── nginx/
│       └── default.conf
├── src/                  # Code Laravel (généré par setup.sh)
└── src_stubs/            # Fichiers à copier dans src/
```
