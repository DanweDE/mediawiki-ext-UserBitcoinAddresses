# User Bitcoin Addresses installation

These are the installation and configuration instructions for the ["User Bitcoin Addresses" MediaWiki extension](../README.md).

## Versions

1.0.0 will be the first version, currently still in development.

## Download and installation

The recommended way to download and install the extension is with [Composer](http://getcomposer.org) using
[MediaWiki 1.22 built-in support for Composer](https://www.mediawiki.org/wiki/Composer). MediaWiki
versions prior to 1.22 can try using Composer via the
[Extension Installer](https://github.com/JeroenDeDauw/ExtensionInstaller/blob/master/README.md)
extension.

#### Installation in MediaWiki 1.22 or higher

Go to the root directory of your MediaWiki installation.

If you have not installed Composer yet, just download http://getcomposer.org/composer.phar into your
current directory.

    wget http://getcomposer.org/composer.phar

Using Composer, install the latest version of the extension with the following command:

    php composer.phar require mediawiki/user-bitcoin-addresses "*"

#### Verify installation success

As final step, you can verify the extension got installed by looking at the Special:Version page on your wiki and verifying the
"User Bitcoin Addresses" extension is listed.

## Configuration

There are currently no configuration options and no MediaWiki extension globals to be documented.
