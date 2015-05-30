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

	public function __construct( $name = null, array $data = array(), $dataName = '' ) {
		parent::__construct( $name, $data, $dataName );
	}

	/**
	 * @dataProvider MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordTestData::validBuildStateBuildersProvider
	 */
	public function testConstruction( $builder, $builderBuildSteps ) {
		$this->assertInstanceOf(
			'MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord',
			new UserBitcoinAddressRecord( $builder )
		);
	}
}
