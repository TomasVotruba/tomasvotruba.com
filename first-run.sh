#!/bin/bash

set -e

# install dependencies
composer install
yarn install

# create env file
cp .env.local.dist .env

# create the manifest.json file
yarn build
