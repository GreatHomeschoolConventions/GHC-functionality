/**
 * Add HTTP protocol to exhibitor URL if not specified.
 *
 * @param  {function} $ jQuery shorthard.
 *
 * @return {null}       Modifies DOM.
 */

/* global
	jQuery
*/
'use strict';

(function($) {

	$('#acf-field-exhibitor_URL').on('focus', function() {
		var thisUrl = $(this).val();

		if (-1 === thisUrl.indexOf('http')) {
			$(this).val('http://' + thisUrl);
		}

	});
}(jQuery));
