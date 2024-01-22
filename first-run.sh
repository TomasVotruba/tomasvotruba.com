#!/bin/bash

# Exit immediately if a pipeline, a list, or a compound command exits with a non-zero status.
set -e

# install dependencies
composer install
yarn install

# create env file
cp .env.local .env

chgrp -R www-data database
chmod -R g+rw database
mkdir -p storage/framework/{cache,sessions,views}

# create the manifest.json file
yarn build

# since Laravel 11 ↓

# needed for clear:cache to work
php artisan migrate --force
