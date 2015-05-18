<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Specials;

use \Exception;
use SpecialPage;
use DerivativeRequest;
use Html;
use HTMLForm;
use Danwe\Bitcoin\Address as BtcAddress;

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

		$this->renderSubmitForm( $this->getContext() );
	}

	protected function renderSubmitForm( $context ) {
		$addressFieldTemplate = [
			'type' => 'textarea',
			'cols' => 34,
			'rows' => 5,
			'spellcheck' => false, // TODO: Works after I5882e gets merged into MW core.
		];
		$formData = [
			'info-intro' => [
				'type' => 'info',
				'vertical-label' => true,
				'label-message' => 'userbtcaddr-submitaddresses-manualinsert-addresses-explanation'
			],
			'addresses' => array_merge( $addressFieldTemplate, [
				'label-message' => 'userbtcaddr-submitaddresses-manualinsert-addresses-label',
				'validation-callback' => [ $this, 'validateAddressInput' ],
			] ),
			'addressesToBeCorrected' => $addressFieldTemplate,
		];
		( new HTMLForm( $formData, $context ) )
			->setMethod( 'post' )
			->setWrapperLegendMsg( 'userbtcaddr-submitaddresses-manualinsert-legend' )
			->setSubmitCallback( [ $this, 'formSubmitted' ] )
			// Renders form if failure or empty. On-success rendering handled in "formSubmitted".
			->show();
	}

	public function formSubmitted( $data, HTMLForm $form ) {
		$this->renderSubmitFormAsEmpty( $form ); // Display form again but empty data.
		return true;
	}

	protected function renderSubmitFormAsEmpty( HtmlForm $form ) {
		$emptyRequest = new DerivativeRequest( $form->getRequest(), [] );
		$emptyRequestContext = clone $form->getContext();;
		$emptyRequestContext->setRequest( $emptyRequest );

		$this->renderSubmitForm( $emptyRequestContext );
	}

	public function validateAddressInput( $value, $alldata, $form ) {
		$allAddresses = $alldata['addresses'] . ' ' . $alldata['addressesToBeCorrected'];
		$addressStrings = array_filter(
			array_unique( preg_split( "/[^A-Za-z0-9]+/", $allAddresses ) ),
			function( $val ) { return $val !== ''; }
		);
		$invalidAddressStrings = [];
		$validAddressStrings = [];

		foreach( $addressStrings as $addressString ) {
			try {
				new BtcAddress( $addressString );
				$validAddressStrings[] = $addressString;
			} catch( Exception $error ) {
				$invalidAddressStrings[] = $addressString;
			}
		}

		$form->mFieldData['addresses'] = implode( "\n", $validAddressStrings );
		$form->mFieldData['addressesToBeCorrected'] = implode( "\n", $invalidAddressStrings );

		if( !empty( $invalidAddressStrings ) ) {

			$msg = $this->msg(
				'userbtcaddr-submitaddresses-manualinsert-addresses-correctionrequest',
				sizeof( $invalidAddressStrings )
			)->parse();
			return HTML::element( 'div', [ 'class' => 'error' ] , $msg );
		}
		else if( empty( $validAddressStrings ) ) {
			return HTML::element( 'div', [ 'class' => 'error' ] , 'no addresses given!' );
		}
		return true;
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
