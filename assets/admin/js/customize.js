(function($) {
	// Widget featured news - Popular news
	$(document).ready(function(){
		$('#customize-theme-controls').on('click','.recent-post-meta-info',function() {
	        var submeta_info = $(this).closest('.meta-info').find('.submeta-info');
	        if ( $(this).is(':checked') ) { 
	            $( submeta_info ).removeAttr('disabled');
	        } else {
	            $( submeta_info ).attr('disabled', 'disabled' );
	        }
	    });

	    // Widget category
	    $('#customize-theme-controls').on('click','.dw-focus-category-display-type',function() {
	        var val = $(this).val();
	        if (val == '3cols') {
	            $('.categories_3cols').removeClass('hide');
	        } else {
	            $('.categories_3cols').addClass('hide');
	        }
	    });

	    // Set cookie 
	    function setCookie(c_name,value,exdays) {
	        var exdate=new Date();
	        exdate.setDate(exdate.getDate() + exdays);
	        var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
	        document.cookie=c_name + "=" + c_value;
	    }

	    $('.select-layout-box label').on('click',function(event){
	        var t = $(this);
	        t.closest('.select-layout-box').find('label').each(function(){
	            $(this).removeClass('active');
	        });
	        if( $(this).data('option') == 'category-layout' ) {
	            setCookie('cat_listing',$(this).data('type'),365);
	        }
	        t.addClass('active');
	    });
	});
})(jQuery);