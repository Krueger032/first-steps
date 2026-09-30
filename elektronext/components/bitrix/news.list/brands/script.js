(function(window) {
	'use strict';

	if(!!window.JCNewsListBrandsComponent)
		return;

	
	window.JCNewsListBrandsComponent = function(params) {
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
			this.showMoreButtonContainer = document.querySelector('[data-entity="brands-show-more-container"]');
			BX.bind(this.showMoreButton, 'click', BX.proxy(this.showMore, this));
		}
		
		this.countriesLinks = document.body.querySelector('.brands-countries-links');
		if(!!this.countriesLinks)
			BX.ready(BX.delegate(this.initCountriesLinks, this));
	};

	window.JCNewsListBrandsComponent.prototype = {
		checkButton: function() {
			if(this.showMoreButton) {
				if(this.navParams.NavPageNomer == this.navParams.NavPageCount) {
					BX.remove(this.showMoreButtonContainer);
				} else {
					BX.insertAfter(this.showMoreButtonContainer, this.container);;
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
				this.showMoreButton.innerHTML = BX.message('BRANDS_LOADING');
			}
		},
		
		showMore: function() {
			if(this.navParams.NavPageNomer < this.navParams.NavPageCount) {
				var data = {};
				data['action'] = 'showMoreBrands';
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
				
			var countryLinks = !!this.countriesLinks && this.countriesLinks.querySelectorAll('.brands-country-link');
			if(!!countryLinks) {
				for(var i in countryLinks) {
					if(countryLinks.hasOwnProperty(i) && BX.type.isDomNode(countryLinks[i])) {
						if(BX.hasClass(countryLinks[i], 'active'))
							defaultData.countryId = countryLinks[i].getAttribute('data-country-id');
					}
				}
			}
			
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
				this.processPagination(result.pagination);
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
		},

		processPagination: function(paginationHtml) {
			if(!paginationHtml)
				return;

			var pagination = document.querySelector('[data-pagination-num="' + this.navParams.NavNum + '"]');
			if(!!pagination)
				pagination.innerHTML = paginationHtml;
			
		},

		initCountriesLinks: function() {
			var countryLinks = this.countriesLinks.querySelectorAll('.brands-country-link'),
				haveActive = false;

			if(!!countryLinks) {
				for(var i in countryLinks) {
					if(countryLinks.hasOwnProperty(i) && BX.type.isDomNode(countryLinks[i])) {
						BX.bind(countryLinks[i], 'click', BX.proxy(this.changeCountryLink, this));

						if(!haveActive) {
							BX.addClass(countryLinks[i], 'active');
							haveActive = true;
						} else {
							BX.removeClass(countryLinks[i], 'active');
						}
					}
				}
			}
		},

		changeCountryLink: function(event) {
			BX.PreventDefault(event);

			var countryId = BX.proxy_context && BX.proxy_context.getAttribute('data-country-id');			
			if(!BX.hasClass(BX.proxy_context, 'active') && countryId) {
				var itemsContainer = document.body.querySelector('.brands-items-container');
				if(!!itemsContainer) {
					itemsContainer.style.opacity = 0.2;
					BX.ajax({
						url: this.templatePath + '/ajax.php' + (document.location.href.indexOf('clear_cache=Y') !== -1 ? '?clear_cache=Y' : ''),
						method: 'POST',
						dataType: 'json',
						timeout: 60,
						data: {
							'action': 'changeCountryLink',
							'requestUri': window.location.pathname,
							'siteId': this.siteId,
							'template': this.template,
							'parameters': this.parameters,
							'countryId': countryId
						},
						onsuccess: BX.delegate(function(result) {
							if(!result || !result.JS)
								return;

							BX.ajax.processScripts(
								BX.processHTML(result.JS).SCRIPT,
								false,
								BX.delegate(function() {						
									BX.cleanNode(itemsContainer);

									if(result.items) {
										var processed = BX.processHTML(result.items, false),
											temporaryNode = BX.create('DIV');
										
										temporaryNode.innerHTML = processed.HTML;

										var items = temporaryNode.querySelector('.brands-items');
										if(!!items) {
											if(result.imgWebp) {
												var srcList = {},
													images = items.querySelectorAll('img');
												
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

												if(Object.keys(srcList).length > 0)
													convertImgToWebp(srcList);
											}
											
											itemsContainer.appendChild(items);

											if(result.imgLazyLoad)
												imgLazyLoad();
										
											this.container = items;
										}

										BX.ajax.processScripts(processed.SCRIPT);
									}
									
									if(result.showMore) {
										var temporaryNode = BX.create('DIV');
										temporaryNode.innerHTML = result.showMore;
										
										var showMoreContainer = temporaryNode.querySelector('.brands-more');
										if(!!showMoreContainer) {
											itemsContainer.appendChild(showMoreContainer);
										
											this.showMoreButtonContainer = showMoreContainer;
											this.showMoreButton = showMoreContainer.querySelector('.btn-more');
											this.showMoreButtonMessage = this.showMoreButton.innerHTML;
											BX.bind(this.showMoreButton, 'click', BX.proxy(this.showMore, this));
										}
									}
									
									if(result.pagination) {
										var temporaryNode = BX.create('DIV');
										temporaryNode.innerHTML = result.pagination;

										var pagination = temporaryNode.querySelector('.brands-pagination');
										if(!!pagination) {
											if(countryId == 0)
												itemsContainer.appendChild(pagination);
										
											this.navParams = {
												NavNum: pagination.getAttribute('data-pagination-num'),
												NavPageNomer: parseInt(pagination.getAttribute('data-pagination-page-nomer')),
												NavPageCount: parseInt(pagination.getAttribute('data-pagination-page-count'))
											};
										}
									}

									new BX.easing({
										duration: 2000,
										start: {opacity: 20},
										finish: {opacity: 100},
										transition: BX.easing.makeEaseOut(BX.easing.transitions.quad),
										step: function(state) {
											itemsContainer.style.opacity = state.opacity / 100;
										},
										complete: function() {
											itemsContainer.removeAttribute('style');
										}
									}).animate();
								}, this)
							);
						}, this)
					});
				}
				
				var countryLinks = this.countriesLinks.querySelectorAll('.brands-country-link');
				if(!!countryLinks) {
					for(var i in countryLinks) {
						if(countryLinks.hasOwnProperty(i) && BX.type.isDomNode(countryLinks[i])) {
							if(countryLinks[i].getAttribute('data-country-id') === countryId) {
								BX.addClass(countryLinks[i], 'active');
							} else {
								BX.removeClass(countryLinks[i], 'active');
							}
						}
					}
				}
			}
		}
	}
})(window);