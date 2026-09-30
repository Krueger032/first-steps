(function() {
	'use strict';

	if(!!window.JCNewsDetailObjects)
		return;
	
	window.JCNewsDetailObjects = function(arParams) {
		this.config = {
			PARAMS: ''
		};
		
		this.visual = {
			ID: ''
		};
		
		this.item = {
			id: 0,
			name: '',
			address: '',
			timezone: '',
			workingHours: {},			
			phone: {},
			whatsapp: {},
			viber: {},
			telegram: {},
			instagram: {},
			email: {},
			skype: {},
			callbackForm: false,
			productsIds: '',
			btnAddReview: true,
			ratingValue: '',
			reviewsCount: 0,
			reviesDeclension: ''
		};

		this.sPanel = null;
		this.sPanelContent = null;
		this.obItem = null;
		
		this.obTabs = null;
		this.obTabsBlock = null;
		this.obTabContainers = null;

		this.sectionsLinksContainer = null;
		
		this.errorCode = 0;
		
		if(typeof arParams === 'object') {
			this.config = arParams.CONFIG;
			this.visual = arParams.VISUAL;
			this.item.id = arParams.ITEM.ID;
			this.item.name = arParams.ITEM.NAME;
			this.item.address = arParams.ITEM.ADDRESS;
			this.item.timezone = arParams.ITEM.TIMEZONE;
			this.item.workingHours = arParams.ITEM.WORKING_HOURS;			
			this.item.phone = arParams.ITEM.PHONE.VALUE;
			this.item.phoneDescription = arParams.ITEM.PHONE.DESCRIPTION;
			this.item.whatsapp = arParams.ITEM.WHATSAPP.VALUE;
			this.item.whatsappDescription = arParams.ITEM.WHATSAPP.DESCRIPTION;
			this.item.viber = arParams.ITEM.VIBER.VALUE;
			this.item.viberDescription = arParams.ITEM.VIBER.DESCRIPTION;
			this.item.telegram = arParams.ITEM.TELEGRAM.VALUE;
			this.item.telegramDescription = arParams.ITEM.TELEGRAM.DESCRIPTION;
			this.item.instagram = arParams.ITEM.INSTAGRAM.VALUE;
			this.item.instagramDescription = arParams.ITEM.INSTAGRAM.DESCRIPTION;
			this.item.email = arParams.ITEM.EMAIL.VALUE;
			this.item.emailDescription = arParams.ITEM.EMAIL.DESCRIPTION;
			this.item.skype = arParams.ITEM.SKYPE.VALUE;
			this.item.skypeDescription = arParams.ITEM.SKYPE.DESCRIPTION;
			this.item.callbackForm = arParams.ITEM.CALLBACK_FORM;
			this.item.productsIds = arParams.ITEM.PRODUCTS_IDS;
			this.item.btnAddReview = arParams.ITEM.BTN_ADD_REVIEW;
			this.item.ratingValue = arParams.ITEM.RATING_VALUE;
			this.item.reviewsCount = arParams.ITEM.REVIEWS_COUNT;
			this.item.reviesDeclension = arParams.ITEM.REVIEWS_DECLENSION;
			
			BX.ready(BX.delegate(this.init, this));
		}
	};

	window.JCNewsDetailObjects.prototype = {
		init: function() {
			this.obItem = BX(this.visual.ID);
			if(!this.obItem) {
				this.errorCode = -1;
			}

			if(this.errorCode === 0) {
				this.obTabs = this.obItem.querySelector('.objects-detail-tabs-container');
				this.obTabsBlock = !!this.obTabs && this.obTabs.querySelector('[data-entity="tabs"]');
				this.obTabContainers = this.obItem.querySelector('.objects-detail-tabs-content');
				
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

				this.sPanel = document.body.querySelector('.slide-panel');
				
				this.showWorkingHoursToday();

				var itemBtn = this.obItem.querySelector('.objects-item-detail-btn');
				if(!!itemBtn)
					BX.bind(itemBtn, 'click', BX.proxy(this.item.callbackForm ? this.showContactsWidthForm : this.showContacts, this));

				BX.addCustomEvent(this, 'showContactsWidthFormRequest', BX.proxy(this.showContactsWidthFormRequest, this));

				if(!!this.sPanel) {		
					BX.bind(document, 'click', BX.delegate(function(e) {
						if(BX.hasClass(this.sPanel, 'active') && BX.findParent(e.target, {attrs: {id: this.visual.ID + '_contacts'}}) && BX.hasClass(e.target, 'icon-arrow-down')) {
							var workingHoursToday = BX.findParent(e.target, {attrs: {'data-entity': 'working-hours-today'}});
							if(!!workingHoursToday)
								BX.style(workingHoursToday, 'display', 'none');
							
							var workingHours = BX(this.visual.ID + '_contacts').querySelector('[data-entity="working-hours"]');
							if(!!workingHours)
								BX.style(workingHours, 'display', '');
							
							e.stopPropagation();
						}
					}, this));
					BX.bind(document, 'click', BX.delegate(function(e) {
						if(BX.hasClass(this.sPanel, 'active') && BX.findParent(e.target, {attrs: {id: this.visual.ID + '_contacts'}}) && BX.hasClass(e.target, 'icon-arrow-up')) {
							var workingHours = BX.findParent(e.target, {attrs: {'data-entity': 'working-hours'}});
							if(!!workingHours)
								BX.style(workingHours, 'display', 'none');
							
							var workingHoursToday = BX(this.visual.ID + '_contacts').querySelector('[data-entity="working-hours-today"]');
							if(!!workingHoursToday)
								BX.style(workingHoursToday, 'display', '');
							
							e.stopPropagation();
						}
					}, this));
					BX.bind(document, 'click', BX.delegate(function(e) {
						if(BX.hasClass(this.sPanel, 'active') && (e.target.getAttribute('data-entity') == 'reviews' || BX.findParent(e.target, {attr: {'data-entity': 'reviews'}}))) {
							window.location.href = window.location.pathname + '#reviews';
							window.location.reload(true);
						}
					}, this));
				}

				var slider = this.obItem.querySelector('.objects-item-detail-slider');
				if(!!slider) {
					BX.addClass(slider, 'owl-carousel');
					$(slider).owlCarousel({
						nav: true,
						navText: ['<div class="owl-prev-icon"><i class="icon-arrow-left"></i></div>', '<div class="owl-next-icon"><i class="icon-arrow-right"></i></div>'],
						navContainer: '.objects-item-detail-slider-container',
						dots: false,
						responsive: {
							0: {
								stagePadding: 0,
								items: 3
							},
							1043: {
								stagePadding: 100,
								items: 3
							},
							1272: {
								stagePadding: 100,
								items: 4
							},
							1440: {
								stagePadding: 100,
								items: 5
							},
							1920: {
								stagePadding: 200,
								items: 5
							}
						}
					});

					var sliderItems = slider.querySelectorAll('.fancyimage');
					if(!!sliderItems) {
						$(sliderItems).fancybox({
							helpers: {
								title: {
									type: 'inside',
									position: 'bottom'
								}
							}
						});
					}
				}
				
				this.sectionsLinksContainer = this.obItem.querySelector('.objects-detail-sections-links');
				if(!!this.sectionsLinksContainer) {
					this.sectionsLinksContainerHeight = 92;
					this.sectionsLinks = this.sectionsLinksContainer.querySelectorAll('.objects-detail-section-link');
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
								className: 'objects-detail-section-link-btn-container'
							},
							children: [
								BX.create('DIV', {
									props: {
										className: 'objects-detail-section-link objects-detail-section-link-btn'
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
			var tabsList = this.obTabs.querySelector('.objects-detail-tabs-list'),
				tabs = !!tabsList && tabsList.querySelectorAll('[data-entity="tab"]'),
				tabValue, targetTab, haveActive = false;			
			
			if(!!tabs.length > 0) {
				BX.addClass(tabsList, 'owl-carousel');
				$(tabsList).owlCarousel({								
					autoWidth: true,
					nav: true,
					navText: ['<i class=\"icon-arrow-left\"></i>', '<i class=\"icon-arrow-right\"></i>'],
					navContainer: '.objects-detail-tabs-scroll',
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

								if(window.location.hash.indexOf(tabValue) > -1) {
									tabs[i].click();
									window.history.pushState("", document.title, window.location.pathname + window.location.search);
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

		showWorkingHoursToday: function() {
			var itemHours = this.obItem.querySelector('.objects-item-detail-hours');
			if(!!itemHours) {
				itemHours.innerHTML = '<div class="objects-item-detail-hours-loader"><div><span></span></div></div>' + BX.message('OBJECTS_ITEM_DETAIL_LOADING');
				BX.removeClass(itemHours, 'objects-item-detail-hours-hidden');
				BX.ajax({
					url: BX.message('OBJECT_TEMPLATE_PATH') + '/ajax.php',
					method: 'POST',
					dataType: 'json',
					timeout: 60,
					data: {							
						action: 'workingHoursToday',
						siteCharset: BX.message('SITE_CHARSET'),
						timezone: this.item.timezone,
						workingHours: this.item.workingHours
					},
					onsuccess: BX.delegate(function(result) {
						var content = '';
						
						if(!!result.today) {
							this.item.workingHoursToday = result.today;

							for(var i in this.item.workingHoursToday) {
								if(this.item.workingHoursToday.hasOwnProperty(i)) {
									if(this.item.workingHoursToday[i].STATUS) {
										content += '<span class="objects-item-detail-hours-icon objects-item-detail-hours-icon-' + (this.item.workingHoursToday[i].STATUS == 'OPEN' ? 'open' : 'closed') + '"></span>';
									}
									if(this.item.workingHoursToday[i].WORK_START && this.item.workingHoursToday[i].WORK_END) {
										if(this.item.workingHoursToday[i].WORK_START != this.item.workingHoursToday[i].WORK_END) {
											content += this.item.workingHoursToday[i].WORK_START + ' - ' + this.item.workingHoursToday[i].WORK_END;
											if(this.item.workingHoursToday[i].BREAK_START && this.item.workingHoursToday[i].BREAK_END) {
												if(this.item.workingHoursToday[i].BREAK_START != this.item.workingHoursToday[i].BREAK_END) {
													content += '<span class="objects-item-detail-hours-break">';
														content += BX.message('OBJECTS_ITEM_DETAIL_BREAK') + ' ' + this.item.workingHoursToday[i].BREAK_START + ' - ' + this.item.workingHoursToday[i].BREAK_END;
													content += '</span>';
												}
											}
										} else {
											content += BX.message('OBJECTS_ITEM_DETAIL_24_HOURS');
										}
									} else {
										content += BX.message('OBJECTS_ITEM_DETAIL_OFF');
									}
								}
							}
						}
						
						itemHours.innerHTML = content;
						if(content.length == 0)
							BX.addClass(itemHours, 'objects-item-detail-hours-hidden');
					}, this)
				});
			}
		},

		adjustContacts: function() {
			var content = '';
			
			if(this.item.address || this.item.workingHours || this.item.workingHoursToday || this.item.phone || this.item.whatsapp || this.item.viber || this.item.telegram || this.item.instagram || this.item.email || this.item.skype || !!this.item.btnAddReview || this.item.reviewsCount > 0) {
				content += '<div class="slide-panel__contacts" id="' + this.visual.ID + '_contacts">';

					if(this.item.address) {
						content += '<div class="slide-panel__contacts-item">';
							content += '<div class="slide-panel__contacts-item__block">';
								content += '<div class="slide-panel__contacts-item__icon"><i class="icon-map-marker"></i></div>';
								content += '<div class="slide-panel__contacts-item__text">' + this.item.address + '</div>';
							content += '</div>';
						content += '</div>';
					}

					if(this.item.workingHoursToday) {
						for(var i in this.item.workingHoursToday) {
							if(this.item.workingHoursToday.hasOwnProperty(i)) {
								content += '<div class="slide-panel__contacts-item" data-entity="working-hours-today">';
									content += '<div class="slide-panel__contacts-item__hours-today">';
										content += '<div class="slide-panel__contacts-item__today-container">';
											content += '<div class="slide-panel__contacts-item__today">';
												content += '<span class="slide-panel__contacts-item__today-icon"><i class="icon-clock"></i></span>';
												content += '<span class="slide-panel__contacts-item__today-title">' + BX.message('OBJECTS_ITEM_DETAIL_TODAY') + '</span>';
												if(this.item.workingHoursToday[i].STATUS) {
													content += '<span class="slide-panel__contacts-item__today-status slide-panel__contacts-item__today-status-' + (this.item.workingHoursToday[i].STATUS == 'OPEN' ? 'open' : 'closed') + '"></span>';
												}
											content += '</div>';
										content += '</div>';
										content += '<div class="slide-panel__contacts-item__hours-break">';
											content += '<div class="slide-panel__contacts-item__hours slide-panel__contacts-item__hours-first">';
												content += '<span class="slide-panel__contacts-item__hours-title">';
													if(this.item.workingHoursToday[i].WORK_START && this.item.workingHoursToday[i].WORK_END) {
														if(this.item.workingHoursToday[i].WORK_START != this.item.workingHoursToday[i].WORK_END) {
															content += this.item.workingHoursToday[i].WORK_START + ' - ' + this.item.workingHoursToday[i].WORK_END;
														} else {
															content += BX.message('OBJECTS_ITEM_DETAIL_24_HOURS');
														}
													} else {
														content += BX.message('OBJECTS_ITEM_DETAIL_OFF');
													}
												content += '</span>';
												content += '<span class="slide-panel__contacts-item__hours-icon"><i class="icon-arrow-down"></i></span>';
											content += '</div>';
											if(this.item.workingHoursToday[i].WORK_START && this.item.workingHoursToday[i].WORK_END) {
												if(this.item.workingHoursToday[i].WORK_START != this.item.workingHoursToday[i].WORK_END) {
													if(this.item.workingHoursToday[i].BREAK_START && this.item.workingHoursToday[i].BREAK_END) {
														if(this.item.workingHoursToday[i].BREAK_START != this.item.workingHoursToday[i].BREAK_END) {
															content += '<div class="slide-panel__contacts-item__break">';
																content += BX.message('OBJECTS_ITEM_DETAIL_BREAK') + ' ' + this.item.workingHoursToday[i].BREAK_START + ' - ' + this.item.workingHoursToday[i].BREAK_END;
															content += '</div>';
														}
													}
												}
											}
										content += '</div>';
									content += '</div>';
								content += '</div>';
							}
						}
					}

					if(this.item.workingHours) {
						content += '<div class="slide-panel__contacts-item" data-entity="working-hours"' + (this.item.workingHoursToday ? 'style="display: none;"' : '') + '>';
							var key = 0;
							for(var i in this.item.workingHours) {
								if(this.item.workingHours.hasOwnProperty(i)) {										
									content += '<div class="slide-panel__contacts-item__hours-today">';
										content += '<div class="slide-panel__contacts-item__today-container">';
											content += '<div class="slide-panel__contacts-item__today">';
												if(key == 0) {
													content += '<span class="slide-panel__contacts-item__today-icon"><i class="icon-clock"></i></span>';
												}
												content += '<span class="slide-panel__contacts-item__today-title">' + (this.item.workingHoursToday && this.item.workingHoursToday.hasOwnProperty(i) ? BX.message('OBJECTS_ITEM_DETAIL_TODAY') : this.item.workingHours[i].NAME) + '</span>';
												if(this.item.workingHoursToday && this.item.workingHoursToday.hasOwnProperty(i) && this.item.workingHoursToday[i].STATUS) {
													content += '<span class="slide-panel__contacts-item__today-status slide-panel__contacts-item__today-status-' + (this.item.workingHoursToday[i].STATUS == 'OPEN' ? 'open' : 'closed') + '"></span>';
												}
											content += '</div>';
										content += '</div>';
										content += '<div class="slide-panel__contacts-item__hours-break">';
											content += '<div class="slide-panel__contacts-item__hours' + (key == 0 ? ' slide-panel__contacts-item__hours-first' : '') + '">';
												content += '<span class="slide-panel__contacts-item__hours-title">';
													if(this.item.workingHours[i].WORK_START && this.item.workingHours[i].WORK_END) {
														if(this.item.workingHours[i].WORK_START != this.item.workingHours[i].WORK_END) {
															content += this.item.workingHours[i].WORK_START + ' - ' + this.item.workingHours[i].WORK_END;
														} else {
															content += BX.message('OBJECTS_ITEM_DETAIL_24_HOURS');
														}
													} else {
														content += BX.message('OBJECTS_ITEM_DETAIL_OFF');
													}
												content += '</span>';
												if(this.item.workingHoursToday && key == 0) {
													content += '<span class="slide-panel__contacts-item__hours-icon"><i class="icon-arrow-up"></i></span>';
												}
											content += '</div>';
											if(this.item.workingHours[i].WORK_START && this.item.workingHours[i].WORK_END) {
												if(this.item.workingHours[i].WORK_START != this.item.workingHours[i].WORK_END) {
													if(this.item.workingHours[i].BREAK_START && this.item.workingHours[i].BREAK_END) {
														if(this.item.workingHours[i].BREAK_START != this.item.workingHours[i].BREAK_END) {
															content += '<div class="slide-panel__contacts-item__break">';
																content += BX.message('OBJECTS_ITEM_DETAIL_BREAK') + ' ' + this.item.workingHours[i].BREAK_START + ' - ' + this.item.workingHours[i].BREAK_END;
															content += '</div>';
														}
													}
												}
											}
										content += '</div>';
									content += '</div>';
									key++;
								}
							}
						content += '</div>';
					}
					
					if(this.item.phone) {
						for(var i in this.item.phone) {
							if(this.item.phone.hasOwnProperty(i)) {
								content += '<div class="slide-panel__contacts-item">';
									content += '<div class="slide-panel__contacts-item__block">';
										content += '<div class="slide-panel__contacts-item__icon"><i class="icon-phone"></i></div>';
										content += '<div class="slide-panel__contacts-item__text">';
											content += '<a class="slide-panel__contacts-item__phone slide-panel__contacts-item__link" href="tel:' + this.item.phone[i].replace(/[^\d\+]/g,'') + '">' + this.item.phone[i] + '</a>';
											if(this.item.phoneDescription.hasOwnProperty(i) && this.item.phoneDescription[i].length > 0) {
												content += '<span class="slide-panel__contacts-item__descr">' + this.item.phoneDescription[i] + '</span>';
											}
										content += '</div>';
									content += '</div>';
								content += '</div>';
							}
						}
					}
					
					if(this.item.whatsapp) {
						for(var i in this.item.whatsapp) {
							if(this.item.whatsapp.hasOwnProperty(i)) {
								content += '<div class="slide-panel__contacts-item">';
									content += '<div class="slide-panel__contacts-item__block">';
										content += '<div class="slide-panel__contacts-item__icon"><i class="fa fa-whatsapp"></i></div>';
										content += '<div class="slide-panel__contacts-item__text">';
											content += '<a target="_blank" class="slide-panel__contacts-item__whatsapp slide-panel__contacts-item__link" href="https://wa.me/' + this.item.whatsapp[i].replace(/[^\d]/g,'') + '">' + this.item.whatsapp[i] + '</a>';
											if(this.item.whatsappDescription.hasOwnProperty(i) && this.item.whatsappDescription[i].length > 0) {
												content += '<span class="slide-panel__contacts-item__descr">' + this.item.whatsappDescription[i] + '</span>';
											}
										content += '</div>';
									content += '</div>';
								content += '</div>';
							}
						}
					}

					if(this.item.viber) {
						var isMobile = BX.hasClass(document.documentElement, 'bx-touch');
						for(var i in this.item.viber) {
							if(this.item.viber.hasOwnProperty(i)) {
								content += '<div class="slide-panel__contacts-item">';
									content += '<div class="slide-panel__contacts-item__block">';
										content += '<div class="slide-panel__contacts-item__icon"><i class="fa fa-phone"></i></div>';
										content += '<div class="slide-panel__contacts-item__text">';
											content += '<a class="slide-panel__contacts-item__viber slide-panel__contacts-item__link" href="viber://' + (!isMobile ? 'chat' : 'add') + '?number=' + (!isMobile ? this.item.viber[i].replace(/[^\d\+]/g,'') : this.item.viber[i].replace(/[^\d]/g,'')) + '">' + this.item.viber[i] + '</a>';
											if(this.item.viberDescription.hasOwnProperty(i) && this.item.viberDescription[i].length > 0) {
												content += '<span class="slide-panel__contacts-item__descr">' + this.item.viberDescription[i] + '</span>';
											}
										content += '</div>';
									content += '</div>';
								content += '</div>';
							}
						}
					}

					if(this.item.telegram) {
						for(var i in this.item.telegram) {
							if(this.item.telegram.hasOwnProperty(i)) {
								content += '<div class="slide-panel__contacts-item">';
									content += '<div class="slide-panel__contacts-item__block">';
										content += '<div class="slide-panel__contacts-item__icon"><i class="fa fa-telegram"></i></div>';
										content += '<div class="slide-panel__contacts-item__text">';
											content += '<a target="_blank" class="slide-panel__contacts-item__link" href="https://t.me/' + this.item.telegram[i] + '">' + this.item.telegram[i] + '</a>';
											if(this.item.telegramDescription.hasOwnProperty(i) && this.item.telegramDescription[i].length > 0) {
												content += '<span class="slide-panel__contacts-item__descr">' + this.item.telegramDescription[i] + '</span>';
											}
										content += '</div>';
									content += '</div>';
								content += '</div>';
							}
						}
					}

					if(this.item.instagram) {
						for(var i in this.item.instagram) {
							if(this.item.instagram.hasOwnProperty(i)) {
								content += '<div class="slide-panel__contacts-item">';
									content += '<div class="slide-panel__contacts-item__block">';
										content += '<div class="slide-panel__contacts-item__icon"><i class="fa fa-instagram"></i></div>';
										content += '<div class="slide-panel__contacts-item__text">';
											content += '<a target="_blank" class="slide-panel__contacts-item__link" href="https://www.instagram.com/' + this.item.instagram[i] + '">' + this.item.instagram[i] + '</a>';
											if(this.item.instagramDescription.hasOwnProperty(i) && this.item.instagramDescription[i].length > 0) {
												content += '<span class="slide-panel__contacts-item__descr">' + this.item.instagramDescription[i] + '</span>';
											}
										content += '</div>';
									content += '</div>';
								content += '</div>';
							}
						}
					}

					if(this.item.email) {
						for(var i in this.item.email) {
							if(this.item.email.hasOwnProperty(i)) {
								content += '<div class="slide-panel__contacts-item">';
									content += '<div class="slide-panel__contacts-item__block">';
										content += '<div class="slide-panel__contacts-item__icon"><i class="icon-mail"></i></div>';
										content += '<div class="slide-panel__contacts-item__text">';
											content += '<a class="slide-panel__contacts-item__link" href="mailto:' + this.item.email[i] + '">' + this.item.email[i] + '</a>';
											if(this.item.emailDescription.hasOwnProperty(i) && this.item.emailDescription[i].length > 0) {
												content += '<span class="slide-panel__contacts-item__descr">' + this.item.emailDescription[i] + '</span>';
											}
										content += '</div>';
									content += '</div>';
								content += '</div>';
							}
						}
					}

					if(this.item.skype) {
						for(var i in this.item.skype) {
							if(this.item.skype.hasOwnProperty(i)) {
								content += '<div class="slide-panel__contacts-item">';
									content += '<div class="slide-panel__contacts-item__block">';
										content += '<div class="slide-panel__contacts-item__icon"><i class="fa fa-skype"></i></div>';
										content += '<div class="slide-panel__contacts-item__text">';
											content += '<a class="slide-panel__contacts-item__link" href="skype:' + this.item.skype[i] + '?chat">' + this.item.skype[i] + '</a>';
											if(this.item.skypeDescription.hasOwnProperty(i) && this.item.skypeDescription[i].length > 0) {
												content += '<span class="slide-panel__contacts-item__descr">' + this.item.skypeDescription[i] + '</span>';
											}
										content += '</div>';
									content += '</div>';
								content += '</div>';
							}
						}
					}

					if(!!this.item.btnAddReview || this.item.reviewsCount > 0) {
						content += '<div class="slide-panel__contacts-item">';
							if(this.item.reviewsCount > 0) {
								content += '<a class="slide-panel__contacts-item__rating-link" href="javascript:void(0)" data-entity="reviews">';
									content += '<span class="slide-panel__contacts-item__rating">';
										content += '<span class="slide-panel__contacts-item__rating-val"' + (this.item.ratingValue <= 4.4 ? ' data-rate="' + parseInt(this.item.ratingValue) + '"' : '') + '>' + this.item.ratingValue + '</span>';
										content += '<span class="slide-panel__contacts-item__rating-reviews-count">' + this.item.reviewsCount + ' ' + this.item.reviesDeclension + '</span>';
									content += '</span>';
									content += '<span class="slide-panel__contacts-item__rating-text">' + BX.message('OBJECTS_ITEM_DETAIL_SEE_REVIEWS') + '</span>';
								content += '</a>';
							} else {
								content += '<a class="btn btn-default" href="javascript:void(0)" data-entity="reviews" role="button"><span>' + BX.message('OBJECTS_ITEM_DETAIL_ADD_REVIEW') + '</span></a>';
							}
						content += '</div>';
					}
				
				content += '</div>';
			}

			this.sPanelContent = content;
		},

		showContactsWidthFormRequest: function(sPanelContent) {
			this.adjustContacts();

			BX.ajax({
				url: BX.message('SITE_DIR') + 'ajax/slide_panel.php',
				method: 'POST',
				dataType: 'json',
				timeout: 60,
				data: {
					action: 'callback_objects'
				},
				onsuccess: BX.delegate(function(result) {
					if(!result.content || !result.JS) {
						sPanelContent.innerHTML = this.sPanelContent;
					} else {
						BX.ajax.processScripts(
							BX.processHTML(result.JS).SCRIPT,
							false,
							BX.delegate(function() {
								var processed = BX.processHTML(result.content),
									temporaryNode = BX.create('DIV');

								temporaryNode.innerHTML = processed.HTML;

								var sPanelFormObjectIdInput = temporaryNode.querySelector('[name="OBJECT_ID"]');
								if(!!sPanelFormObjectIdInput)
									sPanelFormObjectIdInput.value = this.item.id;
								
								sPanelContent.innerHTML = this.sPanelContent + temporaryNode.innerHTML;
								
								BX.ajax.processScripts(processed.SCRIPT);
							}, this)
						);
					}
					
					$(sPanelContent).scrollbar();
				}, this)
			});
		},

		showContactsWidthForm: function(e) {
			if(!!this.sPanel) {
				this.sPanel.appendChild(
					BX.create('DIV', {
						props: {
							className: 'slide-panel__title-wrap'
						},
						children: [
							BX.create('I', {
								props: {
									className: 'icon-phone-call'
								}
							}),						
							BX.create('SPAN', {
								props: {
									className: 'slide-panel__title'
								},
								html: this.item.name
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
					BX.onCustomEvent(this, 'showContactsWidthFormRequest', [sPanelContent]);

				var scrollWidth = window.innerWidth - document.body.clientWidth;
				if(scrollWidth > 0) {
					BX.style(document.body, 'padding-right', scrollWidth + 'px');
					
					var topPanel = document.querySelector('.top-panel');
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

					if(!!this.obTabsBlock && !!this.tabsPanelFixed)
						BX.style(this.obTabsBlock, 'padding-right', scrollWidth + 'px');

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
			
		showContacts: function(e) {
			if(!!this.sPanel) {
				this.adjustContacts();	

				this.sPanel.appendChild(
					BX.create('DIV', {
						props: {
							className: 'slide-panel__title-wrap'
						},
						children: [
							BX.create('I', {
								props: {
									className: 'icon-phone-call'
								}
							}),						
							BX.create('SPAN', {
								props: {
									className: 'slide-panel__title'
								},
								html: this.item.name
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
						html: this.sPanelContent
					})
				);

				var sPanelContent = this.sPanel.querySelector('.slide-panel__content');
				if(!!sPanelContent)
					$(sPanelContent).scrollbar();

				var scrollWidth = window.innerWidth - document.body.clientWidth;
				if(scrollWidth > 0) {
					BX.style(document.body, 'padding-right', scrollWidth + 'px');
					
					var topPanel = document.querySelector('.top-panel');
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

					if(!!this.obTabsBlock && !!this.tabsPanelFixed)
						BX.style(this.obTabsBlock, 'padding-right', scrollWidth + 'px');

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
				var itemProductsContainer = this.obItem.querySelector('.objects-detail-products');
				if(!!itemProductsContainer) {					
					itemProductsContainer.style.opacity = 0.2;
					BX.ajax({
						url: BX.message('OBJECT_TEMPLATE_PATH') + '/ajax.php',
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
						this.sectionLinkBtnSpan.innerHTML = window.innerWidth < 1043 ? BX.message('OBJECTS_ITEM_DETAIL_PRODUCTS_SECTIONS_ALL') : BX.message('OBJECTS_ITEM_DETAIL_PRODUCTS_SECTIONS_SHOW_ALL');
					
					this.sectionsLinksContainer.appendChild(this.sectionLinkBtn);						
				} else if(!!this.sectionLinkBtnAdjusted && !BX.hasClass(this.sectionsLinksContainer, 'active')) {
					this.sectionLinkBtnSpan.innerHTML = window.innerWidth < 1043 ? BX.message('OBJECTS_ITEM_DETAIL_PRODUCTS_SECTIONS_ALL') : BX.message('OBJECTS_ITEM_DETAIL_PRODUCTS_SECTIONS_SHOW_ALL');
				}
			} else if(BX.pos(this.sectionsLinks[Object.keys(this.sectionsLinks).length - 1], true).bottom <= this.sectionsLinksContainerHeight && !!this.sectionLinkBtnAdjusted) {
				this.sectionLinkBtnAdjusted = false;

				this.sectionsLinksContainer.removeChild(this.sectionLinkBtn);
			}
		},

		showHideSectionsLinks: function() {
			if(!BX.hasClass(this.sectionsLinksContainer, 'active')) {
				BX.addClass(this.sectionsLinksContainer, 'active');		
				this.sectionLinkBtnSpan.innerHTML = BX.message('OBJECTS_ITEM_DETAIL_PRODUCTS_SECTIONS_HIDE');
				this.sectionLinkBtnI.className = 'icon-arrow-up';
			} else {
				BX.removeClass(this.sectionsLinksContainer, 'active');
				this.sectionLinkBtnSpan.innerHTML = window.innerWidth < 1043 ? BX.message('OBJECTS_ITEM_DETAIL_PRODUCTS_SECTIONS_ALL') : BX.message('OBJECTS_ITEM_DETAIL_PRODUCTS_SECTIONS_SHOW_ALL');
				this.sectionLinkBtnI.className = 'icon-arrow-down';
			}
		}
	}
})();