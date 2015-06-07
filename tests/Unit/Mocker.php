<?php

namespace MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit;

use User;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockBuilder;

/**
 * Mock creator helper for this package's tests.
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class Mocker extends PHPUnit_Framework_TestCase {
	/**
	 * Creates a MediaWiki User object with mocked methods to behave like a non-anonymous user
	 * with all rights without actually adding it to the database.
	 *
	 * @return User
	 */
	public function newAuthorizedUser() {
		$user = $this
			->getMockBuilder( 'User' )
			->setMethods( [ 'isAnon', 'isAllowed' ] )
			->setConstructorArgs( [ 'SpecialUserBitcoinAddressesTestUser' ] )
			->getMock();
		$user->expects( $this->any() )
			->method( 'isAnon' )
			->will( $this->returnValue( false ) );
		$user->expects( $this->any() )
			->method( 'isAllowed' )
			->will( $this->returnValue( true ) );
		return $user;
	}
}