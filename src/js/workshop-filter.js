(function($){
    $(document).ready(function(){
        $('.legend-key').on('click', function(e){
            e.preventDefault();
            var workshopContainer = '.workshop-schedule',
                clearFilter = '.clear-filters',
                thisTrack = $(this).data('special-track');

            if (thisTrack == 'clear') {
                $('.legend-key.active:not(' + clearFilter + ')').trigger('click');
                $(clearFilter).removeClass('active');
            } else {
                $('.' + thisTrack).toggleClass('active');
                $(workshopContainer).toggleClass(thisTrack + '-active');
                $(clearFilter).addClass('active');
            }
        })
    });
})(jQuery);
