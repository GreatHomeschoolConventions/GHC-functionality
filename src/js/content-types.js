/**
 * Add content filtering.
 *
 * @since  4.0.0
 *
 * @package GHC_Functionality_Plugin
 */

/* global jQuery, speakerAjaxUrl, speakerTagSlickArgs */

'use strict';

(function($) {

	/**
	 * Get REST API repsonse.
	 *
	 * @since  4.0.0
	 *
	 * @param  {string} categoryId Category ID to fetch.
	 *
	 * @returns {Object}           JSON object from API.
	 */
	function getRestResponse(categoryId) {
		$.ajax({
			method: 'POST',
			data: {
				action: 'get_speakers_by_content_tag',
				category: categoryId,
			},
			url: speakerAjaxUrl,
		}).success(function(data) {
			$('.speaker-tags .speakers-container').slick('unslick').html(data).slick(speakerTagSlickArgs);
			$(document.body).trigger('post-load');
		});
	}

	$(document).ready(function() {
		$('.speaker-tags .filter .button').on('click', function(e) {
			e.preventDefault();
			$('.speaker-tags .button').removeClass('active');
			$(this).addClass('active');
			getRestResponse($(this).data('content-tag-id'));
		});

		// Start carousel on document load.
		$('.speakers-container').slick(speakerTagSlickArgs);

	});
}(jQuery));
