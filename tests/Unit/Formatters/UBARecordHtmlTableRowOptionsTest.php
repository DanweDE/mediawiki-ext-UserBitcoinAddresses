<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\Formatters;

use MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserMocker;
use MediaWiki\Ext\UserBitcoinAddresses\Formatters\UBARecordHtmlTableRowOptions;
use MediaWiki\Ext\UserBitcoinAddresses\Formatters\BitcoinAddressFormatter;
use MediaWiki\Ext\UserBitcoinAddresses\Formatters\MWUserDateTimeHtml;

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
	public function testUserGetterAndSetter( $getterAndSetter, $value ) {
		$instance = new UBARecordHtmlTableRowOptions();

		$this->assertNotEquals( null, $instance->$getterAndSetter(),
			'getter returns some default value' );

		$this->assertSame( $instance, $instance->$getterAndSetter( $value ),
			'setter returns self-reference' );

		$this->assertSame( $value, $instance->$getterAndSetter(),
			'getter returns changed value previously set via setter' );
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
}
