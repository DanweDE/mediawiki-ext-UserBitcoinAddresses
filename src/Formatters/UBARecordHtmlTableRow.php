<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Formatters;

use Html;
use DateTime;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord as UBARecord;
use Danwe\Helpers\GetterSetterAccessor as GetterSetter;

/**
 * Formats a single UserBitcoinAddressRecord as HTML table row.
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class UBARecordHtmlTableRow {

	/**
	 * @var UBARHtmlTableRowOptions
	 */
	protected $options;

	public function __construct( UBARecordHtmlTableRowOptions $options = null ) {
		$this->options = $options ? $options : new UBARecordHtmlTableRowOptions();
	}

	/**
	 * Returns or sets the formatter options. Allows to change the options of the formatter instance
	 * even after instantiation in a fluent interface fashioned way.
	 *
	 * @return UBARecordHtmlTableRowOptions|$this
	 */
	public function options( UBARecordHtmlTableRowOptions $options = null ) {
		return GetterSetter::access( $this )
			->property( __FUNCTION__ )
			->getOrSet( $options );
	}

	/**
	 * Formats the given UserBitcoinAddressRecord instance and returns an HTML string.
	 *
	 * @param UBARecord $record
	 * @return string
	 */
	public function format( UBARecord $record ) {
		$printFields = $this->options()->printFields();
		$skipNext = false;
		$cells = '';

		foreach( $printFields as $i => $fieldName ) {
			if( $skipNext ) {
				$skipNext = false;
				continue;
			}
			$colspanCell = $this->handleExposedOnAndPurposeColspan( $record, $printFields, $i );
			if( $colspanCell !== null ) {
				$cells .= $colspanCell;
				$skipNext = true;
			}
			else {
				$cellValue = $this->getCellValue( $fieldName, $record );
				// TODO: Actually, non-existent field should just result in empty cell rather than being ignored!
				if( $cellValue !== null ) {
					$cells .= Html::rawElement( 'td', [], $cellValue );
				}
			}
		}
		return Html::rawElement( 'tr', [], $cells );
	}

	protected function getCellValue( $field, $record ) {
		switch( $field ) {
			case 'id':
				return htmlspecialchars( $record->getId() );

			case 'bitcoinAddress':
				return $this->options->bitcoinAddressFormatter()->format( $record->getBitcoinAddress() );

			case 'user':
				return htmlspecialchars( $record->getUser()->getName() );

			case 'addedOn':
				return $this->formatAddedOn( $record->getAddedOn() );

			case 'exposedOn':
				return $this->formatExposedOn( $record->getExposedOn() );

			case 'purpose':
				return $this->formatPurpose( $record->getPurpose() );

			default:
				return null;
		}
	}

	/**
	 * Handles special formatting case where the "exposedOn" field followed by the "purpose" field
	 * (or the other way around) will be joined in one cell if both values are null.
	 */
	private function handleExposedOnAndPurposeColspan( $record, $printFields, $i ) {
		$fieldName = $printFields[ $i ];

		if( ( $fieldName === 'exposedOn' || $fieldName === 'purpose' )
			&& $record->getExposedOn() === null && $record->getPurpose() === null
			&& array_key_exists( $i + 1, $printFields )
			&& ( $printFields[ $i + 1 ] === 'purpose' || $printFields[ $i + 1 ] === 'exposedOn' )
		) {
			$cellValue1 = $this->getCellValue( $fieldName, $record );
			$cellValue2 = $this->getCellValue( $printFields[ $i + 1 ], $record );

			if( $cellValue1 === $cellValue2 ) {
				return Html::rawElement( 'td', [ 'colspan' => 2 ], $cellValue1 );
			}
		}
		return null;
	}

	/**
	 * @param DateTime|null $value
	 * @return string
	 */
	protected function formatAddedOn( $value ) {
		return $value === null
			? '<i>' . wfMessage( 'userbtcaddr-formatters-unknowndate' )->escaped() . '</i>'
			: $this->options->timeAndDateFormatter()->format( $value );
	}

	/**
	 * @param DateTime|null $value
	 * @return string
	 */
	protected function formatExposedOn( $value ) {
		return $value === null
			? '<i>' . wfMessage( 'userbtcaddr-formatters-addressnotexposedyet' )->escaped() . '</i>'
			: htmlspecialchars( $this->options->timeAndDateFormatter()->format( $value ) );
	}

	/**
	 * @param string|null $value
	 * @return string
	 */
	protected function formatPurpose( $value ) {
		return $value === null
			? '<i>' . wfMessage( 'userbtcaddr-formatters-addressnotexposedyet' )->escaped() . '</i>'
			: htmlspecialchars( $value );
	}
}