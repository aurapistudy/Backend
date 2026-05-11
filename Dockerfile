FROM php:8.2-cli-bookworm

ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /app

RUN apt-get update && apt-get install -y \
    curl \
    git \
    imagemagick \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmagickwand-dev \
    libpng-dev \
    libzip-dev \
    nodejs \
    npm \
    unzip \
    zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install bcmath gd mbstring pdo_mysql zip \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
RUN npm ci
RUN npm run build

COPY railway-start.sh /usr/local/bin/railway-start
RUN chmod +x /usr/local/bin/railway-start

CMD ["railway-start"]
