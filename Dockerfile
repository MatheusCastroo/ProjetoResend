FROM php:8.2-apache-bookworm

RUN docker-php-ext-install pdo_mysql \
    && a2enmod rewrite

COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

COPY . /var/www/html

RUN chmod +x /var/www/html/docker/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/var/www/html/docker/entrypoint.sh"]
CMD ["apache2-foreground"]
