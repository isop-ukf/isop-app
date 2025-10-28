FROM php:8.3-fpm-bullseye

# Update the system
RUN apt update -y
RUN apt upgrade -y

RUN apt install -y build-essential
RUN apt install -y libonig-dev zlib1g-dev libpng-dev libicu-dev libzip-dev libjpeg-dev libfreetype6-dev libwebp-dev libavif-dev libxpm-dev
RUN apt install -y nano procps git

# Install extensions
RUN docker-php-ext-install -j$(nproc) mbstring
RUN docker-php-ext-install -j$(nproc) intl
RUN docker-php-ext-install -j$(nproc) pdo_mysql
RUN docker-php-ext-install -j$(nproc) zip
RUN pecl install zstd
RUN docker-php-ext-enable zstd

# Install GD module  
RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg=/usr/local/lib --with-webp --with-xpm --with-avif
RUN docker-php-ext-install -j$(nproc) gd

# Install EXIF module
RUN docker-php-ext-configure exif --enable-exif
RUN docker-php-ext-install -j$(nproc) exif

# Install OPCache
RUN docker-php-ext-configure opcache --enable-opcache
RUN docker-php-ext-install -j$(nproc) opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Add extra configuration options
RUN echo 'memory_limit = 1024M' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini;
RUN echo 'opcache.memory_consumption = 256' >> /usr/local/etc/php/conf.d/docker-php-opcache.ini;
RUN echo 'max_execution_time = 180' >> /usr/local/etc/php/conf.d/docker-php-exec-time.ini;
RUN echo 'pm.max_children = 32' >> /usr/local/etc/php-fpm.d/zz-docker.conf;
RUN echo 'pm.max_requests = 32' >> /usr/local/etc/php-fpm.d/zz-docker.conf;
RUN echo 'listen.backlog = 256' >> /usr/local/etc/php-fpm.d/zz-docker.conf;

# OPCache tuning
RUN echo 'opcache.enable_file_override=1' >> /usr/local/etc/php/conf.d/docker-php-opcache.ini;
RUN echo 'opcache.interned_strings_buffer=20' >> /usr/local/etc/php/conf.d/docker-php-opcache.ini;

# Performance optimizations
RUN echo 'zend.assertions=-1' >> /usr/local/etc/php/conf.d/docker-php-sw-opts.ini;
RUN echo 'zend.detect_unicode=0' >> /usr/local/etc/php/conf.d/docker-php-sw-opts.ini;
RUN echo 'realpath_cache_ttl=3600' >> /usr/local/etc/php/conf.d/docker-php-sw-opts.ini;

# Copy the backend project
COPY . /app

# Run composer
WORKDIR /app
RUN composer install

# Fix permissions
RUN chgrp -R 33 /app
RUN chown -hR 33:33 /app

# Remove .env if copied
RUN rm /app/.env

# Copy the entrypoint script
COPY ./docker/entrypoint.sh /entrypoint.sh
# Make it executable
RUN chmod +x /entrypoint.sh

STOPSIGNAL SIGTERM

# Run php-fpm
CMD ["/entrypoint.sh"]