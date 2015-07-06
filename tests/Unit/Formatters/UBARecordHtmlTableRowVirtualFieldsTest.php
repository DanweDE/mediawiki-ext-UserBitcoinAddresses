<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\Formatters;

use MediaWiki\Ext\UserBitcoinAddresses\Formatters\UBARecordHtmlTableRowVirtualFields as VirtualFields;

/**
 * @group UserBitcoinAddresses
 * @covers MediaWiki\Ext\UserBitcoinAddresses\Formatters\UBARecordHtmlTableRowVirtualFields
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class UBARecordHtmlTableRowVirtualFieldsTest extends \PHPUnit_Framework_TestCase {

	public function testConstruction() {
		$fields = new VirtualFields();
		$this->assertInstanceOf(
			'MediaWiki\Ext\UserBitcoinAddresses\Formatters\UBARecordHtmlTableRowVirtualFields',
			$fields
		);
		return $fields;
	}

	/**
	 * @depends testConstruction
	 */
	public function testSet( VirtualFields $fields ) {
		$callback = function() { return 'value 1'; };

		$this->assertSame( $fields, $fields->set( 'foo', $callback ),
			'remove() returns self-reference' );

		return $fields;
	}

	/**
	 * @depends testSet
	 */
	public function testHas( VirtualFields $fields ) {
		$this->assertTrue( $fields->has( 'foo' ) );
	}

	/**
	 * @depends testSet
	 */
	public function testGetFieldNames( VirtualFields $fields ) {
		$this->assertEquals( [ 'foo' ], $fields->getFieldNames() );
	}

	/**
	 * @depends testSet
	 * @depends testHas
	 */
	public function testRemove( VirtualFields $fields ) {
		$this->assertSame( $fields, $fields->remove( 'foo' ),
			'remove() returns self-reference' );

		$this->assertFalse( $fields->has( 'foo' ), '"foo" got removed' );

		return $fields;
	}

	/**
	 * @depends testRemove
	 */
	public function testSetTwiceForSameKey( VirtualFields $fields ) {
		$this->testSet(
			$this->testSet( $fields ) ); // add "foo" twice, 2nd should just overwrite first

		$this->testHas( $fields );
		$this->testGetFieldNames( $fields );

		return $fields;
	}

	/**
	 * @depends testSetTwiceForSameKey
	 */
	public function testSetSecondField( VirtualFields $fields ) {
		$callback = function() { return 'value 2'; };

		$this->assertSame( $fields, $fields->set( 'second', $callback ),
			'remove() returns self-reference' );

		return $fields;
	}

	/**
	 * @depends testSetSecondField
	 */
	public function testHasAfterSecondField( VirtualFields $fields ) {
		$this->assertTrue( $fields->has( 'second' ) );
	}

	/**
	 * @depends testSetSecondField
	 */
	public function testGetFieldNamesAfterSecondField( VirtualFields $fields ) {
		$this->assertEquals( [ 'foo', 'second' ], $fields->getFieldNames() );
	}

}
