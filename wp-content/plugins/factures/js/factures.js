jQuery(function($){

    $('#genpdf').click(function(e){
        var $e = $(e.currentTarget);
        $e.prev().css('visibility', 'visible');
        $.get(ajaxurl, {action: 'factures_genpdf', id:$e.attr('data-id')}, function(response){
            console.log(response);
            $e.prev().css('visibility', 'hidden');
        })
        return false;
    });

});
