<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Store;

use DatabaseBase;
use MediaWiki\Ext\UserBitcoinAddresses\Store\UserBitcoinAddressRecordStore;
use MediaWiki\Ext\UserBitcoinAddresses\Store\DBConnectionProvider;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecordBuilder;

/**
 * For building an UserBitcoinAddress instance.
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class UserBitcoinAddressRecordMwDbStore implements UserBitcoinAddressRecordStore {
	/**
	 * @var DBConnectionProvider
	 */
	protected $dbSlaveProvider;

	/**
	 * @var DBConnectionProvider
	 */
	protected $dbMasterProvider;

	/**
	 * @param DBConnectionProvider $dbSlaveProvider
	 * @param DBConnectionProvider $dbMasterProvider
	 */
	function __construct(
		DBConnectionProvider $dbSlaveProvider,
		DBConnectionProvider $dbMasterProvider
	) {
		// TODO
		$this->dbSlaveProvider = $dbSlaveProvider;
		$this->dbMasterProvider = $dbMasterProvider;
	}

	/**
	 * @see UserBitcoinAddressRecordStore::add()
	 */
	public function add( UserBitcoinAddressRecordBuilder $recordBuilder ) {
		// TODO
	}

	/**
	 * @see UserBitcoinAddressRecordStore::update()
	 */
	public function update( $userBitcoinAddressRecord ) {
		// TODO
	}

	/**
	 * @see UserBitcoinAddressRecordStore::fetchAllForUser()
	 */
	public function fetchAllForUser( $userId ) {
		// TODO
	}
}