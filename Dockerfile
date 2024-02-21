#  PHP Drivers
FROM php:7.4-apache
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Instalação das dependências necessárias
RUN apt-get update \
    && apt-get install -y \
        git \
        unzip \
    && rm -rf /var/lib/apt/lists/*

# Instalação do Xdebug
RUN pecl install xdebug-3.0.4 \
    && docker-php-ext-enable xdebug

# Configurações do Xdebug
COPY xdebug.ini /usr/local/etc/php/conf.d/

# Ativar o módulo rewrite
RUN a2enmod rewrite

# Copiar o arquivo .htaccess para o diretório do documento
COPY ./.htaccess /var/www/html/

# Definir o diretório de trabalho
WORKDIR /var/www/html/

# Instalação do driver libpq-dev para auxiliar na instalação do composer
RUN apt-get update \
    && apt-get install -y libpq-dev

# Permissão de super usuário para o composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Intalação do Composer
RUN apt-get install -y unzip \ 
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instalação das dependências de desenvolvimento (incluindo o Composer)
COPY composer.json composer.lock /var/www/html/
RUN cd /var/www/html/ && composer install --no-scripts --no-autoloader
RUN cd /var/www/html/ && composer update --no-interaction