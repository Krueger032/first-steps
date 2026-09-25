(function(window) {
	'use strict';

	if(!!window.JCNewsListCollectComponent)
		return;
	
	window.JCNewsListCollectComponent = function(params) {
		this.formPosting = false;
		this.siteId = params.siteId || '';
		this.template = params.template || '';
		this.templatePath = params.templatePath || '';		
		this.parameters = params.parameters || '';
		
		if(params.navParams) {
			this.navParams = {
				NavNum: params.navParams.NavNum || 1,
				NavPageNomer: parseInt(params.navParams.NavPageNomer) || 1,
				NavPageCount: parseInt(params.navParams.NavPageCount) || 1
			};
		}
		
		this.container = document.querySelector("[data-entity='container-" + this.navParams.NavNum + "']");
		this.showMoreButton = null;
		this.showMoreButtonMessage = null;
		this.showMoreButtonContainer = null;		

		if(params.lazyLoad) {
			this.showMoreButton = document.querySelector('[data-use="show-more-' + this.navParams.NavNum + '"]');
			this.showMoreButtonMessage = this.showMoreButton.innerHTML;
			this.showMoreButtonContainer = document.querySelector('[data-entity="collections-show-more-container"]');
			BX.bind(this.showMoreButton, 'click', BX.proxy(this.showMore, this));
		}

		if(params.loadOnScroll) {
			BX.bind(window, 'scroll', BX.proxy(this.loadOnScroll, this));
		}
	};

	window.JCNewsListCollectComponent.prototype = {
		checkButton: function() {
			if(this.showMoreButton) {
				if(this.navParams.NavPageNomer == this.navParams.NavPageCount) {
					BX.remove(this.showMoreButtonContainer);
				} else {
					BX.insertAfter(this.showMoreButtonContainer, this.container);
				}
			}
		},

		enableButton: function() {
			if(this.showMoreButton) {
				BX.removeClass(this.showMoreButton, 'disabled');
				this.showMoreButton.innerHTML = this.showMoreButtonMessage;
			}
		},

		disableButton: function() {
			if(this.showMoreButton) {
				BX.addClass(this.showMoreButton, 'disabled');
				this.showMoreButton.innerHTML = BX.message('COLLECTIONS_LOADING');
			}
		},

		loadOnScroll: function() {
			var scrollTop = BX.GetWindowScrollPos().scrollTop,
				containerBottom = BX.pos(this.container).bottom;

			if(scrollTop + window.innerHeight > containerBottom) {
				this.showMore();
			}
		},
		
		showMore: function() {
			if(this.navParams.NavPageNomer < this.navParams.NavPageCount) {
				var data = {};
				data['action'] = 'showMoreCollections';
				data['requestUri'] = window.location.pathname;
				data['PAGEN_' + this.navParams.NavNum] = this.navParams.NavPageNomer + 1;

				if(!this.formPosting) {
					this.formPosting = true;
					this.disableButton();
					this.sendRequest(data);
				}
			}
		},

		sendRequest: function(data) {
			var defaultData = {
				siteId: this.siteId,
				template: this.template,
				parameters: this.parameters
			};

			BX.ajax({
				url: this.templatePath + '/ajax.php' + (document.location.href.indexOf('clear_cache=Y') !== -1 ? '?clear_cache=Y' : ''),
				method: 'POST',
				dataType: 'json',
				timeout: 60,
				data: BX.merge(defaultData, data),
				onsuccess: BX.delegate(function(result) {
					if(!result || !result.JS)
						return;

					BX.ajax.processScripts(
						BX.processHTML(result.JS).SCRIPT,
						false,
						BX.delegate(function() {
							this.processShowMoreAction(result);
						}, this)
					);
				}, this)
			});
		},

		processShowMoreAction: function(result) {
			this.formPosting = false;
			this.enableButton();

			if(result) {
				this.navParams.NavPageNomer++;
				this.processItems(result.items, result.imgLazyLoad, result.imgWebp);				
				this.checkButton();
			}
		},

		processItems: function(itemsHtml, lazyLoadImg, webpImg) {
			if(!itemsHtml)
				return;

			var processed = BX.processHTML(itemsHtml, false),
				temporaryNode = BX.create('DIV'),
				items, srcList = {};

			temporaryNode.innerHTML = processed.HTML;
			items = temporaryNode.querySelectorAll('[data-entity="item"]');

			if(items.length) {
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
			}

			BX.ajax.processScripts(processed.SCRIPT);
		}
	}
})(window);