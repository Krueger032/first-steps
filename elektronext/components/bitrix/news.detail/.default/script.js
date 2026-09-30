(function() {
	'use strict';

	if(!!window.JCNewsDetail)
		return;

	window.JCNewsDetail = function(arParams) {
		this.visual = {
			ID: ''
		};

		this.obItem = null;
		
		this.errorCode = 0;

		if(typeof arParams === 'object') {
			this.visual = arParams.VISUAL;
			
			BX.ready(BX.delegate(this.init, this));
		}
	};

	window.JCNewsDetail.prototype =	{
		init: function() {
			this.obItem = BX(this.visual.ID);
			if(!this.obItem)
				this.errorCode = -1;

			if(this.errorCode === 0) {
				this.obGallery = this.obItem.querySelector('.news-detail-gallery-items');
				if(this.obGallery) {
					var galleryItems = this.obGallery.querySelectorAll('.fancyimage');
					if(!!galleryItems) {
						$(galleryItems).fancybox({
							helpers: {
								title: {
									type: 'inside',
									position: 'bottom'
								}
							}
						});
					}

					this.adjustGallery();
					BX.bind(window, 'resize', BX.proxy(this.adjustGallery, this));
				}
			}
		},

		adjustGallery: function() {
			var galleryItemsBg = this.obGallery.querySelectorAll('.news-detail-gallery-item-bg');
			if(!!galleryItemsBg) {
				for(var i in galleryItemsBg) {
					if(galleryItemsBg.hasOwnProperty(i)) {
						BX.remove(galleryItemsBg[i].parentNode);
					}
				}
			}

			var galleryItems = this.obGallery.querySelectorAll('.news-detail-gallery-item'),
				galleryItemsCount = galleryItems.length,
				galleryItemsRowCount = window.innerWidth >= 1043 ? 4 : 2,
				galleryRowsCount = Math.ceil(galleryItemsCount / galleryItemsRowCount),
				galleryItemsBgCount = (galleryItemsRowCount * galleryRowsCount) - galleryItemsCount,
				coeff = 4 / 3;
			
			for(var i = 0; i < galleryItemsBgCount; i++) {			
				this.obGallery.appendChild(BX.create('DIV', {
					props: {className: 'col-xs-6 col-md-3'},
					children: [
						BX.create('DIV', {
							props: {className: 'news-detail-gallery-item news-detail-gallery-item-bg'}
						})
					]
				}));
			}
			
			var galleryItemsAll = this.obGallery.querySelectorAll('.news-detail-gallery-item');
			if(!!galleryItemsAll) {
				for(var i in galleryItemsAll) {
					if(galleryItemsAll.hasOwnProperty(i)) {
						var galleryItemImage = galleryItemsAll[i].querySelector('.news-detail-gallery-item-image');
						if(!!galleryItemImage)
							BX.style(galleryItemImage, 'height', Math.ceil(galleryItemsAll[i].offsetWidth / coeff) + 'px');

						var galleryItemCaption = galleryItemsAll[i].querySelector('.news-detail-gallery-item-caption-wrap');
						if(!!galleryItemCaption)
							BX.style(galleryItemCaption, 'height', Math.ceil(galleryItemsAll[i].offsetWidth / coeff) + 'px');

						if(BX.hasClass(galleryItemsAll[i], 'news-detail-gallery-item-bg'))
							BX.style(galleryItemsAll[i], 'height', Math.ceil(galleryItemsAll[i].offsetWidth / coeff) + 'px');
					}
				}
			}
		}
	}
})();