<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Store;

use DatabaseBase;
use LogicException;
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
	public function add( UserBitcoinAddressRecord $record ) {
		if( $record->getId() !== null ) {
			throw new LogicException(
				'the UserBitcoinAddressRecordBuilder\'s ID is expected to be null' );
		}

		$recordBuilder = UserBitcoinAddressRecordBuilder::extend( $record );

		if( $recordBuilder->getAddedOn() === null ) {
			$recordBuilder->addedOn( new \DateTime() );
		}
		assert( '$recordBuilder->getAddedOn() !== null' );

		$db = $this->dbMasterProvider->getConnection();
		$db->insert(
			'user_bitcoin_addresses',
			$this->serializeRecordForDb( $recordBuilder->build(), $db ),
			__METHOD__
		);
		return $recordBuilder
			->id( $db->insertId() )
			->build();
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

	private function serializeRecordForDb( UserBitcoinAddressRecord $record, DatabaseBase $db ) {
		$postgresSequentialIdOrNull // In case of e.g. MySql this will be null.
			= $db->nextSequenceValue( 'user_bitcoin_addresses_userbtcaddr_id_seq' );
		return [
			'userbtcaddr_id'            => $postgresSequentialIdOrNull,
			'userbtcaddr_user_id'       => $record->getUser()->getId(),
			'userbtcaddr_address'       => $record->getBitcoinAddress()->asString(),
			'userbtcaddr_added_through' => $record->getAddedThrough(),
			'userbtcaddr_added_on'      => $this->serializedDateOrNull( $record->getAddedOn(), $db ),
			'userbtcaddr_exposed_on'    => $this->serializedDateOrNull( $record->getExposedOn(), $db ),
			'userbtcaddr_purpose'       => $record->getPurpose(),
		];
	}

	private function serializedDateOrNull( $value, DatabaseBase $db ) {
		return $value === null
			? null
			: $db->timestamp( date_format( $value, 'YYYYMMDDHHMMSS' ) );
	}
}