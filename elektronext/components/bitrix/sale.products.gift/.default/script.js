(function() {
	'use strict';

	if (!!window.JCSaleProductsGiftComponent)
		return;

	window.JCSaleProductsGiftComponent = function(params) {
		this.formPosting = false;		
		this.siteId = params.siteId || '';
		this.template = params.template || '';
		this.componentPath = params.componentPath || '';
		this.parameters = params.parameters || '';

		this.container = document.querySelector('[data-entity="' + params.container + '"]');
		this.currentProductId = params.currentProductId;

		if(params.initiallyShowHeader) {
			BX.ready(BX.delegate(this.showHeader, this));
		}

		if(params.deferredLoad) {
			BX.ready(BX.delegate(this.deferredLoad, this));
		}

		BX.addCustomEvent(
			'onCatalogStoreProductChange',
			BX.delegate(function(offerId) {
				offerId = parseInt(offerId);

				if(this.currentProductId === offerId) {
					return;
				}

				this.currentProductId = offerId;
				this.offerChangedEvent();
			}, this)
		);
	};

	window.JCSaleProductsGiftComponent.prototype = {
		offerChangedEvent: function() {
			this.sendRequest({action: 'deferredLoad', offerId: this.currentProductId});
		},

		deferredLoad: function() {
			this.sendRequest({action: 'deferredLoad'});
		},

		sendRequest: function(data) {
			var defaultData = {
				siteId: this.siteId,
				template: this.template,
				parameters: this.parameters
			};

			BX.ajax({
				url: this.componentPath + '/ajax.php' + (document.location.href.indexOf('clear_cache=Y') !== -1 ? '?clear_cache=Y' : ''),
				method: 'POST',
				dataType: 'json',
				timeout: 60,
				data: BX.merge(defaultData, data),
				onsuccess: BX.delegate(function(result) {
					if(!result || !result.JS) {
						this.hideHeader();
						BX.cleanNode(this.container);
						return;
					}

					BX.ajax.processScripts(
						BX.processHTML(result.JS).SCRIPT,
						false,
						BX.delegate(function() {
							this.showAction(result, data);
						}, this)
					);
				}, this)
			});
		},

		showAction: function(result, data) {
			if(!data)
				return;

			switch(data.action) {
				case 'deferredLoad':
					this.processDeferredLoadAction(result);
					break;
			}
		},

		processDeferredLoadAction: function(result) {
			if(!result)
				return;
			
			this.processItems(result.items, result.imgLazyLoad, result.imgWebp);
		},
			
		processItems: function(itemsHtml, lazyLoadImg, webpImg) {
			if(!itemsHtml)
				return;

			var processed = BX.processHTML(itemsHtml, false),
				temporaryNode = BX.create('DIV'),
				items, origRows, srcList = {};

			temporaryNode.innerHTML = processed.HTML;

			origRows = this.container.querySelectorAll('[data-entity="item-col"]');
			if(origRows.length) {
				BX.cleanNode(this.container);
				this.showHeader(false);
			} else {
				this.showHeader(true);
			}

			items = temporaryNode.querySelectorAll('[data-entity="item-col"]');
			for(var k in items) {
				if(items.hasOwnProperty(k)) {
					if(webpImg) {
						var images = items[k].querySelectorAll('img');
						if(!!images) {
							for(var i in images) {
								if(images.hasOwnProperty(i)) {
									var imageDataLazyloadSrc = images[i].getAttribute('data-lazyload-src'),
										imageSrc = images[i].getAttribute('src');

									if(!!imageDataLazyloadSrc && imageDataLazyloadSrc.substr(0, 4) !== 'http' && imageDataLazyloadSrc.substr(0, 11) !== 'data:image/' && imageDataLazyloadSrc.indexOf('.webp') == -1) {
										srcList[imageDataLazyloadSrc] = imageDataLazyloadSrc;
									} else if(!!imageSrc && imageSrc.substr(0, 4) !== 'http' && imageSrc.substr(0, 11) !== 'data:image/' && imageSrc.indexOf('.webp') == -1) {
										srcList[imageSrc] = imageSrc;
									}
								}
							}
						}
					}

					items[k].style.opacity = 0;
					this.container.appendChild(items[k]);
				}
			}

			if(webpImg && Object.keys(srcList).length > 0)
				convertImgToWebp(srcList);

			if(lazyLoadImg)
				imgLazyLoad();

			new BX.easing({
				duration: 2000,
				start: {opacity: 0},
				finish: {opacity: 100},
				transition: BX.easing.makeEaseOut(BX.easing.transitions.quad),
				step: function(state) {
					for(var k in items) {
						if(items.hasOwnProperty(k)) {
							items[k].style.opacity = state.opacity / 100;
						}
					}
				},
				complete: function() {
					for(var k in items) {
						if(items.hasOwnProperty(k)) {
							items[k].removeAttribute('style');
						}
					}
				}
			}).animate();
			
			BX.ajax.processScripts(processed.SCRIPT);
		},

		showHeader: function(animate) {
			var parentNode = BX.findParent(this.container, {attr: {'data-entity': 'parent-container'}});
			if(!!parentNode) {
				parentNode.removeAttribute('style');

				var header = parentNode.querySelector('[data-entity="header"]');
				if(!!header && header.getAttribute('data-showed') != 'true') {
					if(animate) {
						header.style.display = '';
						new BX.easing({
							duration: 2000,
							start: {opacity: 0},
							finish: {opacity: 100},
							transition: BX.easing.makeEaseOut(BX.easing.transitions.quad),
							step: function(state){
								header.style.opacity = state.opacity / 100;
							},
							complete: function(){
								header.removeAttribute('style');
								header.setAttribute('data-showed', 'true');
							}
						}).animate();
					} else {
						header.removeAttribute('style');
						header.setAttribute('data-showed', 'true');
					}
				}
			}
		},

		hideHeader: function() {
			var parentNode = BX.findParent(this.container, {attr: {'data-entity': 'parent-container'}});
			if(!!parentNode) {
				parentNode.style.display = 'none';

				var header = parentNode.querySelector('[data-entity="header"]');
				if(header) {
					if(this.animation) {
						this.animation.stop();
					}

					header.style.display = 'none';
					header.style.opacity = 0;
					header.setAttribute('data-showed', 'false');
				}
			}
		}
	}
})();