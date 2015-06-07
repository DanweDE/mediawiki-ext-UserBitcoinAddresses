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
class Mocker {

	/**
	 * @var PHPUnit_Framework_TestCase
	 */
	protected $testCase;

	public function __construct( PHPUnit_Framework_TestCase $testCase ) {
		$this->testCase = $testCase;
	}

	/**
	 * Creates a MediaWiki User object with mocked methods to behave like a non-anonymous user
	 * with all rights without actually adding it to the database.
	 *
	 * @return User
	 */
	public function newAuthorizedUser() {
		$_ = $this->testCase;

		$user = $_
			->getMockBuilder( 'User' )
			->setMethods( [ 'isAnon', 'isAllowed' ] )
			->setConstructorArgs( [ 'SpecialUserBitcoinAddressesTestUser' ] )
			->getMock();
		$user->expects( $_->any() )
			->method( 'isAnon' )
			->will( $_->returnValue( false ) );
		$user->expects( $_->any() )
			->method( 'isAllowed' )
			->will( $_->returnValue( true ) );

		return $user;
	}

    /**
     * Returns a builder object to create mock objects using a fluent interface.
     *
     * @param  string $className
     * @return PHPUnit_Framework_MockObject_MockBuilder
     */
    protected final function getMockBuilder( $className )
    {
        return new PHPUnit_Framework_MockObject_MockBuilder( $this->testCase, $className );
    }
}