<?php
namespace MediaWiki\Ext\UserBitcoinAddresses;

use MediaWiki\Ext\UserBitcoinAddresses\MwBridge;

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

	/**
	 * @since 1.0.0
	 *
	 * @return MwBridge\DBConnectionProvider
	 */
	public function getSlaveConnectionProvider() {
		return new MwBridge\LazyDBConnectionProvider( DB_SLAVE );
	}

	/**
	 * @since 1.0.0
	 *
	 * @return MwBridge\DBConnectionProvider
	 */
	public function getMasterConnectionProvider() {
		return new MwBridge\LazyDBConnectionProvider( DB_MASTER );
	}

	/**
	 * @since 1.0.0
	 *
	 * @return MwBridge\MwUserFactory
	 */
	public function getMwUserFactory() {
		return new MwBridge\StandardMwUserFactory();
	}

	/**
	 * @since 1.0.0
	 *
	 * @return Store\UserBitcoinAddressRecordStore
	 */
	public function getUserBitcoinAddressRecordStore() {
		return new Store\UserBitcoinAddressRecordMwDbStore(
			$this->getSlaveConnectionProvider(),
			$this->getMasterConnectionProvider(),
			$this->getMwUserFactory()
		);
	}

}
