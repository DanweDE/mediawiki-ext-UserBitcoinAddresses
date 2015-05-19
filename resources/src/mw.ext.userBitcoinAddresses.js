/**
 * Entry point for MediaWiki "User Bitcoin Addresses" extension JavaScript code. Adds an extension
 * object to the global mediaWiki.ext object.
 *
 * @licence MIT Licence
 * @author Daniel A. R. Werner < daniel.a.r.werner@gmail.com >
 */

mediaWiki.ext = mediaWiki.ext || {};

/**
 * Object representing the MediaWiki "User Bitcoin Addresses" extension.
 *
 * @since 0.1
 */
mediaWiki.ext.userBitcoinAddresses = ( function( mw ) {
	'use strict';

	/**
	 * Constructor for extension singleton.
	 *
	 * @constructor
	 */
	function MwExtUserBitcoinAddresses() {
	}

	return new MwExtUserBitcoinAddresses(); // expose extension singleton

}( mediaWiki ) );
