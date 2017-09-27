(function($){
    $(document).ready(function() {
        $('input[type="checkbox"][name*="locations-of-interest"]').on('change', function() {
            var $roblyListsInput = $('input[name="robly-lists"]'),
                thisVal = $(this).val(),
                currentVal = $roblyListsInput.val().split(','),
                thisValIndex;

                if (thisVal.indexOf('Greenville') > -1) {
                    var thisConferenceListId = '11159';
                } else if (thisVal.indexOf('Fort Worth') > -1) {
                    var thisConferenceListId = '11161';
                } else if (thisVal.indexOf('Cincinnati') > -1) {
                    var thisConferenceListId = '11157';
                } else if (thisVal.indexOf('Ontario') > -1) {
                    var thisConferenceListId = '11163';
                } else if (thisVal.indexOf('St. Charles') > -1) {
                    var thisConferenceListId = '222196';
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
})(jQuery);
