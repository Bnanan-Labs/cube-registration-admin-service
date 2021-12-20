#!/bin/bash

echo "Copying over Environment variables"
cp -p .env.example .env

echo "Installing dependencies"
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs

echo "Generating application key"
./vendor/bin/sail artisan key:generate

echo "Starting up Sail"
./vendor/bin/sail up -d

echo "Setting up database"
./vendor/bin/sail artisan migrate && ./vendor/bin/sail artisan db:seed EuroSeeder

echo ""
echo "Application is ready to use!"
echo "Playground served here: http://localhost/graphql-playground"
