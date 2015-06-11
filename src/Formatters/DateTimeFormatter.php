<?php
namespace MediaWiki\Ext\UserBitcoinAddresses\Formatters;

use DateTime;

/**
 * Formats a DateTime instance.
 *
 * @since 1.0.0
 *
 * @licence MIT License
 * @author Daniel A. R. Werner
 */
Interface DateTimeFormatter{

	public function format( DateTime $dateTime );
}