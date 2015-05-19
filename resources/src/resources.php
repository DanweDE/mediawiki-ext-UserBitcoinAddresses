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
			'scripts' => array(
				'special.js',
			),
			'styles' => array(
				'special.css',
			),
			'dependencies' => array(
				'mw.ext.userBitcoinAddresses'
			),
		),
	);
} );
