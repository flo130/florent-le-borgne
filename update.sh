#!/bin/bash

#quitte ce script si une erreur arrive
set -e

export SYMFONY_ENV=dev

#update des sources
git fetch origin master
git reset --hard origin/master

#recuperation de composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"

#installation des dependances du projet
php composer.phar update

#suppression du fichier Composer
rm composer.phar

#installation du nouveau schema
php bin/console doctrine:migrations:migrate --no-interaction

#installation des assets
php bin/console assets:install --env=dev --no-debug

#compilation des assets
php bin/console assetic:dump --env=dev --no-debug

#nettoyage du cache
php bin/console cache:clear --env=dev --no-debug
