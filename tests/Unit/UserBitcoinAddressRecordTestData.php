<?php

namespace MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit;

use Danwe\Bitcoin\Address;
use MediaWiki\Ext\UserBitcoinAddresses\UserBitcoinAddressRecordBuilder;
use DateTime;
use User;

/**
 * Data providers for UserBitcoinAddressRecord and -Builder related tests.
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class UserBitcoinAddressRecordTestData {
	/**
	 * @return array
	 */
	public static function validBuilderStepsProvider() {
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
	public static function invalidBuilderStepsProvider() {
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

	/**
	 * Like buildStepsWithValidValuesProvider instead of a valid value, contains an invalid one for
	 * the respective build step.
	 *
	 * @return array( [ string $buildStep, mixed $invalidValue ] )
	 */
	public static function buildStepsWithInvalidValuesProvider() {
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
	 * Takes builderStepsProvider as source and returns an array with arrays where the first
	 * value is the name of a build step member and the second is a value to be used with it.
	 *
	 * @return array
	 */
	public final static function validValuesPerBuildStepProvider() {
		$valuesPerStep = [];
		foreach( static::validBuilderStepsProvider() as $caseBuildSteps ) {
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
	public final static function buildStepsWithValidValuesProvider() {
		$stepsWithValues = [];
		foreach( static::validBuilderStepsProvider() as $caseBuildSteps ) {
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
	public final static function validBuildStateBuildersProvider() {
		return static::buildStepsProviderToBuildInstancesCases( static::validBuilderStepsProvider() );
	}

	/**
	 * Returns a readily set up builder instance where calling build() will result in an error due
	 * to usage of value combinations that result in e.g. a LogicException or because of invalid or
	 * missing values.
	 *
	 * @return array( array( UserBitcoinAddressRecordBuilder, array $buildSteps, string $expectedError ), ... )
	 */
	public final static function invalidBuildStateBuildersProvider() {
		return static::buildStepsProviderToBuildInstancesCases( static::invalidBuilderStepsProvider() );
	}

	protected final static function buildStepsProviderToBuildInstancesCases( $providerValues ) {
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
}

