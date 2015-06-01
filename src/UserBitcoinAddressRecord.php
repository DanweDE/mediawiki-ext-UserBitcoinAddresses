<?php
namespace MediaWiki\Ext\UserBitcoinAddresses;

use Danwe\Bitcoin\Address;
use DateTime;
use InvalidArgumentException;
use LogicException;
use User;

/**
 * Object representing a (compressed) bitcoin addresses owned by a user together with certain
 * meta information.
 *
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class UserBitcoinAddressRecord implements UserBitcoinAddress {

	/** @var int|null */
	protected $id;

	/** @var User */
	protected $user;

	/** @var Address */
	protected $bitcoinAddress;

	/** @var DateTime|null */
	protected $addedOn;

	/** @var string|null */
	protected $addedThrough;

	/** @var DateTime|null */
	protected $exposedOn;

	/** @var string|null */
	protected $purpose;

	/**
	 * @param UserBitcoinAddressRecordBuilder $builder
	 */
	function __construct( UserBitcoinAddressRecordBuilder $builder ) {
		$this->id = $builder->getId();
		$this->user = $builder->getUser();
		$this->bitcoinAddress = $builder->getBitcoinAddress();
		$this->addedOn = $builder->getAddedOn();
		$this->addedThrough = $builder->getAddedThrough();
		$this->exposedOn = $builder->getExposedOn();
		$this->purpose = $builder->getPurpose();

		$this->validate();
	}

	protected function validate() {
		if( !( $this->user instanceof User ) ) {
			throw new InvalidArgumentException(
				'No User object set via UserBitcoinAddressBuilder::user()' );
		}
		if( !( $this->bitcoinAddress instanceof Address ) ) {
			throw new InvalidArgumentException(
				'No BitcoinAddress object set via UserBitcoinAddressBuilder::bitcoinAddress()' );
		}
		$this->consistencyCheck();
	}

	protected function consistencyCheck() {
		if( $this->addedOn !== null && $this->exposedOn !== null
			&& $this->addedOn > $this->exposedOn
		) {
			throw new LogicException( 'date the address was added can not be after its exposure date' );
		}
	}

	/**
	 * @return int|null
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @see UserBitcoinAddress::getUser()
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * @see UserBitcoinAddress::getBitcoinAddress()
	 */
	public function getBitcoinAddress() {
		return $this->bitcoinAddress;
	}

	/**
	 * @return DateTime|null
	 */
	public function getAddedOn() {
		return $this->addedOn;
	}

	/**
	 * @return DateTime|null
	 */
	public function getExposedOn() {
		return $this->exposedOn;
	}

	/**
	 * @return string|null
	 */
	public function getAddedThrough() {
		return $this->addedThrough;
	}

	/**
	 * @return string|null
	 */
	public function getPurpose() {
		return $this->purpose;
	}

	/**
	 * Returns whether the address has knowingly been exposed to other users already.
	 *
	 * @return bool
	 */
	public function isExposed() {
		return $this->getExposedOn() !== null;
	}
}
