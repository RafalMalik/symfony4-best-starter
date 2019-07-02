#!/bin/sh
set -e

php -d memory_limit=-1 /usr/local/bin/composer install --no-interaction

# other stuff - deployer - tests - etc
#./../deploy.sh

exec "$@";