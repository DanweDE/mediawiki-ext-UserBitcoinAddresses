<?php

namespace MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit;

use User;
use Danwe\Bitcoin\Address;
use Datetime;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecordBuilder;

/**
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class UserBitcoinAddressRecordTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @dataProvider MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordTestData::validBuildStateBuildersProvider
	 */
	public function testConstruction(
		UserBitcoinAddressRecordBuilder $builder,
		$builderBuildSteps
	) {
		$this->assertInstanceOf(
			'MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord',
			new UserBitcoinAddressRecord( $builder )
		);
	}

	/**
	 * @dataProvider MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordTestData::invalidBuildStateBuildersProvider
	 */
	public function testConstructionWithInsufficientlySetupBuilder(
		UserBitcoinAddressRecordBuilder $builder,
		$builderBuildSteps,
		$buildException
	) {
		$this->setExpectedException( $buildException );
		new UserBitcoinAddressRecord( $builder );
	}

	/**
	 * @dataProvider instancesAndBuildersProvider
	 */
	public function testGetters(
		UserBitcoinAddressRecord $instance,
		UserBitcoinAddressRecordBuilder $builder
	) {
		foreach( get_class_methods( $builder ) as $method ) {
			if( substr( $method, 0, 3 ) !== 'get' ) {
				continue;
			}
			$this->assertEquals(
				$builder->{ $method }(),
				$instance->{ $method }(),
				"Builder's and instance's \"$method\" return same value."
			);
		}
	}

	/**
	 * @dataProvider equalInstancesProvider
	 */
	public function testEquals(
		UserBitcoinAddressRecord $instance1,
		UserBitcoinAddressRecord $instance2,
		$expected
	) {
		$this->assertTrue( $instance1->equals( $instance2 ) === $expected );
	}

	/**
	 * @dataProvider instancesAndBuildersProvider
	 */
	public function testEqualsWithSameInstance( $instance, $builder ) {
		$this->assertTrue( $instance->equals( $instance ) );
	}

	/**
	 * @return array( [ UserBitcoinAddressRecord, UserBitcoinAddressRecordBuilder ], ... )
	 */
	public static function instancesAndBuildersProvider() {
		$instancesAndBuilders = [];
		foreach( UserBitcoinAddressRecordTestData::validBuildStateBuildersProvider() as $case ) {
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
}
