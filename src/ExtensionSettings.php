<?php
namespace MediaWiki\Ext\UserBitcoinAddresses;

/**
 * Container for the settings contained by this extension.
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner <daniel.a.r.werner@gmail.com>
 */
class ExtensionSettings {
	/**
	 * @var array
	 */
	private $settings;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings
	 */
	public function __construct( array $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Returns the setting with the provided name.
	 * The specified setting needs to exist.
	 *
	 * @since 1.0.0
	 *
	 * @param string $settingName
	 * @return mixed
	 */
	public function get( $settingName ) {
		return $this->settings[$settingName];
	}

	/**
	 * Constructs a new instance of the settings object from global state.
	 *
	 * @since 1.0.0
	 *
	 * @param array $globalVariables
	 * @return Settings
	 */
	public static function newFromGlobals( array $globalVariables ) {
		return new self( array() );
	}
}
