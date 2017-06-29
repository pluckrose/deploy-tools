var $j = jQuery.noConflict();
$j(document).ready(function() {
	$j('#nav > li.parent > a').click(function(e) {
		if( $j('html').hasClass('no-touch') || $j(this).hasClass('touched') ) {
			return true;
		}
		$j('#nav > li.parent > a').removeClass('touched');
		e.preventDefault();
		$j(this).addClass('over').addClass('touched');
		return false;
	})
});