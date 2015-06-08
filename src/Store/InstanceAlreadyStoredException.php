<?php

namespace MediaWiki\Ext\UserBitcoinAddresses\Store;

use Exception;
use InvalidArgumentException;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord;

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
class InstanceAlreadyStoredException extends Exception {

	/**
	 * @var UserBitcoinAddressRecord
	 */
	protected $storeAttemptInstance;

	/**
	 * @var UserBitcoinAddressRecord
	 */
	protected $alreadyStoredInstance;

	/**
	 * @param UserBitcoinAddressRecord $storeAttemptInstance Instance which was subject to be stored.
	 * @param UserBitcoinAddressRecord $alreadyStoredInstance Instance equal to the instance which
	 *        was subject to be stored but which was in the store already.
	 */
	public function __construct(
		UserBitcoinAddressRecord $storeAttemptInstance,
		UserBitcoinAddressRecord $alreadyStoredInstance
	) {
		parent::__construct(
			'can not store already stored UserBitcoinAddressRecord instance'
		);
		if( ! $storeAttemptInstance->equals( $alreadyStoredInstance ) ) {
			throw new InvalidArgumentException(
				'the two given UserBitcoinAddressRecord instances are not equal');
		}
		$this->storeAttemptInstance = $storeAttemptInstance;
		$this->alreadyStoredInstance = $alreadyStoredInstance;
	}

	/**
	 * Returns the UserBitcoinAddressRecord instance which was subject to the store attempt.
	 *
	 * @return UserBitcoinAddressRecord
	 */
	public function getStoreAttemptInstance() {
		return $this->storeAttemptInstance;
	}

	/**
	 * Returns the instance equal to the instance which was subject to be stored but which was in
	 * the store already.
	 *
	 * @return UserBitcoinAddressRecord
	 */
	public function getAlreadyStoredInstance() {
		return $this->alreadyStoredInstance;
	}
}