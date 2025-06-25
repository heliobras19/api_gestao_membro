# Usar imagem oficial do PHP com Apache
FROM php:8.2-apache

# Ativar o mod_rewrite (Laravel precisa para rotas bonitas)
RUN a2enmod rewrite

# Instalar dependências PHP para Laravel
RUN apt-get update && apt-get install -y \
    git unzip zip curl libzip-dev libonig-dev libxml2-dev libcurl4-openssl-dev \
    && docker-php-ext-install pdo pdo_mysql zip mbstring xml curl

# Instalar o Composer corretamente
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


# Copiar configuração customizada do Apache
COPY conf/laravel.conf /etc/apache2/sites-available/000-default.conf

# Copiar o código da aplicação
COPY . /var/www/html

# Definir diretório de trabalho
WORKDIR /var/www/html

# Instalar dependências Laravel
RUN composer install --optimize-autoloader --no-dev --no-interaction

# Corrigir permissões
RUN mkdir -p storage/logs storage/framework/{cache,sessions,views} bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Definir variáveis de ambiente do Laravel
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr

# Expor porta 80 (Apache)
EXPOSE 80
