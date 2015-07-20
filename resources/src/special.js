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
		replaceRemoveButtonsWithLinks();
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

	function replaceRemoveButtonsWithLinks() {
		var $recordsTables = $( '.mwuba-recordstable' );
		var $buttons = $recordsTables.find( 'input.mw-htmlform-submit' );
		var $linkTemplate = $( '<a/>', {
			href: window.location.href,
			'class': 'mwuba-removerecord'
		} );

		$buttons.each( function() {
			var $button = $( this );
			$button.replaceWith( $linkTemplate.clone().prop( {
				text: $button.attr( 'value' )
			} ) );
		} );

		$recordsTables.on( 'click mouseover mouseout', '.mwuba-removerecord', function( e ) {
			var $target = $( e.target );

			switch( e.type ) {
				case 'click':
					e.preventDefault();
					$target.closest( 'form' ).submit();
					break;
				case 'mouseover':
				case 'mouseout':
					$target.parents( 'tr' ).toggleClass( 'mwuba-actionimminent' );
					break;
			}

		} )
	}

} )( jQuery );
