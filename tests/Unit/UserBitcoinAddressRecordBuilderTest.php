<?php

namespace MediaWiki\Ext\UserBitcoinAddresses\Tests;

use Danwe\Bitcoin\Address;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecordBuilder;
use DateTime;
use User;

/**
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class UserBitcoinAddressBuilderTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider validConstructorArgsProvider
	 */
	public function testConstruction( $address, $addressString ) {
		$this->assertInstanceOf(
			'MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecordBuilder',
			new UserBitcoinAddressRecordBuilder()
		);
	}

	/**
	 * @dataProvider buildStepsWithValidValuesProvider
	 */
	public function testSetterGetterPairsWithValidValues( $buildStepSetter, $value ) {
		$this->assertInternalType( 'string', $buildStepSetter );
		$builder = new UserBitcoinAddressRecordBuilder();
		$this->assertBuildStepGetterReturnsNullInitially( $builder, $buildStepSetter );
		$this->assertBuildStepSetterAndGetterWorking( $builder, $buildStepSetter, $value );
	}

	/**
	 * @dataProvider buildStepsWithInvalidValuesProvider
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
	 * @dataProvider validValuesPerBuildStepProvider()
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
	 * @dataProvider validBuilderStepsProvider
	 * @depends testSetterGetterPairsWithValidValues
	 */
	public function testSettingUpBuilderWithValidValues( $buildSteps ) {
		$this->assertBuildStepsWorking( new UserBitcoinAddressRecordBuilder(), $buildSteps );
	}

	/**
	 * @dataProvider invalidBuilderStepsProvider
	 * @depends testSetterGetterPairsWithValidValues
	 */
	public function testSettingUpBuilderWithInvalidValues( $buildSteps ) {
		$this->assertBuildStepsWorking( new UserBitcoinAddressRecordBuilder(), $buildSteps );
		$this->assertTrue( true, 'All steps set up' ); // Necessary if 0 steps.
	}

	/**
	 * @dataProvider validBuildStateBuildersProvider
	 * @depends testSettingUpBuilderWithValidValues
	 */
	public function testBuildFromValidBuildSteps( $builder, $builderBuildSteps ) {
		$this->assertInstanceOf(
			'MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord',
			$builder->build()
		);
	}

	/**
	 * @dataProvider invalidBuildStateBuildersProvider
	 * @depends testSettingUpBuilderWithInvalidValues
	 */
	public function testBuildFromInvalidBuildSteps( $builder, $builderBuildSteps, $buildException ) {
		$this->setExpectedException( $buildException );
		$builder->build(); // Should throw ERROR!
	}

	/**
	 * Returns the builder's getter member name based on a given build step name.
	 *
	 * @param string $buildStep
	 * @return string
	 */
	public function getSettersGetter( $buildStep ) {
		return 'get' . ucfirst( $buildStep );
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

	/**
	 * Takes builderStepsProvider as source and returns an array with arrays where the first
	 * value is the name of a build step member and the second is a value to be used with it.
	 *
	 * @return array
	 */
	public final function validValuesPerBuildStepProvider() {
		$valuesPerStep = [];
		foreach( $this->validBuilderStepsProvider() as $caseBuildSteps ) {
			foreach( $caseBuildSteps[0] as $buildStep => $value ){
				if( !array_key_exists( $buildStep, $valuesPerStep ) ) {
					$valuesPerStep[ $buildStep ] = [ $buildStep, [] ];
				}
				$valuesPerStep[ $buildStep ][ 1 ][] = $value;
			}
		}
		return $valuesPerStep;
	}

	/**
	 * Takes builderStepsProvider as source and returns an array with arrays where the first
	 * value is the name of a build step member and the second is a value to be used with it.
	 * May contain several entries for the same build step.
	 *
	 * @return array( [ string $buildStep, mixed $value ] )
	 */
	public final function buildStepsWithValidValuesProvider() {
		$stepsWithValues = [];
		foreach( $this->validBuilderStepsProvider() as $caseBuildSteps ) {
			foreach( $caseBuildSteps[0] as $buildStep => $value ){
				$stepsWithValues[] = [ $buildStep, $value ];
			}
		}
		return $stepsWithValues;
	}

	/**
	 * Returns a readily set up builder instance where calling build() will result in successful
	 * instantiation of the desired object.
	 *
	 * @return array( array( UserBitcoinAddressRecordBuilder, array $buildSteps ), ... )
	 */
	public function validBuildStateBuildersProvider() {
		return $this->buildStepsProviderToBuildInstancesCases( $this->validBuilderStepsProvider() );
	}

	/**
	 * Returns a readily set up builder instance where calling build() will result in an error due
	 * to usage of value combinations that result in e.g. a LogicException or because of invalid or
	 * missing values.
	 *
	 * @return array( array( UserBitcoinAddressRecordBuilder, array $buildSteps, string $expectedError ), ... )
	 */
	public function invalidBuildStateBuildersProvider() {
		return $this->buildStepsProviderToBuildInstancesCases( $this->invalidBuilderStepsProvider() );
	}

	protected final function buildStepsProviderToBuildInstancesCases( $providerValues ) {
		$buildersCases = [];
		foreach( $providerValues as $caseArgs ) {
			$buildSteps = $caseArgs[ 0 ];
			$builder = new UserBitcoinAddressRecordBuilder();
			foreach( $buildSteps as $buildStepSetter => $value ) {
				$builder->{ $buildStepSetter }( $value );
			}
			$buildersCases[] = array_merge( [ $builder ], $caseArgs );
		}
		return $buildersCases;
	}

	/**
	 * Like buildStepsWithValidValuesProvider instead of a valid value, contains an invalid one for
	 * the respective build step.
	 *
	 * @return array( [ string $buildStep, mixed $invalidValue ] )
	 */
	public function buildStepsWithInvalidValuesProvider() {
		return [
			[ 'id', false ],
			[ 'id', '42' ],
			[ 'id', -42 ],
			[ 'addedThrough', array() ],
			[ 'addedThrough', false ],
			// Can't test for user(), bitcionAddress(), addedOn() and exposedOn() since they are
			// using type hints. Violations would result in fatal error (not cachable by PhpUnit).
		];
	}

	/**
	 * @return array
	 */
	public function validBuilderStepsProvider() {
		return array_chunk ( [
			[
				'id' => null,
				'user' => User::newFromId( 42 ),
				'bitcoinAddress' => new Address( '1C5bSj1iEGUgSTbziymG7Cn18ENQuT36vv' ),
				'addedOn' => new Datetime(),
				'exposedOn' => ( new Datetime() )->add( new \DateInterval( 'PT9M30S' ) ),
				'addedThrough' => 'test',
				'purpose' => 'none',
			], [
				'id' => 42,
				'user' => User::newFromId( 0 ),
				'bitcoinAddress' => new Address( '19dcawoKcZdQz365WpXWMhX6QCUpR9SY4r' ),
				'addedOn' => new Datetime(),
				'exposedOn' => null,
				'addedThrough' => 'foo',
			], [
				'user' => User::newFromId( 0 ),
				'bitcoinAddress' => new Address( '13p1ijLwsnrcuyqcTvJXkq2ASdXqcnEBLE' ),
			], [
				'user' => User::newFromId( 1337 ),
				'bitcoinAddress' => new Address( '1C5bSj1iEGUgSTbziymG7Cn18ENQuT36vv' ),
				'addedOn' => new Datetime(),
				'exposedOn' => new Datetime(),
			],
		], 1 );
	}

	/**
	 * These cases would result in a builder but creating an object from it would throw an
	 * exception because of inconsistencies or missing values.
	 *
	 * @return array
	 */
	public function invalidBuilderStepsProvider() {
		return [
			[
				[],
				'InvalidArgumentException' // No user and bitcoinAddress!
			],
			[
				[
					'bitcoinAddress' => new Address( '1C5bSj1iEGUgSTbziymG7Cn18ENQuT36vv' ),
				],
				'InvalidArgumentException' // No user!
			], [
				[
					'user' => User::newFromId( 42 ),
				],
				'InvalidArgumentException' // No bitcoinAddress!
			], [
				[
					'user' => User::newFromId( 42 ),
					'bitcoinAddress' => new Address( '1C5bSj1iEGUgSTbziymG7Cn18ENQuT36vv' ),
					'addedOn' => ( new Datetime() )->add( new \DateInterval( 'PT9M30S' ) ),
					'exposedOn' => new Datetime(),
				],
				'LogicException' // addedOn > exposedOn
		  	]
		];
	}
}
