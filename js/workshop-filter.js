(function($){
    $(document).ready(function(){
        $('.legend-key').on('click', function(e){
            e.preventDefault();
            var workshopContainer = '.workshop-schedule',
                thisTrack = $(this).data('special-track');

            if (thisTrack == 'clear') {
                $('.legend-key.active').trigger('click');
            } else {
                $('.' + thisTrack).toggleClass('active');
                $(workshopContainer).toggleClass(thisTrack + '-active');
            }
        })
    });
})(jQuery);
