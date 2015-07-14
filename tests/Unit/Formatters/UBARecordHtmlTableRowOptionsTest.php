<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\Formatters;

use MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserMocker;
use MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\SetterAndGetterTester;
use MediaWiki\Ext\UserBitcoinAddresses\Formatters\UBARecordHtmlTableRowOptions;
use MediaWiki\Ext\UserBitcoinAddresses\Formatters\BitcoinAddressFormatter;
use MediaWiki\Ext\UserBitcoinAddresses\Formatters\MWUserDateTimeHtml;
use MediaWiki\Ext\UserBitcoinAddresses\Formatters\UBARecordHtmlTableRowVirtualFields;

/**
 * @group UserBitcoinAddresses
 * @covers MediaWiki\Ext\UserBitcoinAddresses\Formatters\UBARecordHtmlTableRowOptions
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class UBARecordHtmlTableRowOptionsTest extends \PHPUnit_Framework_TestCase {

	public function testConstruction() {
		$this->assertInstanceOf(
			'MediaWiki\Ext\UserBitcoinAddresses\Formatters\UBARecordHtmlTableRowOptions',
			new UBARecordHtmlTableRowOptions()
		);
	}

	/**
	 * @dataProvider setterAndGettersCaseProvider
	 */
	public function testGettersAndSetters( $getterAndSetter, $value ) {
		( new SetterAndGetterTester( $this ) )
			->getAndSet( $getterAndSetter )->on( new UBARecordHtmlTableRowOptions() )
			->initiallyNotNull( true )
			->test( $value );
	}

	public function testVirtualFieldsInitialValue() {
		$instance = new UBARecordHtmlTableRowOptions();
		$this->assertInstanceOf(
			'MediaWiki\Ext\UserBitcoinAddresses\Formatters\UBARecordHtmlTableRowVirtualFields',
			$instance->virtualFields()
		);
	}

	public function testPrintFieldsInitialValue() {
		$instance = new UBARecordHtmlTableRowOptions();
		$this->assertSame(
			[ 'id', 'bitcoinAddress', 'user', 'addedOn', 'exposedOn', 'purpose' ],
			$instance->printFields()
		);
	}

	/**
	 * @dataProvider printAllFieldsCaseProvider
	 */
	public function testPrintAllFields( $virtualFieldNames, $allFields ) {
		$instance = new UBARecordHtmlTableRowOptions();
		$virtualFields = $instance->virtualFields();

		foreach( $virtualFieldNames as $name ) {
			$virtualFields->set( $name, function() { return 'foo'; } );
		}

		$this->assertSame( $instance, $instance->printAllFields(), 'returns self-reference' );
		$this->assertSame( $allFields, $instance->printFields() );
	}

	/**
	 * @dataProvider printFieldsWithoutCaseProvider
	 */
	public function testPrintFieldsWithout( $without, array $expectedPrintFields ) {
		$instance = new UBARecordHtmlTableRowOptions();

		$this->assertSame( $instance, $instance->printFieldsWithout( $without ),
			'returns self-reference' );

		$this->assertSame( $expectedPrintFields, $instance->printFields() );
	}

	/**
	 * @return array( array( string $getterAndSetter, $someValidValue ), ... )
	 */
	public static function setterAndGettersCaseProvider() {
		$mocker = new UserMocker();
		return [
			[ 'bitcoinAddressFormatter', new BitcoinAddressFormatter() ],
			[ 'timeAndDateFormatter', new MWUserDateTimeHtml( $mocker->newUser() ) ],
			[ 'virtualFields', new UBARecordHtmlTableRowVirtualFields() ],
			[ 'printFields', [ 'user', 'bitcoinAddress' ] ],
			[ 'printFields', [] ],
			[ 'printFields', [ 'non-existent-field' ] ],
		];
	}

	/**
	 * @return array( array( string[]|string $without, string[] $expectedPrintFields ), ... )
	 */
	public static function printFieldsWithoutCaseProvider() {
		$defaultValues = [ 'id', 'bitcoinAddress', 'user', 'addedOn', 'exposedOn', 'purpose' ];
		return [
			[
				[], $defaultValues
			], [
				[ 'id' ], [ 'bitcoinAddress', 'user', 'addedOn', 'exposedOn', 'purpose' ]
			], [
				'id', [ 'bitcoinAddress', 'user', 'addedOn', 'exposedOn', 'purpose' ]
			], [
				[ 'addedOn', 'id', 'purpose' ],
				[ 'bitcoinAddress', 'user', 'exposedOn', ]
			], [
				[ 'addedOn', 'id', 'purpose' ],
				[ 'bitcoinAddress', 'user', 'exposedOn', ]
			], [
				'foo', $defaultValues
			], [
				[ 'foo' ], $defaultValues
			]
		];
	}

	/**
	 * @return array( array( string[] $virtualFieldNames, string[] $allFields ), ... )
	 */
	public static function printAllFieldsCaseProvider() {
		$defaultValues = [ 'id', 'bitcoinAddress', 'user', 'addedOn', 'exposedOn', 'purpose' ];
		return [
			[
				[ 'foo' ], array_merge( $defaultValues, [ 'foo' ] ),
			], [
				[], $defaultValues,
			], [
				[ 'a', 'b', 'c' ], array_merge( $defaultValues, [ 'a', 'b', 'c' ] ),
			]
		];
	}
}
