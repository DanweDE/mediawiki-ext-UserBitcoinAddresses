<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\Formatters;

use DateTime;
use MediaWiki\Ext\UserBitcoinAddresses\Formatters\StandardDateTimeFormatter;
use MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\SetterAndGetterTester;

/**
 * @group UserBitcoinAddresses
 * @covers MediaWiki\Ext\UserBitcoinAddresses\Formatters\StandardDateTimeFormatter
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class StandardDateTimeFormatterTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider formatCaseProvider
	 */
	public function testConstruction( $format ) {
		$this->assertInstanceOf(
			'MediaWiki\Ext\UserBitcoinAddresses\Formatters\DateTimeFormatter',
			new StandardDateTimeFormatter( $format )
		);
	}

	/**
	 * @dataProvider formatCaseProvider
	 */
	public function testFormatStringGetterAndSetter( $format ) {
		( new SetterAndGetterTester( $this ) )
			->getAndSet( 'formatString' )->on( new StandardDateTimeFormatter( $format ) )
			->initially( $format )
			->test( 'Ymd' );
	}

	/**
	 * @dataProvider formatCaseProvider
	 */
	public function testFormat( $format, DateTime $date, $formatted ) {
		$formatter = new StandardDateTimeFormatter( $format );
		$this->assertSame( $formatted, $formatter->format( $date ) );
	}

	/**
	 * @return array( array( string $format, DateTime $date, string $formatted ), ... )
	 */
	public static function formatCaseProvider() {
		$date = new DateTime( '2012-03-24 17:45:12' );
		return [
			[ 'Y-m-d H:i:s', $date, '2012-03-24 17:45:12' ],
			[ 'd/m/Y H:i:s', $date, '24/03/2012 17:45:12' ],
			[ 'g:i A', $date, '5:45 PM' ],
			[ 'g:ia \o\n l jS F Y', $date, '5:45pm on Saturday 24th March 2012' ],
		];
	}
}
