#! /bin/bash

set -x

originalDirectory=$(pwd)

cd ..

if [ "$MW" == "master" ]
then
	git clone https://gerrit.wikimedia.org/r/p/mediawiki/core.git phase3 --depth 1
else
	wget https://github.com/wikimedia/mediawiki-core/archive/$MW.tar.gz
	tar -zxf $MW.tar.gz
	mv mediawiki-$MW phase3
fi

cd phase3
git checkout $MW

composer self-update
composer install --prefer-source

mysql -e 'create database its_a_mw;'
php maintenance/install.php --dbtype $DBTYPE --dbuser root --dbname its_a_mw --dbpath $(pwd) --pass nyan TravisWiki admin

cd extensions

cp -r $originalDirectory UserBitcoinAddresses

cd UserBitcoinAddresses
composer install --prefer-source
composer require 'phpunit/phpunit=3.7.*' --prefer-source

cd ../..

echo 'require_once( __DIR__ . "/extensions/UserBitcoinAddresses/UserBitcoinAddresses.php" );' >> LocalSettings.php

echo 'error_reporting(E_ALL| E_STRICT);' >> LocalSettings.php
echo 'ini_set("display_errors", 1);' >> LocalSettings.php
echo '$wgShowExceptionDetails = true;' >> LocalSettings.php
echo '$wgDevelopmentWarnings = true;' >> LocalSettings.php
echo "putenv( 'MW_INSTALL_PATH=$(pwd)' );" >> LocalSettings.php

php maintenance/update.php --quick
