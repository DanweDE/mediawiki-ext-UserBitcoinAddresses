<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Formatters;

use Danwe\Helpers\GetterSetterAccessor as GetterSetter;

/**
 * Allows defining options for UBARecordsHtmlTable formatter.
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class UBARecordsHtmlTableOptions {

	/**
	 * @var UBARecordHtmlTableRow
	 */
	protected $rowFormatter;

	/**
	 * @var bool
	 */
	protected $printHeader = true;

	/**
	 * Sets or gets the UBARecordHtmlTableRow instance responsible for formatting the table's rows.
	 *
	 * @return UBARecordHtmlTableRow|$this
	 */
	public function rowFormatter( UBARecordHtmlTableRow $rowFormatter = null ) {
		return GetterSetter::access( $this )
			->property( __FUNCTION__ )
			->initially( function() {
				return new UBARecordHtmlTableRow();
			} )
			->getOrSet( $rowFormatter );
	}

	/**
	 * Sets or gets whether a header row is supposed to be printed by this formatter.
	 *
	 * @param bool $printHeader
	 * @return bool|$this
	 */
	public function printHeader( $printHeader = null ) {
		return GetterSetter::access( $this )
			->property( __FUNCTION__ )
			->getOrSet( $printHeader );
	}

}