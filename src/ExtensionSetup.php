<?php
namespace MediaWiki\Ext\UserBitcoinAddresses;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
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
		$this->registerSpecialPages();
		$this->registerUnitTests();
	}

	private function registerSpecialPages() {
		$specialNs = 'MediaWiki\Ext\UserBitcoinAddresses\Specials';

		$this->globals['wgSpecialPages']['UserBitcoinAddresses']
			= "$specialNs\\SpecialUserBitcoinAddresses";
	}

	private function registerUnitTests() {
		$rootDirectory = $this->rootDirectory;

		/**
		 * Hook to add PHPUnit test cases.
		 * @see https://www.mediawiki.org/wiki/Manual:Hooks/UnitTestsList
		 *
		 * @since 1.0.0
		 *
		 * @param array $files
		 * @return boolean
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
