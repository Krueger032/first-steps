(function() {
	'use strict';

	if(!!window.JCNewsListGallery)
		return;

	window.JCNewsListGallery = function(params) {
		this.container = BX(params.container);

		BX.ready(BX.delegate(this.init, this));
	};

	window.JCNewsListGallery.prototype = {
		init: function() {
			var items = this.container.querySelectorAll('.fancyimage');
			if(!!items) {
				$(items).fancybox({
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
		},

		adjustGallery: function() {			
			var itemsBg = this.container.querySelectorAll('.gallery-item__bg'),
				i;

			for(i in itemsBg) {
				if(itemsBg.hasOwnProperty(i)) {
					BX.remove(itemsBg[i].parentNode);
				}
			}

			var items = this.container.querySelectorAll('.gallery-item'),
				itemsCount = items.length,
				itemsRowCount = window.innerWidth >= 1043 ? 4 : 2,
				rowsCount = Math.ceil(itemsCount / itemsRowCount),
				itemsBgCount = (itemsRowCount * rowsCount) - itemsCount,
				itemsAll,		
				itemImage,
				itemCaption,		
				coeff = 4 / 3,
				j, k,
				pagination = document.querySelector('.pagination');
			
			for(j = 0; j < itemsBgCount; j++) {			
				if(!!pagination) {
					this.container.insertBefore(BX.create('DIV', {
						props: {className: 'col-xs-6 col-md-3'},
						children: [
							BX.create('DIV', {
								props: {className: 'gallery-item gallery-item__bg'}
							})
						]
					}), pagination.parentNode);
				} else {				
					this.container.appendChild(BX.create('DIV', {
						props: {className: 'col-xs-6 col-md-3'},
						children: [
							BX.create('DIV', {
								props: {className: 'gallery-item gallery-item__bg'}
							})
						]
					}));
				}
			}
			
			itemsAll = this.container.querySelectorAll('.gallery-item');	
			for(k in itemsAll) {
				if(itemsAll.hasOwnProperty(k)) {
					itemImage = itemsAll[k].querySelector('.gallery-item__image');
					if(!!itemImage)
						BX.style(itemImage, 'height', Math.ceil(itemsAll[k].offsetWidth / coeff) + 'px');

					itemCaption = itemsAll[k].querySelector('.gallery-item__caption-wrap');
					if(!!itemCaption)
						BX.style(itemCaption, 'height', Math.ceil(itemsAll[k].offsetWidth / coeff) + 'px');

					if(BX.hasClass(itemsAll[k], 'gallery-item__bg'))
						BX.style(itemsAll[k], 'height', Math.ceil(itemsAll[k].offsetWidth / coeff) + 'px');
				}
			}
		}
	}
})();