<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Formatters;

use Danwe\Helpers\GetterSetterAccessor as GetterSetter;
use DateTime;

/**
 * Formats a DateTime instance via PHP's built-in DateTime::format().
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class StandardDateTimeFormatter implements DateTimeFormatter {

	protected $formatString;

	/**
	 * @param string $formatString
	 */
	public function __construct( $formatString = 'Y-m-d H:i:s' ) {
		if( ! is_string( $formatString ) ) {
			throw new \InvalidArgumentException(
				'$format has to be a DateTime::format() compatible string' );
		}
		$this->formatString = $formatString;
	}

	/**
	 * Setter/getter for the string defining how the formatter will format DateTime objects. Should
	 * be a DateTime::format() compatible string.
	 * See http://php.net/manual/en/function.date.php for format documentation.
	 *
	 * @param string $format
	 * @return $this|string
	 */
	public function formatString( $format = null ) {
		return GetterSetter::access( $this )
			->property( __FUNCTION__ )
			->ofType( 'string' )
			->getOrSet( $format );
	}

	/**
	 * @see TimeAndDateFormatter::Format()
	 */
	public function format( DateTime $dateTime ) {
		return $dateTime->format( $this->formatString );
	}
}