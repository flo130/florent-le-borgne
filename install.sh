#!/bin/bash

#quitte ce script si une erreur arrive
set -e

#récupération des sources 
#git clone https://github.com/flo130/florent-le-borgne

#recuperation de composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"

#installation des dépendances du projet
php composer.phar install --no-dev --optimize-autoloader

#suppression du fichier Composer
rm composer.phar

#changement des droits sur les répertoires de cache et de logs
chmod g+w var/cache var/logs

#vérifie si tout est ok pour faire tourner Symfony
php bin/symfony_requirements

#création de la base de données
php bin/console doctrine:database:create

#installation du nouveau schéma
php bin/console doctrine:migrations:migrate --no-interaction

#installation des assets
php bin/console assets:install --env=prod --no-debug

#compilation des assets
php bin/console assetic:dump --env=prod --no-debug

#nettoyage du cache
php bin/console cache:clear --env=prod --no-debug
