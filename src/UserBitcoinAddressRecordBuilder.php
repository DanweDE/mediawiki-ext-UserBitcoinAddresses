<?php
namespace MediaWiki\Ext\UserBitcoinAddresses;

use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord;
use Danwe\Bitcoin\Address;
use DateTime;
use InvalidArgumentException;
use User;

/**
 * For building an UserBitcoinAddress instance.
 *
 * @example
 *   new UserBitcoinAddressBuilder()
 *     ->user( new User( 1337 ) )
 *     ->bitcoinAddress( new Address( '1Gqk4Tv79P91Cc1STQtU3s1W6277M2CVWu' )
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class UserBitcoinAddressRecordBuilder {

	/** @var string|null */
	protected $id;

	/** @var User */
	protected $user;

	/** @var Address */
	protected $bitcoinAddress;

	/** @var DateTime|null */
	protected $addedOn;

	/** @var string|null */
	protected $addedThrough = null;

	/** @var DateTime|null */
	protected $exposedOn = null;

	/** @var string|null */
	protected $purpose = null;

	public function build() {
		return new UserBitcoinAddressRecord( $this );
	}

	/**
	 * @param int $id
	 * @return UserBitcoinAddressRecordBuilder Same instance for chaining.
	 */
	public function id( $id = null ) {
		if( $id !== null && !is_int( $id ) || $id < 0 ) {
			throw new InvalidArgumentException( 'id has to be a integer >= 0 or null' );
		}
		$this->id = $id;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param User $user
	 * @return UserBitcoinAddressRecordBuilder Same instance for chaining.
	 */
	public function user( User $user ) {
		$this->user = $user;
		return $this;
	}

	/**
	 * @return User
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * @param Address $address
	 * @return UserBitcoinAddressRecordBuilder Same instance for chaining.
	 */
	public function bitcoinAddress( Address $address ) {
		$this->bitcoinAddress = $address;
		return $this;
	}

	/**
	 * @return Address
	 */
	public function getBitcoinAddress() {
		return $this->bitcoinAddress;
	}

	/**
	 * @param DateTime|null $date
	 * @return UserBitcoinAddressRecordBuilder Same instance for chaining.
	 */
	public function addedOn( DateTime $date = null ) {
		$this->addedOn = $date;
		return $this;
	}

	/**
	 * @return DateTime
	 */
	public function getAddedOn() {
		return $this->addedOn;
	}

	/**
	 * @param DateTime|null $date
	 * @return UserBitcoinAddressRecordBuilder Same instance for chaining.
	 */
	public function exposedOn( DateTime $date = null ) {
		$this->exposedOn = $date;
		return $this;
	}

	/**
	 * @return DateTime|null
	 */
	public function getExposedOn() {
		return $this->exposedOn;
	}

	/**
	 * @param string|null $keyword
	 * @return UserBitcoinAddressRecordBuilder Same instance for chaining.
	 */
	public function addedThrough( $keyword = null ) {
		if( $keyword !== null && !is_string( $keyword ) ) {
			throw new InvalidArgumentException( 'keyword has to be a string or null' );
		}
		$this->addedThrough = $keyword;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getAddedThrough() {
		return $this->addedThrough;
	}

	/**
	 * @param string|null $keyword
	 * @return UserBitcoinAddressRecordBuilder Same instance for chaining.
	 */
	public function purpose( $keyword = null ) {
		if( $keyword !== null && !is_string( $keyword ) ) {
			throw new InvalidArgumentException( 'keyword has to be a string or null' );
		}
		$this->purpose = $keyword;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getPurpose() {
		return $this->purpose;
	}
}
