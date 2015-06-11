<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\Formatters;

use MediaWiki\Ext\UserBitcoinAddresses\Formatters\BitcoinAddressMonoSpaceHtml;

/**
 * @group UserBitcoinAddresses
 * @covers MediaWiki\Ext\UserBitcoinAddresses\Formatters\BitcoinAddressMonoSpaceHtml
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class BitcoinAddressMonoSpaceHtmlTest extends BitcoinAddressFormatterTest {

	/**
	 * @see BitcoinAddressFormatterTest::newInstance()
	 */
	public static function newInstance() {
		return new BitcoinAddressMonoSpaceHtml();
	}
}
