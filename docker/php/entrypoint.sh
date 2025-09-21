#!/bin/sh

if [ ! -d /var/www/vendor ]; then
    composer install
fi

php bin/console doctrine:migrations:migrate

exec docker-php-entrypoint "$@"