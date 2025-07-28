FROM php:8.2-apache

# Installe les extensions PHP nécessaires
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copie les fichiers de ton app
COPY . /var/www/html/

# Droits sur les fichiers
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

EXPOSE 80
