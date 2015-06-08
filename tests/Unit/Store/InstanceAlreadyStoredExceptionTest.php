<?php

namespace MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\Store;

use MediaWiki\Ext\UserBitcoinAddresses\Store\InstanceAlreadyStoredException;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord as UBARecord;
use MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordTestData as UBARecordTestData;

/**
 * @group UserBitcoinAddresses
 * @covers MediaWiki\Ext\UserBitcoinAddresses\Store\InstanceAlreadyStoredException
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class InstanceAlreadyStoredExceptionTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider equalRecordsProvider
	 */
	public function testConstruction( UBARecord $record1, UBARecord $record2 ) {
		$exception = new InstanceAlreadyStoredException( $record1, $record2 );

		$this->assertInstanceOf(
			'MediaWiki\Ext\UserBitcoinAddresses\Store\InstanceAlreadyStoredException',
			$exception
		);
		$this->assertInstanceOf(
			'Exception',
			$exception
		);
	}

	/**
	 * @dataProvider unequalRecordsProvider
	 *
	 * @expectedException InvalidArgumentException
	 */
	public function testConstructionWithUnequalRecords( UBARecord $record1, UBARecord $record2 ) {
		new InstanceAlreadyStoredException( $record1, $record2 );
	}

	/**
	 * @dataProvider equalRecordsProvider
	 */
	public function testGetStoreAttemptInstance( UBARecord $record1, UBARecord $record2 ) {
		$exception = new InstanceAlreadyStoredException( $record1, $record2 );

		$this->assertEquals(
			$record1,
			$exception->getStoreAttemptInstance()
		);
	}

	/**
	 * @dataProvider equalRecordsProvider
	 */
	public function testGetAlreadyStoredInstance( UBARecord $record1, UBARecord $record2 ) {
		$exception = new InstanceAlreadyStoredException( $record1, $record2 );

		$this->assertEquals(
			$record2,
			$exception->getAlreadyStoredInstance()
		);
	}

	/**
	 * @return array( array( UBARecord, UBARecord ), ... )
	 */
	public function equalRecordsProvider() {
		$cases = [];
		foreach( UBARecordTestData::equalInstancesProvider() as $case ) {
			$equalRecords = $case[ 2 ];
			if( $equalRecords ) {
				$cases[] = array_slice( $case, 0, 2 );
			}
		}
		return $cases;
	}

	/**
	 * @return array( array( UBARecord, UBARecord ), ... )
	 */
	public function unequalRecordsProvider() {
		$cases = [];
		foreach( UBARecordTestData::equalInstancesProvider() as $case ) {
			$equalRecords = $case[ 2 ];
			if( ! $equalRecords ) {
				$cases[] = array_slice( $case, 0, 2 );
			}
		}
		return $cases;
	}
}
