(function($){
    var popupMakerCookie = [
        'pum-57405', // live site
        'pum-56888'  // local dev site
    ];

    /**
     * Fix quantity pluralization
     * @param {object} thisField      jQuery element representing the DOM element to check
     * @param {object} targetField    jQuery element representing the DOM element to change
     * @param {string} singularString string to display if singular
     * @param {string} pluralString   string to display if 0 or plural
     */
    function changePeopleAgreement(thisField, targetField, singularString, pluralString) {
        if (thisField.val() == 1) {
            targetField.html(singularString);
        } else {
            targetField.html(pluralString);
        }
    }

    /**
     * Show/hide family vs. individual products
     *
     * Used on product pages with (global) Product Add-ons
     */
    function checkFamilySelection() {
        var selectedTicketType = $('.woocommerce-content > .product_cat-registration select[name="attribute_attendee-type"]').val();
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
     * If value is out of allowed min-max range, bring it back into the valid range
     */
    function fixMaxTickets(inputField) {
        if (Number(inputField.attr('max')) > 0 && Number(inputField.val()) > Number(inputField.attr('max'))) {
            inputField.val(inputField.attr('max'));
        }
        if (Number(inputField.val()) < Number(inputField.attr('min'))) {
            inputField.val(inputField.attr('min'));
        }
    }

    /**
     * Extract parameters from URI fragment
     * @param   {string} hash window.location.hash string
     * @returns {object} named key/value pairs for all parameters in fragment hash
     */
    function extractParametersFromHash(hash) {
        var parameters = {},
            hashParameters = hash.substr(1).split('&');

        for (i=0; i < hashParameters.length; i++) {
            var parameterValue = hashParameters[i].split('=');
            parameters[parameterValue[0]] = parameterValue[1];
        }

        return parameters;
    }

    /**
     * Show/hide gift recipient info
     *
     * Used on product pages with (global) Product Add-ons
     */
    function handleGiftMemberships() {
        // handle gift vs. normal purchases
        if ($('.product-addon-gift-options input[type="checkbox"]').is(':checked') == true) {
            // show recipient info and make it required
            $('.product-addon-recipient-information').slideDown();
            $('.product-addon-recipient-information input').attr('required', true);
        } else {
            // hide and don’t require recipient info
            $('.product-addon-recipient-information').slideUp();
            $('.product-addon-recipient-information input').attr('required', false);
        }
    }

    $(document).ready(function(){

        // fix pluralization on registration page, product pages, and in cart
        $('.quantity input[type=number]').on('change keyup', changePeopleAgreement($(this), $('.quantity .people'), 'person', 'people'));
        $('input.qty').on('change keyup', function() {
            changePeopleAgreement($(this), $(this).prevAll('.tickets-qty'), 'Ticket', 'Tickets');
        });
        $('.quantity input[type=number], input.qty').trigger('change');

        // show/hide family member quantity depending on registration type
        if ($('.woocommerce-content > .product_cat-registration').length > 0) {
            checkFamilySelection();
            $('body').on('change', '.product_cat-registration select[name="attribute_attendee-type"]', checkFamilySelection);
        }

        // hide “family members” line from individual registrations in the cart and handle program guides
        if ($('.woocommerce-cart').length > 0) {
            $('dl.variation').each(function() {
                if ($(this).find('dd.variation-RegistrationType p').text().indexOf('Individual') > -1) {
                    // hide Family Members line and fix pluralization
                    $(this).find('.variation-Familymembers').remove();
                    $('input.qty').attr('type', 'hidden').after('1');
                    $('.people').html('person');
                }
            });
            $('.cart_item:contains("Additional Program Guide")').find('.attendees-count').remove();
        }

        // fix for browsers allowing out-of-range inputs
        if (($('input.qty[max], input.qty[min]').length > 0) || ($('input.qty[min]').length > 0)) {
            $('input.qty[max], input.qty[min]').on('change', fixMaxTickets($(this)));
            $('input.qty[max], input.qty[min]').each(function() {
                fixMaxTickets($(this))
            });
        }

        // handle convention changes
        var oldConventionChoice = $('input.registration-choice.convention:checked').val(),
            newConventionChoice;
        $('.registration-choice.convention').on('click, change', function() {
            newConventionChoice = $(this).val();
            $('label.registration-choice.attendee-type').removeClass(oldConventionChoice).addClass(newConventionChoice);
            oldConventionChoice = newConventionChoice;
            $('html, body').animate({
                scrollTop: $('#attendee-type').offset().top - 50
            }, 500);

            // update family members quantities
            $('input[name="family-members"]').trigger('change');
        });

        // update all family members display fields when changed
        $('input[name="family-members"]').on('change keyup', function() {
            fixMaxTickets($(this));
            var familyCount = $(this).val();

            $('input[name="family-members"]').val(familyCount);
            $('input[name^="qty-"]').each(function() {
                $(this).attr('max', familyCount);
                fixMaxTickets($(this));
            });
            $('.product_cat-registration:visible').find('a[data-family-members]').data('family-members', familyCount);
        });
        $('input[name="family-members"]').trigger('change');

        // set family members to 1 if individual ticket type is selected
        $('input[name="attendee-type"]').on('change', function() {
            if ($(this).val() === 'individiual') {
                $('input#family-members').val('1').trigger('change');
            } else {
                fixMaxTickets($('input#family-members'));
            }
        });

        // update add-to-cart button quantity when input is changed
        $('.products input[name^="qty"]').on('change keyup', function() {
            var thisProductQuantity = $(this).val(),
                thisAddToCartButton = $(this).parent().next('.product').find('.add_to_cart_button');

            $('input#family-members').trigger('change');
            fixMaxTickets($(this));
            thisAddToCartButton.data('quantity', thisProductQuantity).attr('data-quantity', thisProductQuantity);
        });

        // add decrement/increment buttons
        // allows touchscreen users to easily modify input.qty, since fixMaxTickets runs on keyup, and deleting the value to enter a new resets the field to the min value
        $('.decrement').on('click', function() {
            var qtyInput = $(this).next('input[type="number"]'),
                currentQty = parseInt(qtyInput.val());
            qtyInput.val(currentQty - 1).trigger('change');
        });

        $('.increment').on('click', function() {
            var qtyInput = $(this).prev('input[type="number"]'),
                currentQty = parseInt(qtyInput.val());
            qtyInput.val(currentQty + 1).trigger('change');
        });

        // show visual feedback while adding product to cart
        $(document.body).on('adding_to_cart', function(e, button, data){
            $(button).parent('.add_to_cart_inline').find('.spinner').removeClass('hidden');
        });

        // hide visual feedback after adding product to cart and add class to parent row for visual indicator
        $(document.body).on('added_to_cart', function(e, fragments, cart_hash, $button){
            $($button).parent('.add_to_cart_inline').find('.spinner').addClass('hidden').parents('tr.product').addClass('added-to-cart');
        });

        // handle location-specific registration page
        if (window.location.hash) {
            // name and info from bot
            if (window.location.hash.length > 3) {
                var allParameters = extractParametersFromHash(window.location.hash),
                    thisConvention = allParameters.convention.toLowerCase();

                // update family members
                if (typeof allParameters.familyMembers !== 'undefined') {
                    $('input#family-members').val(allParameters.familyMembers).trigger('change');
                    if (allParameters.familyMembers === '1') {
                        $('input#attendee-individual').prop('checked', true);
                    }
                }
            } else {
                var thisConvention = window.location.hash.replace('#','').toLowerCase();
            }

            var thisConventionSelector = 'input.registration-choice.convention[value="' + thisConvention + '"]';

            if ((thisConventionSelector).length > 0) {
                $(thisConventionSelector).attr('checked', true).trigger('change');
            }
        }

        // set cookie to disable popup when adding a product to cart
        $(document.body).on('added_to_cart', function() {
            for (i in popupMakerCookie) {
                Cookies.set(popupMakerCookie[i], true, {expires: 365});
            }
            // TODO: change to on checkout submit or something further down the process so we don’t lose people who abandoned checkout
        });

    });
})(jQuery);
