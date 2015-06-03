<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Store;

use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecordBuilder;

/**
 * Store interface for storing and fetching UserBitcoinAddressRecord instances.
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
interface UserBitcoinAddressRecordStore {
	/**
	 * Stores data based on a given UserBitcoinAddressRecord and returns a UserBitcoinAddressRecord
	 * representing the stored data.
	 * In addition to the provided data, the returned UserBitcoinAddressRecord will have an ID
	 * assigned by the store as well as an "addedOn" date if none was provided via the builder.
	 *
	 * @param UserBitcoinAddressRecordBuilder $recordBuilder
	 * @returns UserBitcoinAddressRecord Represents the stored data.
	 *
	 * @throws LogicException If the builder's ID is not null.
	 */
	public function add( UserBitcoinAddressRecordBuilder $recordBuilder );

	/**
	 * Replaces data of a record known to the store with the given UserBitcoinAddressRecord's data.
	 * Updated based on the given UserBitcoinAddressRecord's ID.
	 *
	 * @param UserBitcoinAddressRecord $record
	 * @returns UserBitcoinAddressRecord Returns the updated object.
	 */
	public function update( $record );

	/**
	 * Returns an array with all UserBitcoinAddressRecord instances for a given user. Can be an
	 * empty array if there are not entries for the user.
	 *
	 * @param int $userId
	 * @returns UserBitcoinAddressRecord[]
	 */
	public function fetchAllForUser( $userId );
}