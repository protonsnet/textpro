FROM php:8.2-apache

# 1. Instalar dependências do sistema e extensões PHP necessárias
# libicu-dev é para internacionalização, libpng-dev para imagens
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql gd zip intl

# 2. Ativar mod_rewrite para o roteamento do seu Framework (MVC)
RUN a2enmod rewrite

# 3. Configurar o DocumentRoot corretamente
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 4. Ajustar permissões para que o Apache do Docker possa escrever no storage montado
# O usuário 'www-data' do Docker precisa ser dono da pasta
RUN chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html

# Expor a porta 80 interna do container
EXPOSE 80