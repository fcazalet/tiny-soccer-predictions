# 🚀 Installation (Production)

## Using Docker Compose

- Docker + Docker Compose installed
- Ports `80` and `443` available (HTTPS)

NB: HTTPS setup with Nginx and Docker is outside the scope of this guide. Please refer to the official Docker documentation and Let's Encrypt documentation.

### 1. Setup Docker Config
Clone GIT project on your environment.

Customize Docker compose config.

- Change MySQL default user and password:

```yaml
    environment:
      MYSQL_ROOT_PASSWORD: secret
      MYSQL_DATABASE: tinysp
      MYSQL_USER: tinysp
      MYSQL_PASSWORD: tinysp
```

- Change Nginx config to meet your domain in `docker/nginx/default.conf`
  -  server_name mydomain.com;

- Remove the `mailpit` service in docker-compose.yml config. You will use your SMTP provider.


When ready, start containers:

```bash
docker compose up -d
```

### 2. Setup App

Install PHP dependencies:

```bash
docker compose exec app composer install
```

After this, copy content of the file .env.dev to .env and setup values:

```properties
APP_URL=http://mydomain.com
APP_ENV=production
APP_DEBUG=false
[...]
LOG_LEVEL=error
```

If you want to use Gmail SMTP:

```properties
MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=mymail@gmail.com
MAIL_PASSWORD={your password}
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=mymail@gmail.com
```

```bash
echo "🔑 Génération de la clé app..."
docker compose exec app php artisan key:generate

echo "📦 Installation des dépendances front..."
docker compose exec app bash -c "npm install && npm run build" 2>/dev/null || true

echo "🗄️  Migration base de données..."
docker compose exec app php artisan migrate
```

### 3. Initialize Admin Account

Default admin address is ``admin@tinysp.local``, change it with a real email address in the file ``src/database/seeders/AdminSeeder.php``.

Example:

```
User::firstOrCreate([
    'name'  => 'Admin',
    'email' => 'mymailadmin@gmail.com',
    'role'  => 'admin',
]);
```

When ready launch the admin creation:

```bash
docker compose exec app php artisan db:seed --class=AdminSeeder
```

## Next move

If you want add some data (teams or fixture):

- Soccer WorldCup 2026 : [soccer-worldcup-2026.md](soccer-worldcup-2026.md)

## Help / Customization

Note: Database session seems to not work. Please use file mode for production:

```conf
SESSION_DRIVER=file
```

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

### Change Timezone

Example for Paris timezone (only display):

```conf
APP_DISPLAY_TIMEZONE=Europe/Paris
```

### Change Language

Default language is english.

#### French 🇫🇷

For French language, add this line in .env file:

    APP_LOCALE=fr

Then clear cache :

```bash
docker compose exec app php artisan optimize:clear
```

Now language is french.