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
		$instance = $this->resetRecordId( $instance );
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
	 * @dataProvider MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordTestData::instancesAndBuildersProvider
	 *
	 * @expectedException LogicException
	 */
	public function testAddInstanceWithId( UBARecord $instance, UBARBuilder $builder ) {
		$instance = $builder->id( 42 )->build();
		$store = $this->newInstance();
		$store->add( $instance );
	}

	/**
	 * @dataProvider MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordTestData::instancesAndBuildersProvider
	 */
	public function testFetchById( UBARecord $instance, UBARBuilder $builder ) {
		$store = $this->newInstance();
		$instance = $this->resetRecordId( $instance );
		$updatedInstance = $store->add( $instance );
		$fetchedInstance = $store->fetchById( $updatedInstance->getId() );

		$this->assertNotEquals( null, $fetchedInstance );
		$this->assertTrue( $fetchedInstance->isSameAs( $updatedInstance ) );
	}

	/**
	 * @param UBARecord $record
	 * @return UBARecord
	 */
	protected function resetRecordId( UBARecord $record ) {
		return UBARBuilder::extend( $record )->id( null )->build();
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
