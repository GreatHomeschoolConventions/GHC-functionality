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
    });
})(jQuery);
