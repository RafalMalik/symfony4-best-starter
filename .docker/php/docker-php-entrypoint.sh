#!/bin/sh
set -e

php -d memory_limit=-1 /usr/local/bin/composer install --no-interaction

# other stuff - deployer - tests - etc
#./../deploy.sh

### Symfony Coding Standard ###

vendor/bin/phpcs --config-set installed_paths vendor/escapestudios/symfony2-coding-standard
vendor/bin/phpcs -i
#vendor/bin/phpcs .

exec "$@";