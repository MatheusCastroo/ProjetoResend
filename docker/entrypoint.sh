#!/bin/bash
set -e

php /var/www/html/docker/wait-db.php

exec "$@"
