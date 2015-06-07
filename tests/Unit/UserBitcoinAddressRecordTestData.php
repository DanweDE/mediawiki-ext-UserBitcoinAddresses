<?php

namespace MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit;

use Danwe\Bitcoin\Address;
use Datetime;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecordBuilder;

/**
 * Data providers for UserBitcoinAddressRecord related tests.
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class UserBitcoinAddressRecordTestData {
	/**
	 * @return array( [ UserBitcoinAddressRecord, UserBitcoinAddressRecordBuilder ], ... )
	 */
	public static function instancesAndBuildersProvider() {
		$instancesAndBuilders = [];
		foreach( UserBitcoinAddressRecordBuilderTestData::validBuildStateBuildersProvider() as $case ) {
			$builder = $case[ 0 ];
			$instance = new UserBitcoinAddressRecord( $builder );
			$instancesAndBuilders[] = [ $instance, $builder ];
		}
		return $instancesAndBuilders;
	}

	/**
	 * @return array( [ UserBitcoinAddressRecord, UserBitcoinAddressRecord, boolean $equal ], ... )
	 */
	public static function equalInstancesProvider() {
		$mocker = new Mocker();

		$user1   = $mocker->newUser( 'Dronte' );
		$user1_2 = $mocker->newUser( 'Dronte' );
		$user2   = $mocker->newUser( 'Stork' );

		$addr1   = new Address( '1C5bSj1iEGUgSTbziymG7Cn18ENQuT36vv' );
		$addr1_2 = new Address( '1C5bSj1iEGUgSTbziymG7Cn18ENQuT36vv' );
		$addr2   = new Address( '19dcawoKcZdQz365WpXWMhX6QCUpR9SY4r' );

		$date = new DateTime();

		return [
			[
				( new UserBitcoinAddressRecordBuilder() )
					->id( 42 )
					->user( $user1 )
					->bitcoinAddress( $addr1 )
					->addedOn( $date )
					->exposedOn( $date )
					->addedThrough( 'whatever' )
					->build(),
				( new UserBitcoinAddressRecordBuilder() )
					->id( 24 )
					->user( $user1_2 )
					->bitcoinAddress( $addr1 )
					->purpose( 'some purpose' )
					->build(),
				true
			], [
				( new UserBitcoinAddressRecordBuilder() )
					->id( 42 )
					->user( $user1 )
					->bitcoinAddress( $addr1 )
					->addedOn( $date )
					->build(),
				( new UserBitcoinAddressRecordBuilder() )
					->id( null )
					->user( $user1 )
					->bitcoinAddress( $addr1_2 )
					->build(),
				true
			], [
				( new UserBitcoinAddressRecordBuilder() )
					->user( $user1 )
					->bitcoinAddress( $addr1 )
					->build(),
				( new UserBitcoinAddressRecordBuilder() )
					->user( $user1_2 )
					->bitcoinAddress( $addr1_2 )
					->build(),
				true
			], [
				( new UserBitcoinAddressRecordBuilder() )
					->user( $user1 )
					->bitcoinAddress( $addr1 )
					->build(),
				( new UserBitcoinAddressRecordBuilder() )
					->user( $user2 )
					->bitcoinAddress( $addr2 )
					->build(),
				false
			], [
				( new UserBitcoinAddressRecordBuilder() )
					->user( $user1 )
					->bitcoinAddress( $addr1 )
					->build(),
				( new UserBitcoinAddressRecordBuilder() )
					->user( $user1 )
					->bitcoinAddress( $addr2 )
					->build(),
				false
			], [
				( new UserBitcoinAddressRecordBuilder() )
					->user( $user1 )
					->bitcoinAddress( $addr1 )
					->build(),
				( new UserBitcoinAddressRecordBuilder() )
					->user( $user2 )
					->bitcoinAddress( $addr1 )
					->build(),
				false
			]
		];
	}

	/**
	 * @return array( [ UserBitcoinAddressRecord $instance, UserBitcoinAddressRecord $instanceCopy, UserBitcoinAddressRecord[] $otherInstances ] )
	 */
	public static function sameDataInstancesProvider() {
		$mocker = new Mocker();
		$user = $mocker->newUser();

		$baseInstanceBuilders = [
			( new UserBitcoinAddressRecordBuilder() )
				->id( 1 )
				->user( $user )
				->bitcoinAddress( new Address( '1C5bSj1iEGUgSTbziymG7Cn18ENQuT36vv' ) )
				->addedOn( null )
				->exposedOn( null )
				->addedThrough( null ),
			( new UserBitcoinAddressRecordBuilder() )
				->id( 2 )
				->user( $user )
				->bitcoinAddress( new Address( '1C5bSj1iEGUgSTbziymG7Cn18ENQuT36vv' ) )
				->addedOn( new DateTime( '1980-01-01 1:00' ) )
				->exposedOn( new DateTime( '1990-01-01 2:00' ) )
				->addedThrough( 'whatever' ),
		];
		$varietyValueSets = [
			[ 'id' => 3 ],
			[ 'user' => $mocker->newUser( 'Two' ) ],
			[ 'bitcoinAddress' => new Address( '19dcawoKcZdQz365WpXWMhX6QCUpR9SY4r' ) ],
			[ 'addedOn' => new DateTime('1985-05-03 1:00') ],
			[ 'exposedOn' => new DateTime('1995-07-02 2:00') ],
			[ 'addedThrough' => 'something ese' ],
		];

		$instances = self::combineBaseInstancesWithVariations( $baseInstanceBuilders, $varietyValueSets );
		$cases = [];

		foreach( $instances as $instanceDescription => $instance ) {
			$otherInstances = $instances;
			unset( $otherInstances[ $instanceDescription ] );

			$cases[ $instanceDescription ] = [
				$instance,
				UserBitcoinAddressRecordBuilder::extend( $instance )->build(),
				$otherInstances,
			];
		}
		return $cases;
	}

	protected final static function combineBaseInstancesWithVariations(
		array $baseInstanceBuilders,
		array $varietyValueSets
	) {
		$instances = [];
		foreach( $baseInstanceBuilders as $baseId => $baseInstanceBuilder ) {
			$baseInstanceName = 'base instance ' . ( $baseId + 1 );
			$instances[ $baseInstanceName ] = $baseInstanceBuilder->build();

			foreach( $varietyValueSets as $varietyValues ) {
				$instanceBuilder = UserBitcoinAddressRecordBuilder::extend( $baseInstanceBuilder );

				foreach( $varietyValues as $setter => $value ) {
					$instanceBuilder->{ $setter }( $value );
				}

				$instanceName = implode( '() & ', array_keys( $varietyValues ) ) . '()';
				$i = 0;
				do {
					$i++;
					$instanceDescription = "instance with $instanceName " . ( $i > 1 ? "($i) " : '' )
						. "differing from $baseInstanceName";
				} while( array_key_exists( $instanceDescription, $instances ) );

				$instances[ $instanceDescription ] = $instanceBuilder->build();
			}
		}
		return $instances;
	}
}
