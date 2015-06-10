<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\MwBridge;

use User;

/**
 * Factory for MediaWiki User instances.
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
interface MwUserFactory {

	/**
	 * Returns a User instance with the given ID.
	 *
	 * @param int $id
	 * @return User
	 */
	public function newFromId( $id );
}