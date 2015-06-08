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
	 * @dataProvider MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordTestData::instancesAndBuildersProvider
	 */
	public function testFetchByUserBtcAddress( UBARecord $instance, UBARBuilder $builder ) {
		$store = $this->newStore();
		$instance = $this->recycleInstance( $instance );
		$updatedInstance = $store->add( $instance );

		$fetchedInstance = $store->fetchByUserBtcAddress(
			( new UBARBuilder )
				->bitcoinAddress( $updatedInstance->getBitcoinAddress() )
				->user( $updatedInstance->getUser() )
				->build()
		);

		$this->assertNotEquals( null, $fetchedInstance );
		$this->assertTrue( $fetchedInstance->isSameAs( $updatedInstance ) );
	}

	/**
	 * @dataProvider recordsByUsersInAStoreTestSetupsProvider
	 */
	public function testFetchAllForUser( $testSetup ) {
		list( $store, $user, $usersAddresses ) = $testSetup();
		assert( $user->isAnon() === false );

		$usersAddressesLength = count( $usersAddresses );

		$fetchedUserAddresses = $store->fetchAllForUser( $user );

		$this->assertInternalType( 'array', $fetchedUserAddresses );
		$this->assertEquals( $usersAddressesLength, count( $fetchedUserAddresses ),
			"fetchAllForUser() fetched expected number of instances for user {$user->getName()}" );

		foreach( $usersAddresses as $i => $usersAddress ) {
			assert( $usersAddress->getUser()->equals( $user ) ); // just ensure valid provider data

			$assertionMessage = "fetchAllForUser() fetched expected instance [$i] out of "
				. "$usersAddressesLength for user {$user->getName()}";

			foreach( $fetchedUserAddresses as $fetchedUserAddress ) {
				if( $fetchedUserAddress->isSameAs( $usersAddress  ) ) {
					$this->assertTrue( true, $assertionMessage );
					break 2;
				}
			}
			$this->assertTrue( false, $assertionMessage );
		}
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
			$nonMockUser = $this->getOrCreateUser( $user->getName() );
			$builder->user( $nonMockUser );
		}
		return $builder
			->id( null )
			->build();
	}

	protected final static function getOrCreateUser( $name ) {
		$user = User::createNew( $name );
		return $user !== null
			? $user
			: $user = User::newFromName( $name );
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

	/**
	 * TODO: If bug https://phabricator.wikimedia.org/T101696 got solved, don't return setup
	 *       function but instead provide the setup's values.
	 *
	 * Each case consists of a single test case setup function.
	 * When executed, an array with three values will be provided:
	 *  - A UBARSTore with several UBARecord instances.
	 *  - A User instance.
	 *  - An array of all UBARecord instances of the given user expected to be in the given store.
	 *
	 * @return array( array( Function $testSetup ), ... )
	 */
	public static function recordsByUsersInAStoreTestSetupsProvider() {
		$static = __CLASS__;
		$store = null;
		$setups = [];
		$usersAndBuilders = self::getUniqueUserNamesWithBuilders();

		foreach( $usersAndBuilders as $userName => $recordBuilders ) {
			$setups[ "Setup with instances for user $userName" ] =
				function() use( $static, &$store, $userName, $recordBuilders )
		{
			if( !$store ) {
				$store = $static::newStore();
			}
			// Create user in db within setup function, tricking bug https://phabricator.wikimedia.org/T101696
			$user = User::createNew( $userName ); // If null, user already exists...
			assert( $user !== null ); // Should never happen due to strong user name anyhow.

			$userAddresses = [];

			foreach( $recordBuilders as $builder ) {
				assert( $builder instanceof UBARBuilder );

				$instance = $builder
					->user( $user )
					->id( null )
					->build();

				$userAddresses[] = $store->add( $instance );
			}

			return [
				$store,
				$user,
				$userAddresses,
			];
		}; }

		return array_chunk( $setups, 1 );
	}

	private static function getUniqueUserNamesWithBuilders() {
		// make strong user names since calling the provider n times would result in the same
		// users being used in several tests and there would be n times the user addresses in the
		// MW database than anticipated.
		static $id = 0;
		$id++;
		$time = time();
		$userPrefix = "recordsByUsersInAStoreProvider$id-$time";

		foreach( UserBitcoinAddressRecordTestData::instancesAndBuildersProvider() as $builderCase ) {
			$builders[] =  $builderCase[ 1 ];
		}

		return [
			"$userPrefix User 1" => $builders,
			"$userPrefix User 2 (without addresses)" => [],
			"$userPrefix User 3" => $builders,
			"$userPrefix User 4 (without addresses)" => [],
			"$userPrefix User 5" => $builders,
		];
	}
}
