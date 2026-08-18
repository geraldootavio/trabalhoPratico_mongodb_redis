FROM php:8.2-apache

# Instala dependências do sistema e a extensão PHP do MongoDB
RUN apt-get update && apt-get install -y \
    libssl-dev \
    unzip \
    git \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb

# Ativa o módulo de reescrita do Apache (mod_rewrite)
RUN a2enmod rewrite

# Copia o Composer oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia o conteúdo da pasta php do projeto
COPY php/ /var/www/html/

# Instala as dependências do Composer
RUN composer install --no-dev --optimize-autoloader

# Aponta a raiz do Apache diretamente para a pasta public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Ajusta permissões de arquivos
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
