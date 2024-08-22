#  PHP Drivers
FROM php:7.4-apache

# Instalação das dependências necessárias
RUN apt-get update && apt-get install -y \
    libxml2-dev \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    zlib1g-dev \
    libzip-dev \
    ca-certificates && \
    update-ca-certificates \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd zip pdo pdo_mysql mysqli soap \
    && rm -rf /var/lib/apt/lists/*

# Baixe e instale o stripe-cli
RUN curl -L -O https://github.com/stripe/stripe-cli/releases/download/v1.19.4/stripe_1.19.4_linux_arm64.tar.gz

# Extraia o stripe-cli
RUN tar -xzf stripe_1.19.4_linux_arm64.tar.gz -C /usr/local/bin/
RUN chmod +x /usr/local/bin/stripe

# Remova o arquivo tar.gz
RUN rm stripe_1.19.4_linux_arm64.tar.gz

# Copiar certificados SSL para o conteiner php-apache
COPY ssl/localhost.pem /etc/ssl/certs/localhost.pem
COPY ssl/localhost-key.pem /etc/ssl/private/localhost-key.pem

# Copiar arquivos de configuração
COPY apache-config/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY apache-config/default-ssl.conf /etc/apache2/sites-available/default-ssl.conf

# Atualização das configurações do Apache para usar SSL
RUN a2enmod ssl
RUN a2enmod socache_shmcb
RUN a2ensite default-ssl
RUN a2ensite 000-default

# Reiniciar o Apache
RUN service apache2 restart

# Definir permissões
RUN chown -R www-data:www-data /var/www/html/

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