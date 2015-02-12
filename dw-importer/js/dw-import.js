(function($) {
    "use strict";
    jQuery(document).ready(function($) {
        $('.dw_import').on( 'click', function(){
                var import_true = confirm('Are you sure to import dummy content ? it will overwrite the existing data');
                if( import_true == false ) return;
                $('.notice_import').empty();
                $('.button').hide();
                $('.import_message').html(' Data is being imported please be patient, while the awesomeness is being created :). It\'s take a few minutes, please wait ... ');
                $('.spinner').show();
                var data = {
                    'action': 'dw_import_data'       
                };

           // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            $.post(ajaxurl, data, function(response) {
                $('.import_message').html('<div class="import_message_success">'+ response +'</div>');
                $('.spinner').hide();
                //alert('Got this from the server: ' + response); <i class="fa fa-spinner fa-3x fa-spin"></i>
            });
        });

        $('.check_import').on('click', function() {
            $('.notice_import').empty();
            var data = {
                    'action': 'dw_check_import',   
                };
                $.post(ajaxurl, data, function(response) {
                window.location.replace('themes.php?page=install-required-plugins');

                //alert('Got this from the server: ' + response); <i class="fa fa-spinner fa-3x fa-spin"></i>
            });
            /* Act on the event */
        });
    });
        
})(jQuery);