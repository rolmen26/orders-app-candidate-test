FROM dunglas/frankenphp:php8.3-alpine AS backend

WORKDIR /app

RUN apk add --no-cache \
    curl \
    && apk add --no-cache --virtual .build-deps \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libzip-dev \
        icu-dev \
        oniguruma-dev \
        libxml2-dev \
        postgresql-dev \
        sqlite-dev \
        autoconf \
        g++ \
        make \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        pgsql \
        zip \
        bcmath \
        intl \
        mbstring \
        xml \
        soap \
        gd \
        exif \
        pcntl \
        opcache \
    && apk del .build-deps \
    && rm -rf /tmp/pear /var/cache/apk/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY composer.json ./

RUN composer install \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts \
    --no-progress \
    --no-interaction \
    && rm -rf /root/.composer

COPY . .


WORKDIR /app

COPY package.json package-lock.json ./

RUN npm ci

COPY . .
COPY --from=backend /app/vendor /app/vendor

RUN npm run build

FROM dunglas/frankenphp:php8.3-alpine AS production

LABEL maintainer="Rommel" \
      description="Docker image for Laravel Gymdesk App - Production with Nginx + Supervisor" \
      version="3.0"

ENV TZ=America/Guayaquil \
    HTTP_PORT=80 \
    OCTANE_PORT=8000

RUN apk add --no-cache \
    tzdata \
    ca-certificates \
    libpng \
    libjpeg-turbo \
    freetype \
    libzip \
    icu-libs \
    oniguruma \
    libxml2 \
    postgresql-libs \
    sqlite-libs \
    nginx \
    supervisor \
    && cp /usr/share/zoneinfo/${TZ} /etc/localtime \
    && echo ${TZ} > /etc/timezone \
    && apk del tzdata \
    && rm -rf /var/cache/apk/*

COPY --from=backend /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=backend /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

COPY ./etc/php/php.ini /usr/local/etc/php/conf.d/99-custom.ini
COPY ./etc/nginx/default.conf /etc/nginx/http.d/default.conf

COPY ./etc/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

WORKDIR /app

COPY --from=backend --chown=www-data:www-data /app/vendor /app/vendor
COPY --from=frontend --chown=www-data:www-data /app/public/build /app/public/build

COPY --chown=www-data:www-data . .

RUN mkdir -p \
    /app/storage/framework/cache \
    /app/storage/framework/sessions \
    /app/storage/framework/views \
    /app/storage/logs \
    /app/bootstrap/cache \
    /var/log/supervisor \
    /var/log/nginx \
    /run/nginx \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache \
    && chown -R www-data:www-data /var/log/nginx \
    && chown -R www-data:www-data /var/lib/nginx

EXPOSE ${HTTP_PORT}

EXPOSE ${OCTANE_PORT}

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

