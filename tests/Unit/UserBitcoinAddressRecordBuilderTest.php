<?php

namespace MediaWiki\Ext\UserBitcoinAddresses\Tests;

use Danwe\Bitcoin\Address;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecordBuilder;
use Danwe\Bitcoin\Address as BtcAddress;
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
	 * @dataProvider buildStepsWithValuesProvider
	 */
	public function testSetterGetterPairs( $buildStepSetter, $value ) {
		$this->assertInternalType( 'string', $buildStepSetter );

		$buildStepGetter = $this->getSettersGetter( $buildStepSetter );
		$builder = new UserBitcoinAddressRecordBuilder();

		$this->assertEquals(
			$builder->{ $buildStepGetter }(),
			null,
			'UserBitcoinAddressBuilder::' . $buildStepGetter . '() returns null initially.'
		);
		$this->assertBuildStepSetterAndGetterWorking( $builder, $buildStepSetter, $value );
	}

	/**
	 * Tests whether setting a value, then setting another value for the same build step is working
	 * properly.
	 *
	 * @dataProvider valuesPerBuildStepProvider()
	 * @depends testSetterGetterPairs
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
	 * @dataProvider validBuildStepsProvider
	 * @depends testSetterGetterPairs
	 */
	public function testBuildFromValidBuildSteps( $buildSteps ) {
		$builder = new UserBitcoinAddressRecordBuilder();

		$this->assertBuildStepsWorking( $builder, $buildSteps );
		$this->assertInstanceOf(
			'MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord',
			$builder->build()
		);
	}

	/**
	 * Test will use builders setters which should not throw errors. When calling build an error
	 * should be thrown due to inconsistency issues.
	 *
	 * @dataProvider inconsistentBuildStepsProvider
	 * @depends testSetterGetterPairs
	 */
	public function testBuildFromInconsistentBuildSteps( $buildSteps, $buildException ) {
		$builder = new UserBitcoinAddressRecordBuilder();

		$this->assertBuildStepsWorking( $builder, $buildSteps );
		$this->assertTrue( true,
			'Builder was brought into an inconsistent state but did not thrown an exception'
		);
		$this->setExpectedException( $buildException );
		$builder->build(); // Should throw ERROR!
	}

	/**
	 * Returns the builder's getter member name based on a given setter member name.
	 *
	 * @param string $setterName
	 * @return string
	 */
	public function getSettersGetter( $setterName ) {
		return 'get' . ucfirst( $setterName );
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
	public final function valuesPerBuildStepProvider() {
		$valuesPerStep = [];
		foreach( $this->validBuildStepsProvider() as $caseBuildSteps ) {
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
	 *
	 * @return array
	 */
	public final function buildStepsWithValuesProvider() {
		$stepsWithValues = [];
		foreach( $this->validBuildStepsProvider() as $caseBuildSteps ) {
			foreach( $caseBuildSteps[0] as $buildStep => $value ){
				$stepsWithValues[] = [ $buildStep, $value ];
			}
		}
		return $stepsWithValues;
	}

	/**
	 * @return array
	 */
	public function validBuildStepsProvider() {
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
	 * exception because of inconsistencies.
	 *
	 * @return array
	 */
	public function inconsistentBuildStepsProvider() {
		return [
			[
				[
					'user' => User::newFromId( 42 ),
					'bitcoinAddress' => new Address( '1C5bSj1iEGUgSTbziymG7Cn18ENQuT36vv' ),
					'addedOn' => ( new Datetime() )->add( new \DateInterval( 'PT9M30S' ) ),
					'exposedOn' => new Datetime(),
				],
				'LogicException' // addedOn > exposedOn
			],
		];
	}
}
