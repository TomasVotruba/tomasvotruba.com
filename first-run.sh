#!/bin/bash

# Exit immediately if a pipeline, a list, or a compound command exits with a non-zero status.
set -e

# install dependencies
composer install
yarn install

# create env file
cp .env.local.dist .env

# create the manifest.json file
yarn build
