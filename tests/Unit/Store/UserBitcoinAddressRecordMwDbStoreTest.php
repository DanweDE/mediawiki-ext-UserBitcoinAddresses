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
	 * TODO: If bug https://phabricator.wikimedia.org/T101696 got solved, use @ dataProvider instead!
	 */
	public function testFetchAllForUser() {
		foreach( $this->recordsByUsersInAStoreProvider() as $case ) {
			$this->innerTestFetchAllForUser( $case[0], $case[1], $case[2] );
		}
	}

	protected function innerTestFetchAllForUser( UBARStore $store, User $user, array $usersAddresses ) {
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
	 * IMPORTANT: Don't use this with actual @dataProvider. Due to bug https://phabricator.wikimedia.org/T101696
	 *            the created objects would end up in the production db rather than in the test db.
	 *
	 * For each case there will be three parameters provided:
	 *  - A UBARSTore with several UBARecord instances.
	 *  - A User instance.
	 *  - An array of all UBARecord instances of the given user expected to be in the given store.
	 *
	 * @return array( array( UBARSTore $allUsersAddresses, User $user, UBARecord[] $usersAddresses ), ... )
	 */
	public static function recordsByUsersInAStoreProvider() {
		// make strong user names since calling the provider n times would result in the same
		// users being used in several tests and there would be n times the user addresses in the
		// MW database than anticipated.
		static $id = 0;
		$id++;
		$time = time();
		$userPrefix = "recordsByUsersInAStoreProvider$id $time";
		$userNames = [
			"$userPrefix User 1",
			"$userPrefix User 3",
			"$userPrefix User 2",
		];

		$store = static::newStore();
		$cases = [];

		foreach( $userNames as $userName ) {
			$user = User::createNew( $userName ); // If null, user already exists...
			assert( $user !== null ); // Should never happen due to strong user name anyhow.
			//assert( $user->getName() === $userName );
			$user->load();

			$userAddresses = [];

			foreach( UserBitcoinAddressRecordTestData::instancesAndBuildersProvider() as $case ) {
				$builder = $case[ 1 ];
				assert( $builder instanceof UBARBuilder );

				$instance = $builder
					->user( $user )
					->id( null )
					->build();

				$userAddresses[] = $store->add( $instance );

				$fetched = $store->fetchById( $userAddresses[ count( $userAddresses ) - 1 ]->getId() );
				assert( $fetched instanceof UBARecord );
			}
			$fetchedAll = $store->fetchAllForUser( $user );
			assert(
				'count( $fetchedAll ) === count( $userAddresses )',
				count( $fetchedAll ) . '_' . count( $userAddresses )
			);

			$cases[ "instances for user $userName" ] = [
				$store,
				$user,
				$userAddresses,
			];
		}
		return $cases;
	}
}
