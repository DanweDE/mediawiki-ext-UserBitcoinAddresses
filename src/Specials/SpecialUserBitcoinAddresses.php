<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Specials;

use SpecialPage;
use Html;

/**
 * @since 1.0.0
 */
class SpecialUserBitcoinAddresses extends SpecialPage {
	/**
	 * @see SpecialPage::__construct
	 */
	public function __construct() {
		parent::__construct( 'UserBitcoinAddresses' );
	}

	public function execute( $subPage ) {
		parent::execute( $subPage );
	}

	/**
	 * @see SpecialPage::getDescription
	 */
	public function getDescription() {
		return $this->msg( 'special-' . strtolower( $this->getName() ) )->text();
	}

	/**
	 * @see SpecialPage::getGroupName
	 */
	function getGroupName() {
		return 'users';
    }

}
