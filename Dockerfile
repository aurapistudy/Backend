FROM php:8.2-cli-bookworm

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_MEMORY_LIMIT=-1 \
    NODE_OPTIONS=--max-old-space-size=2048 \
    DEBIAN_FRONTEND=noninteractive

WORKDIR /app

RUN apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates \
    curl \
    git \
    gnupg \
    imagemagick \
    librsvg2-bin \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmagickwand-dev \
    libonig-dev \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    unzip \
    zip \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) bcmath gd mbstring pdo_mysql zip \
    && printf "\n" | pecl install imagick-3.7.0 \
    && docker-php-ext-enable imagick \
    && if [ -f /etc/ImageMagick-6/policy.xml ]; then \
         sed -i 's/rights="none" pattern="SVG"/rights="read|write" pattern="SVG"/g' /etc/ImageMagick-6/policy.xml; \
         sed -i 's/rights="none" pattern="MVG"/rights="read|write" pattern="MVG"/g' /etc/ImageMagick-6/policy.xml; \
         sed -i 's/rights="none" pattern="PS"/rights="read|write" pattern="PS"/g' /etc/ImageMagick-6/policy.xml; \
         sed -i 's/rights="none" pattern="PDF"/rights="read|write" pattern="PDF"/g' /etc/ImageMagick-6/policy.xml; \
       fi \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-scripts

COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

COPY . .

RUN npm run build && rm -rf node_modules
RUN composer dump-autoload --optimize --no-dev

COPY railway-start.sh /usr/local/bin/railway-start
RUN chmod +x /usr/local/bin/railway-start

EXPOSE 8080

CMD ["railway-start"]
