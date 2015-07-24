<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Specials;

use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord as UBARecord;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecordBuilder as UBARBuilder;
use MediaWiki\Ext\UserBitcoinAddresses\Store\UserBitcoinAddressRecordStore as UBARStore;
use MediaWiki\Ext\UserBitcoinAddresses\Store\InstanceAlreadyStoredException;
use MediaWiki\Ext\UserBitcoinAddresses\Formatters\UBARecordsHtmlTable;
use MediaWiki\Ext\UserBitcoinAddresses\Formatters\BitcoinAddressMonoSpaceHtml;
use MediaWiki\Ext\UserBitcoinAddresses\Formatters\MWUserDateTimeHtml;
use Exception;
use SpecialPage;
use DerivativeRequest;
use Html;
use HTMLForm;
use Danwe\Bitcoin\Address as BtcAddress;

/**
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class SpecialUserBitcoinAddresses extends SpecialPage {

	/**
	 * @var UBARStore
	 */
	protected $store;

	/**
	 * Valid Bitcoin addresses collected during field validation. Will be used in form submit
	 * callback to save some execution time for redundant work.
	 *
	 * @var BtcAddress[]
	 */
	protected $validBtcAddresses;

	/**
	 * Records which have been stored after form has been submitted.
	 *
	 * @var UBARecord[]
	 */
	protected $storedRecords;

	/**
	 * Records which were supposed to be stored after form has been submitted but which are
	 * redundant with the user's existing addresses.
	 *
	 * @var UBARecord[]
	 */
	protected $existingRecords;

	/**
	 * @see SpecialPage::__construct
	 */
	public function __construct( UBARStore $userBitcoinAddressStore ) {
		parent::__construct( 'UserBitcoinAddresses' );

		$this->store = $userBitcoinAddressStore;
	}

	public function execute( $subPage ) {
		parent::execute( $subPage );

		$this->requireLogin();
		$this->checkReadOnly();

		$tryToSubmitNewRecord = true;
		$removeRecordWithId = $this->getRequest()->getInt( 'wpid', -1 );
		if( $removeRecordWithId > -1
			&& $this->getRequest()->getText( 'wpaction') === 'remove'
		) {
			$tryToSubmitNewRecord = false;
			$removedRecord = $this->store->removeById( $removeRecordWithId );
			if( $removedRecord !== null ) {
				$this->renderRemovedRecordReport( $removedRecord );
			}
		}

		$this->renderSubmitForm( $this->getContext(), $tryToSubmitNewRecord );
		
		$this->renderUsersBitcoinAddresses();

		$this->getOutput()->addModules( 'mw.ext.userBitcoinAddresses.special' );
	}

	protected function renderSubmitForm( $context, $trySubmit = true ) {
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
		$form = ( new HTMLForm( $formData, $context ) )
			->setMethod( 'post' )
			->setWrapperLegendMsg( 'userbtcaddr-submitaddresses-manualinsert-legend' )
			->setId( 'userbtcaddr-submitaddresses-manualinsert' )
			->setSubmitCallback( [ $this, 'formSubmitted' ] );
			// Renders form if failure or empty. On-success rendering handled in "formSubmitted".

		if( $trySubmit ) {
			$form->show();
		} else {
			$form->prepareForm()->displayForm( false );
		}
	}

	public function formSubmitted( $data, HTMLForm $form ) {
		$this->storeValidBitcoinAddresses();
		$this->renderAddedRecordsReport();
		$this->renderSubmitFormAsEmpty( $form ); // Display form again but empty data.
		return true;
	}

	protected function storeValidBitcoinAddresses() {
		$this->storedRecords = [];
		$this->existingRecords = [];

		$recordBuilder = ( new UBARBuilder() )
			->user( $this->getContext()->getUser() );

		foreach( $this->validBtcAddresses as $btcAddress ) {
			$recordBuilder->bitcoinAddress( $btcAddress );

			try {
				$this->storedRecords[] = $this->store->add( $recordBuilder->build() );
			} catch( InstanceAlreadyStoredException $e ) {
				$this->existingRecords[] = $e->getAlreadyStoredInstance();
			}
		}
	}

	protected function renderSubmitFormAsEmpty( HtmlForm $form ) {
		$emptyRequest = new DerivativeRequest( $form->getRequest(), [] );
		$emptyRequestContext = clone $form->getContext();;
		$emptyRequestContext->setRequest( $emptyRequest );

		$this->renderSubmitForm( $emptyRequestContext );
	}

	public function renderRemovedRecordReport( UBARecord $record ) {
		$html = $this->msg( 'userbtcaddr-removeaddresse-report', $record->getBitcoinAddress() );

		$this->getOutput()->addHtml( $html );
		$this->getOutput()->addElement( 'hr' );
	}

	public function renderAddedRecordsReport() {
		$storedRecords = $this->storedRecords;
		$existingRecords = $this->existingRecords;
		$storedRecordsLength = count( $storedRecords );
		$existingRecordsLength = count( $existingRecords );
		$html = '';

		if( $storedRecordsLength > 0 ) {
			$html .= $this->msg(
				'userbtcaddr-submitaddresses-manualinsert-submitstatus-added',
				$storedRecordsLength,
				$storedRecords[ 0 ]->getBitcoinAddress()->asString()
			)->parseAsBlock();
		}

		if( $existingRecordsLength === 1 ) {
			$html .= $this->msg(
				'userbtcaddr-submitaddresses-manualinsert-submitstatus-duplicate',
				$existingRecords[ 0 ]->getBitcoinAddress()->asString()
			)->parseAsBlock();
		}
		else if ( $existingRecordsLength > 1 ) {
			$html .= $this->msg(
				$storedRecordsLength > 0
					? 'userbtcaddr-submitaddresses-manualinsert-submitstatus-duplicates'
					: 'userbtcaddr-submitaddresses-manualinsert-submitstatus-duplicatesonly',
				$existingRecordsLength
			)->parseAsBlock();

			$existingAddressesLi = [];
			foreach( $existingRecords as $record ) {
				$existingAddressesLi[ $record->getId() ] =
					Html::openElement( 'li' )
					. $this->msg(
						'userbtcaddr-submitaddresses-duplicateaddressformat',
						$record->getId(),
						$record->getBitcoinAddress()->asString() )->parse()
					. Html::closeElement( 'li' );

			}
			ksort( $existingAddressesLi );
			$html .= Html::rawElement( 'ul', [], implode( '', $existingAddressesLi ) );
		}

		$this->getOutput()->addHtml( $html );
		$this->getOutput()->addElement( 'hr' );
	}
	
	protected function renderUsersBitcoinAddresses() {
		$usersUBARecords = $this->store->fetchAllForUser( $this->getUser() );

		if( !count( $usersUBARecords ) ) {
			$html = $this->msg( 'userbtcaddr-noaddressesyet' )->text();
		} else {
			$tableFormatter = $this->getUBARTableFormatter();
			$html = $tableFormatter->format( $usersUBARecords );
		}

		$this->getOutput()->addHtml( $html );
	}

	protected function getUBARTableFormatter() {
		$tableFormatter = new UBARecordsHtmlTable();
		$rowOptions = $tableFormatter->options()->rowFormatter()->options();
		$rowOptions
			->bitcoinAddressFormatter( new BitcoinAddressMonoSpaceHtml() )
			->timeAndDateFormatter( new MWUserDateTimeHtml( $this->getUser() ) )
			->virtualFields()
				->set( 'removeLink', function( UBARecord $record ) {
					$recordId = $record->getId();
					return ( new HTMLForm( [
						'id' => [
							'type' => 'hidden',
							'default' => $recordId
						],
						'action' => [
							'type' => 'hidden',
							'default' => 'remove'
						]
					], $this->getContext() ) )
						->setMethod( 'post' )
						->setSubmitTextMsg( 'userbtcaddr-removeaddresse-button-label' )
						->setSubmitName( "remove$recordId" )
						->prepareForm()
						->getHTML( false );

				} );
		$rowOptions->printAllFieldsWithout( 'user' );

		return $tableFormatter;
	}

	public function validateAddressInput( $value, $allData, $form ) {
		$allAddresses = $allData['addresses'] . ' ' . $allData['addressesToBeCorrected'];
		$addressStrings = array_filter(
			array_unique( preg_split( "/[^A-Za-z0-9]+/", $allAddresses ) ),
			function( $val ) { return $val !== ''; }
		);
		$this->validBtcAddresses = [];
		$invalidAddressStrings = [];
		$validAddressStrings = [];

		foreach( $addressStrings as $addressString ) {
			try {
				$address = new BtcAddress( $addressString );

				$this->validBtcAddresses[] = $address;
				$validAddressStrings[] = $address->asString();
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
			)->text();
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

	/**
	 * Returns the store used by the special page to access and save the user's bitcoin addresses.
	 *
	 * @return UBARStore
	 */
	public function getUserBitcoinAddressStore() {
		return $this->store;
	}
}
