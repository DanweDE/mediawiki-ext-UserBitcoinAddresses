<?php
namespace MediaWiki\Ext\UserBitcoinAddresses;

use User;

/**
 * MediaWiki's global User::newFromID() behind MwUserFactory interface.
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class StandardMwUserFactory implements MwUserFactory{

	/**
	 * @see MwUserStore::newFromId()
	 */
	public function newFromId( $id ) {
		return User::newFromId( $id );
	}
}