(function(window) {
	'use strict';

	BX.ready(function() {
		//TOP_PANEL//
		var sMenuInt2 = BX.hasClass(document.body, 'slide-menu-interface-2-0-1') || 
			BX.hasClass(document.body, 'slide-menu-interface-2-0-1-inner') || 
			BX.hasClass(document.body, 'slide-menu-interface-2-0-2') ||
			BX.hasClass(document.body, 'slide-menu-interface-2-0-3'),
			topPanelContainer = document.body.querySelector('.top-panel-wrapper'),		
			topPanel = !!topPanelContainer && topPanelContainer.querySelector('.top-panel'),
			topPanelThead = !!topPanel && topPanel.querySelector('.top-panel__thead'),
			topPanelTfoot = !!topPanel && topPanel.querySelector('.top-panel__tfoot');

		if(!!topPanelThead && !!topPanelTfoot) {
			function showHideTopPanelTfoot() {
				if(window.innerWidth < 1043) {
					if(window.location.pathname.indexOf('/personal/') > -1)
						BX.addClass(topPanelTfoot, 'hidden-xs hidden-sm');
				} else {
					if(BX.hasClass(topPanelTfoot, 'hidden-xs hidden-sm'))
						BX.removeClass(topPanelTfoot, 'hidden-xs hidden-sm');
				}
			}

			showHideTopPanelTfoot();
			BX.bind(window, 'resize', function() {
				showHideTopPanelTfoot();
			});
			
			var isTopPanelFixed = false,
				lastScrollTop = 0;

			function checkTopTopPanel() {
				var scrollTop = !!document.body.style.top ? Math.abs(parseInt(BX.style(document.body, 'top'), 10)) : BX.GetWindowScrollPos().scrollTop,
					topPanelContainerTop = topPanelContainer.getBoundingClientRect().top + scrollTop;
				
				if(window.innerWidth < 1043) {
					var topPanelTheadHeight = topPanelThead.offsetHeight,
						topPanelTfootHeight = topPanelTfoot.offsetHeight;

					if(scrollTop >= topPanelContainerTop) {
						if(!isTopPanelFixed) {
							isTopPanelFixed = true;
							if(!sMenuInt2) {
								BX.style(topPanelContainer, 'height', topPanelTheadHeight + topPanelTfootHeight + 'px');
								BX.style(topPanelContainer, 'paddingTop', topPanelTheadHeight + 'px');
								BX.addClass(topPanelThead, 'fixed');
							}
						} else if(!sMenuInt2) {						
							if(!BX.hasClass(topPanelTfoot, 'visible') && scrollTop < lastScrollTop) {							
								BX.style(topPanelTfoot, 'top', '-' + topPanelTfootHeight + 'px');								
								BX.addClass(topPanelTfoot, 'fixed visible');							
								new BX.easing({
									duration: 300,
									start: {top: - topPanelTfootHeight},
									finish: {top: topPanelTheadHeight},
									transition: BX.easing.transitions.linear,
									step: function(state) {
										if(!!isTopPanelFixed)
											BX.style(topPanelTfoot, 'top', state.top + 'px');
									}
								}).animate();							
							} else if(!!BX.hasClass(topPanelTfoot, 'visible') && scrollTop > lastScrollTop) {
								BX.removeClass(topPanelTfoot, 'visible');
								new BX.easing({
									duration: 300,							
									start: {top: topPanelTheadHeight},
									finish: {top: - topPanelTfootHeight},
									transition: BX.easing.transitions.linear,
									step: function(state) {										
										BX.style(topPanelTfoot, 'top', state.top + 'px');
									}
								}).animate();
							}						
						}
					} else if(!!isTopPanelFixed && scrollTop < topPanelContainerTop) {
						isTopPanelFixed = false;
						if(!sMenuInt2) {
							topPanelContainer.removeAttribute('style');
							BX.removeClass(topPanelThead, 'fixed');
							topPanelTfoot.removeAttribute('style');
							BX.removeClass(topPanelTfoot, 'fixed');
							BX.removeClass(topPanelTfoot, 'visible');
						}
					}				
				} else {
					if(!isTopPanelFixed && scrollTop >= topPanelContainerTop) {
						isTopPanelFixed = true;
						BX.style(topPanelContainer, 'height', topPanel.offsetHeight + 'px');
						BX.addClass(topPanel, 'fixed');	
					} else if(!!isTopPanelFixed && scrollTop < topPanelContainerTop) {
						isTopPanelFixed = false;				
						topPanelContainer.removeAttribute('style');
						BX.removeClass(topPanel, 'fixed');
					}
				}
				lastScrollTop = scrollTop;
			}

			checkTopTopPanel();
			BX.bind(window, 'scroll', function() {
				checkTopTopPanel();
			});

			BX.bind(window, 'resize', function() {
				if(window.innerWidth < 1043 && !!BX.hasClass(topPanel, 'fixed')) {
					BX.removeClass(topPanel, 'fixed');
					if(!sMenuInt2) {
						BX.style(topPanelContainer, 'height', topPanelThead.offsetHeight + topPanelTfoot.offsetHeight + 'px');
						BX.addClass(topPanelThead, 'fixed');
					} else {
						topPanelContainer.removeAttribute('style');
					}
				} else if(window.innerWidth >= 1043 && !!isTopPanelFixed) {
					if(!sMenuInt2) {
						BX.removeClass(topPanelThead, 'fixed');
						if(!!BX.hasClass(topPanelTfoot, 'fixed')) {				
							BX.removeClass(topPanelTfoot, 'fixed');
							BX.removeClass(topPanelTfoot, 'visible');
							topPanelTfoot.removeAttribute('style');
						}
					}
					BX.style(topPanelContainer, 'height', topPanel.offsetHeight + 'px');
					BX.addClass(topPanel, 'fixed');
				}
			});
		}
		
		//PRO_WS//
		if (BX.hasClass(document.body, 'legacy-cookies-agreement')) {
			var sc_options = {
				text: 'Пользуясь нашим сайтом, вы соглашаетесь с тем, что <a target="_blank" href="/legacy-cookies-agreement/">мы используем cookies</a>.',
				textButton: "Принять",
			};

			function getCookie(name) {
				let matches = document.cookie.match(
					new RegExp(
						"(?:^|; )" +
						name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, "\\\$1") +
						"=([^;]*)",
					),
				);
				return matches ? decodeURIComponent(matches[1]) : undefined;
			}

			function setCookie(name, value, options = {}) {
				options = Object.assign({ path: "/" }, options);

				if (options.expires instanceof Date) {
					options.expires = options.expires.toUTCString();
				}

				let updatedCookie =
					encodeURIComponent(name) + "=" + encodeURIComponent(value);

				for (let optionKey in options) {
					updatedCookie += "; " + optionKey;
					let optionValue = options[optionKey];
					if (optionValue !== true) {
						updatedCookie += "=" + optionValue;
					}
				}

				document.cookie = updatedCookie;
			}

			function sc_widget(options) {
				if (options) {
					let sc_widget = document.createElement("div");
					let sc_widget_text = document.createElement("div");
					let sc_agree_button = document.createElement("button");
					let sc_agree_text = document.createTextNode(options.textButton);

					sc_widget.className = "sc-widget";

					sc_widget_text.className = "sc-widget__text";
					sc_widget_text.innerHTML = options.text;

					sc_agree_button.className = "btn btn-primary";
					sc_agree_button.append(sc_agree_text);

					sc_widget.append(sc_widget_text);
					sc_widget.append(sc_agree_button);

					sc_agree_button.onclick = () => {
						let expiresDate = new Date();
						expiresDate.setFullYear(expiresDate.getFullYear() + 1);
						setCookie('sc_cookies', true, { expires: expiresDate });
						sc_widget.remove();
					}

					document.body.append(sc_widget);
				}
			}

			if (!getCookie("sc_cookies")) {
				sc_widget(sc_options);
			}
		}
		//PRO_WS//
		
		//SEARCH//	
		var btnShowSearch = document.body.querySelector('[data-entity="showSearch"]');	
		if(!!btnShowSearch) {
			BX.bind(btnShowSearch, 'click', function(e) {
				var topPanelSearch = document.body.querySelector('.top-panel__search'),
					slidePanel = document.body.querySelector('.slide-panel');
				if(!!topPanelSearch && !!slidePanel) {
					slidePanel.appendChild(
						BX.create('DIV', {
							props: {
								className: 'slide-panel__title-wrap'
							},
							children: [
								BX.create('I', {
									props: {
										className: 'icon-search'
									}
								}),						
								BX.create('SPAN', {
									props: {
										className: 'slide-panel__title'
									},
									html: BX.message('SLIDE_PANEL_SEARCH_TITLE')
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

					slidePanel.appendChild(
						BX.create('DIV', {
							props: {
								className: 'slide-panel__content scrollbar-inner'
							}
						})
					);

					var slidePanelContent = slidePanel.querySelector('.slide-panel__content');
					if(!!slidePanelContent) {
						slidePanelContent.appendChild(topPanelSearch);
						$(slidePanelContent).scrollbar();
						
						var iOS = navigator.userAgent.match(/iPhone|iPad|iPod/i);
						if(iOS == null) {
							var slidePanelInput = slidePanelContent.querySelector('input[name="q"]');
							if(!!slidePanelInput)
								slidePanelInput.focus();
						}
					}

					var scrollWidth = window.innerWidth - document.body.clientWidth;
					if(scrollWidth > 0) {
						BX.style(document.body, 'padding-right', scrollWidth + 'px');
						
						if(!!topPanel) {
							if(BX.hasClass(topPanel, 'fixed'))
								BX.style(topPanel, 'padding-right', scrollWidth + 'px');						
							if(!!topPanelThead && BX.hasClass(topPanelThead, 'fixed'))
								BX.style(topPanelThead, 'padding-right', scrollWidth + 'px');						
							if(!!topPanelTfoot && BX.hasClass(topPanelTfoot, 'fixed'))
								BX.style(topPanelTfoot, 'padding-right', scrollWidth + 'px');
						}

						var sectionPanel = document.body.querySelector('.catalog-section-panel');
						if(!!sectionPanel && BX.hasClass(sectionPanel, 'fixed'))
							BX.style(sectionPanel, 'padding-right', scrollWidth + 'px');
						
						var tabsPanel = document.body.querySelector('[data-entity="tabs"]');
						if(!!tabsPanel && BX.hasClass(tabsPanel, 'fixed'))
							BX.style(tabsPanel, 'padding-right', scrollWidth + 'px');

						var objectsMap = document.body.querySelector('.objects-map');
						if(!!objectsMap)
							BX.style(objectsMap, 'padding-right', scrollWidth + 'px');

						var catalogCompareList = document.body.querySelector('.catalog-compare-list');
						if(!!catalogCompareList && BX.hasClass(catalogCompareList, 'active'))
							BX.style(catalogCompareList, 'margin-left', '-' + scrollWidth/2 + 'px');
					}
					
					var scrollTop = BX.GetWindowScrollPos().scrollTop;
					if(!!scrollTop && scrollTop > 0)
						BX.style(document.body, 'top', '-' + scrollTop + 'px');
							
					BX.addClass(document.body, 'slide-panel-active');
					BX.addClass(slidePanel, 'active');
				
					document.body.appendChild(
						BX.create('DIV', {
							props: {
								className: 'modal-backdrop slide-panel__backdrop fadeInBig'
							}
						})
					);

					e.stopPropagation();
				}
			});
		}
		
		//SHARE//
		var shareIcon = document.body.querySelector('[data-entity="showShare"]'),
			shareContent = document.body.querySelector('[data-entity="shareContent"]');
		
		if(!!shareIcon && !!shareContent) {
			BX.bind(shareIcon, 'click', function() {
				if(BX.isNodeHidden(shareContent)) {
					BX.style(shareContent, 'display', 'flex');
					BX.addClass(shareIcon, 'active');
				} else {
					BX.style(shareContent, 'display', 'none');
					BX.removeClass(shareIcon, 'active');
				}
			});
			
			BX.bind(document, 'click', function(event) {
				if(!BX.isNodeHidden(shareContent) && 
					!BX.findParent(event.target, {attr: {'data-entity': 'showShare'}}, false) && event.target.getAttribute('data-entity') != 'showShare' &&
					!BX.findParent(event.target, {attr: {'data-entity': 'shareContent'}}, false) && event.target.getAttribute('data-entity') != 'shareContent'
				) {
					BX.style(shareContent, 'display', 'none');
					BX.removeClass(shareIcon, 'active');
					event.stopPropagation();
				}			
			});
		}
		
		//TABS//
		var tabsContainer = document.body.querySelector('[data-entity="main-tabs"]'),
			tabsTabs = !!tabsContainer && tabsContainer.querySelector('.tabs__tabs'),
			tabs = !!tabsContainer && tabsContainer.querySelectorAll('[data-entity="tab"]'),
			tabsContentContainer = document.body.querySelector('[data-entity="main-tabs-content"]'),		
			tabValue, targetTab,
			haveActive = false, i;			
		
		if(!!tabsTabs) {
			BX.loadScript(BX.message('SITE_TEMPLATE_PATH') + '/js/owlCarousel/owl.carousel.min.js', function() {
				BX.addClass(tabsTabs, 'owl-carousel');
				$(tabsTabs).owlCarousel({								
					autoWidth: true,
					nav: true,
					navText: ['<i class=\"icon-arrow-left\"></i>', '<i class=\"icon-arrow-right\"></i>'],
					navContainer: '.tabs__scroll',
					dots: false,			
				});
				if(!!tabs) {
					for(var i in tabs) {
						if(tabs.hasOwnProperty(i) && BX.type.isDomNode(tabs[i])) {				
							tabValue = tabs[i].getAttribute('data-value');
							if(tabValue) {
								targetTab = tabsContentContainer.querySelector('[data-value="' + tabValue + '"]');
								if(BX.type.isDomNode(targetTab)) {
									if(!haveActive) {
										BX.addClass(tabs[i], 'active');								
										BX.show(targetTab);
										haveActive = true;
									} else {
										BX.removeClass(tabs[i], 'active');								
										BX.hide(targetTab);
									}				
									BX.bind(tabs[i], 'click', function(event) {
										BX.PreventDefault(event);

										var targetTabValue = this.getAttribute('data-value'),
											j, k;
										
										if(!BX.hasClass(this, 'active') && targetTabValue) {
											var tabsContent = tabsContentContainer.querySelectorAll('[data-entity="tab-content"]');
											if(!!tabsContent) {
												for(var j in tabsContent) {
													if(tabsContent.hasOwnProperty(j) && BX.type.isDomNode(tabsContent[j])) {
														if(tabsContent[j].getAttribute('data-value') == targetTabValue) {
															BX.show(tabsContent[j]);
														} else {
															BX.hide(tabsContent[j]);
														}
													}
												}
											}
											for(k in tabs) {
												if(tabs.hasOwnProperty(k) && BX.type.isDomNode(tabs[k])) {
													if(tabs[k].getAttribute('data-value') == targetTabValue) {
														BX.addClass(tabs[k], 'active');
													} else {
														BX.removeClass(tabs[k], 'active');
													}
												}
											}
										}
									});
								}
							}
						}
					}
				}
			});
		}
		
		//SLIDE_PANEL//
		//SLIDE_PANEL_CONTACTS//
		//SLIDE_PANEL_CART//
		//POPUP_PANEL//
		//SEARCH_YANDEX//
		var slidePanel = document.body.querySelector('.slide-panel');
		
		function slidePanelClose() {
			var slidePanelBack = document.body.querySelector('.slide-panel__backdrop'),
				slidePanelSearch = slidePanel.querySelector('.top-panel__search');
				btnShowSearch = document.body.querySelector('[data-entity="showSearch"]');
			
			BX.removeClass(slidePanel, 'active');
			
			if(!!slidePanelSearch) {
				var slidePanelInput = slidePanelSearch.querySelector('input[name="q"]');
				if(!!slidePanelInput && BX.findParent(slidePanelInput, {className: 'search-yandex-form'})) {
					BX.isYandexSearchInputFocused = false;
					BX.yandexSearchInputValue = slidePanelInput.value;
					BX.removeClass(slidePanelInput, 'active');
					slidePanelInput.value = '';
				}
				btnShowSearch.parentNode.insertBefore(slidePanelSearch, btnShowSearch.nextSibling);				
			}
			
			BX.cleanNode(slidePanel);
			
			BX.removeClass(slidePanelBack, 'fadeInBig');
			BX.addClass(slidePanelBack, 'fadeOutBig');
			
			setTimeout(function() {
				BX.remove(slidePanelBack);
			}, 300);
			
			BX.removeClass(document.body, 'slide-panel-active');
			
			var slidePanelContacts = document.body.querySelector('.slide-panel-contacts'),
				slidePanelCart = document.body.querySelector('.slide-panel-cart'),
				slidePanelMap = document.body.querySelector('.slide-panel-map'),
				popupPanel = document.body.querySelector('.popup-panel');
			
			if(!slidePanelContacts && !slidePanelCart && !slidePanelMap && !popupPanel) {
				BX.style(document.body, 'padding-right', '');
				
				var scrollTop = Math.abs(parseInt(BX.style(document.body, 'top'), 10));
				if(!!scrollTop && scrollTop > 0) {
					window.scrollTo(0, scrollTop);
					BX.style(document.body, 'top', '');
				}
				
				if(!!topPanel) {
					BX.style(topPanel, 'padding-right', '');
					if(!!topPanelThead)
						BX.style(topPanelThead, 'padding-right', '');
					if(!!topPanelTfoot)
						BX.style(topPanelTfoot, 'padding-right', '');
				}

				var sectionPanel = document.body.querySelector('.catalog-section-panel');
				if(!!sectionPanel)
					BX.style(sectionPanel, 'padding-right', '');

				var tabsPanel = document.body.querySelector('[data-entity="tabs"]');
				if(!!tabsPanel)
					BX.style(tabsPanel, 'padding-right', '');

				var objectsMap = document.body.querySelector('.objects-map');
				if(!!objectsMap)
					BX.style(objectsMap, 'padding-right', '');

				var catalogCompareList = document.body.querySelector('.catalog-compare-list');
				if(!!catalogCompareList)
					BX.style(catalogCompareList, 'margin-left', '');
			}
		}

		function slidePanelContactsClose() {
			var slidePanelContacts = document.body.querySelector('.slide-panel-contacts'),
				slidePanelContactsBack = document.body.querySelector('.slide-panel-contacts__backdrop');

			BX.removeClass(slidePanelContacts, 'slidePanelContactsRightIn');
			BX.addClass(slidePanelContacts, 'slidePanelContactsRightOut');
			
			BX.removeClass(slidePanelContactsBack, 'fadeInBig');
			BX.addClass(slidePanelContactsBack, 'fadeOutBig');
			
			setTimeout(function() {
				BX.remove(slidePanelContacts);
				BX.remove(slidePanelContactsBack);
			}, 300);
			
			BX.removeClass(document.body, 'slide-panel-contacts-active');
			
			var slidePanelCart = document.body.querySelector('.slide-panel-cart'),
				slidePanelMap = document.body.querySelector('.slide-panel-map'),
				popupPanel = document.body.querySelector('.popup-panel');

			if(!slidePanelCart && !slidePanelMap && !popupPanel) {
				BX.style(document.body, 'padding-right', '');
				
				var scrollTop = Math.abs(parseInt(BX.style(document.body, 'top'), 10));
				if(!!scrollTop && scrollTop > 0) {
					window.scrollTo(0, scrollTop);
					BX.style(document.body, 'top', '');
				}
				
				if(!!topPanel) {
					BX.style(topPanel, 'padding-right', '');
					if(!!topPanelThead)
						BX.style(topPanelThead, 'padding-right', '');
					if(!!topPanelTfoot)
						BX.style(topPanelTfoot, 'padding-right', '');
				}

				var sectionPanel = document.body.querySelector('.catalog-section-panel');
				if(!!sectionPanel)
					BX.style(sectionPanel, 'padding-right', '');

				var tabsPanel = document.body.querySelector('[data-entity="tabs"]');
				if(!!tabsPanel)
					BX.style(tabsPanel, 'padding-right', '');

				var objectsMap = document.body.querySelector('.objects-map');
				if(!!objectsMap)
					BX.style(objectsMap, 'padding-right', '');

				var catalogCompareList = document.body.querySelector('.catalog-compare-list');
				if(!!catalogCompareList)
					BX.style(catalogCompareList, 'margin-left', '');
			}
		}
		
		function slidePanelCartClose() {
			var slidePanelCart = document.body.querySelector('.slide-panel-cart'),
				slidePanelCartFooter = slidePanelCart.querySelector('[data-entity="basketFooter"]'),
				slidePanelCartBack = document.body.querySelector('.slide-panel-cart__backdrop');

			BX.removeClass(slidePanelCart, 'slidePanelCartRightIn');
			BX.addClass(slidePanelCart, 'slidePanelCartRightOut');

			if(!!slidePanelCartFooter) {
				BX.removeClass(slidePanelCartFooter, 'slidePanelCartFooterRightIn');
				BX.addClass(slidePanelCartFooter, 'slidePanelCartFooterRightOut');
			}

			BX.removeClass(slidePanelCartBack, 'fadeInBig');
			BX.addClass(slidePanelCartBack, 'fadeOutBig');
			
			setTimeout(function() {
				BX.remove(slidePanelCart);
				BX.remove(slidePanelCartBack);
			}, 300);
			
			BX.removeClass(document.body, 'slide-panel-cart-active');

			BX.removeCustomEvent(BX.Sale.BasketComponent, 'quickOrderRequest', BX.proxy(BX.Sale.BasketComponent.quickOrderRequest, BX.Sale.BasketComponent));
			
			var slidePanelMap = document.body.querySelector('.slide-panel-map'),
				popupPanel = document.body.querySelector('.popup-panel');

			if(!slidePanelMap && !popupPanel) {
				BX.style(document.body, 'padding-right', '');
				
				var scrollTop = Math.abs(parseInt(BX.style(document.body, 'top'), 10));
				if(!!scrollTop && scrollTop > 0) {
					window.scrollTo(0, scrollTop);
					BX.style(document.body, 'top', '');
				}
				
				if(!!topPanel) {
					BX.style(topPanel, 'padding-right', '');
					if(!!topPanelThead)
						BX.style(topPanelThead, 'padding-right', '');
					if(!!topPanelTfoot)
						BX.style(topPanelTfoot, 'padding-right', '');
				}

				var sectionPanel = document.body.querySelector('.catalog-section-panel');
				if(!!sectionPanel)
					BX.style(sectionPanel, 'padding-right', '');

				var tabsPanel = document.body.querySelector('[data-entity="tabs"]');
				if(!!tabsPanel)
					BX.style(tabsPanel, 'padding-right', '');

				var objectsMap = document.body.querySelector('.objects-map');
				if(!!objectsMap)
					BX.style(objectsMap, 'padding-right', '');

				var catalogCompareList = document.body.querySelector('.catalog-compare-list');
				if(!!catalogCompareList)
					BX.style(catalogCompareList, 'margin-left', '');
			}
		}

		function slidePanelMapClose() {
			var slidePanelMap = document.body.querySelector('.slide-panel-map'),
				slidePanelMapBack = document.body.querySelector('.slide-panel-map__backdrop');

			BX.removeClass(slidePanelMap, 'slidePanelMapRightIn');
			BX.addClass(slidePanelMap, 'slidePanelMapRightOut');
			
			BX.removeClass(slidePanelMapBack, 'fadeInBig');
			BX.addClass(slidePanelMapBack, 'fadeOutBig');
			
			setTimeout(function() {
				BX.remove(slidePanelMap);
				BX.remove(slidePanelMapBack);
			}, 300);
			
			BX.removeClass(document.body, 'slide-panel-map-active');

			var popupPanel = document.body.querySelector('.popup-panel');			
			
			if(!popupPanel) {
				BX.style(document.body, 'padding-right', '');
				
				var scrollTop = Math.abs(parseInt(BX.style(document.body, 'top'), 10));
				if(!!scrollTop && scrollTop > 0) {
					window.scrollTo(0, scrollTop);
					BX.style(document.body, 'top', '');
				}
				
				if(!!topPanel) {
					BX.style(topPanel, 'padding-right', '');
					if(!!topPanelThead)
						BX.style(topPanelThead, 'padding-right', '');
					if(!!topPanelTfoot)
						BX.style(topPanelTfoot, 'padding-right', '');
				}

				var sectionPanel = document.body.querySelector('.catalog-section-panel');
				if(!!sectionPanel)
					BX.style(sectionPanel, 'padding-right', '');

				var tabsPanel = document.body.querySelector('[data-entity="tabs"]');
				if(!!tabsPanel)
					BX.style(tabsPanel, 'padding-right', '');

				var objectsMap = document.body.querySelector('.objects-map');
				if(!!objectsMap)
					BX.style(objectsMap, 'padding-right', '');

				var catalogCompareList = document.body.querySelector('.catalog-compare-list');
				if(!!catalogCompareList)
					BX.style(catalogCompareList, 'margin-left', '');
			}
		}

		function popupPanelClose() {
			var popupPanel = document.body.querySelector('.popup-panel'),
				popupPanelBack = document.body.querySelector('.popup-panel__backdrop');
			
			if(popupPanel.hasAttribute('data-location-href')) {
				if(history.scrollRestoration) {
					history.scrollRestoration = 'manual';
				}
				window.history.pushState('', document.title, popupPanel.getAttribute('data-location-href'));
			}
			
			BX.removeClass(popupPanel, 'fadeInBig');
			BX.addClass(popupPanel, 'fadeOutBig');

			BX.removeClass(popupPanelBack, 'fadeInBig');
			BX.addClass(popupPanelBack, 'fadeOutBig');

			setTimeout(function() {
				BX.remove(popupPanel);
				BX.remove(popupPanelBack);
			}, 300);
			
			BX.removeClass(document.body, 'popup-panel-active');

			if(!!BX.catalogElementPopup)
				BX.catalogElementPopup.destroy();

			if(!!BX.catalogSetPopup)
				BX.catalogSetPopup.destroy();
			
			BX.style(document.body, 'padding-right', '');
			
			var scrollTop = Math.abs(parseInt(BX.style(document.body, 'top'), 10));
			if(!!scrollTop && scrollTop > 0) {
				window.scrollTo(0, scrollTop);
				BX.style(document.body, 'top', '');
			}
			
			if(!!topPanel) {
				BX.style(topPanel, 'padding-right', '');
				if(!!topPanelThead)
					BX.style(topPanelThead, 'padding-right', '');
				if(!!topPanelTfoot)
					BX.style(topPanelTfoot, 'padding-right', '');
			}

			var sectionPanel = document.body.querySelector('.catalog-section-panel');
			if(!!sectionPanel)
				BX.style(sectionPanel, 'padding-right', '');

			var tabsPanel = document.body.querySelector('[data-entity="tabs"]');
			if(!!tabsPanel)
				BX.style(tabsPanel, 'padding-right', '');

			var objectsMap = document.body.querySelector('.objects-map');
			if(!!objectsMap)
				BX.style(objectsMap, 'padding-right', '');

			var catalogCompareList = document.body.querySelector('.catalog-compare-list');
			if(!!catalogCompareList)
				BX.style(catalogCompareList, 'margin-left', '');
		}

		function searchYandexClose() {
			var searchYandex = document.body.querySelector('.search-yandex-popup'),
				searchYandexBack = document.body.querySelector('.search-yandex__backdrop'),
				searchYandexInput = searchYandex.querySelector('input[name="q"]');

			BX.yandexSearchInputValue = searchYandexInput.value;

			BX.removeClass(searchYandex, 'fadeInBig');
			BX.addClass(searchYandex, 'fadeOutBig');

			BX.removeClass(searchYandexBack, 'fadeInBig');
			BX.addClass(searchYandexBack, 'fadeOutBig');

			setTimeout(function() {
				BX.remove(searchYandex);
				BX.remove(searchYandexBack);
			}, 300);
			
			BX.removeClass(document.body, 'search-yandex-active');

			BX.style(document.body, 'padding-right', '');
			
			var scrollTop = Math.abs(parseInt(BX.style(document.body, 'top'), 10));
			if(!!scrollTop && scrollTop > 0) {
				window.scrollTo(0, scrollTop);
				BX.style(document.body, 'top', '');
			}
			
			if(!!topPanel) {
				BX.style(topPanel, 'padding-right', '');
				if(!!topPanelThead)
					BX.style(topPanelThead, 'padding-right', '');
				if(!!topPanelTfoot)
					BX.style(topPanelTfoot, 'padding-right', '');
			}

			var sectionPanel = document.body.querySelector('.catalog-section-panel');
			if(!!sectionPanel)
				BX.style(sectionPanel, 'padding-right', '');

			var tabsPanel = document.body.querySelector('[data-entity="tabs"]');
			if(!!tabsPanel)
				BX.style(tabsPanel, 'padding-right', '');

			var objectsMap = document.body.querySelector('.objects-map');
			if(!!objectsMap)
				BX.style(objectsMap, 'padding-right', '');

			var catalogCompareList = document.body.querySelector('.catalog-compare-list');
			if(!!catalogCompareList)
				BX.style(catalogCompareList, 'margin-left', '');
		}

		function checkSearchYandexSlidePopupPanelClick(e) {
			var searchYandex = document.body.querySelector('.search-yandex-popup');
			if(!!searchYandex) {
				if(BX.hasClass(e.target, 'search-yandex-close') || 
					BX.findParent(e.target, {className: 'search-yandex-close'}) ||
					!BX.findParent(e.target, {className: 'search-yandex-popup'})
				) {
					searchYandexClose();
					e.preventDefault();
					e.stopPropagation();
				}
			} else if(!!slidePanel && BX.hasClass(slidePanel, 'active')) {				
				if(BX.hasClass(e.target, 'slide-panel__close') ||
					BX.findParent(e.target, {className: 'slide-panel__close'}) || (
					!BX.findParent(e.target, {className: 'slide-panel'}) &&
					!BX.findParent(e.target, {className: 'main-user-consent-request-popup'}) &&
					!BX.findParent(e.target, {className: 'iti--container'})
				)) {
					slidePanelClose();
					e.preventDefault();
					e.stopPropagation();
				}
			} else {
				var slidePanelContacts = document.body.querySelector('.slide-panel-contacts');
				if(!!slidePanelContacts) {
					if(BX.hasClass(e.target, 'slide-panel-contacts__close') ||
						BX.findParent(e.target, {className: 'slide-panel-contacts__close'}) || (
						!BX.findParent(e.target, {className: 'slide-panel-contacts'}) &&
						!BX.findParent(e.target, {tagName: 'ymaps'}) &&
						!BX.findParent(e.target, {className: 'main-user-consent-request-popup'}) &&
						!BX.findParent(e.target, {className: 'iti--container'})
					)) {
						slidePanelContactsClose();
						e.preventDefault();
						e.stopPropagation();
					}
				} else {
					var slidePanelCart = document.body.querySelector('.slide-panel-cart');
					if(!!slidePanelCart) {
						if(BX.hasClass(e.target, 'slide-panel-cart__close') ||
							BX.findParent(e.target, {className: 'slide-panel-cart__close'}) ||
							!BX.findParent(e.target, {className: 'slide-panel-cart'})
						) {
							slidePanelCartClose();
							e.preventDefault();
							e.stopPropagation();
						}
					} else {
						var slidePanelMap = document.body.querySelector('.slide-panel-map');
						if(!!slidePanelMap) {
							if(BX.hasClass(e.target, 'slide-panel-map__close') ||
								BX.findParent(e.target, {className: 'slide-panel-map__close'}) || (
								!BX.findParent(e.target, {className: 'slide-panel-map'}) &&
								!BX.findParent(e.target, {tagName: 'ymaps'})
							)) {
								slidePanelMapClose();
								e.preventDefault();
								e.stopPropagation();
							}
						} else {
							var popupPanel = document.body.querySelector('.popup-panel');
							if(!!popupPanel) {
								if(BX.hasClass(e.target, 'popup-panel__close') ||
									BX.findParent(e.target, {className: 'popup-panel__close'}) || (
									!BX.findParent(e.target, {className: 'popup-panel'}) &&
									!BX.findParent(e.target, {className: 'main-user-consent-request-popup'}) &&
									!BX.findParent(e.target, {className: 'iti--container'}) &&
									!BX.findParent(e.target, {className: 'popup-window'}) &&
									!BX.hasClass(e.target, 'popup-window-overlay') &&
									!BX.hasClass(e.target, 'fancybox-overlay') &&
									!BX.findParent(e.target, {className: 'fancybox-overlay'}) &&
									!BX.hasClass(e.target, 'fancybox-close')
								)) {
									popupPanelClose();
									e.preventDefault();
									e.stopPropagation();
								}
							}
						}
					}
				}
			}
		}

		BX.bind(document, 'mousedown', function(e) {
			if(e.button == 0)
				checkSearchYandexSlidePopupPanelClick(e);
		});

		BX.bind(document, 'touchend', function(e) {
			checkSearchYandexSlidePopupPanelClick(e);
		});
		
		BX.bind(document, 'keydown', function(e) {
			if(e.keyCode == 27) {
				var searchYandex = document.body.querySelector('.search-yandex-popup');
				if(!!searchYandex) {
					searchYandexClose();
					e.stopPropagation();
				} else if(!!slidePanel && BX.hasClass(slidePanel, 'active')) {
					slidePanelClose();
					e.stopPropagation();
				} else {
					var slidePanelContacts = document.body.querySelector('.slide-panel-contacts');
					if(!!slidePanelContacts) {
						slidePanelContactsClose();
						e.stopPropagation();
					} else {
						var slidePanelCart = document.body.querySelector('.slide-panel-cart');
						if(!!slidePanelCart) {
							slidePanelCartClose();
							e.stopPropagation();
						} else {
							var slidePanelMap = document.body.querySelector('.slide-panel-map');
							if(!!slidePanelMap) {
								slidePanelMapClose();
								e.stopPropagation();
							} else {
								var fancybox = document.body.querySelector('.fancybox-overlay');
								if(!fancybox) {
									var popupPanel = document.body.querySelector('.popup-panel');
									if(!!popupPanel) {
										popupPanelClose();
										e.stopPropagation();
									}
								}
							}
						}
					}
				}
			}
		});

		BX.bind(window, 'resize', function(e) {
			if(!!slidePanel && BX.hasClass(slidePanel, 'active') && !!slidePanel.querySelector('.top-panel__search') && window.innerWidth >= 1043) {
				slidePanelClose();
				e.stopPropagation();
			} else {
				var searchYandex = document.body.querySelector('.search-yandex-popup');
				if(!!searchYandex && window.innerWidth < 1043) {
					searchYandexClose();
					e.stopPropagation();
				}
			}
		});

		BX.bind(window, 'popstate', function(e) {
			var popupPanel = document.body.querySelector('.popup-panel');
			if(!!popupPanel) {
				popupPanelClose();
				e.stopPropagation();
			} else if(e.target && e.target.location && e.target.location.href) {
				window.location.href = e.target.location.href;
			}
		});
		
		//SCROLL_UP//
		var upButton = document.body.querySelector('.scroll-up');
		if(!!upButton) {
			BX.bind(upButton, "click", function() {
				var windowScroll = BX.GetWindowScrollPos();
				new BX.easing({
					duration: 500,
					start: {scroll: windowScroll.scrollTop},
					finish: {scroll: 0},
					transition: BX.easing.makeEaseOut(BX.easing.transitions.quart),
					step: function(state) {
						window.scrollTo(0, state.scroll);
					}
				}).animate();
			});
			BX.bind(window, 'scroll', function() {			
				var scrollTop = BX.GetWindowScrollPos().scrollTop;			
				if(scrollTop > 150) {
					upButton.style.bottom = '22px';
				} else {
					upButton.style.bottom = '';
				}
			});
		}
	});

	if(window.BX) {
		BX.scrollToNode = function(node) {
			var obNode = BX(node),
				arNodePos = BX.pos(obNode),
				sMenuInt2 = BX.hasClass(document.body, 'slide-menu-interface-2-0-1-inner'),
				topPanel = document.body.querySelector('.top-panel'),
				topPanelHeight = 0,
				topPanelThead = !!topPanel && topPanel.querySelector('.top-panel__thead'),
				topPanelTfoot = !!topPanel && topPanel.querySelector('.top-panel__tfoot'),	
				sectionPanel = document.body.querySelector('.catalog-section-panel'),
				sectionPanelHeight = !!sectionPanel ? sectionPanel.offsetHeight : 0,
				tabsPanel = document.body.querySelector('[data-entity="tabs"]'),
				tabsPanelHeight = !!tabsPanel ? tabsPanel.offsetHeight : 0;
				
			if(window.innerWidth < 1043 && !sMenuInt2) {
				if(!!topPanelThead) {
					topPanelHeight = topPanelThead.offsetHeight;
					if(!!topPanelTfoot && !!BX.hasClass(topPanelTfoot, 'visible'))
						topPanelHeight += topPanelTfoot.offsetHeight;
				}
			} else if(window.innerWidth >= 1043) {
				if(!!topPanel)
					topPanelHeight = topPanel.offsetHeight;
			}
			
			window.scrollTo(arNodePos.left, arNodePos.top - topPanelHeight - sectionPanelHeight - tabsPanelHeight - (window.innerWidth < 1043 ? 0 : 40));
		}
	}
})(window);