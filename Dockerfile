FROM php:8.2-apache

RUN a2enmod rewrite

RUN docker-php-ext-install pdo pdo_mysql

COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

RUN a2dissite 000-default && a2ensite 000-default

COPY . /var/www/html/

RUN mkdir -p /var/www/html/storage/uploads /var/www/html/storage/logs && \
    chown -R www-data:www-data /var/www/html/storage

EXPOSE 80

CMD ["apache2-foreground"]
