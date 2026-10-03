(function(window) {
	'use strict';

	if(!window.JCNewsListPromoComponent) {
		window.JCNewsListPromoComponent = function(params) {
			this.container = BX(params.container);
			this.itemsCount = params.itemsCount;

			BX.ready(BX.delegate(this.init, this));
		};

		window.JCNewsListPromoComponent.prototype = {
			init: function() {
				BX.addClass(this.container, 'owl-carousel');
				$(this.container).owlCarousel({
					nav: true,
					navText: ['<div class="owl-prev-icon"><i class="icon-arrow-left"></i></div>', '<div class="owl-next-icon"><i class="icon-arrow-right"></i></div>'],
					navContainer: '.promotions-items-container',
					dots: false,
					responsive: {
						0: {
							items: 1
						},
						992: {
							items: this.itemsCount > 3 ? 3 : this.itemsCount
						}
					}
				});

				var items = this.container.querySelectorAll('.promotions-item');
				for(var i in items) {
					if(items.hasOwnProperty(i) && BX.type.isDomNode(items[i])) {
						var itemTimer = items[i].querySelector('[data-entity="timer"]');
						if(!!itemTimer) {
							var itemActiveToStr = itemTimer.getAttribute('data-active-to'),
								itemActiveTo = eval("(" + itemActiveToStr + ")");
							
							if(Object.keys(itemActiveTo).length > 0) {
								$(itemTimer).countdown({
									until: new Date(itemActiveTo.YYYY, itemActiveTo.MM - 1, itemActiveTo.DD, itemActiveTo.HH ? itemActiveTo.HH : '00', itemActiveTo.MI ? itemActiveTo.MI : '00'),
									padZeroes: true,
									expiryText: BX.message('PROMOTIONS_ITEM_COMPLETED'),
									alwaysExpire: true
								});
							}
						}
					}
				}
			}
		}
	}
})(window);