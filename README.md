# ⚽ Tiny Soccer Predictions

Lightweight PHP self-hosted webapp for creating soccer prediction competitions between small groups of friends or colleagues. Authentication with email address and one-time-password (OTP).

Languages : EN / FR

## Live Demo

Link : http://tinysp-demo.picmid.com/

<img alt="Screenshot of TinySP My Results view" src="/docs/images/scr_myresults.png" width="400"><img alt="Screenshot of TinySP Leaderboard view" src="/docs/images/scr_leaderboard.png" width="400">

## Stack

- **Laravel 11** (PHP 8.3)
- **MariaDB 11**
- **Mailpit** (SMTP local + interface web) (for development)
- **Nginx**
- **Docker Compose**

---

## 🚀 Installation (Production)

👉 Production setup: [docs/production-install.md](docs/production-install.md)


## Development

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
chmod +x setup-dev.sh
./setup-dev.sh
```

### 5. Initialize DB

Initialize DB with default admin : admin@tinysp.local

```bash
docker compose exec app php artisan db:seed --class=AdminSeeder
```

### 6. Initialize WorldCup 2026

```bash
docker compose exec app php artisan db:seed --class=WorldCup2026Seeder
```

---

## Development Setup

## 🌐 Accès

| Service | URL |
|---|---|
| Application | http://localhost:8080 |
| Mailpit (emails) | http://localhost:8025 |

Admin email default test : admin@tinysp.local
Use mailpit for testing mailing.

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

---

 ## Credits

Flags SVG assets used in this project are derived from [flag-icons](https://github.com/lipis/flag-icons) licensed under the MIT License.
