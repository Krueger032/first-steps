(function() {
	'use strict';

	if(!!window.JCNewsDetailArticles)
		return;
	
	window.JCNewsDetailArticles = function(arParams) {
		this.config = {
			PARAMS: ''
		};
		
		this.visual = {
			ID: ''
		};
		
		this.item = {
			productsIds: ''
		};

		this.obItem = null;
		
		this.obTabs = null;
		this.obTabsBlock = null;
		this.obTabContainers = null;

		this.sectionsLinksContainer = null;
		
		this.errorCode = 0;
		
		if(typeof arParams === 'object') {
			this.config = arParams.CONFIG;
			this.visual = arParams.VISUAL;
			this.item.productsIds = arParams.ITEM.PRODUCTS_IDS;

			BX.ready(BX.delegate(this.init, this));
		}
	};

	window.JCNewsDetailArticles.prototype = {
		init: function() {
			this.obItem = BX(this.visual.ID);
			if(!this.obItem) {
				this.errorCode = -1;
			}

			if(this.errorCode === 0) {
				this.obTabs = this.obItem.querySelector('.articles-detail-tabs-container');
				this.obTabsBlock = !!this.obTabs && this.obTabs.querySelector('[data-entity="tabs"]');
				this.obTabContainers = this.obItem.querySelector('.articles-detail-tabs-content');
				
				if(!!this.obTabs) {
					this.initTabs();
					
					if(this.obTabsBlock) {
						this.tabsPanelFixed = false;
						this.tabsPanelScrolled = false;
						this.lastScrollTop = 0;
						this.checkTopTabsBlockScroll();
						BX.bind(window, 'scroll', BX.proxy(this.checkTopTabsBlockScroll, this));
						BX.bind(window, 'resize', BX.proxy(this.checkTopTabsBlockResize, this));

						this.checkActiveTabsBlock();
						BX.bind(window, 'scroll', BX.proxy(this.checkActiveTabsBlock, this));
						BX.bind(window, 'resize', BX.proxy(this.checkActiveTabsBlock, this));
					}
				}
				
				this.sectionsLinksContainer = this.obItem.querySelector('.articles-detail-sections-links');
				if(!!this.sectionsLinksContainer) {
					this.sectionsLinksContainerHeight = 92;
					this.sectionsLinks = this.sectionsLinksContainer.querySelectorAll('.articles-detail-section-link');
					if(!!this.sectionsLinks) {
						this.initSectionsLinks();

						this.sectionLinkBtnSpan = BX.create('SPAN');

						this.sectionLinkBtnI = BX.create('I', {
							props: {
								className: 'icon-arrow-down'
							}
						});

						this.sectionLinkBtn = BX.create('DIV', {
							props: {
								className: 'articles-detail-section-link-btn-container'
							},
							children: [
								BX.create('DIV', {
									props: {
										className: 'articles-detail-section-link articles-detail-section-link-btn'
									},
									children: [
										this.sectionLinkBtnSpan,
										this.sectionLinkBtnI
									],
									events: {
										click: BX.proxy(this.showHideSectionsLinks, this)
									}
								})
							]
						});

						this.sectionLinkBtnAdjusted = false;

						this.adjustSectionLinkBtn();
						BX.bind(window, 'resize', BX.proxy(this.adjustSectionLinkBtn, this));
						BX.addCustomEvent(window, 'slideMenu', BX.proxy(this.adjustSectionLinkBtn, this));
					}
				}
			}
		},

		initTabs: function() {
			var tabsList = this.obTabs.querySelector('.articles-detail-tabs-list'),
				tabs = !!tabsList && tabsList.querySelectorAll('[data-entity="tab"]'),
				tabValue, targetTab, haveActive = false;			
			
			if(!!tabs.length > 0) {
				BX.addClass(tabsList, 'owl-carousel');
				$(tabsList).owlCarousel({								
					autoWidth: true,
					nav: true,
					navText: ['<i class=\"icon-arrow-left\"></i>', '<i class=\"icon-arrow-right\"></i>'],
					navContainer: '.articles-detail-tabs-scroll',
					dots: false,			
				});
					
				for(var i in tabs) {
					if(tabs.hasOwnProperty(i) && BX.type.isDomNode(tabs[i])) {
						tabValue = tabs[i].getAttribute('data-value');
						if(tabValue) {
							targetTab = this.obTabContainers.querySelector('[data-value="' + tabValue + '"]');
							if(BX.type.isDomNode(targetTab)) {
								BX.bind(tabs[i], 'click', BX.proxy(this.changeTab, this));

								if(!haveActive) {
									BX.addClass(tabs[i], 'active');
									haveActive = true;
								} else {
									BX.removeClass(tabs[i], 'active');
								}
							}
						}
					}
				}
			}
		},

		checkTopTabsBlockScroll: function() {
			var sMenuInt2 = BX.hasClass(document.body, 'slide-menu-interface-2-0-1-inner'),
				topPanel = document.querySelector('.top-panel'),
				topPanelHeight = 0,
				topPanelThead = !!topPanel && topPanel.querySelector('.top-panel__thead'),
				topPanelTfoot = !!topPanel && topPanel.querySelector('.top-panel__tfoot'),
				tabsPanelContainerTop = BX.pos(this.obTabs).top,
				tabsPanel = this.obTabsBlock,				
				tabsPanelHeight = tabsPanel.offsetHeight,
				scrollTop = BX.GetWindowScrollPos().scrollTop;
			
			if(window.innerWidth < 1043) {
				if(!!topPanelThead && !!BX.hasClass(topPanelThead, 'fixed')) {
					topPanelHeight = topPanelThead.offsetHeight;
					if(!!topPanelTfoot && !!BX.hasClass(topPanelTfoot, 'visible'))
						topPanelHeight += topPanelTfoot.offsetHeight;
				}

				if(scrollTop + topPanelHeight >= tabsPanelContainerTop) {
					if(!this.tabsPanelFixed) {
						this.tabsPanelFixed = true;
						BX.style(this.obTabs, 'height', tabsPanelHeight + 'px');				
						BX.style(tabsPanel, 'top', topPanelHeight + 'px');	
						BX.addClass(tabsPanel, 'fixed');
					} else if(!sMenuInt2){
						if(!this.tabsPanelScrolled && topPanelHeight > 0 && scrollTop < this.lastScrollTop) {
							this.tabsPanelScrolled = true;
							var tabsPanelScrolled = this.tabsPanelScrolled;
							new BX.easing({
								duration: 300,
								start: {top: Math.abs(parseInt(BX.style(tabsPanel, 'top'), 10))},
								finish: {top: topPanelHeight},
								transition: BX.easing.transitions.linear,
								step: function(state) {
									if(!!tabsPanelScrolled)
										BX.style(tabsPanel, 'top', state.top + 'px');								
								}
							}).animate();
						} else if(!!this.tabsPanelScrolled && topPanelHeight > 0 && scrollTop > this.lastScrollTop) {
							this.tabsPanelScrolled = false;
							new BX.easing({
								duration: 300,
								start: {top: Math.abs(parseInt(BX.style(tabsPanel, 'top'), 10))},
								finish: {top: topPanelHeight},
								transition: BX.easing.transitions.linear,
								step: function(state) {
									BX.style(tabsPanel, 'top', state.top + 'px');								
								}
							}).animate();
						}
					}
				} else if(!!this.tabsPanelFixed && (scrollTop + topPanelHeight < tabsPanelContainerTop)) {
					this.tabsPanelFixed = false;
					this.tabsPanelScrolled = false;
					this.obTabs.removeAttribute('style');
					tabsPanel.removeAttribute('style');
					BX.removeClass(tabsPanel, 'fixed');
				}
			} else {
				if(!!topPanel && !!BX.hasClass(topPanel, 'fixed'))
					topPanelHeight = topPanel.offsetHeight;
				
				if(!this.tabsPanelFixed && (scrollTop + topPanelHeight >= tabsPanelContainerTop)) {
					this.tabsPanelFixed = true;
					BX.style(this.obTabs, 'height', tabsPanelHeight + 'px');
					BX.style(tabsPanel, 'top', topPanelHeight + 'px');
					BX.addClass(tabsPanel, 'fixed');
				} else if(!!this.tabsPanelFixed && (scrollTop + topPanelHeight < tabsPanelContainerTop)) {
					this.tabsPanelFixed = false;
					this.obTabs.removeAttribute('style');
					tabsPanel.removeAttribute('style');
					BX.removeClass(tabsPanel, 'fixed');
				}
			}
			this.lastScrollTop = scrollTop;
		},

		checkTopTabsBlockResize: function() {
			if(!!BX.hasClass(this.obTabsBlock, 'fixed')) {
				var topPanel = document.querySelector('.top-panel'),
					topPanelHeight = 0,
					topPanelThead = !!topPanel && topPanel.querySelector('.top-panel__thead'),
					topPanelTfoot = !!topPanel && topPanel.querySelector('.top-panel__tfoot');					
				
				if(window.innerWidth < 1043) {
					if(!!topPanelThead && !!BX.hasClass(topPanelThead, 'fixed')) {
						topPanelHeight = topPanelThead.offsetHeight;
						if(!!topPanelTfoot && !!BX.hasClass(topPanelTfoot, 'visible'))
							topPanelHeight += topPanelTfoot.offsetHeight;
					}
				} else {
					if(!!topPanel && !!BX.hasClass(topPanel, 'fixed'))
						topPanelHeight = topPanel.offsetHeight;
					this.tabsPanelScrolled = false;
				}
				
				BX.style(this.obTabsBlock, 'top', topPanelHeight + 'px');
			}
		},

		checkActiveTabsBlock: function() {
			var topPanel = document.querySelector('.top-panel'),
				topPanelHeight = 0,
				topPanelThead = !!topPanel && topPanel.querySelector('.top-panel__thead'),
				topPanelTfoot = !!topPanel && topPanel.querySelector('.top-panel__tfoot'),
				tabsPanel = this.obTabsBlock,
				tabsPanelHeight = 0,				
				containers = this.obTabContainers.querySelectorAll('[data-entity="tab-container"]'),
				tabs = this.obTabs.querySelectorAll('[data-entity="tab"]'),
				scrollTop = BX.GetWindowScrollPos().scrollTop;

			if(!!containers && !!tabs) {
				if(window.innerWidth < 1043) {
					if(!!topPanelThead && !!BX.hasClass(topPanelThead, 'fixed')) {
						topPanelHeight = topPanelThead.offsetHeight;
						if(!!topPanelTfoot && !!BX.hasClass(topPanelTfoot, 'visible'))
							topPanelHeight += topPanelTfoot.offsetHeight;
					}
				} else {
					if(!!topPanel && !!BX.hasClass(topPanel, 'fixed'))
						topPanelHeight = topPanel.offsetHeight;
				}

				if(!!tabsPanel && !!BX.hasClass(tabsPanel, 'fixed'))
					tabsPanelHeight = tabsPanel.offsetHeight;

				var fullScrollTop = scrollTop + topPanelHeight + tabsPanelHeight;
				
				var containersLength = Object.keys(containers).length;
				for(var i in containers) {
					if(containers.hasOwnProperty(i) && BX.type.isDomNode(containers[i])) {
						var containerValue = containers[i].getAttribute('data-value');
						if(containerValue) {
							if(fullScrollTop >= BX.pos(containers[i]).top && fullScrollTop <= BX.pos(containers[containersLength - 1]).bottom) {
								for(var j in tabs) {
									if(tabs.hasOwnProperty(j) && BX.type.isDomNode(tabs[j])) {
										var tabValue = tabs[j].getAttribute('data-value');
										if(tabValue) {
											if(tabValue === containerValue)
												BX.addClass(tabs[j], 'active');
											else
												BX.removeClass(tabs[j], 'active');
										}
									}
								}
							} else if(fullScrollTop > BX.pos(containers[containersLength - 1]).bottom) {
								for(var j in tabs) {
									if(tabs.hasOwnProperty(j) && BX.type.isDomNode(tabs[j]))
										BX.removeClass(tabs[j], 'active');
								}
							}
						}
					}
				}
			}
		},
		
		changeTab: function(event) {			
			BX.PreventDefault(event);

			BX.unbind(window, 'scroll', BX.proxy(this.checkActiveTabsBlock, this));
			
			var targetTabValue = BX.proxy_context && BX.proxy_context.getAttribute('data-value'),
				containers, tabs;

			if(!!targetTabValue) {				
				containers = this.obTabContainers.querySelectorAll('[data-entity="tab-container"]');
				if(!!containers) {
					for(var i in containers) {
						if(containers.hasOwnProperty(i) && BX.type.isDomNode(containers[i])) {
							if(containers[i].getAttribute('data-value') === targetTabValue) {
								var sMenuInt2 = BX.hasClass(document.body, 'slide-menu-interface-2-0-1-inner'),
									topPanel = document.querySelector('.top-panel'),
									topPanelHeight = 0,
									topPanelThead = !!topPanel && topPanel.querySelector('.top-panel__thead'),
									topPanelTfoot = !!topPanel && topPanel.querySelector('.top-panel__tfoot'),
									tabContainerTop = BX.pos(containers[i]).top,
									scrollTop = BX.GetWindowScrollPos().scrollTop;

								if(window.innerWidth < 1043 && !sMenuInt2) {
									if(!!topPanelThead) {
										topPanelHeight = topPanelThead.offsetHeight;
										if(scrollTop + this.obTabsBlock.offsetHeight + topPanelHeight > tabContainerTop) {
											if(!!topPanelTfoot)
												topPanelHeight += topPanelTfoot.offsetHeight;
										}
									}
								} else if(window.innerWidth >= 1043) {
									if(!!topPanel)
										topPanelHeight = topPanel.offsetHeight;
								}
								
								new BX.easing({
									duration: 500,
									start: {scroll: scrollTop},
									finish: {scroll: tabContainerTop - this.obTabsBlock.offsetHeight - topPanelHeight},
									transition: BX.easing.makeEaseOut(BX.easing.transitions.quint),
									step: BX.delegate(function(state) {
										window.scrollTo(0, state.scroll);
									}, this),
									complete: BX.delegate(function() {
										BX.bind(window, 'scroll', BX.proxy(this.checkActiveTabsBlock, this));
									}, this)
								}).animate();
							}
						}
					}
				}
				
				tabs = this.obTabs.querySelectorAll('[data-entity="tab"]');
				if(!!tabs) {
					for(var i in tabs) {
						if(tabs.hasOwnProperty(i) && BX.type.isDomNode(tabs[i])) {
							if(tabs[i].getAttribute('data-value') === targetTabValue)
								BX.addClass(tabs[i], 'active');
							else
								BX.removeClass(tabs[i], 'active');
						}
					}
				}
			}
		},
			
		initSectionsLinks: function() {
			var haveActive = false;
			for(var i in this.sectionsLinks) {
				if(this.sectionsLinks.hasOwnProperty(i) && BX.type.isDomNode(this.sectionsLinks[i])) {
					BX.bind(this.sectionsLinks[i], 'click', BX.proxy(this.changeSectionLink, this));

					if(!haveActive) {
						BX.addClass(this.sectionsLinks[i], 'active');
						haveActive = true;
					} else {
						BX.removeClass(this.sectionsLinks[i], 'active');
					}
				}
			}
		},

		changeSectionLink: function(event) {
			BX.PreventDefault(event);

			var sectionId = BX.proxy_context && BX.proxy_context.getAttribute('data-section-id');			
			if(!BX.hasClass(BX.proxy_context, 'active') && sectionId) {
				var itemProductsContainer = this.obItem.querySelector('.articles-detail-products');
				if(!!itemProductsContainer) {					
					itemProductsContainer.style.opacity = 0.2;
					BX.ajax({
						url: BX.message('ARTICLE_TEMPLATE_PATH') + '/ajax.php',
						method: 'POST',
						dataType: 'json',
						timeout: 60,
						data: {
							'action': 'changeSectionLink',
							'siteId': BX.message('SITE_ID'),
							'parameters': this.config.PARAMS,
							'productsIds': this.item.productsIds,
							'sectionId': sectionId
						},
						onsuccess: BX.delegate(function(result) {
							if(!result.content || !result.JS)
								return;

							BX.ajax.processScripts(
								BX.processHTML(result.JS).SCRIPT,
								false,
								BX.delegate(function() {
									var processed = BX.processHTML(result.content, false);

									itemProductsContainer.innerHTML = processed.HTML;

									if(result.imgWebp) {
										var srcList = {},
											images = itemProductsContainer.querySelectorAll('img');
										
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

									if(result.imgLazyLoad)
										imgLazyLoad();

									new BX.easing({
										duration: 2000,
										start: {opacity: 20},
										finish: {opacity: 100},
										transition: BX.easing.makeEaseOut(BX.easing.transitions.quad),
										step: function(state) {
											itemProductsContainer.style.opacity = state.opacity / 100;
										},
										complete: function() {
											itemProductsContainer.removeAttribute('style');
										}
									}).animate();

									BX.ajax.processScripts(processed.SCRIPT);
								}, this)
							);
						}, this)
					});
				}
				
				for(var i in this.sectionsLinks) {
					if(this.sectionsLinks.hasOwnProperty(i) && BX.type.isDomNode(this.sectionsLinks[i])) {
						if(this.sectionsLinks[i].getAttribute('data-section-id') === sectionId) {
							BX.addClass(this.sectionsLinks[i], 'active');
						} else {
							BX.removeClass(this.sectionsLinks[i], 'active');
						}
					}
				}
			}
		},

		adjustSectionLinkBtn: function() {
			if(BX.pos(this.sectionsLinks[Object.keys(this.sectionsLinks).length - 1], true).bottom > this.sectionsLinksContainerHeight) {
				if(!this.sectionLinkBtnAdjusted) {
					this.sectionLinkBtnAdjusted = true;
					
					if(!BX.hasClass(this.sectionsLinksContainer, 'active'))
						this.sectionLinkBtnSpan.innerHTML = window.innerWidth < 1043 ? BX.message('ARTICLES_ITEM_DETAIL_PRODUCTS_SECTIONS_ALL') : BX.message('ARTICLES_ITEM_DETAIL_PRODUCTS_SECTIONS_SHOW_ALL');
					
					this.sectionsLinksContainer.appendChild(this.sectionLinkBtn);						
				} else if(!!this.sectionLinkBtnAdjusted && !BX.hasClass(this.sectionsLinksContainer, 'active')) {
					this.sectionLinkBtnSpan.innerHTML = window.innerWidth < 1043 ? BX.message('ARTICLES_ITEM_DETAIL_PRODUCTS_SECTIONS_ALL') : BX.message('ARTICLES_ITEM_DETAIL_PRODUCTS_SECTIONS_SHOW_ALL');
				}
			} else if(BX.pos(this.sectionsLinks[Object.keys(this.sectionsLinks).length - 1], true).bottom <= this.sectionsLinksContainerHeight && !!this.sectionLinkBtnAdjusted) {
				this.sectionLinkBtnAdjusted = false;

				this.sectionsLinksContainer.removeChild(this.sectionLinkBtn);
			}
		},

		showHideSectionsLinks: function() {
			if(!BX.hasClass(this.sectionsLinksContainer, 'active')) {
				BX.addClass(this.sectionsLinksContainer, 'active');		
				this.sectionLinkBtnSpan.innerHTML = BX.message('ARTICLES_ITEM_DETAIL_PRODUCTS_SECTIONS_HIDE');
				this.sectionLinkBtnI.className = 'icon-arrow-up';
			} else {
				BX.removeClass(this.sectionsLinksContainer, 'active');
				this.sectionLinkBtnSpan.innerHTML = window.innerWidth < 1043 ? BX.message('ARTICLES_ITEM_DETAIL_PRODUCTS_SECTIONS_ALL') : BX.message('ARTICLES_ITEM_DETAIL_PRODUCTS_SECTIONS_SHOW_ALL');
				this.sectionLinkBtnI.className = 'icon-arrow-down';
			}
		}
	}
})();