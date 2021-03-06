/**
 * Add WooCommerce register page functionality.
 *
 * @package GHC_Functionality_Plugin
 */

/* global Cookies, jQuery */

'use strict';

(function($) {

	/**
	 * Set popup maker cookies values so we can unset the cookie later.
	 *
	 * @since  3.0.0
	 *
	 * @type {Array}
	 */
	var popupMakerCookie = [
		'pum-57405', // Live site.
		'pum-56888', // Local dev site.
	];

	/**
	 * Fix quantity pluralization
	 *
	 * @param {Object} thisField      jQuery element representing the DOM element to check.
	 * @param {Object} targetField    jQuery element representing the DOM element to change.
	 * @param {string} singularString string to display if singular.
	 * @param {string} pluralString   string to display if 0 or plural.
	 *
	 * @returns {void} Modifies the DOM.
	 */
	function changePeopleAgreement(thisField, targetField, singularString, pluralString) {
		if ('1' === thisField.val()) {
			targetField.html(singularString);
		} else {
			targetField.html(pluralString);
		}
	}

	/**
	 * Show/hide family vs. individual products.
	 *
	 * Used on product pages with (global) Product Add-ons.
	 *
	 * @returns {void} Modifies the DOM.
	 */
	function checkFamilySelection() {
		var selectedTicketType = $('.woocommerce-content > .registration select[name="attribute_attendee-type"]').val();

		if (selectedTicketType.indexOf('Family') > -1) {
			$('.product-addon-family-members').slideDown();
			$('option[value="1-individual-1"]').hide();
			$('.product-addon-family-members select.addon').val('');
		} else {
			$('.product-addon-family-members').slideUp();
			$('option[value="1-individual-1"]').show();
			$('.product-addon-family-members select.addon').val('1-individual-1');
		}
	}

	/**
	 * If value is out of allowed min-max range, bring it back into the valid range.
	 *
	 * @param {Object} inputField Field to validate.
	 *
	 * @returns {void} Modifies the DOM.
	 */
	function fixMaxTickets(inputField) {
		if (Number(inputField.attr('max')) > 0 && Number(inputField.val()) > Number(inputField.attr('max'))) {
			inputField.val(inputField.attr('max'));
		}
		if (Number(inputField.val()) < Number(inputField.attr('min'))) {
			inputField.val(inputField.attr('min'));
		}
	}

	$(document).ready(function() {

		/** Hide all options if no convention is selected. */
		if ($('input.convention.filter').length > 0 && 0 === $('input.convention.filter:checked').length) {
			$('.filter-target').hide();
		}

		/** Reset options visibility once a convention has been chosen. */
		$('input.convention.filter').one('change', function() {
			$('.filter-target, table .filter-target').css({ display: '' });
		});

		/** Fix pluralization on registration page, product pages, and in cart. */
		$('.quantity input[type=number]').on('change keyup', changePeopleAgreement($(this), $('.quantity .people'), 'person', 'people'));
		$('input.qty').on('change keyup', function() {
			changePeopleAgreement($(this), $(this).prevAll('.tickets-qty'), 'Ticket', 'Tickets');
		});
		$('.quantity input[type=number], input.qty').trigger('change');

		/** Show/hide family member quantity depending on registration type. */
		if ($('.woocommerce-content > .registration').length > 0) {
			checkFamilySelection();
			$('body').on('change', '.registration select[name="attribute_attendee-type"]', checkFamilySelection);
		}

		/** Hide “family members” line from individual registrations in the cart and handle program guides. */
		if ($('.woocommerce-cart').length > 0) {
			$('dl.variation').each(function() {
				if ($(this).find('dd.variation-RegistrationType p').text().indexOf('Individual') > -1) {

					// Hide Family Members line and fix pluralization.
					$(this).find('.variation-Familymembers').remove();
					$('input.qty').attr('type', 'hidden').after('1');
					$('.people').html('person');
				}
			});
			$('.cart_item:contains("Additional Program Guide")').find('.attendees-count').remove();
		}

		/** Fix for browsers allowing out-of-range inputs. */
		if (($('input.qty[max], input.qty[min]').length > 0) || ($('input.qty[min]').length > 0)) {

			// Run on change.
			$('input.qty[max], input.qty[min]').on('change', fixMaxTickets($(this)));

			// Run on load.
			$('input.qty[max], input.qty[min]').each(function() {
				fixMaxTickets($(this));
			});
		}

		/** Update all family members display fields when changed. */
		$('input[name="family-members"]').on('change keyup', function() {
			fixMaxTickets($(this));
			var familyCount = $(this).val();

			$('input[name="family-members"]').val(familyCount);
			$('input[name^="qty-"]').each(function() {
				$(this).attr('max', familyCount);
				fixMaxTickets($(this));
			});
			$('.registration:visible').find('a[data-family-members]').data('family-members', familyCount);
		});
		$('input[name="family-members"]').trigger('change');

		/** Set family members to 1 if individual ticket type is selected. */
		$('input[name="attendee-type"]').on('change', function() {
			if ('individual' === $(this).val()) {
				$('input#family-members').val('1').trigger('change');
			} else {
				fixMaxTickets($('input#family-members'));
			}
		});

		/** Update add-to-cart button quantity when input is changed. */
		$('.products input[name^="qty"]').on('change keyup', function() {
			var thisProductQuantity = $(this).val(),
				thisAddToCartButton = $(this).parent().next('.product').find('.add_to_cart_button');

			$('input#family-members').trigger('change');
			fixMaxTickets($(this));
			thisAddToCartButton.data('quantity', thisProductQuantity).attr('data-quantity', thisProductQuantity);
		});

		/**
		 * Add decrement/increment buttons.
		 *
		 * Allows touchscreen users to easily modify input.qty, since fixMaxTickets runs on keyup, and deleting the value to enter a new resets the field to the min value.
		 */
		$('.decrement').on('click', function() {
			var qtyInput = $(this).next('input[type="number"]'),
				currentQty = parseInt(qtyInput.val(), 10);

			qtyInput.val(currentQty - 1).trigger('change');
		});

		$('.increment').on('click', function() {
			var qtyInput = $(this).prev('input[type="number"]'),
				currentQty = parseInt(qtyInput.val(), 10);

			qtyInput.val(currentQty + 1).trigger('change');
		});

		/**
		 * Set cookie to disable popup when adding a product to cart.
		 *
		 * @since  3.0.0.
		 *
		 * @return {void} Sets a cookie.
		 */
		$(document.body).on('added_to_cart', function() {

			for (var i in popupMakerCookie) {
				if (popupMakerCookie[i]) {
					Cookies.set(popupMakerCookie[i], true, { expires: 365 });
				}
			}

			// TODO: change to on checkout submit or something further down the process so we don’t lose people who abandoned checkout.
		});

	});
}(jQuery));
