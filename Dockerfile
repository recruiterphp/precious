FROM php:7.4-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libssl-dev \
    libcurl4-openssl-dev \
    pkg-config \
    && rm -rf /var/lib/apt/lists/*

# Copy Composer from official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy composer files
COPY composer.json composer.lock* ./

# Required to allow autoload
RUN mkdir -p tests/data tests/PHPStan/Rule/data/ tests/PHPStan/Reflection/data/

# Install dependencies including dev dependencies for testing
RUN composer install

# Copy application code
COPY . .

# Includes autoload for classes under test
RUN composer dumpautoload

# Set environment variable for Composer
ENV COMPOSER_ALLOW_SUPERUSER=1

CMD ["tail", "-f", "/dev/null"]
