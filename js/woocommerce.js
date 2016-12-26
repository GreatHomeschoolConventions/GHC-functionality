(function($){
    $(document).ready(function(){

        // fix pluralization on product pages and in cart
        changePeopleAgreement();
        $('.quantity input[type=number]').on('change', changePeopleAgreement);
        function changePeopleAgreement() {
            if ($('.quantity input[type=number]').val() == 1) {
                $('.quantity .people').html('person');
            } else {
                $('.quantity .people').html('people');
            }
        }

        // show/hide family member quantity depending on registration type
        if ($('.woocommerce-content > .product_cat-registration').length > 0) {
            checkFamilySelection();
            $('body').on('change', '.product_cat-registration select[name="attribute_attendee-type"]', checkFamilySelection);
        }
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

        // show/hide gift recipient info
        if ($('.woocommerce-content > .product_cat-registration').length > 0) {
            handleGiftMemberships();
            $('body').on('change', '.product-addon-gift-options input[type="checkbox"]', handleGiftMemberships);
        }
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

        // fix for browsers allowing out-of-range inputs (Safari)
        if (($('input.qty[max], input.qty[min]').length > 0) || ($('input.qty[min] ').length > 0)) {
            $('input.qty[max]').on('change', function() {
                if (Number($(this).attr('max')) > 0 && Number($(this).val()) > Number($(this).attr('max'))) {
                    $(this).val($(this).attr('max'));
                }
                if (Number($(this).val()) < Number($(this).attr('min'))) {
                    $(this).val($(this).attr('min'));
                }
            });
        }

    });
})(jQuery);
