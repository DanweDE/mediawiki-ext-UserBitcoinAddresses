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
class MockerTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider newAuthorizedUsersProvider
	 */
	public function testNewAuthorizedUser( User $user, $expected ) {
		$this->assertInstanceOf( 'User', $user );

		$this->assertFalse( $user->isAnon(),
			'mocked user instance is not an anonymous user' );

		$this->assertTrue( $user->isAllowed( 'edit' ),
			'mocked user has edit rights' );

		$this->assertEquals( $user->getId(),   $expected[ 'id' ] );
		$this->assertEquals( $user->getName(), $expected[ 'name' ] );
	}

	public function testGetNextContinuousUserId() {
		$mocker1 = new Mocker();
		$mocker2 = new Mocker();

		$this->assertEquals(
			$mocker1->getNextContinuousUserId(),
			$mocker2->getNextContinuousUserId(),
			'two Mocker instances return same value for getNextContinuousUserId()'
		);

		$mocker1->newAuthorizedUser();

		$this->assertEquals(
			$mocker1->getNextContinuousUserId(),
			$mocker2->getNextContinuousUserId(),
			'two Mocker instances still return same value for getNextContinuousUserId() after one'
				. ' of them instantiated new user mock while the other one did not'
		);
	}

	public static function newAuthorizedUsersProvider() {
		$mocker1 = new Mocker();
		$mocker2 = new Mocker();
		$id = $mocker1->getNextContinuousUserId() - 1; // In case another test using the Mocker runs first!

		return [
			'without custom name' => [
				$mocker1->newAuthorizedUser(), [
					'id' => ++$id,
					'name' => "Mocked User $id",
				]
			],
			'with custom name' => [
				$mocker1->newAuthorizedUser( 'Jimmy' ), [
					'id' => ++$id,
					'name' => "Jimmy",
				]
			],
			'without custom name again' => [
				$mocker1->newAuthorizedUser(), [
					'id' => ++$id,
					'name' => "Mocked User $id",
				]
			],
			'without custom name but another Mocker instance' => [
				$mocker2->newAuthorizedUser(), [
					'id' => ++$id,
					'name' => "Mocked User $id",
				]
			],
		];
	}
}

