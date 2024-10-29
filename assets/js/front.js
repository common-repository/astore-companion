jQuery(document).ready(function($) {
		/* woocommerce */

	$('#grid').click(function() {
		$(this).addClass('active');
		$('#list').removeClass('active');
		$.cookie('gridcookie','grid', { path: '/' });
		$('.archive .post-wrap ul.products').fadeOut(300, function() {
			$(this).addClass('grid').removeClass('list').fadeIn(300);
		});
		return false;
	});

	$('#list').click(function() {
		$(this).addClass('active');
		$('#grid').removeClass('active');
		$.cookie('gridcookie','list', { path: '/' });
		$('.archive .post-wrap ul.products').fadeOut(300, function() {
			$(this).removeClass('grid').addClass('list').fadeIn(300);
		});
		return false;
	});

	if ($.cookie('gridcookie')) {
        $('.archive .post-wrap ul.products, #gridlist-toggle').addClass(jQuery.cookie('gridcookie'));
    }

    if ($.cookie('gridcookie') == 'grid') {
        $('.gridlist-toggle #grid').addClass('active');
        $('.gridlist-toggle #list').removeClass('active');
    }

    if ($.cookie('gridcookie') == 'list') {
        $('.gridlist-toggle #list').addClass('active');
        $('.gridlist-toggle #grid').removeClass('active');
    }

	$('#gridlist-toggle a').click(function(event) {
	    event.preventDefault();
	});
	

});