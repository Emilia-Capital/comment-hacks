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

	// init
	var activeTab = window.location.hash.replace('#top#', '');

	// default to first tab
	if (activeTab === '' || activeTab === '#_=_') {
		activeTab = jQuery('.wpseotab').attr('id');
	}

	jQuery('#' + activeTab).addClass('active');
	jQuery('#' + activeTab + '-tab').addClass('nav-tab-active');

	jQuery('.nav-tab-active').click();
});
