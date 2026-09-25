(function() {
	'use strict';

	if(!!window.JCNewsListBlockServices)
		return;

	window.JCNewsListBlockServices = function(params) {
		this.container = BX(params.container);
		
		BX.ready(BX.delegate(this.adjustBlockServices, this));
		BX.bind(window, 'resize', BX.proxy(this.adjustBlockServices, this));
	};

	window.JCNewsListBlockServices.prototype =	{		
		adjustBlockServices: function() {			
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