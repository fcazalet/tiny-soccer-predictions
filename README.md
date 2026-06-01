# ⚽ Tiny Soccer Predictions

Lightweight PHP web app for creating soccer prediction competitions between small groups of friends or colleagues. Authentication with email address and one-time-password (OTP).

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
