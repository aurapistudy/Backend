FROM php:8.2-cli-bookworm

ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /app

RUN apt-get update && apt-get install -y \
    curl \
    git \
    imagemagick \
    librsvg2-bin \
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
    && pecl install imagick-3.7.0 \
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

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
RUN npm ci
RUN npm run build

COPY railway-start.sh /usr/local/bin/railway-start
RUN chmod +x /usr/local/bin/railway-start

CMD ["railway-start"]
