<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Formatters;

/**
 * @group UserBitcoinAddresses
 * @group Database
 * @covers MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecordMwDbStore
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class UBARecordHtmlTableRowUnknownVirtualFieldException extends \Exception {

	/** @var string */
	protected $unknownField;

	/** @var UBARecordHtmlTableRowVirtualFields */
	protected $context;

	/**
	 * @param string $unknownField
	 * @param UBARecordHtmlTableRowVirtualFields $context
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( $unknownField, UBARecordHtmlTableRowVirtualFields $context ) {
		if( !is_string( $unknownField ) ) {
			throw new InvalidArgumentException( '$unknownField is expected to be a string' );
		}

		parent::__construct(
			"\"$unknownField\" is an unknown virtual field within the used context" );

		$this->unknownField = $unknownField;
		$this->context = $context;
	}

	/**
	 * @return string
	 */
	public function getUnknownField() {
		return $this->unknownField;
	}

	/**
	 * @return UBARecordHtmlTableRowVirtualFields
	 */
	public function getContext() {
		return $this->context;
	}
}