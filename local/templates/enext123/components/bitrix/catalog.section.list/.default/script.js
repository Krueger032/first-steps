(function() {
	'use strict';

	if(!!window.JCCatalogSectionList)
		return;

	window.JCCatalogSectionList = function(params) {
		this.container = BX(params.container);
		
		BX.ready(BX.delegate(this.adjustSections, this));
		BX.bind(window, 'resize', BX.proxy(this.adjustSections, this));
	};

	window.JCCatalogSectionList.prototype =	{		
		adjustSections: function() {
			var sectionItems = this.container.querySelectorAll('.section-item'),
				sectionItemPic,
				coeff = 4 / 3,
				i;
			
			for(i in sectionItems) {
				if(sectionItems.hasOwnProperty(i)) {
					sectionItemPic = sectionItems[i].querySelector('.section-item__pic');
					if(!!sectionItemPic)
						BX.style(sectionItemPic, 'height', Math.ceil(sectionItemPic.offsetWidth / coeff) + 'px');
				}
			}
		}
	}
})();