<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\Formatters;

use Danwe\Bitcoin\Address;
use MediaWiki\Ext\UserBitcoinAddresses\Formatters\BitcoinAddressFormatter;

/**
 * @group UserBitcoinAddresses
 * @covers MediaWiki\Ext\UserBitcoinAddresses\Formatters\BitcoinAddressFormatter
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class BitcoinAddressFormatterTest extends \PHPUnit_Framework_TestCase {

	public static function newInstance() {
		return new BitcoinAddressFormatter();
	}

	public function testConstruction() {
		$this->assertInstanceOf(
			'MediaWiki\Ext\UserBitcoinAddresses\Formatters\BitcoinAddressFormatter',
			$this->newInstance()
		);
	}

	/**
	 * @dataProvider addressProvider
	 */
	public function testFormat( Address $address ) {
		$formatter = $this->newInstance();
		$this->assertInternalType( 'string', $formatter->format( $address ) );
	}

	/**
	 * @return array( array( User ), ... )
	 */
	public static function addressProvider() {
		return array_chunk( [
			new Address( '1C5bSj1iEGUgSTbziymG7Cn18ENQuT36vv' ),
			new Address( '1Gqk4Tv79P91Cc1STQtU3s1W6277M2CVWu' ),
			new Address( '1JwMWBVLtiqtscbaRHai4pqHokhFCbtoB4' ),
		], 1 );
	}
}
