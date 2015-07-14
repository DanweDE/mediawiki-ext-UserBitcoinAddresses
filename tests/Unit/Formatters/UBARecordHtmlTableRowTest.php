<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\Formatters;

use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord as UBARecord;
use MediaWiki\Ext\UserBitcoinAddresses\Formatters\UBARecordHtmlTableRow as Formatter;
use MediaWiki\Ext\UserBitcoinAddresses\Formatters\UBARecordHtmlTableRowOptions as FormatterOptions;
use MediaWiki\Ext\UserBitcoinAddresses\Formatters\UBARecordHtmlTableRowVirtualFields as VirtualFields;
use MediaWiki\Ext\UserBitcoinAddresses\Formatters\BitcoinAddressMonoSpaceHtml;
use MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\SetterAndGetterTester;
use MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordTestData;

/**
 * @group UserBitcoinAddresses
 * @covers MediaWiki\Ext\UserBitcoinAddresses\Formatters\UBARecordHtmlTableRow
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class UBARecordHtmlTableRowTest extends \MediaWikiTestCase {

	/**
	 * @dataProvider formatterOptionsProvider
	 */
	public function testConstruction( $options ) {
		$this->assertInstanceOf(
			'MediaWiki\Ext\UserBitcoinAddresses\Formatters\UBARecordHtmlTableRow',
			new Formatter( $options )
		);
	}

	/**
	 * @dataProvider formatterOptionsProvider
	 */
	public function testOptionsGetterAndSetter( $options ) {
		$test = ( new SetterAndGetterTester( $this ) )
			->getAndSet( 'options' )->on( new Formatter( $options ) );

		$options === null
			? $test->initiallyNotNull()
			: $test->initially( $options );

		$test->test( new FormatterOptions() );
	}

	/**
	 * @dataProvider formatterPrintFieldOptionsProvider
	 */
	public function testFormat( array $printFields, $existingFields, $expectColspan ) {
		foreach( UserBitcoinAddressRecordTestData::instancesAndBuildersProvider() as $i => $case ) {
			list( $record, ) = $case;

			$options = ( new FormatterOptions() )
				->printFields( $printFields );
			$formatter = new Formatter( $options );
			$isColspanCase = $record->getPurpose() === null && $record->getExposedOn() === null;

			$html = $formatter->format( $record );
			$this->assertInternalType( 'string', $html );

			if( $existingFields > 0 ) {
				$expectedCells = $existingFields - ( $isColspanCase && $expectColspan );
				$this->assertTag( [
					'tag' => 'tr',
					'children' => [
						'count' => $expectedCells,
						'only' => [ 'tag' => 'td' ],
					],
				], $html, "(record #$i) $expectedCells cells in row $html" );
			} else {
				$this->assertSame( '<tr></tr>', $html,
					"(record #$i) no fields printed, empty row" );
			}
		}
	}

	public function testFormatWithVirtualFields() {
		$formatter = new Formatter();
		$formatter->options()
			->printFields( [ 'id', 'vField1', 'vField2' ] )
			->virtualFields()
				->set( 'vField1', function( UBARecord $record ) {
					return '1.' . $record->getId();
				} )
				->set( 'vField2', function( UBARecord $record ) {
					return '2.' . $record->getId();
				} )
		;

		$record = $this->getMockBuilder( 'MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord' )
			->disableOriginalConstructor()
			->getMock();

		$record->method( 'getId' )
			->willReturn( 42 );

		$this->assertRegExp( '!>1\.42</td>.+>2\.42</td>!', $formatter->format( $record ) );
	}

	/**
	 * @return array( array( FormatterOptions|null ), ... )
	 */
	public static function formatterOptionsProvider() {
		return array_chunk( [
			null,
			new FormatterOptions(),
			( new FormatterOptions )
				->printFields( [ 'id', 'user' ] )
				->bitcoinAddressFormatter( new BitcoinAddressMonoSpaceHtml() )
				->virtualFields(
					( new VirtualFields() )
						->set( 'vField1', function( $record ) { return 'foo'; } )
				)
		], 1 );
	}

	/**
	 * @return array( array( string[] $printFields, int $existingFields, $expectColspan ), ... )
	 */
	public static function formatterPrintFieldOptionsProvider() {
		return [
			'all six existing fields, colspan exposedOn/purpose if both null' => [
				[ 'id', 'bitcoinAddress', 'user', 'addedOn', 'exposedOn', 'purpose' ], 6, true
			],
			'three existing fields, no colspan case' => [
				[ 'purpose', 'id', 'exposedOn' ], 3, false
			],
			'one existing, two non-existing fields' => [
				[ 'id', 'foo', 'bar' ], 1, false
			],
			'no existing fields' => [
				[ 'foo' ], 0, false
			],
			'no fields' => [
				[], 0, false
			],
			'purpose/exposedOn, expect colspan if both null' => [
				[ 'purpose', 'exposedOn' ], 2, true
			],
			'exposedOn/purpose, expect colspan if both null' => [
				[ 'exposedOn', 'purpose' ], 2, true
			],
		];
	}
}
