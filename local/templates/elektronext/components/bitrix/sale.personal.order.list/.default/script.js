(function() {
	'use strict';

	if(!!window.JCSalePersonalOrderList)
		return;

	window.JCSalePersonalOrderList = function(params) {
		this.container = BX(params.container);
		this.hoverTimer = null;

		BX.ready(BX.delegate(this.init, this));
	};

	window.JCSalePersonalOrderList.prototype = {
		init: function() {
			var tabs = this.container.querySelector('.sale-order-tabs-list');
			if(!!tabs) {
				BX.addClass(tabs, 'owl-carousel');
				$(tabs).owlCarousel({								
					autoWidth: true,
					nav: true,
					navText: ['<i class=\"icon-arrow-left\"></i>', '<i class=\"icon-arrow-right\"></i>'],
					navContainer: '.sale-order-tabs-scroll',
					dots: false,			
				});
			}

			var items = this.container.querySelectorAll('.sale-order-item-container');
			if(!!items) {
				for(var i in items) {
					if(items.hasOwnProperty(i) && BX.type.isDomNode(items[i])) {
						BX.bind(items[i], 'mouseenter', BX.proxy(this.hoverOn, this));
						BX.bind(items[i], 'mouseleave', BX.proxy(this.hoverOff, this));
					}
				}
			}
		},

		hoverOn: function(event) {
			var item = event.target;

			clearTimeout(this.hoverTimer);
			item.style.height = getComputedStyle(item).height;
			BX.addClass(item, 'hover');

			BX.PreventDefault(event);
		},

		hoverOff: function(event) {
			var item = event.target;

			BX.removeClass(item, 'hover');
			this.hoverTimer = setTimeout(
				BX.delegate(function() {
					item.style.height = 'auto';
				}, this),
				300
			);

			BX.PreventDefault(event);
		}
	}
})();