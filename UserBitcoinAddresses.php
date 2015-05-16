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

// Never load twice:
if( class_exists( 'MediaWiki\Ext\UserBitcoinAddresses\Extension' ) ) {
	return 1;
}

// Include Composer autoloader if present:
if ( is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
	include_once( __DIR__ . '/vendor/autoload.php' );
}

use MediaWiki\Ext\UserBitcoinAddresses\Extension;
use MediaWiki\Ext\UserBitcoinAddresses\ExtensionSetup;
use MediaWiki\Ext\UserBitcoinAddresses\ExtensionSettings;

call_user_func( function() {
	global $wgExtensionCredits, $wgMessagesDirs, $wgExtensionFunctions;

	$wgExtensionCredits[ 'specialpage' ][] = array(
		'path' => __FILE__,
		'name' => 'User Bitcoin Addresses',
		'descriptionmsg' => 'userbtcaddr-desc',
		'version' => Extension::VERSION,
		'author' => array(
			'[https://www.mediawiki.org/wiki/User:Danwe Daniel A. R. Werner]',
		),
		'url' => 'https://www.mediawiki.org/wiki/Extension:UserBitcoinAddresses',
		'license-name' => 'MIT'
	);

	$wgMessagesDirs['UserBitcoinAddresses'] = __DIR__ . '/i18n';

	$wgExtensionFunctions[] = function() {
		global $wgHooks;

		$extension = new Extension( ExtensionSettings::newFromGlobals( $GLOBALS ) );
		$extensionSetup = new ExtensionSetup( $extension, $GLOBALS, __DIR__ );

		$extensionSetup->run();
	};
} );

require_once 'UserBitcoinAddresses.settings.php';
