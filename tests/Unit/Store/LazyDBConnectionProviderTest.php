<?php

namespace Tests\Unit\SubPageList;

use MediaWiki\Ext\UserBitcoinAddresses\Store\DBConnectionProvider;
use MediaWiki\Ext\UserBitcoinAddresses\Store\LazyDBConnectionProvider;

/**
 * @note Taken from SubPageList extension.
 * @TODO Common base with SubPageList extension and others.
 *
 * @group UserBitcoinAddresses
 * @covers MediaWiki\Ext\UserBitcoinAddresses\Store\LazyDBConnectionProvider
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class LazyDBConnectionProviderTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider constructorProvider
	 *
	 * @param int $dbId
	 */
	public function testConstructor( $dbId ) {
		new LazyDBConnectionProvider( $dbId );

		$this->assertTrue( true );
	}

	public function constructorProvider() {
		return array(
			array( DB_MASTER ),
			array( DB_SLAVE ),
		);
	}

	/**
	 * @dataProvider instanceProvider
	 *
	 * @param DBConnectionProvider $connProvider
	 */
	public function testGetConnection( DBConnectionProvider $connProvider ) {
		$connection = $connProvider->getConnection();

		$this->assertInstanceOf( 'DatabaseBase', $connection );
		$this->assertTrue( $connection === $connProvider->getConnection() );

		$connProvider->releaseConnection();

		$this->assertInstanceOf( 'DatabaseBase', $connProvider->getConnection() );
	}

	public function instanceProvider() {
		return array(
			array( new LazyDBConnectionProvider( DB_MASTER ) ),
			array( new LazyDBConnectionProvider( DB_SLAVE ) ),
		);
	}

}
