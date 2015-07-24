<?php

namespace MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\Specials;

use FauxRequest;
use MediaWiki\Ext\UserBitcoinAddresses\Specials\SpecialUserBitcoinAddresses;
use MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserMocker;
use MediaWiki\Ext\UserBitcoinAddresses\Store\UserBitcoinAddressRecordMwDbStore as UBARMwDbStore;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecordBuilder as UBARBuilder;
use MediaWiki\Ext\UserBitcoinAddresses\MwBridge\LazyDBConnectionProvider;
use MediaWiki\Ext\UserBitcoinAddresses\MwBridge\StandardMwUserFactory;

/**
 * @group UserBitcoinAddresses
 * @group SpecialPage
 * @group UserBitcoinAddressesSpecialPage
 * @group Database
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class SpecialUserBitcoinAddressesTest extends SpecialPageTestBase {

	protected static $btcAddresses = [
		'1Ax4gZtb7gAit2TivwejZHYtNNLT18PUXJ',
		'1AGNa15ZQXAZUgFiqJ2i7Z2DPU2J6hW62i',
		'1C5bSj1iEGUgSTbziymG7Cn18ENQuT36vv',
		'1Gqk4Tv79P91Cc1STQtU3s1W6277M2CVWu',
	];

	protected function newSpecialPage() {
		return new SpecialUserBitcoinAddresses(
			new UBARMwDbStore(
				new LazyDBConnectionProvider( DB_SLAVE ),
				new LazyDBConnectionProvider( DB_MASTER ),
				new StandardMwUserFactory()
			)
		);
	}

	protected function newSpecialPageCompatibleWithMockUsers( $mocker ) {
		return new SpecialUserBitcoinAddresses(
			new UBARMwDbStore(
				new LazyDBConnectionProvider( DB_SLAVE ),
				new LazyDBConnectionProvider( DB_MASTER ),
				$mocker
			)
		);
	}

	public function testNoAddressesNoTable() {
		$mocker = new UserMocker();
		$user = $mocker->newAuthorizedUser();

		list( $html ) = $this->executeSpecialPage( '', null, 'qqx', $user );

		$this->assertRegExp(
			'/\(userbtcaddr-noaddressesyet\)/', $html );

		$this->assertNotRegExp(
			'/<table\s+[^>]*class="mwuba-recordstable"/', $html );
	}

	public function testAddressesInTable() {
		$mocker = new UserMocker();
		$specialPage = $this->newSpecialPageCompatibleWithMockUsers( $mocker );
		$store = $specialPage->getUserBitcoinAddressStore();
		$user = $mocker->newAuthorizedUser();
		$address = ( new UBARBuilder() )
			->user( $user )
			->bitcoinAddress( self::$btcAddresses[ 1 ] )
			->build();
		$store->add( $address );

		list( $html ) = $this->executeSpecialPage( '', null, 'qqx', $user );

		$this->assertNotRegExp(
			'/\(userbtcaddr-noaddressesyet\)/', $html );

		$this->assertTag( array(
			'tag' => 'table',
			'attributes' => array(
				'class' => 'mwuba-recordstable',
			),
			'descendant' => array(
				'tag' => 'tr',
				'descendant' => array(
					'tag' => 'code',
					'content' => $address->getBitcoinAddress()->asString(),
				)
			)
		), $html );
	}

	/**
	 * @dataProvider formInputAndExpectedOutputProvider
	 */
	public function testValidAndInvalidAddressesInserted( $input, $result ) {
		$mocker = new UserMocker();
		$user = $mocker->newAuthorizedUser();

		$request = new FauxRequest( array(
			'wpaddresses' => $input[ 'addresses' ],
			'wpaddressesToBeCorrected' => $input[ 'addressesToBeCorrected' ],
			'wpEditToken' => $user->getEditToken(),
		), true );

		list( $html ) = $this->executeSpecialPage( '', $request, 'qqx', $user );

		$this->assertTag( array(
			'tag' => 'textarea',
			'attributes' => array(
				'id' => 'mw-input-wpaddresses',
				'name' => 'wpaddresses',
			),
			'content' => $result[ 'addresses' ],
		), $html, '\"addresses\" form field content is matching expectations' );

		$this->assertTag( array(
			'tag' => 'textarea',
			'attributes' => array(
				'id' => 'mw-input-wpaddressesToBeCorrected',
				'name' => 'wpaddressesToBeCorrected',
			),
			'content' => $result[ 'addressesToBeCorrected' ],
		), $html, '\"addresses to be corrected\" form field content is matching expectations' );
	}

	public static function formInputAndExpectedOutputProvider() {
		list( $addr1, $addr2, $addr3, $addr4 ) = self::$btcAddresses;
		return [
			'valid addresses in first field and empty, to be ignored values' => [
				[
					'addresses'              => "$addr1 ,_,,,  $addr2",
					'addressesToBeCorrected' => "",
				], [
					'addresses'              => "",
					'addressesToBeCorrected' => "",
				]
			],
			'valid and invalid values in first addresses field' => [
				[
					'addresses'              => "$addr1 bar $addr2 foo",
					'addressesToBeCorrected' => "",
				], [
					'addresses'              => self::glue( $addr1, $addr2 ),
					'addressesToBeCorrected' => self::glue( "bar", "foo" ),
				]
			],
			'different separators in first field and duplicate addresses' => [
				[
					'addresses'              => "bar, $addr1\n$addr2, $addr1 bar",
					'addressesToBeCorrected' => "",
				], [
					'addresses'              => self::glue( $addr1, $addr2 ),
					'addressesToBeCorrected' => "bar",
				]
			],
			'invalid address in first field, valid addresses in second one' => [
				[
					'addresses'              => "foo",
					'addressesToBeCorrected' => "$addr1, $addr2 $addr3\n$addr4",
				], [
					'addresses'              => self::glue( $addr1, $addr2, $addr3, $addr4 ),
					'addressesToBeCorrected' => "foo",
				]
			],
			'valid and invalid values in both address fields' => [
				[
					'addresses'              => "$addr1, $addr2 $addr3 foo bar",
					'addressesToBeCorrected' => "$addr1, $addr2 $addr3 foo bar",
				], [
					'addresses'              => self::glue( $addr1, $addr2, $addr3 ),
					'addressesToBeCorrected' => self::glue( "foo", "bar" ),
				]
			]
		];
	}

	/**
	 * @dataProvider manualInsertionSessions
	 */
	public function testSubmitReportAfterManualInsertion(
		array $existingAddresses,
		array $insertAddresses,
		array $expected
	) {
		$mocker = new UserMocker();
		$specialPage = $this->newSpecialPageCompatibleWithMockUsers( $mocker );
		$store = $specialPage->getUserBitcoinAddressStore();
		$user = $mocker->newAuthorizedUser();
		$builder = ( new UBARBuilder() )->user( $user );

		foreach( $existingAddresses as $address ) {
			$store->add(
				$builder->bitcoinAddress( $address )->build()
			);
		};

		$request = new FauxRequest( array(
			'wpaddresses' => implode( ' ', $insertAddresses ),
		), true );

		list( $html ) = $this->executeSpecialPage( '', $request, 'qqx', $user, $specialPage );

		foreach( $expected as $regex ) {
			$this->assertRegExp( $regex, $html );
		}
	}

	public static function manualInsertionSessions() {
		list( $addr1, $addr2, $addr3, $addr4 ) = self::$btcAddresses;
		return [
			'insert one new address' => [
				[],
				[ $addr1 ],
				[
					"/\\(userbtcaddr-submitaddresses-manualinsert-submitstatus-added: 1, $addr1\\)/",
				]
			],
			'insert multiple new addresses' => [
				[],
				[ $addr1, $addr2, $addr3 ],
				[
					'/\(userbtcaddr-submitaddresses-manualinsert-submitstatus-added: 3/',
				]
			],
			'one address inserted but exists already' => [
				[ $addr1 ],
				[ $addr1 ],
				[
					"/\\(userbtcaddr-submitaddresses-manualinsert-submitstatus-duplicate: $addr1/",
				]
			],
			'multiple addresses inserted but all exist already' => [
				[ $addr1, $addr2 ],
				[ $addr1, $addr2 ],
				[
					'/\(userbtcaddr-submitaddresses-manualinsert-submitstatus-duplicatesonly: 2\)/',
					"/\\(userbtcaddr-submitaddresses-duplicateaddressformat: \\d+, $addr1/",
					"/\\(userbtcaddr-submitaddresses-duplicateaddressformat: \\d+, $addr1/",
				]
			],
			'one address inserted, another exists already' => [
				[ $addr1 ],
				[ $addr1, $addr2 ],
				[
					"/\\(userbtcaddr-submitaddresses-manualinsert-submitstatus-added: 1, $addr2\\)/" ,
					"/\\(userbtcaddr-submitaddresses-manualinsert-submitstatus-duplicate: $addr1/",
				]
			],
			'Multiple addresses inserted, another exists already' => [
				[ $addr1 ],
				[ $addr1, $addr2, $addr3 ],
				[
					'/\(userbtcaddr-submitaddresses-manualinsert-submitstatus-added: 2/' ,
					"/\\(userbtcaddr-submitaddresses-manualinsert-submitstatus-duplicate: $addr1/",
				]
			],
			'Multiple addresses inserted, multiple exist already' => [
				[ $addr1, $addr2 ],
				[ $addr1, $addr2, $addr3, $addr4 ],
				[
					'/\(userbtcaddr-submitaddresses-manualinsert-submitstatus-added: 2/' ,
					'/\(userbtcaddr-submitaddresses-manualinsert-submitstatus-duplicates: 2/',
					"/\\(userbtcaddr-submitaddresses-duplicateaddressformat: \\d+, $addr1/",
					"/\\(userbtcaddr-submitaddresses-duplicateaddressformat: \\d+, $addr2/",
				]
			],
		];
	}

	public function testAddressRemoved() {
		$mocker = new UserMocker();
		$specialPage = $this->newSpecialPageCompatibleWithMockUsers( $mocker );
		$store = $specialPage->getUserBitcoinAddressStore();
		$user = $mocker->newAuthorizedUser();
		$address = ( new UBARBuilder() )
			->user( $user )
			->bitcoinAddress( self::$btcAddresses[ 0 ] )
			->build();
		$storedAddress = $store->add( $address );

		$request = new FauxRequest( array(
			'wpaction' => 'remove',
			'wpid' => strval( $storedAddress->getId() ),
			'wpEditToken' => $user->getEditToken(),
		), true );

		list( $html ) = $this->executeSpecialPage( '', $request, 'qqx', $user );

		$this->assertRegExp(
			"/\\(userbtcaddr-removeaddresse-report: {$address->getBitcoinAddress()}\\)/", $html );
	}

	/**
	 * @return string
	 */
	protected static function glue() {
		return implode( "\n", func_get_args() );
	}
}
