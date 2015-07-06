<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\Formatters;

use MediaWiki\Ext\UserBitcoinAddresses\Formatters\UBARecordHtmlTableRowUnknownVirtualFieldException as UnknownVirtualFieldException;
use MediaWiki\Ext\UserBitcoinAddresses\Formatters\UBARecordHtmlTableRowVirtualFields as VirtualFields;

/**
 * @group UserBitcoinAddresses
 * @covers MediaWiki\Ext\UserBitcoinAddresses\Formatters\UBARecordHtmlTableRowUnknownVirtualFieldException
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class UBARecordHtmlTableRowUnknownVirtualFieldExceptionTest extends \PHPUnit_Framework_TestCase {

	public function testConstruction() {
		$fields = new VirtualFields();
		$unknownField = 'someField';
		$exception = new UnknownVirtualFieldException( $unknownField, $fields );

		$this->assertInstanceOf(
			'MediaWiki\Ext\UserBitcoinAddresses\Formatters\UBARecordHtmlTableRowUnknownVirtualFieldException',
			$exception
		);
		return [ $exception, $unknownField, $fields ];
	}

	/**
	 * @depends testConstruction
	 */
	public function testGetUnknownField( $args ) {
		list( $exception, $unknownField, $context ) = $args;
		$this->assertSame( $unknownField, $exception->getUnknownField() );
	}

	/**
	 * @depends testConstruction
	 */
	public function testGetContext( $args ) {
		list( $exception, $unknownField, $context ) = $args;
		$this->assertSame( $context, $exception->getContext() );
	}
}
