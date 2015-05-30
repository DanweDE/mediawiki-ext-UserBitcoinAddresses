<?php

namespace MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit;

use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord;

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
	public function testConstruction( $builder, $builderBuildSteps ) {
		$this->assertInstanceOf(
			'MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord',
			new UserBitcoinAddressRecord( $builder )
		);
	}

	/**
	 * @dataProvider MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordTestData::invalidBuildStateBuildersProvider
	 */
	public function testConstructionWithInsufficientlySetupBuilder( $builder, $builderBuildSteps, $buildException ) {
		$this->setExpectedException( $buildException );
		new UserBitcoinAddressRecord( $builder );
	}

	/**
	 * @dataProvider instancesAndBuildersProvider
	 */
	public function testGetters( $instance, $builder ) {
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
	 * @return array( [ UserBitcoinAddressRecord, UserBitcoinAddressRecordBuilder ] )
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
}
