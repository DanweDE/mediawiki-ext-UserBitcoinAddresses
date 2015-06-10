<?php

namespace MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit;

use User;

/**
 * @group UserBitcoinAddresses
 * @covers MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\Mocker
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class UserMockerTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider newAuthorizedUsersProvider
	 */
	public function testNewUser( User $user, $expected, UserMocker $mocker ) {
		$this->assertInstanceOf( 'User', $user );

		$this->assertFalse( $user->isAnon(),
			'mocked user instance is not an anonymous user' );

		$this->assertEquals( $user->getId(),   $expected[ 'id' ] );
		$this->assertEquals( $user->getName(), $expected[ 'name' ] );
	}

	/**
	 * @dataProvider newAuthorizedUsersProvider
	 */
	public function testNewAuthorizedUser( User $user, $expected, UserMocker $mocker ) {
		$this->testNewUser( $user, $expected, $mocker );

		$this->assertTrue( $user->isAllowed( 'edit' ),
			'mocked user has edit rights' );
	}

	public function testGetNextContinuousUserId() {
		$mocker1 = new UserMocker();
		$mocker2 = new UserMocker();

		$this->assertEquals(
			$mocker1->getNextContinuousUserId(),
			$mocker2->getNextContinuousUserId(),
			'two UserMocker instances return same value for getNextContinuousUserId()'
		);

		$mocker1->newAuthorizedUser();

		$this->assertEquals(
			$mocker1->getNextContinuousUserId(),
			$mocker2->getNextContinuousUserId(),
			'two UserMocker instances still return same value for getNextContinuousUserId() after one'
				. ' of them instantiated new user mock while the other one did not'
		);
	}

	/**
	 * @dataProvider newAuthorizedUsersProvider
	 */
	public function testNewFromId( User $user, $expected, UserMocker $mocker ) {
		$userById = $mocker->newFromId( $expected[ 'id' ] );
		$this->assertEquals( $user, $userById );

		$this->setExpectedException( 'BadFunctionCallException' );
		( new UserMocker() )->newFromId( $expected[ 'id' ] );
	}

	public static function newAuthorizedUsersProvider() {
		$mocker1 = new UserMocker();
		$mocker2 = new UserMocker();
		$id = $mocker1->getNextContinuousUserId() - 1; // In case another test using the UserMocker runs first!

		return [
			'without custom name' => [
				$mocker1->newAuthorizedUser(), [
					'id' => ++$id,
					'name' => "Mocked User $id",
				], $mocker1
			],
			'with custom name' => [
				$mocker1->newAuthorizedUser( 'Jimmy' ), [
					'id' => ++$id,
					'name' => "Jimmy",
				], $mocker1
			],
			'without custom name again' => [
				$mocker1->newAuthorizedUser(), [
					'id' => ++$id,
					'name' => "Mocked User $id",
				], $mocker1
			],
			'without custom name but another UserMocker instance' => [
				$mocker2->newAuthorizedUser(), [
					'id' => ++$id,
					'name' => "Mocked User $id",
				], $mocker2
			],
		];
	}
}

