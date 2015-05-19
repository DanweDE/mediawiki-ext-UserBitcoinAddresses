<?php
/**
 * @licence MIT Licence
 * @author Daniel A. R. Werner < daniel.a.r.werner@gmail.com >
 *
 * @codeCoverageIgnore
 */
return call_user_func( function() {

	$moduleTemplate = array(
		'localBasePath' => __DIR__,
		'remoteExtPath' => 'UserBitcoinAddresses/resources/src',
	);

	return array(
		'mw.ext.userBitcoinAddresses' => $moduleTemplate + array(
			'scripts' => array(
				'mw.ext.userBitcoinAddresses.js',
			),
			'styles' => array(
			),
			'dependencies' => array(
			),
		),
		'mw.ext.userBitcoinAddresses.special' => $moduleTemplate + array(
			'position' => 'top',
			'scripts' => array(
				'special.js',
			),
			'styles' => array(
				'special.css',
			),
			'dependencies' => array(
				'mw.ext.userBitcoinAddresses',
				'jquery.inputautoexpand'
			),
		),
		// VENDOR: (TODO: Move these from here and Wikibase to somewhere where we can share code)
		'jquery.inputautoexpand' => $moduleTemplate + array(
			'scripts' => array(
				'vendor/jquery.inputautoexpand.js',
			),
			'dependencies' => array(
				'jquery.event.special.eachchange',
			),
		),
		'jquery.event.special.eachchange' => $moduleTemplate + array(
			'scripts' => array(
				'vendor/jquery.event.special.eachchange.js'
			),
			'dependencies' => array(
				'jquery.client',
			),
		),
	);
} );
