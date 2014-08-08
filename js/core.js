var staff =
{
	data : "",
	init : function() {
		$.getJSON("js/staff.json", function(json) {
			staff.data = json;
		}).done(function() { staff.output(); });
	},
	output : function() {
		$page = $('#staff').children('.people');
		for(i=0; i<this.data.length; i++) {
			for(j=0; j<this.data[i].length; j++) {
				dir = 'data/' + (i+1) + '/' + this.data[i][j].name;
				label = '<div class="ui top left attached ' +  (this.data[i][j].type==1?'teal':'red') + ' label">' + this.data[i][j].name + '</div>';
				column = '<div class="ui two column relaxed grid group"><div class="column photo"><a href="' + dir + '.jpg" data-lightbox="group' + (i+1) + '" data-title="' + this.data[i][j].name + '"><img src="' + dir + '.jpg" /></a></div><div class="column description"></div></div>'
				$page.eq(i).append('<div class="ui segment">' + label + column + '</div>')
				$('.description').last().load(dir+'.txt');
			}
		}
	}
};
jQuery(document).ready(function($) {
	staff.init();
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
	$('.popup').popup({
		transition: "vertical flip"
	});
});
