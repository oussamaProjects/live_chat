/**
*
* L'nk Live Promo Module
*
* @author    Abdelakbir el Ghazouani - By L'nkboot.fr
* @copyright 2016-2020 Abdelakbir el Ghazouani - By L'nkboot.fr (http://www.lnkboot.fr)
* @license   Commercial license see license.txt
* @category  Module
* Support by mail  : contact@lnkboot.fr
*
*/

$(document).ready(function() {
    if (typeof helper_config_tabs != 'undefined' && typeof unique_field_id != 'undefined')
	{
		$.each(helper_config_tabs, function(index) {
			$('#'+unique_field_id+'fieldset_'+index+' .form-wrapper').prepend('<div class="tab-content panel" />');
			$('#'+unique_field_id+'fieldset_'+index+' .form-wrapper').prepend('<ul class="nav nav-tabs" />');
			$.each(helper_config_tabs[index], function(key, value) {
				// Move every form-group into the correct .tab-content > .tab-pane
				$('#'+unique_field_id+'fieldset_'+index+' .tab-content').append('<div id="'+key+'" class="tab-pane" />');
				var elemts = $('#'+unique_field_id+'fieldset_'+index).find("[data-tab-id='" + key + "']");
				$(elemts).appendTo('#'+key);
				// Add the item to the .nav-tabs
				if (elemts.length != 0)
					$('#'+unique_field_id+'fieldset_'+index+' .nav-tabs').append('<li class="lp_'+key+'"><a href="#'+key+'" data-toggle="tab">'+value+'</a></li>');
			});
			// Activate the first tab
			$('#'+unique_field_id+'fieldset_'+index+' .tab-content div').first().addClass('active');
			$('#'+unique_field_id+'fieldset_'+index+' .nav-tabs li').first().addClass('active');
		});
    }
    $('input[name="LP_range_cart_number"]').change()

    $(document).on('change', 'input[name="LP_range_order_number"]', function(e){
        e.preventDefault();
        if($(this).val() != 'custom'){
            $('.lp_order_range_date > .form-group').addClass('d-none');
        }else{
            $('.lp_order_range_date > .form-group').removeClass('d-none');
        }
        return true;
    });
    $(document).on('change', 'input[name="LP_range_cart_number"]', function(e){
        e.preventDefault();
        if($(this).val() != 'custom'){
            $('.lp_cart_range_date > .form-group').addClass('d-none');
        }else{
            $('.lp_cart_range_date > .form-group').removeClass('d-none');
        }
        return true;
    });
});
