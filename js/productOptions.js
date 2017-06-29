var $j = jQuery.noConflict();

$j(document).ready(function () {
    $j('.product-options .price-notice').each(function () {

        var optionPrice = $j(this).find('.price').html();

        if(optionPrice == '£0.00') {
            $j(this).hide();
        }

    });
});