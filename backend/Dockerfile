FROM php:8.4-cli-alpine
RUN sed -i 's/dl-cdn.alpinelinux.org/mirrors.aliyun.com/g' /etc/apk/repositories

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    zip \
    unzip \
    postgresql-dev \
    linux-headers \
    $PHPIZE_DEPS

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql pcntl opcache sockets \
    && pecl install redis xdebug \
    && docker-php-ext-enable redis xdebug

# Configure Xdebug
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.log=/tmp/xdebug.log" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.discover_client_host=false" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install PHP dependencies (with dev dependencies for development)
RUN composer install --optimize-autoloader --no-interaction --verbose

# Install RoadRunner binary
RUN ./vendor/bin/rr get-binary

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port
EXPOSE 8000

# Default command (can be overridden in docker-compose)
CMD ["php", "artisan", "octane:start", "--server=roadrunner", "--host=0.0.0.0", "--port=8000"]
