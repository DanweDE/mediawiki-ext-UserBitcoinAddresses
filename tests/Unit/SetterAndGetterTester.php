<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Tests\Unit;

use PHPUnit_Framework_TestCase;

/**
 * Generic test helper for functions serving as both setter and getter.
 *
 * TODO: Move into separate package.
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class SetterAndGetterTester {

	/**
	 * @var PHPUnit_Framework_TestCase
	 */
	protected $testCase;

	protected $testOnInstance;
	protected $getterAndSetterName;
	protected $initiallyNotNull = false;
	protected $initialValue;
	protected $ranTest = false;

	public function __construct( PHPUnit_Framework_TestCase $testCase ) {
		$this->testCase = $testCase;
	}

	public function on( $instance ) {
		$this->testOnInstance = $instance;
		return $this;
	}

	public function getAndSet( $getterAndSetterName ) {
		$this->getterAndSetterName = $getterAndSetterName;
		return $this;
	}

	public function initiallyNotNull( $trueOrFalse = true ) {
		$this->initiallyNotNull = $trueOrFalse;
		return $this;
	}

	public function initially( $value ) {
		if( $value !== null ) {
			$this->initiallyNotNull( true );
		}
		$this->initialValue = $value;
		return $this;
	}

	public function test( $validValue ) {
		if( ! $this->testOnInstance ) {
			throw new \BadFunctionCallException( 'should call on() first to define test subject' );
		}
		if( ! $this->getterAndSetterName ) {
			throw new \BadFunctionCallException( 'should call setAndGet() first to define setter/getter member' );
		}
		$this->ranTest = true;

		$instance = $this->testOnInstance;
		$getterAndSetter = $this->getterAndSetterName;

		if( ! $this->ranTest
			&& ( $this->initiallyNotNull || $this->initialValue !== null )
		) {
			$this->testCase->assertNotEquals( null, $instance->$getterAndSetter(),
				'getter returns some default value' );
		}

		if( $this->initialValue ) {
			$this->testCase->assertSame( $this->initialValue, $instance->$getterAndSetter(),
				'expected initial value' );
		}

		$this->testCase->assertSame( $instance, $instance->$getterAndSetter( $validValue ),
			'setter returns self-reference' );

		$this->testCase->assertSame( $validValue, $instance->$getterAndSetter(),
			'getter returns changed value previously set via setter' );

		return $this;
	}
}