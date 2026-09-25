BX.ready(function() {
	var tabs = document.querySelector('.spsp-tabs');
	if(!!tabs) {
		BX.addClass(tabs, 'owl-carousel');
		$(tabs).owlCarousel({								
			autoWidth: true,
			nav: true,
			navText: ['<i class=\"icon-arrow-left\"></i>', '<i class=\"icon-arrow-right\"></i>'],
			navContainer: '.spsp-tabs-scroll',
			dots: false,			
		});
	}
});