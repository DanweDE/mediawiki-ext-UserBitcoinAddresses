<?php

namespace MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\Specials;

use MediaWikiTestCase;
use FauxRequest;
use MediaWiki\Ext\UserBitcoinAddresses\Specials\SpecialUserBitcoinAddresses;

/**
 * @group UserBitcoinAddresses
 * @group SpecialPage
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class SpecialUserBitcoinAddressesTest extends SpecialPageTestBase {

	protected function newSpecialPage() {
		$page = $this
			->getMockBuilder( 'MediaWiki\Ext\UserBitcoinAddresses\Specials\SpecialUserBitcoinAddresses' )
			->disableOriginalClone()
			->setMethods( [ 'userCanExecute' ] )
			->getMock();
		$page->expects( $this->any() )
			->method( 'userCanExecute' )
			->will( $this->returnValue( true ) );
		return $page;
	}

	/**
	 * @dataProvider formInputAndExpectedOutputProvider
	 */
	public function testValidAndInvalidAddressesInserted( $input, $result ) {
		$user = $this
			->getMockBuilder( 'User' )
			->setMethods( [ 'isAnon' ])
			->setConstructorArgs( [ 'SpecialUserBitcoinAddressesTestUser' ] )
			->getMock();
		$user->expects( $this->any() )
			->method( 'isAnon' )
			->will( $this->returnValue( false ) );

		$request = new FauxRequest( array(
			'wpaddresses' => $input[ 'addresses' ],
			'wpaddressesToBeCorrected' => $input[ 'addressesToBeCorrected' ],
			'title' => $this->newSpecialPage()->getTitle()->getPrefixedText(),
			'wpEditToken' => $user->getEditToken(),
		), true );

		list( $output, ) = $this->executeSpecialPage( '', $request, 'qqx', $user );

		$this->assertTag( array(
			'tag' => 'textarea',
			'attributes' => array(
				'id' => 'mw-input-wpaddresses',
				'name' => 'wpaddresses',
			),
			'content' => $result[ 'addresses' ],
		), $output, '\"addresses\" form field content is matching expectations' );

		$this->assertTag( array(
			'tag' => 'textarea',
			'attributes' => array(
				'id' => 'mw-input-wpaddressesToBeCorrected',
				'name' => 'wpaddressesToBeCorrected',
			),
			'content' => $result[ 'addressesToBeCorrected' ],
		), $output, '\"addresses to be corrected\" form field content is matching expectations' );
	}

	public static function formInputAndExpectedOutputProvider() {
		function glue() {
			return implode( "\n", func_get_args() );
		}
		$addr1 = '1Ax4gZtb7gAit2TivwejZHYtNNLT18PUXJ';
		$addr2 = '1AGNa15ZQXAZUgFiqJ2i7Z2DPU2J6hW62i';
		$addr3 = '1C5bSj1iEGUgSTbziymG7Cn18ENQuT36vv';
		$addr4 = '1Gqk4Tv79P91Cc1STQtU3s1W6277M2CVWu';

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
					'addresses'              => glue( $addr1, $addr2 ),
					'addressesToBeCorrected' => glue( "bar", "foo" ),
				]
			],
			'different separators in first field and duplicate addresses' => [
				[
					'addresses'              => "bar, $addr1\n$addr2, $addr1 bar",
					'addressesToBeCorrected' => "",
				], [
					'addresses'              => glue( $addr1, $addr2 ),
					'addressesToBeCorrected' => "bar",
				]
			],
			'invalid address in first field, valid addresses in second one' => [
				[
					'addresses'              => "foo",
					'addressesToBeCorrected' => "$addr1, $addr2 $addr3\n$addr4",
				], [
					'addresses'              => glue( $addr1, $addr2, $addr3, $addr4 ),
					'addressesToBeCorrected' => "foo",
				]
			],
			'valid and invalid values in both address fields' => [
				[
					'addresses'              => "$addr1, $addr2 $addr3 foo bar",
					'addressesToBeCorrected' => "$addr1, $addr2 $addr3 foo bar",
				], [
					'addresses'              => glue( $addr1, $addr2, $addr3 ),
					'addressesToBeCorrected' => glue( "foo", "bar" ),
				]
			]
		];
	}

}
