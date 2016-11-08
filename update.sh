#!/bin/bash

#quitte ce script si une erreur arrive
set -e

#update des sources
git fetch origin master
git reset --hard origin/master

#recuperation de composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"

#installation des dépendances du projet
php composer.phar update

#suppression du fichier Composer
rm composer.phar

#changement des droits sur les répertoires de cache et de logs
chmod g+w var/cache var/logs

#vérifie si tout est ok pour faire tourner Symfony
php bin/symfony_requirements

#installation du nouveau schéma
php bin/console doctrine:migrations:migrate --no-interaction

#installation des assets
php bin/console assets:install --env=prod --no-debug

#compilation des assets
php bin/console assetic:dump --env=prod --no-debug

#nettoyage du cache
php bin/console cache:clear --env=prod --no-debug
