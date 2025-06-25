# Dockerfile para Render
FROM richarvey/nginx-php-fpm:3.1.6

# Copiar código da aplicação
COPY . .

# Script de configuração Laravel
COPY conf/laravel.conf /etc/nginx/sites-available/default.conf

# Definir variáveis de ambiente
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Laravel specific
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

# Definir diretório de trabalho
WORKDIR /var/www/html

# Instalar dependências
RUN composer install --optimize-autoloader --no-dev --no-interaction

# Compilar assets (se usares Vite/Mix)
#RUN npm ci && npm run build

# Configurar permissões
RUN chown -Rf www-data.www-data /var/www/html/storage/ /var/www/html/bootstrap/cache/
RUN chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Expor porta
EXPOSE 80