FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y git \
    libcurl4 \
    libcurl4-openssl-dev

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql \
    curl

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www