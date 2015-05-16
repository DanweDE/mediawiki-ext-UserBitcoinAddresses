<?php
namespace MediaWiki\Ext\UserBitcoinAddresses;

/**
 * Top level factory for the extension.
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner <daniel.a.r.werner@gmail.com>
 */
class Extension {
	/**
	 * The extension's version.
	 */
	const VERSION = '1.0.0 alpha';

	/**
	 * @since 1.0.0
	 *
	 * @var Settings
	 */
	private $settings;

	public function __construct( ExtensionSettings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * @since 1.0.0
	 *
	 * @return Settings
	 */
	public function getSettings() {
		return $this->settings;
	}
}
