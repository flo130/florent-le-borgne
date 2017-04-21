#!/bin/bash

#quitte ce script si une erreur arrive
set -e

export SYMFONY_ENV=prod

#rÃ©cupÃ©ration des sources 
git clone https://github.com/flo130/florent-le-borgne.git
cd florent-le-borgne

#recuperation de composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"

#installation des dÃ©pendances du projet
composer install --no-dev --optimize-autoloader

#suppression du fichier Composer
rm composer.phar

#changement des droits sur les rÃ©pertoires de cache et de logs
HTTPDUSER=`ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var
sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var

#vÃ©rifie si tout est ok pour faire tourner Symfony
php bin/symfony_requirements

#vÃ©rifie si les basic de la sÃ©curitÃ© sont ok
php bin/console security:check

#crÃ©ation de la base de donnÃ©es
php bin/console doctrine:database:create

#installation du nouveau schÃ©ma
php bin/console doctrine:migrations:migrate --no-interaction

#installation des assets
php bin/console assets:install --env=prod --no-debug

#compilation des assets
php bin/console assetic:dump --env=prod --no-debug

#nettoyage du cache
php bin/console cache:clear --env=prod --no-debug
