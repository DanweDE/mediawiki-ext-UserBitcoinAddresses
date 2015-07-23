# mediawiki-ext-UserBitcoinAddresses
MediaWiki extension for having users manage their Bitcoin addresses usable for multiple purposes but 
without any specific purpose implemented or suggested by this extension itself.

The main feature of the extension is its `Special:UserBitcoinAddresses` special page which allows 
each registered wiki user to associate Bitcoin addresses with him or herself. Other users can not
see those addresses by default. Other extensions might use these user related Bitcoin addresses
for various purposes.

[![Build Status](https://travis-ci.org/DanweDE/mediawiki-ext-UserBitcoinAddresses.svg?branch=master)](https://travis-ci.org/DanweDE/mediawiki-ext-UserBitcoinAddresses)

## Requirements

* MediaWiki 1.25+
* PHP 5.4+
* [Composer](https://getcomposer.org/) for the [installation](docs/INSTALL.md).

## Contributing

Feel free to fork the [code on GitHub](https://github.com/DanweDE/mediawiki-ext-UserBitcoinAddresses) 
and to submit pull requests.

You can run the PHPUnit tests by navigating into the `tests/phpunit` directory of your MediaWiki
installation and running

    php phpunit.php -c ../../extensions/UserBitcoinAddresses