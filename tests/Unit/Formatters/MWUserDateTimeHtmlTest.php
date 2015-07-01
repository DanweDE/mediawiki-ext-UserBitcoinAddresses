<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\Formatters;

use User;
use DateTime;
use MediaWiki\Ext\UserBitcoinAddresses\Formatters\MWUserDateTimeHtml;
use MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserMocker;
use MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\SetterAndGetterTester;

/**
 * @group UserBitcoinAddresses
 * @covers MediaWiki\Ext\UserBitcoinAddresses\Formatters\MWUserDateTimeHtml
 *
 * @since 1.0.0
 *

 * @author Daniel A. R. Werner
 */
class MWUserDateTimeHtmlTest extends \PHPUnit_Framework_TestCase {

		/**
		 * @dataProvider userProvider
		 */
		public function testConstruction( User $user ) {
			$this->assertInstanceOf(
				'MediaWiki\Ext\UserBitcoinAddresses\Formatters\MWUserDateTimeHtml',
				new MWUserDateTimeHtml( $user )
			);
		}

	/**
	 * @dataProvider userProvider
	 */
	public function testUserGetterAndSetter( User $user ) {
		( new SetterAndGetterTester( $this ) )
			->getAndSet( 'user' )->on( new MWUserDateTimeHtml( $user ) )
			->initially( $user )
			->test( ( new UserMocker() )->newUser() );
	}

	/**
	 * @dataProvider userProvider
	 */
	public function testFormat( User $user ) {
		$formatter = new MWUserDateTimeHtml( $user );
		$this->assertInternalType( 'string', $formatter->format( new DateTime() ) );
	}

	/**
	 * @return array( array( User ), ... )
	 */
	public static function userProvider() {
		$mocker = new UserMocker();
		return array_chunk( [
			$mocker->newUser( 'Some User' ),
			$mocker->newUser( 'Another User' ),
		], 1 );
	}
}
