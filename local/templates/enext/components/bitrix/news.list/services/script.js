(function() {
	'use strict';

	if(!!window.JCNewsListServices)
		return;

	window.JCNewsListServices = function(params) {
		this.container = BX(params.container);
		
		BX.ready(BX.delegate(this.adjustServices, this));
		BX.bind(window, 'resize', BX.proxy(this.adjustServices, this));
	};

	window.JCNewsListServices.prototype =	{		
		adjustServices: function() {			
			var servicesItems = this.container.querySelectorAll('.services-item'),
				servicesItemPic,
				coeff = 4 / 3,
				i;
			
			for(i in servicesItems) {
				if(servicesItems.hasOwnProperty(i)) {
					servicesItemPic = servicesItems[i].querySelector('.services-item__pic');
					if(!!servicesItemPic)
						BX.style(servicesItemPic, 'height', Math.ceil(servicesItemPic.offsetWidth / coeff) + 'px');
				}
			}
		}
	}
})();