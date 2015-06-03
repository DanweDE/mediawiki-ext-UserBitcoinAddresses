<?php

namespace MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\Store;

use MediaWikiTestCase;
use MediaWiki\Ext\UserBitcoinAddresses\Store\LazyDBConnectionProvider;
use MediaWiki\Ext\UserBitcoinAddresses\Store\UserBitcoinAddressRecordMwDbStore as UBARStore;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord as UBARecord;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecordBuilder as UBARBuilder;

/**
 * @group UserBitcoinAddresses
 * @covers MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecordMwDbStore
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class UserBitcoinAddressRecordMwDbStoreTest extends MediaWikiTestCase {

	public function testConstruction() {
		$this->assertInstanceOf(
			'MediaWiki\Ext\UserBitcoinAddresses\Store\UserBitcoinAddressRecordMwDbStore',
			$this->newInstance()
		);
	}

	/**
	 * @dataProvider MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordTestData::instancesAndBuildersProvider
	 */
	public function testAdd( UBARecord $instance, UBARBuilder $builder ) {
		$store = $this->newInstance();
		$instance = UBARBuilder::extend( $instance )->id( null )->build();
		$updatedInstance = $store->add( $instance );

		$this->assertTrue(
			$instance !== $updatedInstance,
			'Returned instance is not same as provided instance.'
		);
		$this->assertInstanceOf(
			'MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord',
			$updatedInstance
		);
	}

	/**
	 * @return UBARStore
	 */
	public function newInstance() {
		return new UBARStore(
			new LazyDBConnectionProvider( DB_SLAVE ),
			new LazyDBConnectionProvider( DB_MASTER )
		);
	}
}
