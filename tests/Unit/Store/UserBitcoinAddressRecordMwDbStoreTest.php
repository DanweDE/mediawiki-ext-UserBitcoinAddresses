<?php

namespace MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\Store;

namespace MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit;
use MediaWikiTestCase;
use MediaWiki\Ext\UserBitcoinAddresses\Store\LazyDBConnectionProvider;
use MediaWiki\Ext\UserBitcoinAddresses\Store\UserBitcoinAddressRecordMwDbStore as UBARStore;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord as UBARecord;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecordBuilder as UBARBuilder;
use User;

/**
 * @group UserBitcoinAddresses
 * @group Database
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
			$this->newStore()
		);
	}

	/**
	 * @dataProvider MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordTestData::instancesAndBuildersProvider
	 */
	public function testAdd( UBARecord $instance, UBARBuilder $builder ) {
		$instance = $this->recycleInstance( $instance );
		$updatedInstance = $this->newStore()->add( $instance );

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
		$instance = UBARBuilder::extend( $this->recycleInstance( $instance ) )
			->id( 42 )
			->build();

		$this->newStore()->add( $instance );
	}

	/**
	 * @dataProvider MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordTestData::instancesAndBuildersProvider
	 *
	 * @expectedException InvalidArgumentException
	 */
	public function testAddInstanceWithAnonymousUser( UBARecord $instance, UBARBuilder $builder ) {
		$anonymousUser = User::newFromName( 'Anonymous User' );
		assert( $anonymousUser->isAnon() ); // Just make sure!

		$instance = UBARBuilder::extend( $this->recycleInstance( $instance ) )
			->user( $anonymousUser )
			->build();

		$this->newStore()->add( $instance );
	}

	/**
	 * @dataProvider MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordTestData::instancesAndBuildersProvider
	 */
	public function testFetchById( UBARecord $instance, UBARBuilder $builder ) {
		$store = $this->newStore();
		$instance = $this->recycleInstance( $instance );
		$updatedInstance = $store->add( $instance );
		$fetchedInstance = $store->fetchById( $updatedInstance->getId() );

		$this->assertNotEquals( null, $fetchedInstance );
		$this->assertTrue( $fetchedInstance->isSameAs( $updatedInstance ) );
	}

	/**
	 * Makes a UBARecord instance provided via some data provider usable for store tests, meaning
	 * that an ID will be removed and that a (mocked) user object would be replaced with one that
	 * has a guaranteed database entry.
	 *
	 * @param UBARecord $record
	 * @return UBARecord
	 */
	protected function recycleInstance( UBARecord $record ) {
		$builder = UBARBuilder::extend( $record );

		$user = $builder->getUser();
		if( $user ) {
			$nonMockUser = User::createNew( $user->getName() );
			if( $nonMockUser === null ) {
				$nonMockUser = User::newFromName( $user->getName() );
			}
			$builder->user( $nonMockUser );
		}
		return $builder
			->id( null )
			->build();
	}

	/**
	 * @return UBARStore
	 */
	public static function newStore() {
		return new UBARStore(
			new LazyDBConnectionProvider( DB_SLAVE ),
			new LazyDBConnectionProvider( DB_MASTER )
		);
	}
}
