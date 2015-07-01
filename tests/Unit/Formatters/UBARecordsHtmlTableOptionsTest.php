<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\Formatters;

use MediaWiki\Ext\UserBitcoinAddresses\Formatters\UBARecordsHtmlTableOptions;

/**
 * @group UserBitcoinAddresses
 * @covers MediaWiki\Ext\UserBitcoinAddresses\Formatters\UBARecordsHtmlTableOptions
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class UBARecordsHtmlTableOptionsTest extends \PHPUnit_Framework_TestCase {

	public function testConstruction() {
		$this->assertInstanceOf(
			'MediaWiki\Ext\UserBitcoinAddresses\Formatters\UBARecordsHtmlTableOptions',
			new UBARecordsHtmlTableOptions()
		);
	}

}
