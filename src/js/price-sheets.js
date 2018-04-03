(function($){
    $(document).ready(function(){
        var addonText = $('#footnotes .addon').text().replace('*',''),
            individualText = $('#footnotes .individual').text().replace('**',''),
            familyText = $('#footnotes .family').text().replace('***','');

        // add title text
        $('th[scope="col"] a[href="#footnotes"]').attr('title', addonText);
        $('td.individual a[href="#footnotes"]').attr('title', individualText);
        $('td.family a[href="#footnotes"]').attr('title', familyText);

        // handle clicks
        $('a[href="#footnotes"]').on('click',function(){
            $('#return').removeAttr('id');
            $(this).parents('tr').attr('id', 'return');
        });
    });
})(jQuery);
