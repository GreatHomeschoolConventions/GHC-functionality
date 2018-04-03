(function($){
    $('document').ready(function(){
        $('.popmake-content').on('click', 'a', function(e) {
            // get popup cookie settings
            var popup = $(this).parents('.popmake-overlay'),
                triggers = popup.data('popmake').triggers,
                cookieTriggers;

            // loop over all triggers
            for (i = 0; i < triggers.length; i++) {
                cookieTriggers = triggers[i].settings.cookie_name;

                // check for cookie-based triggers
                if (triggers[i].type === 'auto_open' && cookieTriggers.length > 0) {
                    for (j = 0; j < cookieTriggers.length; j++) {
                        // if found, set a 30-day cookie to prevent this popup from showing again
                        Cookies.set(cookieTriggers[j], true, {expires: 30});
                        console.log('Setting cookie ' + cookieTriggers[j]);
                    }
                }
            }
        });
    });
})(jQuery);
