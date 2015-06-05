<?php

namespace MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit;

use User;
use Danwe\Bitcoin\Address;
use Datetime;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecordBuilder;
use MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordBuilderTestData;

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
		$user = User::newFromName( 'Dronte' );
		$user2 = User::newFromName( 'Stork' );
		$addr = new Address( '1C5bSj1iEGUgSTbziymG7Cn18ENQuT36vv' );
		$addr2 = new Address( '19dcawoKcZdQz365WpXWMhX6QCUpR9SY4r' );

		return [
			[
				( new UserBitcoinAddressRecordBuilder() )
					->id( 42 )
					->user( $user )
					->bitcoinAddress( $addr )
					->addedOn( new Datetime() )
					->exposedOn( new DateTime() )
					->addedThrough( 'whatever' )
					->build(),
				( new UserBitcoinAddressRecordBuilder() )
					->id( 24 )
					->user( $user )
					->bitcoinAddress( $addr )
					->purpose( 'some purpose' )
					->build(),
				true
			], [
				( new UserBitcoinAddressRecordBuilder() )
					->id( 42 )
					->user( $user )
					->bitcoinAddress( $addr )
					->addedOn( new Datetime() )
					->build(),
				( new UserBitcoinAddressRecordBuilder() )
					->id( null )
					->user( $user )
					->bitcoinAddress( $addr )
					->build(),
				true
			], [
				( new UserBitcoinAddressRecordBuilder() )
					->user( $user )
					->bitcoinAddress( $addr )
					->build(),
				( new UserBitcoinAddressRecordBuilder() )
					->user( $user )
					->bitcoinAddress( $addr )
					->build(),
				true
			], [
				( new UserBitcoinAddressRecordBuilder() )
					->user( $user )
					->bitcoinAddress( $addr )
					->build(),
				( new UserBitcoinAddressRecordBuilder() )
					->user( $user2 )
					->bitcoinAddress( $addr2 )
					->build(),
				false
			], [
				( new UserBitcoinAddressRecordBuilder() )
					->user( $user )
					->bitcoinAddress( $addr )
					->build(),
				( new UserBitcoinAddressRecordBuilder() )
					->user( $user )
					->bitcoinAddress( $addr2 )
					->build(),
				false
			], [
				( new UserBitcoinAddressRecordBuilder() )
					->user( $user )
					->bitcoinAddress( $addr )
					->build(),
				( new UserBitcoinAddressRecordBuilder() )
					->user( $user2 )
					->bitcoinAddress( $addr )
					->build(),
				false
			]
		];
	}

	/**
	 * @return array( [ UserBitcoinAddressRecord $instance, UserBitcoinAddressRecord $instanceCopy, UserBitcoinAddressRecord[] $otherInstances ] )
	 */
	public static function sameDataInstancesProvider() {
		$baseInstanceBuilders = [
			( new UserBitcoinAddressRecordBuilder() )
				->id( 1 )
				->user( User::newFromName( 'One' ) )
				->bitcoinAddress( new Address( '1C5bSj1iEGUgSTbziymG7Cn18ENQuT36vv' ) )
				->addedOn( null )
				->exposedOn( null )
				->addedThrough( null ),
			( new UserBitcoinAddressRecordBuilder() )
				->id( 2 )
				->user( User::newFromName( 'One' ) )
				->bitcoinAddress( new Address( '1C5bSj1iEGUgSTbziymG7Cn18ENQuT36vv' ) )
				->addedOn( new DateTime( '2000-01-01 20:00' ) )
				->exposedOn( new DateTime( '2010-01-01 20:00' ) )
				->addedThrough( 'whatever' ),
		];
		$varietyValueSets = [
			[ 'id' => 3 ],
			[ 'user' => User::newFromName( 'Two' ) ],
			[ 'bitcoinAddress' => new Address( '19dcawoKcZdQz365WpXWMhX6QCUpR9SY4r' ) ],
			[ 'addedOn' => new DateTime('2005-01-01 20:00') ],
			[ 'exposedOn' => new DateTime('2020-01-01 20:00') ],
			[ 'addedThrough' => 'something else' ],
		];
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
}
