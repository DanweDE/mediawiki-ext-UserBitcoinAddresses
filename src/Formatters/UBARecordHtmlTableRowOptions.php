<?php

namespace MediaWiki\Ext\UserBitcoinAddresses\Formatters;

/**
 * Allows defining options for UBARHtmlTableRow formatter.
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
		return $this->getOrSet( 'bitcoinAddressFormatter', $formatter );
	}

	private function getBitcoinAddressFormatter() {
		if( $this->bitcoinAddressFormatter === null ) {
			$this->bitcoinAddressFormatter = new BitcoinAddressMonoSpaceHtml();
		}
		return $this->bitcoinAddressFormatter;
	}

	/**
	 * Sets or gets a formatter to be used for formatting "addedOn" and "exposedOn" time and date
	 * values.
	 *
	 * @param DateTimeFormatter $formatter
	 * @return DateTimeFormatter|$this
	 */
	public function timeAndDateFormatter( DateTimeFormatter $formatter = null ) {
		return $this->getOrSet( 'timeAndDateFormatter', $formatter );
	}

	private function getTimeAndDateFormatter() {
		if( $this->timeAndDateFormatter === null ) {
			$this->timeAndDateFormatter = new StandardDateTimeFormatter();
		}
		return $this->timeAndDateFormatter;
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
		return $this->getOrSet( 'fields', $fields );
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

	/**
	 * Helper for combined getter/setter members.
	 *
	 * @param string $member
	 * @param mixed $value
	 * @return mixed|$this
	 */
	protected final function getOrSet( $member, $value ) {
		if( $value === null ) {
			$memberGetter = 'get' . ucfirst( $member );
			return method_exists( $this, $memberGetter )
				? $this->$memberGetter()
				: $this->$member;
		}
		$this->$member = $value;
		return $this;
	}
}