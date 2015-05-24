<?php
namespace MediaWiki\Ext\UserBitcoinAddresses;

use Danwe\Bitcoin\Address as BitcoinAddress;
use User;

/**
 * Object representing a (compressed) bitcoin addresses owned by a user.
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
interface UserBitcoinAddress {
	/**
	 * @return User
	 */
	public function getUser();

	/**
	 * @return BitcoinAddress
	 */
	public function getBitcoinAddress();
}