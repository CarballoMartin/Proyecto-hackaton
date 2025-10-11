# docker/php/Dockerfile
FROM php:8.2-fpm-alpine

# Argumentos para el UID/GID del usuario
ARG PUID=1000
ARG PGID=1000

# Instalar dependencias del sistema
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    libpq \
    postgresql-dev \
    oniguruma-dev \
    libxml2-dev \
    linux-headers \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install pdo_pgsql \
    && docker-php-ext-install zip \
    && docker-php-ext-install exif \
    && docker-php-ext-install pcntl \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install opcache \
    && docker-php-ext-install intl \
    && docker-php-ext-install soap \
    && docker-php-ext-install sockets \
    && docker-php-ext-install xml \
    && docker-php-ext-install mbstring

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crear usuario y grupo para la aplicación
RUN addgroup -g ${PGID} laravel \
    && adduser -u ${PUID} -G laravel -s /bin/sh -D laravel

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar el código de la aplicación (se hará a través de volúmenes en docker-compose para desarrollo, pero es bueno para producción)
COPY . /var/www/html

# Crear directorios de storage y cache que fueron ignorados por .dockerignore
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache/data storage/logs bootstrap/cache

# Configurar permisos
RUN chown -R laravel:laravel /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Exponer el puerto de PHP-FPM
EXPOSE 9000

# Cambiar al usuario de la aplicación
USER laravel

# Comando para iniciar PHP-FPM
CMD ["php-fpm"]