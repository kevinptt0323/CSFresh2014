jQuery(document).ready(function($) {
	var NavBar = $('#NavBar');
	var NavOffset = NavBar.offset().top;
	$(window).scroll(function () {
		if($(this).scrollTop() > NavOffset) {
			NavBar.addClass('Float');
		}
		else {
			NavBar.removeClass('Float').children('.item').removeClass('active');
		}
	});
	$('.Nav > a').click(function() {
		var index = $(this).parent().children('a').index(this);
	});
});
