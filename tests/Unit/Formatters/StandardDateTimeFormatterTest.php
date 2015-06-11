<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\Formatters;

use User;
use DateTime;
use MediaWiki\Ext\UserBitcoinAddresses\Formatters\StandardDateTimeFormatter;

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
		$formatter = new StandardDateTimeFormatter( $format );

		$this->assertEquals( $format, $formatter->formatString(),
			'getter returns format string given in constructor');

		$anotherFormat = 'Ymd';

		$this->assertEquals( $formatter, $formatter->formatString( $anotherFormat ),
			'setter returns self-reference' );

		$this->assertEquals( $anotherFormat, $formatter->formatString(),
			'getter returns new format string set via setter' );
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
