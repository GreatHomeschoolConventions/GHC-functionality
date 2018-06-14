/**
 * Update hidden Robly lists field with selected values.
 *
 * @package GHC_Functionality_Plugin
 *
 * @param  {function} $ jQuery reference.
 *
 * @return {null}       Modifies DOM.
 */

/* global
	document, jQuery
*/

'use strict';

(function($) {
	$(document).ready(function() {
		$('input[type="checkbox"][name="conventions[]"]').on('change', function() {
			var $roblyListsInput = $('input[name="robly-lists"]'),
				thisVal = $(this).val(),
				currentVal = $roblyListsInput.val().split(','),
				thisValIndex,
				thisConferenceListId = '';

			if (thisVal.indexOf('Greenville') > -1) {
				thisConferenceListId = '11159';
			} else if (thisVal.indexOf('Fort Worth') > -1) {
				thisConferenceListId = '11161';
			} else if (thisVal.indexOf('Cincinnati') > -1) {
				thisConferenceListId = '11157';
			} else if (thisVal.indexOf('Ontario') > -1) {
				thisConferenceListId = '11163';
			} else if (thisVal.indexOf('St. Charles') > -1) {
				thisConferenceListId = '222196';
			} else if (thisVal.indexOf('New York') > -1) {
				thisConferenceListId = '305507';
			} else if (thisVal.indexOf('Florida') > -1) {
				thisConferenceListId = '11174';
			}

			if ($(this).attr('checked')) {
				currentVal.push(thisConferenceListId);
			} else {
				thisValIndex = currentVal.indexOf(thisConferenceListId);
				if (thisValIndex > -1) {
					currentVal.splice(thisValIndex, 1);
				}
			}

			$roblyListsInput.val(currentVal.join());
		});
	});
}(jQuery));
