<?php
namespace MediaWiki\Ext\UserBitcoinAddresses;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use DatabaseUpdater;
use SplFileInfo;

/**
 * Contains the logic for setting up the extension.
 *
 * @since 1.0.0
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ExtensionSetup {

	/**
	 * @var Extension
	 */
	private $extension;

	/**
	 * @var array[]
	 */
	private $globals;

	/**
	 * @var string
	 */
	private $rootDirectory;

	/**
	 * @param Extension $extension
	 * @param array &$globals Array the setup is operating on. Should be $GLOBALS for production.
	 * @param string $rootDirectory
	 */
	public function __construct( Extension $extension, array &$globals, $rootDirectory ) {
		$this->globals =& $globals;
		$this->extension = $extension;
		$this->rootDirectory = $rootDirectory;
	}

	/**
	 * Sets up the extension.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->registerResources();
		$this->registerSpecialPages();
		$this->registerHooks();
		$this->registerUnitTests();
	}

	private function registerResources() {
		// Resource Loader module registration
		$this->globals['wgResourceModules'] = array_merge(
			isset( $this->globals['wgResourceModules'] )
				? $this->globals['wgResourceModules']
				: array(),
			include( "{$this->rootDirectory}/resources/src/resources.php" )
		);
	}

	private function registerSpecialPages() {
		$specialNs = 'MediaWiki\Ext\UserBitcoinAddresses\Specials';

		$this->globals['wgSpecialPages']['UserBitcoinAddresses']
			= "$specialNs\\SpecialUserBitcoinAddresses";
	}

	public function registerHooks() {
		/**
		 * Allows to add message keys accepted in the "warning" url parameter on redirects to
		 * Special:UserLogin.
		 *
		 * Added in MW 1.25. Fallback handling added in SpecialUserBitcoinAddresses.php
		 *
		 * @see https://www.mediawiki.org/wiki/Manual:Hooks/LoginFormValidErrorMessages
		 */
		$this->globals['wgHooks']['LoginFormValidErrorMessages'][] = function( &$messages ) {
			$messages[] = 'userbtcaddr-loginrequired';
			return true;
		};

		/**
		 * This will setup database tables for layer functionality.
		 *
		 * @see https://www.mediawiki.org/wiki/Manual:Hooks/LoadExtensionSchemaUpdates
		 */
		$this->globals['wgHooks']['LoadExtensionSchemaUpdates'][] = function( DatabaseUpdater $updater ) {
			switch( $GLOBALS['wgDBtype'] ) {
				case 'mysql':
				case 'sqlite':
					$updater->addExtensionTable(
						'user_bitcoin_addresses',
						"{$this->rootDirectory}/schema/UserBitcoinAddresses.sql"
					);
					break;
			}
			return true;
		};
	}

	private function registerUnitTests() {
		$rootDirectory = $this->rootDirectory;

		/**
		 * Hook to add PHPUnit test cases.
		 *
		 * @see https://www.mediawiki.org/wiki/Manual:Hooks/UnitTestsList
		 */
		$this->globals['wgHooks']['UnitTestsList'][] = function( array &$files ) use ( $rootDirectory ) {
			$directoryIterator = new RecursiveDirectoryIterator( $rootDirectory . '/tests/' );

			/** @var SplFileInfo $fileInfo */
			foreach ( new RecursiveIteratorIterator( $directoryIterator ) as $fileInfo ) {
				if ( substr( $fileInfo->getFilename(), -8 ) === 'Test.php' ) {
					$files[] = $fileInfo->getPathname();
				}
			}

			return true;
		};
	}

}
