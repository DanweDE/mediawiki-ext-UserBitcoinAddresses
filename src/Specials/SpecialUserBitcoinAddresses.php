<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Specials;

use SpecialPage;
use Html;
use HTMLForm;

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

		$this->requireLogin();
		$this->checkReadOnly();

		$this->buildSubmitForm();
	}


	protected function buildSubmitForm() {
		$form = new HTMLForm( array(
			'myfield1' => array(
				'label-message' => 'userbtcaddr-submitaddresses-manualinsert-addresses-label',
				'type' => 'textarea',
				'cols' => 34,
				'rows' => 5,
				'spellcheck' => false, // TODO: Implement support for this in MW core.
			),
		), $this->getContext() );
		$form->setMethod( 'post' );
		$form->setWrapperLegendMsg( 'userbtcaddr-submitaddresses-manualinsert-legend' );

		$form->prepareForm()->displayForm( false );
	}

	/**
	 * @see SpecialPage::requireLogin
	 */
	public function requireLogin(
		$reasonMsg = null, $titleMsg = 'exception-nologin'
	) {
		global $wgVersion;

		if( $reasonMsg === null ) {
			$reasonMsg = version_compare( $wgVersion, '1.25rc', '>=' )
				? 'userbtcaddr-loginrequired' // added in LoginFormValidErrorMessages hook (MW 1.25)
				: 'prefsnologintext2';
		}
		parent::requireLogin( $reasonMsg, $titleMsg );
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
