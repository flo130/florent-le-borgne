#!/bin/bash

#quitte ce script si une erreur arrive
set -e

export SYMFONY_ENV=prod

#update des sources
git fetch origin master
git reset --hard origin/master

#installation du nouveau schema
php bin/console doctrine:migrations:migrate --no-interaction

#installation des assets
php bin/console assets:install --env=prod --no-debug

#compilation des assets
php bin/console assetic:dump --env=prod --no-debug