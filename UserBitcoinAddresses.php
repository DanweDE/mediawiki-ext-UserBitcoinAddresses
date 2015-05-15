<?php
/**
 * Initialization file for the "User Bitcoin Addresses" extension.
 *
 * @licence MIT License
 * @author Daniel A. R. Werner <daniel.a.r.werner@gmail.com>
 *
 * @codeCoverageIgnore
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

if ( defined( 'UserBitcoinAddresses_VERSION' ) ) {
	return 1;
}
define( 'UserBitcoinAddresses_VERSION', '1.0.0 alpha' );

// Include Composer autoloader if present:
if ( is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
	include_once( __DIR__ . '/vendor/autoload.php' );
}

use MediaWiki\Ext\UserBitcoinAddresses as UserBtcAddr;

call_user_func( function() {
	global $wgExtensionCredits, $wgMessagesDirs, $wgExtensionFunctions;

	$wgExtensionCredits[ 'specialpage' ][] = array(
		'path' => __FILE__,
		'name' => 'User Bitcoin Addresses',
		'descriptionmsg' => 'userbtcaddr-desc',
		'version' => UserBitcoinAddresses_VERSION,
		'author' => array(
			'[https://www.mediawiki.org/wiki/User:Danwe Daniel A. R. Werner]',
		),
		'url' => 'https://www.mediawiki.org/wiki/Extension:UserBitcoinAddresses',
		'license-name' => 'MIT'
	);

	$wgMessagesDirs['UserBitcoinAddresses'] = __DIR__ . '/i18n';

	$wgExtensionFunctions[] = function() {};
} );

require_once 'UserBitcoinAddresses.settings.php';
