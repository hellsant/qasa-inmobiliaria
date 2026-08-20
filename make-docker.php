<?php
// make-docker.php · Uso: php make-docker.php
$root = __DIR__;
@mkdir($root.'/docker', 0777, true);

/* ===== Dockerfile ===== */
file_put_contents($root.'/Dockerfile', <<<'EOF'
FROM php:8.4-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx supervisor unzip libzip-dev libpng-dev libjpeg62-turbo-dev libwebp-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) gd pdo_sqlite zip \
    && rm -rf /var/lib/apt/lists/* \
    && rm -f /etc/nginx/sites-enabled/default

RUN echo "upload_max_filesize=300M\npost_max_size=310M\nmemory_limit=512M\nmax_execution_time=600" \
    > /usr/local/etc/php/conf.d/uploads.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh && chown -R www-data:www-data /var/www/html

EXPOSE 80
CMD ["/entrypoint.sh"]
EOF
);

/* ===== nginx ===== */
file_put_contents($root.'/docker/nginx.conf', <<<'EOF'
server {
    listen 80;
    server_name _;
    root /var/www/html/public;
    index index.php;
    client_max_body_size 310M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
    location ~ /\.(?!well-known) { deny all; }
}
EOF
);

/* ===== supervisor ===== */
file_put_contents($root.'/docker/supervisord.conf', <<<'EOF'
[supervisord]
nodaemon=true
logfile=/var/log/supervisord.log

[program:php-fpm]
command=php-fpm -F
autostart=true
autorestart=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0

[program:nginx]
command=nginx -g "daemon off;"
autostart=true
autorestart=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
EOF
);

/* ===== entrypoint ===== */
file_put_contents($root.'/docker/entrypoint.sh', <<<'EOF'
#!/bin/sh
set -e
cd /var/www/html

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views \
         storage/logs storage/app/public bootstrap/cache
touch database/database.sqlite

if [ ! -f .env ]; then cp .env.example .env; fi
if ! grep -q "^APP_KEY=base64" .env; then php artisan key:generate --force || true; fi

php artisan storage:link || true
php artisan migrate --force
chown -R www-data:www-data storage bootstrap/cache database

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
EOF
);

/* ===== docker-compose ===== */
file_put_contents($root.'/docker-compose.yml', <<<'EOF'
services:
  qasa:
    build: .
    container_name: qasa
    restart: unless-stopped
    ports:
      - "80:80"
    volumes:
      - storage:/var/www/html/storage
      - dbdata:/var/www/html/database
    environment:
      - APP_ENV=production
      - APP_DEBUG=false

volumes:
  storage:
  dbdata:
EOF
);

/* ===== .dockerignore ===== */
file_put_contents($root.'/.dockerignore', <<<'EOF'
.git
.env
*.sqlite
vendor
node_modules
storage/app/public/*
storage/logs/*
storage/framework/views/*
docker-compose.yml
EOF
);

echo "✔ Archivos Docker creados: Dockerfile, docker-compose.yml, docker/{nginx,supervisord,entrypoint}, .dockerignore\n";
echo "\nAhora subilos a Git:\n";
echo "  git add .\n  git commit -m \"Docker deploy\"\n  git push\n";