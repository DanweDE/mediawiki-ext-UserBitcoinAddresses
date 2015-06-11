<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Formatters;

use User;
use Language;
use DateTime;

/**
 * Formats a DateTime instance as plain text according to the user's preferences.
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
class MWUserDateTimeHtml implements DateTimeFormatter {

	/**
	 * @var User
	 */
	protected $user;

	/**
	 * @var Language
	 */
	protected $language;

	/**
	 * @TODO: 2nd parameter to choose Language::userTime(), ::userDate() or ::userTimeAndDate().
	 *
	 * @param User $user
	 */
	public function __construct( User $user ) {
		$this->user = $user;
		// Can't see good reason to inject Language object. Each language will inherit from base
		// Language but the userTimeAndDate() function will be the same in each implementation
		// and will instead take a formatting string from User which User in turn takes from a
		// LanguageXx class. Twisted...
		$this->language = new Language();
	}

	/**
	 * @return User|$this
	 */
	public function user( User $user = null ) {
		if( $user ) { // SETTER:
			$this->user = $user;
			return $this;
		}
		// GETTER:
		return $this->user;
	}

	/**
	 * @see TimeAndDateFormatter::Format()
	 */
	public function format( DateTime $dateTime ) {
		$formatted = $this->language->userTimeAndDate(
			$dateTime->format( 'YmdHis' ),
			$this->user
		);
		return htmlspecialchars( $formatted );
	}
}