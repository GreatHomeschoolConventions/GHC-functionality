/**
 * Set 30-day cookies to prevent popups from re-opening when closed.
 *
 * @package GHC_Functionality_Plugin
 *
 * @param  {function} $ jQuery reference.
 *
 * @return {null}       Sets a cookie.
 */

/* global
	Cookies, jQuery
 */

'use strict';

(function($) {
	$('document').ready(function() {
		$('.popmake-content').on('click', 'a', function() {

			// Get popup cookie settings.
			var i,
				j,
				popup = $(this).parents('.popmake-overlay'),
				triggers = popup.data('popmake').triggers,
				cookieTriggers;

			// Loop over all triggers.
			for (i = 0; i < triggers.length; i++) {
				cookieTriggers = triggers[i].settings.cookie_name;

				// Check for cookie-based triggers.
				if (triggers[i].type === 'auto_open' && cookieTriggers.length > 0) {
					for (j = 0; j < cookieTriggers.length; j++) {

						// If found, set a 30-day cookie to prevent this popup from showing again.
						Cookies.set(cookieTriggers[j], true, { expires: 30 });
					}
				}
			}
		});
	});
}(jQuery));
