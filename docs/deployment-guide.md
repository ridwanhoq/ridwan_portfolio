# Deployment guide (Linux VPS)

Tested assumptions: Ubuntu 24.04, nginx, PHP-FPM 8.4, MySQL 8, Node 22. Adjust paths for other distributions or managed hosting.

> **PHP version:** the lock file requires PHP **8.4.1+**. Don't assume the server matches your laptop. Check with `php -v` and run `composer check-platform-reqs` on the server before deploying.

## 1. Server packages

```bash
sudo apt install nginx mysql-server php8.4-fpm php8.4-cli php8.4-mysql php8.4-gd php8.4-mbstring \
  php8.4-xml php8.4-curl php8.4-intl php8.4-zip php8.4-bcmath unzip
# Composer: https://getcomposer.org/download/   Node 22: https://nodejs.org (or nvm)
php -r 'var_dump(gd_info()["WebP Support"]);'   # must be true
```

## 2. Database

```sql
CREATE DATABASE portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'portfolio'@'localhost' IDENTIFIED BY 'a-long-random-password';
GRANT ALL PRIVILEGES ON portfolio.* TO 'portfolio'@'localhost';
```

## 3. Code and environment

```bash
sudo mkdir -p /var/www/portfolio && sudo chown $USER:www-data /var/www/portfolio
git clone <repository-url> /var/www/portfolio && cd /var/www/portfolio
cp .env.example .env
```

Production `.env` (minimum):

```dotenv
APP_NAME="Your Name"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_DATABASE=portfolio
DB_USERNAME=portfolio
DB_PASSWORD=a-long-random-password

FILESYSTEM_DISK=public
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
CACHE_STORE=database          # or redis
QUEUE_CONNECTION=sync         # nothing is queued; notifications are sent after the response

MAIL_MAILER=smtp              # only needed for contact notifications
MAIL_HOST=...
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS=no-reply@your-domain.com

CONTACT_NOTIFY_EMAIL=you@example.com   # receives contact-form messages
```

Never commit `.env`. It is already in `.gitignore`.

## 4. Install, build and migrate

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
npm ci
npm run build:ssr            # client + SSR bundles (use `npm run build` to skip SSR)
php artisan migrate --force
php artisan db:seed --force  # first deploy only; needs ADMIN_EMAIL/ADMIN_PASSWORD, or skip and:
php artisan portfolio:admin you@your-domain.com
php artisan storage:link
php artisan optimize         # caches config, routes, views and events
```

The seeder refuses to run in production without a real `ADMIN_EMAIL` and `ADMIN_PASSWORD`. Remove `ADMIN_PASSWORD` from `.env` afterwards.

## 5. Permissions

```bash
sudo chown -R $USER:www-data /var/www/portfolio
sudo find /var/www/portfolio -type f -exec chmod 644 {} \;
sudo find /var/www/portfolio -type d -exec chmod 755 {} \;
sudo chmod -R ug+rwx storage bootstrap/cache
chmod 640 .env
```

## 6. nginx

`/etc/nginx/sites-available/portfolio`:

```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/portfolio/public;
    index index.php;

    client_max_body_size 10M;   # media uploads are limited to 8 MB in the app

    gzip on;
    gzip_comp_level 6;
    gzip_types text/css application/javascript application/json image/svg+xml application/xml text/plain;

    # Fingerprinted build assets and uploaded images can be cached long-term.
    location /build/ { expires 1y; add_header Cache-Control "public, immutable"; try_files $uri =404; }
    location /storage/ { expires 30d; add_header Cache-Control "public"; try_files $uri =404; }

    location / { try_files $uri $uri/ /index.php?$query_string; }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    }

    # Never serve dotfiles (.env, .git) or PHP from uploads.
    location ~ /\.(?!well-known) { deny all; }
    location ~* ^/storage/.*\.php$ { deny all; }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/portfolio /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

The application adds `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`, `Cross-Origin-Opener-Policy`, and `Strict-Transport-Security` over HTTPS.

## 7. HTTPS

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

In production the app forces `https://` URLs (`URL::forceHttps()`), and `SESSION_SECURE_COOKIE=true` keeps cookies HTTPS-only.

## 8. Server-side rendering (recommended)

SSR sends complete HTML on the first request, which improves first paint and how link previews and crawlers see the page. If the SSR server is down, Inertia falls back to client rendering automatically.

`/etc/systemd/system/portfolio-ssr.service`:

```ini
[Unit]
Description=Portfolio Inertia SSR
After=network.target

[Service]
User=www-data
WorkingDirectory=/var/www/portfolio
ExecStart=/usr/bin/php artisan inertia:start-ssr
Restart=always

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl daemon-reload && sudo systemctl enable --now portfolio-ssr
```

To disable SSR, set `INERTIA_SSR_ENABLED=false` and build with `npm run build`.

## 9. Queues and scheduler

Not required. Contact notifications are sent after the response is delivered. No scheduled tasks are defined.

## 10. Updating

```bash
cd /var/www/portfolio
php artisan down
git pull
composer install --no-dev --optimize-autoloader
npm ci && npm run build:ssr
php artisan migrate --force
php artisan optimize
php artisan up
sudo systemctl restart php8.4-fpm portfolio-ssr
```

## 11. Backups

Back up two things: the database and uploaded files.

```bash
# /etc/cron.d/portfolio-backup (daily at 02:30, keep 14 days)
30 2 * * * www-data mysqldump --single-transaction portfolio | gzip > /var/backups/portfolio/db-$(date +\%F).sql.gz
45 2 * * * www-data tar -czf /var/backups/portfolio/files-$(date +\%F).tar.gz -C /var/www/portfolio storage/app/public storage/app/private
0 3 * * * www-data find /var/backups/portfolio -type f -mtime +14 -delete
```

Copy backups off the server (object storage or another host) and test a restore occasionally.

## 12. Go-live checklist

- [ ] `APP_DEBUG=false`, `APP_ENV=production`, correct `APP_URL` with https
- [ ] `https://your-domain.com/robots.txt` allows crawling and lists the sitemap
- [ ] `/sitemap.xml` lists your projects
- [ ] Admin password is strong; consider enabling 2FA (user menu → Settings → Security)
- [ ] Finish the dashboard setup checklist
- [ ] Add the Search Console verification code in Admin → Settings and submit the sitemap
- [ ] Send a test message through the contact form and confirm the notification email
