/*jslint browser:true */
jQuery(document).ready(function() {
	'use strict';

	jQuery('#yoast-tabs').find('a').click(function() {
		jQuery('#yoast-tabs').find('a').removeClass('nav-tab-active');
		jQuery('.yoasttab').removeClass('active');

		var id = jQuery(this).attr('id').replace('-tab', '');
		jQuery('#' + id).addClass('active');
		jQuery(this).addClass('nav-tab-active');
	});

	/**
	 * Makes sure we store the action hash so we can return to the right hash.
	 */
	function yoastCHSetTabHash() {
		var conf = jQuery( '#yoast-ch-conf' ),
			currentUrl;

		if ( conf.length ) {
			currentUrl = conf.attr( 'action' ).split( '#' )[ 0 ];
			conf.attr( 'action', currentUrl + window.location.hash );
		}
	}

	/**
	 * When the hash changes, get the base url from the action and then add the current hash.
	 */
	jQuery( window ).on( 'hashchange', yoastCHSetTabHash );

	/**
	 * Set the initial active tab in the settings pages.
	 */
	function setInitialActiveTab() {
		var activeTabId = window.location.hash.replace( '#top#', '' );
		/*
		 * WordPress uses fragment identifiers for its own in-page links, e.g.
		 * `#wpbody-content` and other plugins may do that as well. Also, facebook
		 * adds a `#_=_` see wordpress-seo PR 506. In these cases and when it's
		 * empty, default to the first tab.
		 */
		if ( '' === activeTabId || '#' === activeTabId.charAt( 0 ) ) {
			/*
			 * Reminder: jQuery attr() gets the attribute value for only the first
			 * element in the matched set so this will always be the first tab id.
			 */
			activeTabId = jQuery( '.yoasttab' ).attr( 'id' );
		}

		jQuery( '#' + activeTabId ).addClass( 'active' );
		jQuery( '#' + activeTabId + '-tab' ).addClass( 'nav-tab-active' ).click();
	}

	// When the hash changes, get the base url from the form action and then add the current hash to the url.
	yoastCHSetTabHash();
	// Set the initial active tab.
	setInitialActiveTab();
} );
