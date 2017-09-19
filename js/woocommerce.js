(function($){
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
        $('.quantity input[type=number]').on('change', changePeopleAgreement($(this), $('.quantity .people'), 'person', 'people'));
        $('input.qty').on('change', function() {
            changePeopleAgreement($(this), $(this).next('.tickets-qty'), 'ticket', 'tickets');
        });
        $('.quantity input[type=number], input.qty').trigger('change');

        // show/hide family member quantity depending on registration type
        if ($('.woocommerce-content > .product_cat-registration').length > 0) {
            checkFamilySelection();
            $('body').on('change', '.product_cat-registration select[name="attribute_attendee-type"]', checkFamilySelection);
        }

        // show/hide gift recipient info
        if ($('.woocommerce-content > .product_cat-registration').length > 0) {
            handleGiftMemberships();
            $('body').on('change', '.product-addon-gift-options input[type="checkbox"]', handleGiftMemberships);
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

        // update family members display fields when changed
        $('input#family-members').on('change', function() {
            var familyCount = $(this).val();

            $('input[name="family-members-display"]').val(familyCount);
            $('input[name^="qty-"]').each(function() {
                $(this).attr('max', familyCount);
                fixMaxTickets($(this));
            });
        });
        $('input#family-members').trigger('change');

        // set family members to 1 if individual ticket type is selected
        $('input#attendee-individual').on('change', function() {
            $('input#family-members').val('1').trigger('change');
        });

        // update add-to-cart button quantity when input is changed
        $('.products input[name^="qty"]').on('change', function () {
            var thisProductQuantity = $(this).val(),
                thisAddToCartButton = $(this).next('.product').find('.add_to_cart_button');

            thisAddToCartButton.attr('data-quantity', thisProductQuantity);
            $('input#family-members').trigger('change');
        });

    });
})(jQuery);
