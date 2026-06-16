# Deployment Guide

This guide covers deploying the application on a normal VPS using **Nginx**, **PHP 8.3 FPM**, and **MySQL** (or MariaDB) on Ubuntu 24.04 LTS.

---

## 1. Server Requirements

- Ubuntu 24.04 LTS (or any Debian-based server)
- Nginx
- PHP 8.3 FPM with required extensions
- MySQL 8.0+ or MariaDB 10.6+
- Composer 2.x
- Node.js 20+ and npm (for building frontend assets)
- A domain pointed to the server (`mautamu.site`)
- SSL certificate (Let's Encrypt recommended)

---

## 2. Install Dependencies

```bash
# Update the system
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y nginx mysql-server php8.3-fpm php8.3-cli php8.3-mysql \
    php8.3-mbstring php8.3-xml php8.3-bcmath php8.3-curl php8.3-zip \
    php8.3-intl php8.3-gd php8.3-sqlite3 php8.3-redis php8.3-opcache \
    unzip git curl npm

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

---

## 3. Create the Application User and Directory

```bash
# Create a dedicated user (optional but recommended)
sudo useradd -m -s /bin/bash tgen

# Create the web root
sudo mkdir -p /var/www/t-gen
sudo chown -R tgen:tgen /var/www/t-gen
```

---

## 4. Clone the Project

Run as the `tgen` user:

```bash
sudo -u tgen -H bash

cd /var/www/t-gen
git clone <your-repo-url> .
```

---

## 5. Install PHP and Node Dependencies

```bash
cd /var/www/t-gen

# Install PHP packages
composer install --no-dev --optimize-autoloader

# Install frontend dependencies
npm ci

# Build production assets
npm run build
```

---

## 6. Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your production values. At minimum, update:

```dotenv
APP_NAME=T-Gen
APP_ENV=production
APP_DEBUG=false
APP_URL=https://mautamu.site

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tgen
DB_USERNAME=tgen
DB_PASSWORD=your_secure_password

CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database

# Mail
MAIL_MAILER=smtp
MAIL_HOST=mail.example.com
MAIL_PORT=587
MAIL_USERNAME=noreply@mautamu.site
MAIL_PASSWORD=your_mail_password
MAIL_FROM_ADDRESS=noreply@mautamu.site
MAIL_FROM_NAME="${APP_NAME}"

# Payment gateway keys (fill the ones you use)
SONICPESA_API_KEY=
SNIPPE_API_KEY=
SNIPPE_WEBHOOK_URL=https://mautamu.site/webhook/snippe
MOBILIPA_API_KEY=
```

---

## 7. Create the Database

```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE tgen CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'tgen'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON tgen.* TO 'tgen'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Back in the app directory:

```bash
php artisan migrate --force
php artisan db:seed --class=PaymentGatewaySeeder --force
```

---

## 8. Set File Permissions

```bash
sudo chown -R tgen:www-data /var/www/t-gen
sudo find /var/www/t-gen -type f -exec chmod 644 {} \;
sudo find /var/www/t-gen -type d -exec chmod 755 {} \;

# Laravel writable directories
sudo chmod -R 775 /var/www/t-gen/storage
sudo chmod -R 775 /var/www/t-gen/bootstrap/cache

# Make sure group sticky bit is set for new files
sudo chmod -R g+s /var/www/t-gen/storage /var/www/t-gen/bootstrap/cache
```

---

## 9. Nginx Configuration

Create a new Nginx server block:

```bash
sudo nano /etc/nginx/sites-available/mautamu.site
```

Paste the following configuration:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name mautamu.site www.mautamu.site;
    root /var/www/t-gen/public;
    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Block access to sensitive Laravel files
    location ~ /\.env {
        deny all;
    }

    # Allow large video uploads (custom pages)
    client_max_body_size 512M;

    # Logging
    access_log /var/log/nginx/mautamu.site-access.log;
    error_log  /var/log/nginx/mautamu.site-error.log;
}
```

Enable the site and remove the default:

```bash
sudo ln -s /etc/nginx/sites-available/mautamu.site /etc/nginx/sites-enabled/
sudo rm /etc/nginx/sites-enabled/default

sudo nginx -t
sudo systemctl reload nginx
```

---

## 10. PHP-FPM Pool Tuning (optional)

Edit the PHP 8.3 FPM pool:

```bash
sudo nano /etc/php/8.3/fpm/pool.d/www.conf
```

Make sure the user matches your web server user:

```ini
user = www-data
group = www-data
listen.owner = www-data
listen.group = www-data
```

Restart PHP-FPM after changes:

```bash
sudo systemctl restart php8.3-fpm
```

---

## 11. SSL with Let's Encrypt

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d mautamu.site -d www.mautamu.site
```

Certbot will automatically update the Nginx config to use HTTPS and redirect HTTP to HTTPS.

---

## 12. Queue Worker with Supervisor

The app uses the `database` queue driver. Install Supervisor and configure a worker:

```bash
sudo apt install -y supervisor
sudo nano /etc/supervisor/conf.d/tgen-worker.conf
```

```ini
[program:tgen-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/t-gen/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=tgen
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/supervisor/tgen-worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start "tgen-worker:*"
```

---

## 13. Scheduler Cron

Add the Laravel scheduler to cron:

```bash
sudo crontab -u tgen -e
```

```cron
* * * * * cd /var/www/t-gen && php artisan schedule:run >> /dev/null 2>&1
```

---

## 14. Caching for Production

After deployment, run:

```bash
cd /var/www/t-gen
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

Clear caches during updates:

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear
```

---

## 15. Deployment Update Script

Use this checklist when deploying updates:

```bash
cd /var/www/t-gen

git pull origin main
composer install --no-dev --optimize-autoloader
npm ci
npm run build

php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

sudo supervisorctl restart "tgen-worker:*"
```

---

## 16. Useful Commands

```bash
# Check Nginx status
sudo systemctl status nginx

# Check PHP-FPM status
sudo systemctl status php8.3-fpm

# Tail Laravel logs
tail -f /var/www/t-gen/storage/logs/laravel.log

# Tail Nginx error log
sudo tail -f /var/log/nginx/mautamu.site-error.log
```

---

## Notes

- Keep `.env` file secure and never commit it.
- Use strong passwords for the database and mail accounts.
- Enable automatic firewall rules (`ufw allow 'Nginx Full'` and `ufw allow OpenSSH`).
- The `client_max_body_size 512M` setting is required for custom page video uploads.
