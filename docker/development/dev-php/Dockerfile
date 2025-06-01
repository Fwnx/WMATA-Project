FROM php:8.4-cli-alpine

# Install system dependencies
RUN apk add --no-cache \
    bash \
    git \
    # Required for building PHP extensions
    autoconf \
    g++ \
    make

# Install PHP extensions
RUN pecl install redis && \
    docker-php-ext-enable redis && \
    # Install other required PHP extensions
    docker-php-ext-install opcache

# Install Composer https://getcomposer.org/download/
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'.PHP_EOL; } else { echo 'Installer corrupt'.PHP_EOL; unlink('composer-setup.php'); exit(1); }" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');"

# Add Composer to the path
RUN mv composer.phar /usr/local/bin/composer

# Install Symfony CLI
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash && \
    apk add symfony-cli

# Set recommended PHP.ini settings
RUN echo "memory_limit=256M" > /usr/local/etc/php/conf.d/memory-limit.ini

WORKDIR /var/www

# Copy .env.local if it exists
COPY .env.local /var/www/.env.local

EXPOSE 8000