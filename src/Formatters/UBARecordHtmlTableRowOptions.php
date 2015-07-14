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
	/** @var BitcoinAddressFormatter */
	protected $bitcoinAddressFormatter = null;

	/** @var DateTimeFormatter */
	protected $timeAndDateFormatter = null;

	/** @var string[] */
	protected $printFields;

	/** @var UBARecordHtmlTableRowVirtualFields */
	protected $virtualFields;

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
	 * Sets or gets a UBARecordHtmlTableRowVirtualFields instance holding virtual field definitions.
	 *
	 * @param UBARecordHtmlTableRowVirtualFields $virtualFields
	 * @return UBARecordHtmlTableRowVirtualFields|$this
	 */
	public function virtualFields( UBARecordHtmlTableRowVirtualFields $virtualFields = null ) {
		return GetterSetter::access( $this )
			->property( __FUNCTION__ )
			->initially( function() {
				return new UBARecordHtmlTableRowVirtualFields();
			} )
			->getOrSet( $virtualFields );
	}

	/**
	 * Allows sto set which fields the formatter should print. Prints them in the given order.
	 * Allowed values for "real" fields:
	 *   id, bitcoinAddress, user, addedOn, exposedOn, purpose
	 * In addition to real fields, "virtual" fields given via virtualFields can be provided.
	 * If no parameter is set then this works as a getter to retrieve the fields to be printed.
	 * By default, only real fields will be printed. Use printAllFields() for convenience if all
	 * fields should be printed.
	 *
	 * @param string[] $fields
	 * @return $this
	 */
	public function printFields( array $fields = null ) {
		return GetterSetter::access( $this )
			->property( __FUNCTION__ )
			->initially( function() {
				return $this->getRealFields();
			} )
			->getOrSet( $fields );
	}

	/**
	 * Convenience function to print all fields (virtual and real) rather than real fields only.
	 *
	 * @return $this
	 */
	public function printAllFields() {
		$virtualFields = $this->virtualFields()->getFieldNames();
		$this->printFields = array_merge( $this->getRealFields(), $virtualFields );

		return $this;
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
		$virtualFields = $this->virtualFields()->getFieldNames();
		$allFields = array_merge( $this->getRealFields(), $virtualFields );

		$this->printFields = array_values( // re-index array
			array_diff( $allFields, $fields ) );

		return $this;
	}

	private function getRealFields() {
		return [ 'id', 'bitcoinAddress', 'user', 'addedOn', 'exposedOn', 'purpose' ];
	}

}