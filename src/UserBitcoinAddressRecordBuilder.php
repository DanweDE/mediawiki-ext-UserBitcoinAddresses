<?php
namespace MediaWiki\Ext\UserBitcoinAddresses;

use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord;
use Danwe\Bitcoin\Address;
use DateTime;
use InvalidArgumentException;
use OOUI\Exception;
use User;

/**
 * For building an UserBitcoinAddress instance.
 *
 * @example
 *   new UserBitcoinAddressBuilder()
 *     ->user( new User( 1337 ) )
 *     ->bitcoinAddress( new Address( '1Gqk4Tv79P91Cc1STQtU3s1W6277M2CVWu' )
 *     ->build();
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class UserBitcoinAddressRecordBuilder implements ExtendableAsUserBitcoinAddressRecordBuilder {

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
	 * Sets the ID or nullifies it.
	 *
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
	 * Sets the user or nullifies it. Object can't be built without an User instance.
	 *
	 * @param User|null $user
	 * @return UserBitcoinAddressRecordBuilder Same instance for chaining.
	 */
	public function user( $user ) {
		if( $user !== null && !( $user instanceof User ) ) {
			throw new InvalidArgumentException( '$user has to be as User instance or null' );
		}
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
	 * Sets the address or nullifies it. Object can't be built without an Address instance.
	 *
	 * @param Address|null $address
	 * @return UserBitcoinAddressRecordBuilder Same instance for chaining.
	 */
	public function bitcoinAddress( $address ) {
		if( $address !== null && !( $address instanceof Address ) ) {
			throw new InvalidArgumentException( '$user has to be as User instance or null' );
		}
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
	 * Sets the date the object was added or nullifies the information.
	 *
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
	 * Sets the date the object was exposed to other users or nullifies the information.
	 * null indicates the address has not yet been shown to other users.
	 *
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
	 * Sets the indicator string of how the user's address has been added or nullifies the
	 * information.
	 *
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
	 * Sets the indicator string of how what the user's addresses purpose was when it was exposed
	 * or could be used to indicate a future purpose if not yet exposed. null indicates no specific
	 * purpose.
	 *
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

	/**
	 * Creates a new builder with all setters set to the value of a given object's getters. The
	 * getters of the object have to match the builder's getters.
	 *
	 * @param ExtendableAsUserBitcoinAddressRecordBuilder $obj
	 * @return UserBitcoinAddressRecordBuilder
	 */
	public static function extend( ExtendableAsUserBitcoinAddressRecordBuilder $obj ) {
		$builder = new UserBitcoinAddressRecordBuilder();
		$methods = get_class_methods( 'MediaWiki\Ext\UserBitcoinAddresses\ExtendableAsUserBitcoinAddressRecordBuilder');
		foreach( $methods as $method ) {
			if( substr( $method, 0, 3 ) !== 'get' ) {
				continue;
			}
			$setter = substr( $method, 3 );
			try {
				$val = $obj->$method();
			} catch( \Exception $e ) {
				throw new InvalidArgumentException(
					'given object does not have getters matching those of the builder class' );
			}
			$builder->$setter( $val );
		}
		return $builder;
	}
}
