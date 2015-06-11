<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Formatters;

use Danwe\Bitcoin\Address;

/**
 * Formats a Bitcoin address instance as HTML with mono space font.
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class BitcoinAddressMonoSpaceHtml extends BitcoinAddressFormatter {

	/**
	 * @see BitcoinAddressFormatter::Format()
	 */
	public function format( Address $address ) {
		return '<code>' . htmlspecialchars( $address->asString() ) . '</code>';
	}
}