<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Store;

use LogicException;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddress;
use User;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord;

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
	 * Stores data based on a given UserBitcoinAddressRecord. Returns an UserBitcoinAddressRecord
	 * representing the stored data.
	 * In addition to the given UserBitcoinAddressRecord instance, the returned instance will have
	 * an ID assigned by the store as well as an "addedOn" date if none was provided by the builder.
	 *
	 * @param UserBitcoinAddressRecord $record
	 * @returns UserBitcoinAddressRecord Represents the stored data.
	 *
	 * @throws LogicException If the given instance's UserBitcoinAddressRecord::getId() is not null.
	 * @throws InstanceAlreadyStoredException If an equivalent instance has already
	 *         been stored.
	 */
	public function add( UserBitcoinAddressRecord $record );

	/**
	 * Replaces data of a record known to the store with the given UserBitcoinAddressRecord's data.
	 * Updated based on the given UserBitcoinAddressRecord's ID.
	 *
	 * @param UserBitcoinAddressRecord $record
	 * @returns UserBitcoinAddressRecord Returns the updated object.
	 */
	public function update( $record );

	/**
	 * Returns a stored UserBitcoinAddressRecord instance with the given ID or null if none exists.
	 *
	 * @param int $id
	 * @return UserBitcoinAddressRecord|null
	 */
	public function fetchById( $id );

	/**
	 * Returns a stored UserBitcoinAddressRecord instance with the given user and bitcoin address
	 * or null if none exists.
	 *
	 * @param UserBitcoinAddress $userBitcoinAddress
	 * @return UserBitcoinAddressRecord|null
	 */
	public function fetchByUserBtcAddress( UserBitcoinAddress $userBitcoinAddress );

	/**
	 * Returns an array with all UserBitcoinAddressRecord instances for a given user. Can be an
	 * empty array if there are not entries for the user.
	 *
	 * @param int $userId
	 * @returns UserBitcoinAddressRecord[]
	 */
	public function fetchAllForUser( User $user );
}