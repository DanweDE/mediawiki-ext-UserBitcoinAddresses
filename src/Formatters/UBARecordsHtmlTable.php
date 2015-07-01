<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Formatters;

use Html;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord as UBARecord;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord;
use Danwe\Helpers\GetterSetterAccessor as GetterSetter;

/**
 * Formats an array of UserBitcoinAddressRecord as HTML table.
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class UBARecordsHtmlTable {

	/**
	 * @var UBARHtmlTableRowOptions
	 */
	protected $options;

	public function __construct( UBARecordsHtmlTableOptions $options = null ) {
		$this->options = $options ? $options : new UBARecordsHtmlTableOptions();
	}

	/**
	 * Returns or sets the formatter options. Allows to change the options of the formatter instance
	 * even after instantiation in a fluent interface fashioned way.
	 *
	 * @return UBARecordsHtmlTableOptions|$this
	 */
	public function options( UBARecordsHtmlTableOptions $options = null ) {
		return GetterSetter::access( $this )
			->property( __FUNCTION__ )
			->getOrSet( $options );
	}

	/**
	 * Formats the given array of UserBitcoinAddressRecord instances as HTML table.
	 *
	 * @param UBARecord[] $records
	 * @return string
	 */
	public function format( array $records ) {
		$rows = $this->options()->printHeader()
			? [ $this->buildHeaderRow() ]
			: [];

		foreach( $records as $i => $record ) {
			if( !( $record instanceof UserBitcoinAddressRecord ) ) {
				throw new \InvalidArgumentException(
					"\$record[$i] is not an UserBitcoinAddressRecord instance but of type "
					. gettype( $record ) );
			}
			$rows[] = $this->options()->rowFormatter()->format( $record );
		}

		return Html::rawElement( 'table',
			[ 'class' => 'wikitable sortable' ], implode( '', $rows ) );
	}

	/**
	 * @return string HTML for the table's header row.
	 */
	protected function buildHeaderRow() {
		foreach( $this->options->rowFormatter()->options()->printFields() as $field ) {
			$lcField = strtolower( $field );

			// TODO: Don't use global wfMessage, use options for message management.
			$ths[] = Html::element( 'th', [], wfMessage(
				"userbtcaddr-formatters-recordstable-th-$lcField" )->text() );
			// Standard message string combinations:
			//  - userbtcaddr-formatters-recordstable-th-id
			//  - userbtcaddr-formatters-recordstable-th-bitcoinaddress
			//  - userbtcaddr-formatters-recordstable-th-user
			//  - userbtcaddr-formatters-recordstable-th-addedon
			//  - userbtcaddr-formatters-recordstable-th-exposedon
			//  - userbtcaddr-formatters-recordstable-th-purpose
		}
		return Html::rawElement( 'tr', [], implode( '', $ths ) );
	}
}
