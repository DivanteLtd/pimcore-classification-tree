#!/bin/bash

set -eu

PROJECT_DIR="./tmp/"
DEPENDENCIES=$(pwd)"/dependencies.txt"

PACKAGE_NAME="ClassificationTreeBundle"
BUNDLE_NAME="DivanteClassificationTreeBundle"

DB_HOST="192.168.10.10"
DB_PORT=3306
DB_USERNAME="homestead"
DB_PASSWORD="secret"
DB_DATABASE="pimcore_test"

echo -e "\e[34m=> Start installing project \e[0m"

echo -e "\e[32m=> Clean old project files \e[0m"
rm -rf $PROJECT_DIR

echo -e "\e[32m=> Cloning Pimcore Skeleton \e[0m"
git clone https://github.com/pimcore/skeleton.git $PROJECT_DIR

echo -e "\e[32m=> Copy package to project \e[0m"
cp -r src/$PACKAGE_NAME $PROJECT_DIR/src/
cp -r tests $PROJECT_DIR/tests/
cp -r phpunit.xml $PROJECT_DIR/phpunit.xml
cp -r composer.json $PROJECT_DIR/composer.local.json

cd $PROJECT_DIR

echo -e "\e[32m=> Install dependencies \e[0m"
COMPOSER_DISCARD_CHANGES=true COMPOSER_MEMORY_LIMIT=-1 composer install --no-interaction --optimize-autoloader

echo -e "\e[32m=> Create Database \e[0m"
mysql --host=$DB_HOST --port=$DB_PORT --user=$DB_USERNAME --password=$DB_PASSWORD \
    -e "DROP DATABASE IF EXISTS $DB_DATABASE; CREATE DATABASE pimcore_test CHARSET=utf8mb4;"

echo -e "\e[32m=> Install Pimcore \e[0m"
vendor/bin/pimcore-install \
    --ignore-existing-config \
    --admin-username admin \
    --admin-password admin \
    --mysql-host-socket $DB_HOST \
    --mysql-database pimcore_test \
    --mysql-username $DB_USERNAME \
    --mysql-password $DB_PASSWORD \
    --mysql-port $DB_PORT \
    --no-debug \
    --no-interaction \

echo -e "\e[32m=> Enable Bundles \e[0m"
while read -r line; do
    bin/console pimcore:bundle:enable $line -n
done < $DEPENDENCIES

bin/console pimcore:bundle:enable $BUNDLE_NAME -n

echo -e "\e[32m=> Done! \e[0m"
