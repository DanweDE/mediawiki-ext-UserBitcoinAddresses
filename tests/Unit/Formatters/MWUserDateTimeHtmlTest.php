<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\Formatters;

use User;
use DateTime;
use MediaWiki\Ext\UserBitcoinAddresses\Formatters\MWUserDateTimeHtml;
use MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserMocker;

/**
 * @group UserBitcoinAddresses
 * @covers MediaWiki\Ext\UserBitcoinAddresses\Formatters\MWUserDateTimeHtml
 *
 * @since 1.0.0
 *
 * @licence MIT License
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
		$formatter = new MWUserDateTimeHtml( $user );

		$this->assertEquals( $user, $formatter->user(),
			'getter returns user given in constructor');

		$newUser = ( new UserMocker() )->newUser();

		$this->assertEquals( $formatter, $formatter->user( $newUser ),
			'setter returns self-reference' );

		$this->assertEquals( $newUser, $formatter->user(),
			'getter returns new user set via setter' );
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
