<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Formatters;

use LogicException;
use InvalidArgumentException;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord as UBARecord;

/**
 * Hash map of "virtual" field names and callback functions computing the virtual fields' values.
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class UBARecordHtmlTableRowVirtualFields {

	protected $fields;

	public function __construct() {
		$this->fields = [];
	}

	/**
	 * Sets a virtual field's callback under the given name.
	 *
	 * @param string $fieldName
	 * @param callable $callback The callback gets the row's UserBitcoinAddressRecord instance
	 *        as first parameter and is expected to return the virtual field value as a string.
	 * @return $this
	 */
	public function set( $fieldName, $callback ) {
		if( !is_string( $fieldName ) ) {
			throw new InvalidArgumentException( '$fieldName is expected to be a string' );
		}
		if( !is_callable( $callback ) ) {
			throw new InvalidArgumentException( '$callback is expected to be a callable' );
		}
		$this->fields[ $fieldName ] = $callback;

		return $this;
	}

	public function has( $fieldName ) {
		if( !is_string( $fieldName ) ) {
			throw new InvalidArgumentException( '$fieldName is expected to be a string' );
		}
		return array_key_exists( $fieldName, $this->fields );
	}

	/**
	 * Removes a virtual field.
	 *
	 * @param string $fieldName
	 * @return $this
	 */
	public function remove( $fieldName ) {
		if( !is_string( $fieldName ) ) {
			throw new InvalidArgumentException( '$fieldName is expected to be a string' );
		}
		unset( $this->fields[ $fieldName ] );

		return $this;
	}

	/**
	 * Returns all field names which have callback functions set.
	 *
	 * @return string[]
	 */
	public function getFieldNames() {
		return array_keys( $this->fields );
	}

	/**
	 * @param string $fieldName
	 * @param UBARecord $record
	 * @return string
	 *
	 * @throws UBARecordHtmlTableRowUnknownVirtualFieldException
	 * @throws LogicException If the field's callback function does not return a string.
	 */
	public function computeValueFor( $fieldName, UBARecord $record ) {
		if( !is_string( $fieldName ) ) {
			throw new InvalidArgumentException( '$fieldName is expected to be a string' );
		}
		if( !$this->has( $fieldName ) ) {
			throw new UBARecordHtmlTableRowUnknownVirtualFieldException( $fieldName, $this );
		}

		$value = $this->fields[ $fieldName ]( $record );

		if( !is_string( $value ) ) {
			throw new LogicException( "the value computed for field \"$fieldName\" is a "
				. gettype( $value ) . " while a string was expected" );
		}

		return $value;
	}
}