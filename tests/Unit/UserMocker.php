<?php

namespace MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit;

use User;
use PHPUnit_Framework_TestCase;
use MediaWiki\Ext\UserBitcoinAddresses\MwBridge\MwUserFactory;

/**
 * User mock creator helper for this package's tests.
 *
 * In tests for code depending on UserBitcoinAddressRecordStore, a instance of this class should be
 * injected as a MwUserFactory in the used UserBitcoinAddressRecordStore instance and using mocked
 * User instances for UserBitcoinAddressRecord instances stored in that store won't be a problem
 * when fetching records again from the store.
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class UserMocker extends PHPUnit_Framework_TestCase implements MwUserFactory {

	protected static $continuousUserId = 1;

	protected $mockedUsers = [];

	/**
	 * Returns a User object previously created by another mocker method based on its ID. If no
	 * object with the given ID exists, then a new simple user will be mocked with this id (not
	 * implemented yet).
	 *
	 * @throws BadFunctionCallException If given ID has not been used for mocked User previously.
	 *
	 * @param int $id
	 * @return User
	 */
	public function newFromId( $id ) {
		if( ! is_int( $id ) ) {
			throw new \InvalidArgumentException( '$id needs to be an integer' );
		}

		if( ! array_key_exists( $id, $this->mockedUsers ) ) {
			throw new \BadFunctionCallException(
				'No user with the given ID has previously been mocked. Handling this is not yet implemented.' );
		}
		return $this->mockedUsers[ $id ];
	}

	/**
	 * Creates a MediaWiki User object with mocked methods to behave like a non-anonymous user.
	 *
	 * @return User
	 */
	public function newUser( $name = null ) {
		$user = $this->newUserMock( $this->getDecorateUserMockMethods() );

		$this->decorateUserMock( $user, $name );

		return $this->mockedUsers[ $user->getId() ] = $user;
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

		return $this->mockedUsers[ $user->getId() ] = $user;
	}

	public function getNextContinuousUserId() {
		return static::$continuousUserId;
	}

	/**
	 * @param array $methods
	 * @return User
	 */
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