/**
 * Improve price sheet interactivity.
 *
 * @package GHC_Functionality_Plugin
 *
 * @param  {function} $ jQuery reference.
 *
 * @return {null}       Modifies DOM.
 */

/* global
	jQuery
 */

'use strict';

(function($) {
	$('document').ready(function() {
		var addonText = $('#footnotes .addon').text().replace('*', ''),
			individualText = $('#footnotes .individual').text().replace('**', ''),
			familyText = $('#footnotes .family').text().replace('***', '');

		// Add title text.
		$('th[scope="col"] a[href="#footnotes"]').attr('title', addonText);
		$('td.individual a[href="#footnotes"]').attr('title', individualText);
		$('td.family a[href="#footnotes"]').attr('title', familyText);

		// Handle clicks.
		$('a[href="#footnotes"]').on('click', function() {
			$('#return').removeAttr('id');
			$(this).parents('tr').attr('id', 'return');
		});
	});
}(jQuery));
