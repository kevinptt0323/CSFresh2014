jQuery(document).ready(function($) {
	var $NavBar = $('#NavBar'), NavOffset = $NavBar.offset().top;
	$(window).scroll(function () {
		if($(this).scrollTop() > NavOffset) {
			$NavBar
				.removeClass('item')
				.addClass('Float')
				.addClass('secondary')
				.addClass('vertical');
		}
		else {
			$NavBar
				.addClass('item')
				.removeClass('Float')
				.removeClass('secondary')
				.removeClass('vertical')
				.children('.item')
				.removeClass('active');
		}
	});
	$('.Nav > a').click(function() {
		var index = $(this).parent().children('a').index(this);
	});
});
