<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\MwBridge;

use MediaWiki\Ext\UserBitcoinAddresses\MwBridge\StandardMwUserFactory;

/**
 * @group UserBitcoinAddresses
 * @covers MediaWiki\Ext\UserBitcoinAddresses\StandardMwUserFactory
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class StandardMwUserFactoryTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider idsProvider
	 */
	public function testNewFromId( $id ) {
		$user = ( new StandardMwUserFactory )->newFromId( $id );
		$this->assertInstanceOf( 'User', $user );
	}

	/**
	 * @return array( array( int ), ... )
	 */
	public function idsProvider() {
		return array_chunk( [
			0,
			1,
			42,
			1337
		], 1 );
	}
}