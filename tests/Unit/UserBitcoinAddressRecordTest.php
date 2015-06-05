<?php

namespace MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit;

use User;
use Danwe\Bitcoin\Address;
use Datetime;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecordBuilder;

/**
 * @group UserBitcoinAddresses
 * @covers MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class UserBitcoinAddressRecordTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @dataProvider MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordBuilderTestData::validBuildStateBuildersProvider
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
	 * @dataProvider MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordBuilderTestData::invalidBuildStateBuildersProvider
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
	 * @dataProvider MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordTestData::instancesAndBuildersProvider
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
	 * @dataProvider MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordTestData::equalInstancesProvider
	 */
	public function testEquals(
		UserBitcoinAddressRecord $instance1,
		UserBitcoinAddressRecord $instance2,
		$expected
	) {
		$this->assertTrue( $instance1->equals( $instance2 ) === $expected );
	}

	/**
	 * @dataProvider MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordTestData::instancesAndBuildersProvider
	 */
	public function testEqualsWithSameInstance( $instance, $builder ) {
		$this->assertTrue( $instance->equals( $instance ) );
	}

	/**
	 * @dataProvider MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordTestData::sameDataInstancesProvider
	 */
	public function testIsSameAs(
		UserBitcoinAddressRecord $instance,
		UserBitcoinAddressRecord $instanceCopy,
		array $otherInstances
	) {
		$expectSame = $instance->getId() !== null;
		$message = $expectSame
			? 'both instances are equal'
			: 'instances are not equal because they have no ID';

		$this->assertTrue( $instance->isSameAs( $instanceCopy ) === $expectSame, $message );
		$this->assertTrue( $instanceCopy->isSameAs( $instance ) === $expectSame );

		foreach( $otherInstances as $description => $otherInstance ) {
			$this->assertInternalType( 'string', $description );
			$this->assertFalse( $instance->isSameAs( $otherInstance ), 'instance not same as ' . $description );
		}
	}

	/**
	 * @dataProvider MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordTestData::sameDataInstancesProvider
	 */
	public function testIsSameAsWithSameInstance(
		UserBitcoinAddressRecord $instance,
		UserBitcoinAddressRecord $instanceCopy,
		array $otherInstances
	) {
		$this->assertTrue( $instance->isSameAs( $instance ) );
	}
}
