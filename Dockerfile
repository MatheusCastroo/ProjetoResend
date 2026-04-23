# Imagem leve: PHP CLI + server embutido (sem Apache). Ideal para o painel/API.
FROM php:8.2-cli-alpine3.20

RUN set -eux; \
  apk add --no-cache --virtual .build-ext-deps $PHPIZE_DEPS \
    curl-dev \
    mariadb-connector-c-dev; \
  docker-php-ext-install pdo_mysql curl; \
  apk del .build-ext-deps; \
  rm -rf /tmp/* /var/cache/apk/* /var/www/html/*

WORKDIR /var/www/html

COPY . /var/www/html

RUN chmod +x /var/www/html/docker/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/var/www/html/docker/entrypoint.sh"]
CMD ["php", "-S", "0.0.0.0:80", "-t", "/var/www/html/public", "/var/www/html/docker/router.php"]
