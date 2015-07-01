<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Formatters;

use Danwe\Helpers\GetterSetterAccessor as GetterSetter;

/**
 * Allows defining options for UBARecordHtmlTableRow formatter.
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class UBARecordHtmlTableRowOptions {

	/**
	 * @var BitcoinAddressFormatter
	 */
	protected $bitcoinAddressFormatter = null;

	/**
	 * @var DateTimeFormatter
	 */
	protected $timeAndDateFormatter = null;

	/**
	 * @var string[]
	 */
	protected $fields = [
		'id', 'bitcoinAddress', 'user', 'addedOn', 'exposedOn', 'purpose'
	];

	/**
	 * Sets or gets a formatter to be used for formatting Bitcoin addresses.
	 *
	 * @param BitcoinAddressFormatter $formatter
	 * @return BitcoinAddressFormatter|$this
	 */
	public function bitcoinAddressFormatter( BitcoinAddressFormatter $formatter = null ) {
		return GetterSetter::access( $this )
			->property( __FUNCTION__ )
			->initially( function() {
				return new BitcoinAddressMonoSpaceHtml();
			} )
			->getOrSet( $formatter );
	}

	/**
	 * Sets or gets a formatter to be used for formatting "addedOn" and "exposedOn" time and date
	 * values.
	 *
	 * @param DateTimeFormatter $formatter
	 * @return DateTimeFormatter|$this
	 */
	public function timeAndDateFormatter( DateTimeFormatter $formatter = null ) {
		return GetterSetter::access( $this )
			->property( __FUNCTION__ )
			->initially( function() {
				return new StandardDateTimeFormatter();
			} )
			->getOrSet( $formatter );
	}

	/**
	 * Allows sto set which fields the formatter should print. Prints them in the given order.
	 * Allowed values:
	 *   "id", "bitcoinAddress", "user", "addedOn", "exposedOn", "purpose"
	 * If no parameter is set then this works as a getter to retrieve the fields to be printed.
	 *
	 * @param string[] $fields
	 * @return $this
	 */
	public function printFields( array $fields = null ) {
		return GetterSetter::access( $this )
			->property( 'fields' )
			->getOrSet( $fields );
	}

	/**
	 * Convenience function to remove one or more fields from the "printFields" option.
	 *
	 * @param string[]|string $fields
	 * @return $this
	 */
	public function printFieldsWithout( $fields ) {
		if( is_string( $fields ) ) {
			$fields = [ $fields ];
		}
		$this->fields = array_values( // re-index array
			array_diff( $this->fields, $fields ) );

		return $this;
	}

}