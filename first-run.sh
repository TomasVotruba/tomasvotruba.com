# install dependnencies
composer install
yarn install

# create env file
cp .env.local.dist .env

# create the manifest.json file
yarn build
