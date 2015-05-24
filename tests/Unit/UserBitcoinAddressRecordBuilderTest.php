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
		$builder = new UserBitcoinAddressRecordBuilder();
		$validValues[] = $validValues[ 0 ];

		foreach( $validValues as $i => $value ) {
			$this->assertBuildStepSetterAndGetterWorking( $builder, $buildStepSetter, $value );
		}
	}

	/**
	 * @dataProvider buildStepsProvider
	 * @depends testSetterGetterPairs
	 */
	public function testBuild( $buildSteps ) {
		$builder = new UserBitcoinAddressRecordBuilder();

		foreach( $buildSteps as $buildStepSetter => $value ) {
			$this->assertBuildStepSetterAndGetterWorking( $builder, $buildStepSetter, $value );
		}
		$this->assertInstanceOf(
			'MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecord',
			$builder->build()
		);
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
	 * Takes builderStepsProvider as source and returns an array with arrays where the first
	 * value is the name of a build step member and the second is a value to be used with it.
	 *
	 * @return array
	 */
	public function valuesPerBuildStepProvider() {
		$valuesPerStep = [];
		foreach( $this->buildStepsProvider() as $caseBuildSteps ) {
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
	public function buildStepsWithValuesProvider() {
		$stepsWithValues = [];
		foreach( $this->buildStepsProvider() as $caseBuildSteps ) {
			foreach( $caseBuildSteps[0] as $buildStep => $value ){
				$stepsWithValues[] = [ $buildStep, $value ];
			}
		}
		return $stepsWithValues;
	}

	/**
	 * @return array
	 */
	public function buildStepsProvider() {
		return array_chunk ( [
			[
				'id' => null,
				'user' => User::newFromId( 42 ),
				'bitcoinAddress' => new Address( '1C5bSj1iEGUgSTbziymG7Cn18ENQuT36vv' ),
				'addedOn' => new Datetime(),
				'exposedOn' => ( new Datetime() )->add( new \DateInterval( 'PT9M30S' ) ),
				'addedThrough' => 'test',
				'purpose' => 'none'
			], [
				'id' => 42,
				'user' => User::newFromId( 0 ),
				'bitcoinAddress' => new Address( '19dcawoKcZdQz365WpXWMhX6QCUpR9SY4r' ),
				'addedOn' => new Datetime(),
				'addedThrough' => 'foo'
			], [
				'user' => User::newFromId( 0 ),
				'bitcoinAddress' => new Address( '13p1ijLwsnrcuyqcTvJXkq2ASdXqcnEBLE' ),
			]
		], 1 );
	}
}
