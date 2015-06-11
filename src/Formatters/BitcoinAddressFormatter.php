<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Formatters;

use Danwe\Bitcoin\Address;

/**
 * Formats a Danwe\Bitcoin\Address instance.
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class BitcoinAddressFormatter {

	/**
	 * Formats the given bitcoin address
	 *
	 * @param Address $address
	 * @return string Address formatted as plain text.
	 */
	public function format( Address $address ) {
		return $address->asString();
	}
}