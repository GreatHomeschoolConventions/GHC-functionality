(function($){
    $('#acf-field-exhibitor_URL').on('focus',function() {
        var thisUrl = $(this).val();
        if(thisUrl.indexOf('http') === -1) {
            $(this).val('http://' + thisUrl);
        }
    });
})(jQuery);
