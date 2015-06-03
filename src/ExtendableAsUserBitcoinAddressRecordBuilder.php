<?php
namespace MediaWiki\Ext\UserBitcoinAddresses;

use Danwe\Bitcoin\Address;
use User;

/**
 * Indicates that an instance can serve as base to be extended by UserBitcoinAddressRecordBuilder::extend.
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
interface ExtendableAsUserBitcoinAddressRecordBuilder {
	/**
	 * @return int|null
	 */
	public function getId();

	/**
	 * @return User|null
	 */
	public function getUser();

	/**
	 * @return Address|null
	 */
	public function getBitcoinAddress();

	/**
	 * @return DateTime|null
	 */
	public function getAddedOn();

	/**
	 * @return DateTime|null
	 */
	public function getExposedOn();

	/**
	 * @return string|null
	 */
	public function getAddedThrough();

	/**
	 * @return string|null
	 */
	public function getPurpose();
}