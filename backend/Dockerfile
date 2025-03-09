# PHP FPM Stage
FROM php:8.2-fpm AS php

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    git \
    curl \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    unzip \
    libpq-dev \
    locales \
    jpegoptim optipng pngquant gifsicle \
    supervisor \
    cron \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    opcache \
    xml

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Configure opcache for production
RUN docker-php-ext-configure opcache --enable-opcache \
    && echo 'opcache.memory_consumption=128' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo 'opcache.interned_strings_buffer=8' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo 'opcache.max_accelerated_files=4000' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo 'opcache.revalidate_freq=2' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo 'opcache.fast_shutdown=1' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo 'opcache.enable_cli=1' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

# Set up PHP configuration for Laravel
RUN echo 'memory_limit = 512M' >> /usr/local/etc/php/conf.d/docker-php-memory-limit.ini \
    && echo 'upload_max_filesize = 100M' >> /usr/local/etc/php/conf.d/docker-php-upload-limit.ini \
    && echo 'post_max_size = 100M' >> /usr/local/etc/php/conf.d/docker-php-upload-limit.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create Laravel directory structure first
RUN mkdir -p \
    /var/www/html/app \
    /var/www/html/bootstrap/cache \
    /var/www/html/config \
    /var/www/html/database \
    /var/www/html/public \
    /var/www/html/resources \
    /var/www/html/routes \
    /var/www/html/storage/app/public \
    /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/testing \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/logs

# Copy configuration files
COPY php.ini /usr/local/etc/php/php.ini
COPY .env.example /var/www/html/.env.example

# Create laravel user and group
RUN groupadd -g 1000 laravel && \
    useradd -u 1000 -g laravel -m -s /bin/bash laravel && \
    mkdir -p /home/laravel/.composer && \
    chown -R laravel:laravel /home/laravel

# Set proper ownership and permissions
RUN chown -R laravel:www-data /var/www/html && \
    chmod -R 775 /var/www/html/storage && \
    chmod -R 775 /var/www/html/bootstrap/cache

# Set up php-fpm to use the laravel user
RUN echo "user = laravel" >> /usr/local/etc/php-fpm.d/www.conf && \
    echo "group = www-data" >> /usr/local/etc/php-fpm.d/www.conf

# The application code will be mounted later or copied during deployment
# COPY . /var/www/html

# Switch to non-root user
USER laravel

# Expose port 9000 for php-fpm
EXPOSE 9000

# Start php-fpm
CMD ["php-fpm"]

# Nginx Stage
FROM nginx:stable-alpine AS nginx

# Copy nginx configuration
COPY ./docker/nginx/conf.d /etc/nginx/conf.d/

# Remove default nginx site
RUN rm -rf /etc/nginx/conf.d/default.conf

WORKDIR /var/www/html

# Copy application code from PHP stage (will be empty since we're mounting volumes)
COPY --from=php /var/www/html /var/www/html

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]

