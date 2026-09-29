(function(window) {
	'use strict';

	if(!window.JCNewsListReviewsComponent) {	
		window.JCNewsListReviewsComponent = function(params) {
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
			
			this.parentContainer = BX(params.parentContainer);
			this.container = this.parentContainer.querySelector("[data-entity='container-" + this.navParams.NavNum + "']");
			this.showMoreButton = null;
			this.showMoreButtonMessage = null;
			this.showMoreButtonContainer = null;		

			if(params.lazyLoad) {
				this.showMoreButton = this.parentContainer.querySelector('[data-use="show-more-' + this.navParams.NavNum + '"]');
				this.showMoreButtonMessage = this.showMoreButton.innerHTML;
				this.showMoreButtonContainer = this.parentContainer.querySelector('[data-entity="reviews-show-more-container"]');
				BX.bind(this.showMoreButton, 'click', BX.proxy(this.showMore, this));
			}
			
			this.evaluationStars = this.parentContainer.querySelector('.reviews-evaluation-stars');			
			if(!!this.evaluationStars)
				BX.ready(BX.delegate(this.initEvaluationStars, this));
			
			this.addReviewButton = this.parentContainer.querySelector('[data-entity="addReview"]');
			if(!!this.addReviewButton)
				BX.bind(this.addReviewButton, 'click', BX.proxy(this.addReview, this));
			
			this.ratingList = this.parentContainer.querySelector('.reviews-general-stats-rating-list');
			if(!!this.ratingList)
				BX.ready(BX.delegate(this.initRatingList, this));

			BX.addCustomEvent(this, 'addReviewRequest', BX.proxy(this.addReviewRequest, this));
		};

		window.JCNewsListReviewsComponent.prototype = {
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
					this.showMoreButton.innerHTML = BX.message('REVIEWS_LOADING');
				}
			},
			
			showMore: function() {
				if(this.navParams.NavPageNomer < this.navParams.NavPageCount) {
					var data = {};
					data['action'] = 'showMoreReviews';
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
					
				var ratingItems = !!this.ratingList && this.ratingList.querySelectorAll('.reviews-general-stats-rating-item-container');
				if(!!ratingItems) {
					for(var i in ratingItems) {
						if(ratingItems.hasOwnProperty(i) && BX.type.isDomNode(ratingItems[i])) {
							if(BX.hasClass(ratingItems[i], 'active'))
								defaultData.ratingId = ratingItems[i].getAttribute('data-rating-id');
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
					this.processItems(result.items);				
					this.checkButton();
				}
			},

			processItems: function(itemsHtml, position) {
				if(!itemsHtml)
					return;

				var processed = BX.processHTML(itemsHtml, false),
					temporaryNode = BX.create('DIV'),
					items;

				temporaryNode.innerHTML = processed.HTML;
				items = temporaryNode.querySelectorAll('[data-entity="item"]');

				if(items.length) {
					for(var k in items) {
						if(items.hasOwnProperty(k)) {
							items[k].style.opacity = 0;
							this.container.appendChild(items[k]);
						}
					}
					
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

			initEvaluationStars: function() {
				var evaluationStar = this.evaluationStars.querySelectorAll('.reviews-evaluation-star'),
					evaluationVal = this.parentContainer.querySelector('.reviews-evaluation-val');
				
				if(!!evaluationStar) {				
					BX.bind(this.evaluationStars, 'click', BX.delegate(function(e) {
						var target = e.target;
						if(BX.hasClass(target, 'reviews-evaluation-star')) {
							for(var i in evaluationStar) {
								if(evaluationStar.hasOwnProperty(i) && BX.type.isDomNode(evaluationStar[i])) {
									BX.removeClass(evaluationStar[i], 'reviews-evaluation-star-current');
								}
							}
							
							BX.addClass(target, 'reviews-evaluation-star-current');
							
							this.addReview(e);
						}
					}, this));
					
					BX.bind(this.evaluationStars, 'mouseover', BX.delegate(function(e) {
						var target = e.target;
						if(BX.hasClass(target, 'reviews-evaluation-star')) {
							if(!!evaluationVal)
								evaluationVal.innerHTML = target.getAttribute('data-value');

							for(var i in evaluationStar) {
								if(evaluationStar.hasOwnProperty(i) && BX.type.isDomNode(evaluationStar[i])) {
									BX.removeClass(evaluationStar[i], 'reviews-evaluation-star-active');
								}
							}
							
							BX.addClass(target, 'reviews-evaluation-star-active');
							
							for(var i in evaluationStar) {
								if(evaluationStar.hasOwnProperty(i) && BX.type.isDomNode(evaluationStar[i])) {
									if(BX.hasClass(evaluationStar[i], 'reviews-evaluation-star-active'))
										break;
									else
										BX.addClass(evaluationStar[i], 'reviews-evaluation-star-active');
								}
							}
						}
					}, this));
				
					BX.bind(this.evaluationStars, 'mouseout', BX.delegate(function() {
						if(!!evaluationVal)
							evaluationVal.innerHTML = BX.message('REVIEWS_EVALUATION_VALUE');
						
						for(var i in evaluationStar) {
							if(evaluationStar.hasOwnProperty(i) && BX.type.isDomNode(evaluationStar[i])) {
								BX.addClass(evaluationStar[i], 'reviews-evaluation-star-active');
							}
						}
						
						for(var i = Object.keys(evaluationStar).length; i >= 0; i--) {
							if(evaluationStar.hasOwnProperty(i) && BX.type.isDomNode(evaluationStar[i])) {
								if(BX.hasClass(evaluationStar[i], 'reviews-evaluation-star-current')) {
									if(!!evaluationVal)
										evaluationVal.innerHTML = evaluationStar[i].getAttribute('data-value');
									break;
								} else {
									BX.removeClass(evaluationStar[i], 'reviews-evaluation-star-active');
								}
							}
						}
					}, this));
				}
			},

			addReviewRequest: function(target, sPanelContent) {
				var data = {
					action: 'addReviewRequest',
					parameters: this.parameters
				};

				if(BX.hasClass(target, 'reviews-evaluation-stars')) {
					var evaluationStar = target.querySelectorAll('.reviews-evaluation-star');
					if(!!evaluationStar) {
						for(var i in evaluationStar) {
							if(evaluationStar.hasOwnProperty(i) && BX.type.isDomNode(evaluationStar[i])) {
								if(BX.hasClass(evaluationStar[i], 'reviews-evaluation-star-current'))
									data.ratingId = evaluationStar[i].getAttribute('data-rating-id');
								
								BX.adjust(evaluationStar[i], {
									props: {
										className: 'icon-star-s reviews-evaluation-star'
									}
								});
							}
						}
					}
				}
				
				BX.ajax({
					url: this.templatePath + '/ajax.php',
					method: 'POST',
					dataType: 'json',
					timeout: 60,
					data: data,
					onsuccess: BX.delegate(function(result) {
						if(!result.content || !result.JS) {
							BX.cleanNode(sPanelContent);
							
							sPanelContent.appendChild(BX.create('DIV', {
								props: {
									className: 'slide-panel__form'
								},
								children: [
									BX.create('DIV', {							
										props: {
											className: 'alert alert-error'
										},
										html: BX.message('SLIDE_PANEL_UNDEFINED_ERROR')
									})
								]
							}));
						} else {
							BX.ajax.processScripts(
								BX.processHTML(result.JS).SCRIPT,
								false,
								BX.delegate(function() {
									var processed = BX.processHTML(result.content);
									
									sPanelContent.innerHTML = processed.HTML;
									
									BX.ajax.processScripts(processed.SCRIPT);
								}, this)
							);
						}
						
						$(sPanelContent).scrollbar();
					}, this)
				});
			},

			addReview: function(e) {
				this.sPanel = document.body.querySelector('.slide-panel');
				if(!!this.sPanel) {
					this.sPanel.appendChild(
						BX.create('DIV', {
							props: {
								className: 'slide-panel__title-wrap'
							},
							children: [
								BX.create('I', {
									props: {
										className: 'icon-comment'
									}
								}),						
								BX.create('SPAN', {
									props: {
										className: 'slide-panel__title'
									},
									html: BX.message('REVIEWS_YOUR_REVIEW')
								}),
								BX.create('SPAN', {
									props: {
										className: 'slide-panel__close'
									},
									children: [
										BX.create('I', {
											props: {
												className: 'icon-close'
											}
										})
									]
								})
							]
						})
					);

					this.sPanel.appendChild(
						BX.create('DIV', {
							props: {
								className: 'slide-panel__content scrollbar-inner'
							},
							children: [
								BX.create('DIV', {
									props: {
										className: 'slide-panel__loader'
									},
									html: '<div><span></span></div>'
								})
							]
						})
					);

					var sPanelContent = this.sPanel.querySelector('.slide-panel__content');
					if(!!sPanelContent)
						BX.onCustomEvent(this, 'addReviewRequest', [BX.proxy_context, sPanelContent]);

					var scrollWidth = window.innerWidth - document.body.clientWidth;
					if(scrollWidth > 0) {
						BX.style(document.body, 'padding-right', scrollWidth + 'px');
						
						var topPanel = document.body.querySelector('.top-panel');
						if(!!topPanel) {
							if(BX.hasClass(topPanel, 'fixed'))
								BX.style(topPanel, 'padding-right', scrollWidth + 'px');
							
							var topPanelThead = topPanel.querySelector('.top-panel__thead');
							if(!!topPanelThead && BX.hasClass(topPanelThead, 'fixed'))
								BX.style(topPanelThead, 'padding-right', scrollWidth + 'px');
							
							var topPanelTfoot = topPanel.querySelector('.top-panel__tfoot');
							if(!!topPanelTfoot && BX.hasClass(topPanelTfoot, 'fixed'))
								BX.style(topPanelTfoot, 'padding-right', scrollWidth + 'px');
						}

						var tabsPanel = document.body.querySelector('[data-entity="tabs"]');
						if(!!tabsPanel && BX.hasClass(tabsPanel, 'fixed'))
							BX.style(tabsPanel, 'padding-right', scrollWidth + 'px');

						var catalogCompareList = document.querySelector('.catalog-compare-list');
						if(!!catalogCompareList && BX.hasClass(catalogCompareList, 'active'))
							BX.style(catalogCompareList, 'margin-left', '-' + scrollWidth/2 + 'px');
					}

					var scrollTop = BX.GetWindowScrollPos().scrollTop;
					if(!!scrollTop && scrollTop > 0)
						BX.style(document.body, 'top', '-' + scrollTop + 'px');

					BX.addClass(document.body, 'slide-panel-active')
					BX.addClass(this.sPanel, 'active');

					document.body.appendChild(
						BX.create('DIV', {
							props: {
								className: 'modal-backdrop slide-panel__backdrop fadeInBig'
							}
						})
					);

					e.stopPropagation();
				}
			},
				
			initRatingList: function() {
				var ratingItems = this.ratingList.querySelectorAll('.reviews-general-stats-rating-item-container');
				if(!!ratingItems) {
					for(var i in ratingItems) {
						if(ratingItems.hasOwnProperty(i) && BX.type.isDomNode(ratingItems[i]))
							if(!BX.hasClass(ratingItems[i], 'disabled'))
								BX.bind(ratingItems[i], 'click', BX.proxy(this.changeRatingItem, this));
					}
				}
			},
			
			changeRatingItem: function(event) {
				BX.PreventDefault(event);

				var ratingId = BX.proxy_context && BX.proxy_context.getAttribute('data-rating-id');			
				if(!BX.hasClass(BX.proxy_context, 'active') && ratingId) {
					var itemsContainer = this.parentContainer.querySelector('.reviews-items-container');
					if(!!itemsContainer) {
						itemsContainer.style.opacity = 0.2;
						BX.ajax({
							url: this.templatePath + '/ajax.php' + (document.location.href.indexOf('clear_cache=Y') !== -1 ? '?clear_cache=Y' : ''),
							method: 'POST',
							dataType: 'json',
							timeout: 60,
							data: {
								'action': 'changeRatingItem',
								'requestUri': window.location.pathname,
								'siteId': this.siteId,
								'template': this.template,
								'parameters': this.parameters,
								'ratingId': ratingId
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

											var items = temporaryNode.querySelector('.reviews-items');
											if(!!items) {
												itemsContainer.appendChild(items);
											
												this.container = items;
											}

											BX.ajax.processScripts(processed.SCRIPT);
										}
										
										if(result.showMore) {
											var temporaryNode = BX.create('DIV');
											temporaryNode.innerHTML = result.showMore;
											
											var showMoreContainer = temporaryNode.querySelector('.reviews-more');
											if(!!showMoreContainer) {
												itemsContainer.appendChild(showMoreContainer);
											
												this.showMoreButtonContainer = showMoreContainer;
												this.showMoreButton = showMoreContainer.querySelector('.btn-more');
												this.showMoreButtonMessage = this.showMoreButton.innerHTML;
												BX.bind(this.showMoreButton, 'click', BX.proxy(this.showMore, this));
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

					var ratingItems = this.ratingList.querySelectorAll('.reviews-general-stats-rating-item-container');
					if(!!ratingItems) {
						for(var i in ratingItems) {
							if(ratingItems.hasOwnProperty(i) && BX.type.isDomNode(ratingItems[i])) {
								if(ratingItems[i].getAttribute('data-rating-id') === ratingId) {
									BX.addClass(ratingItems[i], 'active');
								} else {
									BX.removeClass(ratingItems[i], 'active');
								}
							}
						}
					}
				}
			}
		}
	}

	if(!window.JCNewsListReviews) {
		window.JCNewsListReviews = function(arParams) {		
			this.visual = {
				ID: ''
			};
			
			this.item = {								
				iblockId: 0,
				id: 0
			};
			
			this.obItem = null;
			this.obLike = null;
			this.obLikes = null;
			
			this.errorCode = 0;
			
			if(typeof arParams === 'object') {
				this.visual = arParams.VISUAL;
				this.item.iblockId = arParams.ITEM.IBLOCK_ID;
				this.item.id = arParams.ITEM.ID;

				BX.ready(BX.delegate(this.init, this));
			}
		};

		window.JCNewsListReviews.prototype = {
			init: function() {
				this.obItem = BX(this.visual.ID);
				if(!this.obItem) {
					this.errorCode = -1;
				}

				if(this.errorCode === 0) {
					var itemPhotos = this.obItem.querySelectorAll('.fancyimage');
					if(!!itemPhotos) {
						$(itemPhotos).fancybox({
							helpers: {
								title: {
									type: 'inside',
									position: 'bottom'
								}
							}
						});
					}

					this.checkLiked();

					this.obLike = this.obItem.querySelector('[data-entity="like"]');
					if(!!this.obLike)
						BX.bind(this.obLike, 'click', BX.proxy(this.like, this));

					this.obLikes = this.obItem.querySelector('.reviews-item-likes');
				}
			},

			checkLiked: function() {
				BX.ajax({
					url: BX.message('REVIEWS_TEMPLATE_PATH') + '/ajax.php',
					method: 'POST',
					dataType: 'json',
					timeout: 60,
					data: {							
						action: 'checkLiked',
						iblockId: this.item.iblockId,
						reviewId: this.item.id
					},
					onsuccess: BX.delegate(function(result) {
						this.setLiked(!!result.liked ? true : false);
					}, this)
				});
			},

			like: function() {
				var isLiked = BX.hasClass(this.obLike, 'reviews-item-liked');
				
				BX.ajax({
					url: BX.message('REVIEWS_TEMPLATE_PATH') + '/ajax.php',
					method: 'POST',
					dataType: 'json',
					timeout: 60,
					data: {							
						action: !isLiked ? 'addLike' : 'deleteLike',
						siteServerName: BX.message('SITE_SERVER_NAME'),
						iblockId: this.item.iblockId,
						reviewId: this.item.id
					},
					onsuccess: BX.delegate(function(result) {
						this.setLiked(!!result.liked ? true : false);
						if(!!result.likes)
							this.setLikes(result.likes);
					}, this)
				});
			},

			setLiked: function(state) {
				if(!this.obLike)
					return;

				state ? BX.addClass(this.obLike, 'reviews-item-liked') : BX.removeClass(this.obLike, 'reviews-item-liked');
				
				var icon = this.obLike.querySelector('[data-entity="like-icon"]');
				if(!!icon) {
					BX.adjust(icon, {
						props: {
							className: state ? 'icon-heart-s' : 'icon-heart-b'
						}
					});
				}
			},

			setLikes: function(state) {
				if(!this.obLikes)
					return;

				if(state == 'plus') {
					this.obLikes.innerHTML = '+' + (parseInt(this.obLikes.innerHTML, 10) + 1);
					if(BX.hasClass(this.obLikes, 'reviews-item-likes-empty'))
						BX.removeClass(this.obLikes, 'reviews-item-likes-empty');
				} else if(state == 'minus') {
					var countLikes = (parseInt(this.obLikes.innerHTML, 10) - 1);
					this.obLikes.innerHTML = (countLikes > 0 ? '+' : '') + countLikes;
					if(countLikes == 0)
						BX.addClass(this.obLikes, 'reviews-item-likes-empty');
				}
			}
		}
	}
})(window);