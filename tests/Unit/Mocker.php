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

	protected static $continuousUserId = 1;

	/**
	 * Creates a MediaWiki User object with mocked methods to behave like a non-anonymous user.
	 *
	 * @return User
	 */
	public function newUser( $name = null ) {
		$user = $this->newUserMock( $this->getDecorateUserMockMethods() );

		$this->decorateUserMock( $user, $name );

		return $user;
	}

	/**
	 * Creates a MediaWiki User object with mocked methods to behave like a non-anonymous user
	 * with all rights without actually adding it to the database.
	 *
	 * @return User
	 */
	public function newAuthorizedUser( $name = null ) {
		$user = $this->newUserMock( $this->getDecorateUserMockMethods() + [ 'isAllowed' ] );

		$this->decorateUserMock( $user, $name );

		$user->expects( $this->any() )
			->method( 'isAllowed' )
			->will( $this->returnValue( true ) );

		return $user;
	}

	public function getNextContinuousUserId() {
		return static::$continuousUserId;
	}

	protected function newUserMock( $methods = [] ) {
		return $this
			->getMockBuilder( 'User' )
			->setMethods( $methods )
			->getMock();
	}

	protected function decorateUserMock( User $user, $name ) {
		$id = static::$continuousUserId++;

		$user->expects( $this->any() )
			->method( 'isAnon' )
			->will( $this->returnValue( false ) );
		$user->expects( $this->any() )
			->method( 'getId' )
			->will( $this->returnValue( $id ) );
		$user->expects( $this->any() )
			->method( 'getName' )
			->will( $this
				->returnValue(
					is_string( $name ) ? $name : 'Mocked User ' . $id ) );
	}

	protected function getDecorateUserMockMethods() {
		return [ 'isAnon', 'getId', 'getName' ];
	}
}