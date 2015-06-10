<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Store;

use DatabaseBase;
use LogicException;
use InvalidArgumentException;
use User;
use DateTime;
use stdClass;
use Danwe\Bitcoin\Address as BitcoinAddress;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddress;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecordBuilder;
use MediaWiki\Ext\UserBitcoinAddresses\MwUserFactory;

/**
 * For building an UserBitcoinAddress instance.
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class UserBitcoinAddressRecordMwDbStore implements UserBitcoinAddressRecordStore {
	protected static $instanceFields = [
		'userbtcaddr_id',
		'userbtcaddr_user_id',
		'userbtcaddr_address',
		'userbtcaddr_added_through',
		'userbtcaddr_added_on',
		'userbtcaddr_exposed_on',
		'userbtcaddr_purpose',
	];

	/**
	 * @var DBConnectionProvider
	 */
	protected $dbSlaveProvider;

	/**
	 * @var DBConnectionProvider
	 */
	protected $dbMasterProvider;

	/**
	 * @var MwUserFactory
	 */
	protected $mwUserFactory;

	/**
	 * @param DBConnectionProvider $dbSlaveProvider
	 * @param DBConnectionProvider $dbMasterProvider
	 * @param MwUserFactory $mwUserFactory
	 */
	function __construct(
		DBConnectionProvider $dbSlaveProvider,
		DBConnectionProvider $dbMasterProvider,
		MwUserFactory $mwUserFactory
	) {
		$this->dbSlaveProvider = $dbSlaveProvider;
		$this->dbMasterProvider = $dbMasterProvider;
		$this->mwUserFactory = $mwUserFactory;
	}

	/**
	 * @see UserBitcoinAddressRecordStore::add()
	 *
	 * @throws InvalidArgumentException If given user is anonymous.
	 */
	public function add( UserBitcoinAddressRecord $record ) {
		if( $record->getId() !== null ) {
			throw new LogicException(
				'the UserBitcoinAddressRecordBuilder\'s ID is expected to be null' );
		}
		if( $record->getUser()->isAnon() ) {
			throw new InvalidArgumentException( 'expected $record to have a non-anonymous user' );
		}
		$equalStoredRecord = $this->fetchByUserBtcAddress( $record );
		if( $equalStoredRecord ) {
			throw new InstanceAlreadyStoredException( $record, $equalStoredRecord );
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
	 * @see UserBitcoinAddressRecordStore::fetchById()
	 */
	public function fetchById( $id ) {
		return
			$this->fetchSingleInstanceByConditions( [
				'userbtcaddr_id' => $id,
			] );
	}

	/**
	 * @see UserBitcoinAddressRecordStore::fetchByUserBtcAddress()
	 */
	public function fetchByUserBtcAddress( UserBitcoinAddress $userBitcoinAddress ) {
		return
			$this->fetchSingleInstanceByConditions( [
				'userbtcaddr_user_id' => $userBitcoinAddress->getUser()->getId(),
				'userbtcaddr_address' => $userBitcoinAddress->getBitcoinAddress()->asString(),
			] );
	}

	/**
	 * @param array $conditions DatabaseBase::selectRow 3rd argument compatible conditions.
	 * @return UserBitcoinAddressRecord|null
	 */
	protected function fetchSingleInstanceByConditions( array $conditions ) {
		$db = $this->dbSlaveProvider->getConnection();
		$fields = self::$instanceFields;

		$row = $db->selectRow(
			'user_bitcoin_addresses',
			$fields,
			$conditions,
			__METHOD__
		);
		if( $row === false ) {
			return null;
		}

		return $this->buildUserBitcoinAddressRecordFromDbRow( $row );
	}

	/**
	 * @see UserBitcoinAddressRecordStore::fetchAllForUser()
	 */
	public function fetchAllForUser( User $user ) {
		if( $user->isAnon() ) {
			return []; // We don't really support anonymous users.
		}

		$db = $this->dbSlaveProvider->getConnection();
		$fields = self::$instanceFields;

		$res = $db->select(
			'user_bitcoin_addresses',
			$fields,
			[ 'userbtcaddr_user_id' => $user->getId() ],
			__METHOD__
		);

		$addresses = [];
		foreach ( $res as $row ) {
			$addresses[] = $this->buildUserBitcoinAddressRecordFromDbRow( $row );
		}
		return $addresses;
	}


	protected function buildUserBitcoinAddressRecordFromDbRow( stdClass $row ) {
		$userId = intval( $row->userbtcaddr_user_id );
		$user = $this->mwUserFactory->newFromId( $userId );
		$btcAddr = new BitcoinAddress( $row->userbtcaddr_address );
		$addedOn = $this->deserializeDateOrNull( $row->userbtcaddr_added_on );
		$exposedOn = $this->deserializeDateOrNull( $row->userbtcaddr_exposed_on );

		return ( new UserBitcoinAddressRecordBuilder() )
			->id( intval( $row->userbtcaddr_id ) )
			->user( $user )
			->bitcoinAddress( $btcAddr )
			->addedThrough( $row->userbtcaddr_added_through )
			->addedOn( $addedOn )
			->exposedOn( $exposedOn )
			->purpose( $row->userbtcaddr_purpose )
			->build();
	}

	private function serializeRecordForDb( UserBitcoinAddressRecord $record, DatabaseBase $db ) {
		$postgresSequentialIdOrNull // In case of e.g. MySql this will be null.
			= $db->nextSequenceValue( 'user_bitcoin_addresses_userbtcaddr_id_seq' );

		$serializedAddedOn = $this->serializeDateOrNull( $record->getAddedOn(), $db );
		$serializedExposedOn = $this->serializeDateOrNull( $record->getExposedOn(), $db );
		return [
			'userbtcaddr_id'            => $postgresSequentialIdOrNull,
			'userbtcaddr_user_id'       => $record->getUser()->getId(),
			'userbtcaddr_address'       => $record->getBitcoinAddress()->asString(),
			'userbtcaddr_added_through' => $record->getAddedThrough(),
			'userbtcaddr_added_on'      => $serializedAddedOn,
			'userbtcaddr_exposed_on'    => $serializedExposedOn,
			'userbtcaddr_purpose'       => $record->getPurpose(),
		];
	}

	private function serializeDateOrNull( $value, DatabaseBase $db ) {
		return $value === null
			? null
			: $db->timestamp( $value->format( 'YmdHis' ) );
	}

	private function deserializeDateOrNull( $value ) {
		return !$value
			? null
			: DateTime::createFromFormat( 'YmdHis', $value );
	}
}