# ⚽ Tiny Soccer Predictions

Lightweight PHP web app for creating soccer prediction competitions between small groups of friends or colleagues. Authentication with email address and one-time-password (OTP).

Languages : EN / FR

## Stack

- **Laravel 11** (PHP 8.3)
- **MariaDB 11**
- **Mailpit** (SMTP local + interface web)
- **Nginx**
- **Docker Compose**

---

## 🚀 Install

### 1. Prerequisites

- Docker + Docker Compose installed
- Ports `8080` et `8025` available

### 2. Start containers

```bash
docker compose up -d
```

### 3. Install dependencies

```bash
docker compose exec app composer install
```

### 4. Configure and customize

Modify setup.sh to meet your needs then run :

```bash
chmod +x setup.sh
./setup.sh
```

### 5. Initialize DB on Worldcup2026

```bash
docker compose exec app php artisan db:seed --class=WorldCup2026Seeder
```

---

## 🌐 Accès

| Service | URL |
|---|---|
| Application | http://localhost:8080 |
| Mailpit (emails) | http://localhost:8025 |

Admin email default test : admin@tinysp.local
Use mailpit for testing mailing.

---

## Configuration / Customization

### Configure SMTP

To use a real SMTP server, please change theses variables in .env file.

Example with Gmail account:

```conf
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS=your-email@gmail.com
```

After changing .env file run :

```bash
docker compose exec app php artisan optimize:clear
```

### Change language

Default language is english.

#### French 🇫🇷

For French language, add this line in .env file:

    APP_LOCALE=fr

Then clear cache :

```bash
docker compose exec app php artisan optimize:clear
```

Now language is french.

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

# Migrate DB
docker compose exec app php artisan migrate

# Load data for Soccer WorldCup 2026
docker compose exec app php artisan migrate
```

---

## 🔄 Benchmark

Running Docker Compose on IDLE (CORE i3-3110 2.40GHz, RAM 4Go):
 * Nginx : CPU <0.01%, MEM ~4MB
 * PHP-FPM : CPU 0.01%, MEM ~40MB
 * MARIADB : CPU 0.02%, MEM ~20MB
 * MAILPIT (optional on production) : CPU <0.01%, MEM ~40MB