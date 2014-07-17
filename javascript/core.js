jQuery(document).ready(function($) {
	var NavBar = $('#NavBar');
	var NavBar2 = $('#NavBar2');
	var NavOffset = NavBar.offset().top;
	$(window).scroll(function () {
		if($(this).scrollTop() > NavOffset) {
			NavBar2.show();
			NavBar.hide();
		}
		else {
			NavBar2.hide();
			NavBar.show();
		}
	});
});
