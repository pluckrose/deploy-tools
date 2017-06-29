function eqHeight(parent, child) {

    jQuery(parent).each(function(){

    	var heights = jQuery(this).find(child).map(function ()
        {
            return jQuery(this).height();
        }).get(),

        maxHeight = Math.max.apply(null, heights);

        jQuery(this).find(child).height(maxHeight+'px');

    });

}

jQuery(document).ready(function() {



});