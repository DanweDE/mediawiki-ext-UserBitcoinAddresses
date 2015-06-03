<?php

namespace MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit;

use User;
use Danwe\Bitcoin\Address;
use Datetime;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecordBuilder;
use MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordTest;

/**
 * @group UserBitcoinAddresses
 * @covers MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecordBuilder
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class UserBitcoinAddressRecordBuilderTest extends \PHPUnit_Framework_TestCase {

	public static $builderSetters = [
		'id',
		'bitcoinAddress',
		'user',
		'addedOn',
		'exposedOn',
		'addedThrough',
		'purpose'
	];

	public function testConstruction() {
		$this->assertInstanceOf(
			'MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecordBuilder',
			new UserBitcoinAddressRecordBuilder()
		);
	}

	/**
	 * @dataProvider MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordTestData::buildStepsWithValidValuesProvider
	 */
	public function testSetterGetterPairsWithValidValues( $buildStepSetter, $value ) {
		$this->assertInternalType( 'string', $buildStepSetter );
		$builder = new UserBitcoinAddressRecordBuilder();
		$this->assertBuildStepGetterReturnsNullInitially( $builder, $buildStepSetter );
		$this->assertBuildStepSetterAndGetterWorking( $builder, $buildStepSetter, $value );
	}

	/**
	 * @dataProvider MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordTestData::buildStepsWithInvalidValuesProvider
	 */
	public function testSetterGetterPairsWithInvalidValues( $buildStepSetter, $invalidValue ) {
		$this->assertInternalType( 'string', $buildStepSetter );
		$builder = new UserBitcoinAddressRecordBuilder();
		$this->assertBuildStepGetterReturnsNullInitially( $builder, $buildStepSetter );

		$this->setExpectedException( 'InvalidArgumentException' );
		$builder->{ $buildStepSetter }( $invalidValue );
	}

	/**
	 * Tests whether setting a value, then setting another value for the same build step is working
	 * properly.
	 *
	 * @dataProvider MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordTestData::validValuesPerBuildStepProvider()
	 * @depends testSetterGetterPairsWithValidValues
	 */
	public function testSettingRepeatedly( $buildStepSetter, $validValues ) {
		$validValues[] = $validValues[ 0 ];

		foreach( $validValues as $i => $value ) {
			$this->assertBuildStepSetterAndGetterWorking(
				new UserBitcoinAddressRecordBuilder(), $buildStepSetter, $value
			);
		}
	}

	/**
	 * @dataProvider MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordTestData::validBuilderStepsProvider
	 * @depends testSetterGetterPairsWithValidValues
	 */
	public function testSettingUpBuilderWithValidValues( $buildSteps ) {
		$this->assertBuildStepsWorking( new UserBitcoinAddressRecordBuilder(), $buildSteps );
	}

	/**
	 * @dataProvider MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordTestData::invalidBuilderStepsProvider
	 * @depends testSetterGetterPairsWithValidValues
	 */
	public function testSettingUpBuilderWithInvalidValues( $buildSteps ) {
		$this->assertBuildStepsWorking( new UserBitcoinAddressRecordBuilder(), $buildSteps );
		$this->assertTrue( true, 'All steps set up' ); // Necessary if 0 steps.
	}

	/**
	 * All setters should accept null without failing.
	 */
	public function testSettingNull() {
		$builder = new UserBitcoinAddressRecordBuilder();
		foreach( static::$builderSetters as $setter ) {
			$this->assertBuildStepSetterAndGetterWorking( $builder, $setter, null );
		}
	}

	/**
	 * @dataProvider MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordTestData::validBuildStateBuildersProvider
	 * @depends testSettingUpBuilderWithValidValues
	 */
	public function testBuildFromValidBuildSteps( $builder, $builderBuildSteps ) {
		$this->assertInstanceOf(
			'MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord',
			$builder->build()
		);
	}

	/**
	 * @dataProvider MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordTestData::invalidBuildStateBuildersProvider
	 * @depends testSettingUpBuilderWithInvalidValues
	 */
	public function testBuildFromInvalidBuildSteps( $builder, $builderBuildSteps, $buildException ) {
		$this->setExpectedException( $buildException );
		$builder->build(); // Should throw ERROR!
	}

	/**
	 * @dataProvider MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordTestData::validBuildStateBuildersProvider
	 * @depends testSettingUpBuilderWithInvalidValues
	 */
	public function testBuildMultipleTimesCreatesSeparateInstances( $builder, $builderBuildSteps, $buildException ) {
		$build1 = $builder->build();
		$build2 = $builder->build();
		$this->assertTrue( $build1 !== $build2 );
	}

	/**
	 * @dataProvider extendableObjectProvider
	 */
	public function testExtendReturnsNewInstance( $extendableObject ) {
		$extendedBuilder = UserBitcoinAddressRecordBuilder::extend( $extendableObject );

		$this->assertInstanceOf( 'MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecordBuilder', $extendedBuilder );
		$this->assertTrue( $extendableObject !== $extendedBuilder, 'extended builder is not original object' );

		$this->assertEquals( $extendableObject->getAddedOn(), $extendedBuilder->getAddedOn() );
		$extendedBuilder->addedOn( new DateTime() );
		$this->assertNotEquals( $extendableObject->getAddedOn(), $extendedBuilder->getAddedOn() );
		//$extendableObject->getAddedOn() !== $extendedBuilder

	}

	/**
	 * @dataProvider MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordTestData::validBuildStateBuildersProvider
	 * @depends testSettingUpBuilderWithValidValues
	 */
	public function testExtendWithBuilder( $builder, $builderBuildSteps ) {
		$extendedBuilder = UserBitcoinAddressRecordBuilder::extend( $builder );
		$this->assertBuilderEquals( $builder, $extendedBuilder );
	}

	/**
	 * @dataProvider MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit\UserBitcoinAddressRecordTestData::validBuildStateBuildersProvider
	 * @depends testSettingUpBuilderWithValidValues
	 */
	public function testExtendWithBuiltInstance( $builder, $builderBuildSteps ) {
		$builtInstance = $builder->build();
		$extendedBuilder = UserBitcoinAddressRecordBuilder::extend( $builtInstance );
		$this->assertBuilderEquals( $builder, $extendedBuilder );
	}

	/**
	 * Returns the builder's getter member name based on a given build step name.
	 *
	 * @param string $buildStep
	 * @return string
	 */
	public static function getSettersGetter( $buildStep ) {
		return 'get' . ucfirst( $buildStep );
	}

	public function assertBuilderEquals(
		UserBitcoinAddressRecordBuilder $builder1,
		UserBitcoinAddressRecordBuilder $builder2
	) {
		$s1 = serialize( $builder1 );
		$s2 = serialize( $builder2 );
		$this->assertEquals( $s1, $s2, 'builders in serialized form are equal' );
	}

	/**
	 * Asserts that a build step's getter is returning null initially.
	 *
	 * @param UserBitcoinAddressRecordBuilder $builder
	 * @param string $buildStep
	 */
	public function assertBuildStepGetterReturnsNullInitially( $builder, $buildStep ) {
		$buildStepGetter = $this->getSettersGetter( $buildStep );
		$this->assertEquals(
			$builder->{ $buildStepGetter }(),
			null,
			'UserBitcoinAddressBuilder::' . $buildStepGetter . '() returns null initially.'
		);
	}

	/**
	 * Asserts that as build step's getter is returning what has been set via the setter. Is
	 * executing the setting/getting steps in the process.
	 *
	 * @param UserBitcoinAddressRecordBuilder $builder
	 * @param string $buildStepSetter
	 * @param mixed $value
	 */
	public function assertBuildStepSetterAndGetterWorking( $builder, $buildStepSetter, $value ) {
		$buildStepGetter = $this->getSettersGetter( $buildStepSetter );
		$this->assertEquals(
			$builder->{ $buildStepSetter }( $value ),
			$builder,
			'UserBitcoinAddressBuilder::' . $buildStepSetter . '() returns self reference.'
		);
		$this->assertEquals(
			$builder->{ $buildStepGetter }(),
			$value,
			'UserBitcoinAddressBuilder::' . $buildStepGetter . '() returns value previously set.'
		);
	}

	/**
	 * Does assertBuildStepSetterAndGetterWorking for a whole array of setter/value pairs provided
	 * as $buildSteps in the second argument.
	 *
	 * @param UserBitcoinAddressRecordBuilder $builder
	 * @param array $buildSteps Setter name as key, value as value.
	 */
	public function assertBuildStepsWorking( $builder, $buildSteps ) {
		foreach( $buildSteps as $buildStepSetter => $value ) {
			$this->assertBuildStepSetterAndGetterWorking( $builder, $buildStepSetter, $value );
		}
	}

	public static function extendableObjectProvider() {
		return [
			'"empty" UserBitcoinAddressRecordBuilder instance' => [
				new UserBitcoinAddressRecordBuilder()
			],
			'UserBitcoinAddressRecord instance' => [
				( new UserBitcoinAddressRecordBuilder() )
					->id( 24 )
					->user( User::newFromName( 'nobody' ) )
					->bitcoinAddress( new Address( '1C5bSj1iEGUgSTbziymG7Cn18ENQuT36vv' ) )
					->purpose( 'some purpose' )
					->build()
			],
		];
	}
}
