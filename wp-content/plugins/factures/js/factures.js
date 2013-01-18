jQuery(function($){

    $('#genpdf').click(function(e){
        
        var $e = $(e.currentTarget);
        $e.prev().css('visibility', 'visible');
        $.get(ajaxurl, {action: 'factures_genpdf', id:$e.attr('data-id')}, function(response){
            $e.prev().css('visibility', 'hidden');
            if(response == "ok") {
                $e.closest('.inside').load(ajaxurl, {action: 'factures_getmetabox', id:$e.attr('data-id')});
            } else {
                $e.closest('.inside').html(response);
            }
        })
        return false;
    });

});
