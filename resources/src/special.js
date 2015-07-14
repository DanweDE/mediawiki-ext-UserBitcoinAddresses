/**
 * JavaScript for 'UserBitcoinAddresses' special page.
 *
 * @licence MIT Licence
 * @author Daniel A. R. Werner < daniel.a.r.werner@gmail.com >
 */
( function( $ ) {
	'use strict';

	$( document ).ready( function() {
		formatTextareas();
		addFieldsetContainserrorClasses();
	} );

	function formatTextareas() {
		var $addr1 = $( '#mw-input-wpaddresses' ); // valid addresses
		var $addr2 = $( '#mw-input-wpaddressesToBeCorrected' ); // invalid addresses

		if( $addr2.text() === '' ) {
			$addr2.hide();
		} else if( $addr1.text() === '' ) {
			$addr1.hide();
		}

		$addr1.add( $addr2 )
			.inputautoexpand( {
				expandWidth: false,
				expandHeight: true
			} )
			.attr( 'spellcheck', 'false' )
		;
	}

	function addFieldsetContainserrorClasses() {
		$( '#mw-content-text' ).find( 'fieldset' ).has( 'error' ).addClass( 'containserror' );
	}

} )( jQuery );
