(function() {
	'use strict';

	if(!!window.JCCatalogComponent)
		return;

	window.JCCatalogComponent = function() {
		BX.ready(BX.delegate(this.init, this));
	};

	window.JCCatalogComponent.prototype = {
		init: function() {
			this.checkSectionPanel();

			this.toogle = BX('catalog-section-toggle');
			if(!!this.toogle) {
				BX.bind(this.toogle, 'bxchange', BX.proxy(this.changeSectionToogle, this));
				
				this.toogleAdjusted = false;
				this.adjustSectionToogle();
				BX.bind(window, 'resize', BX.proxy(this.adjustSectionToogle, this));
			}

			this.sectionSortContainer = document.body.querySelector('[data-role="catalogSectionSort"]');
			this.sectionSortPopup = this.sectionSortContainer && this.sectionSortContainer.querySelector('[data-role="dropdownContent"]');
			if(this.sectionSortContainer && this.sectionSortPopup) {
				BX.bind(this.sectionSortContainer, 'click', BX.proxy(this.showSectionSortDropDownPopup, this));
				BX.bind(document, 'click', BX.proxy(this.hideSectionSortDropDownPopup, this));
			}
			
			this.sectionLinksContainer = document.body.querySelector('.catalog-section-links');
			if(!!this.sectionLinksContainer) {
				this.sectionLinksContainerHeight = 92;
				this.sectionLinks = this.sectionLinksContainer.querySelectorAll('.catalog-section-link');
				if(!!this.sectionLinks) {
					this.sectionLinksWidth = 0;
					for(var i in this.sectionLinks) {
						if(this.sectionLinks.hasOwnProperty(i) && BX.type.isDomNode(this.sectionLinks[i]))
							this.sectionLinksWidth += this.sectionLinks[i].offsetWidth + Math.abs(parseInt(BX.style(this.sectionLinks[i], 'marginLeft'), 10));
					}
					
					this.sectionLinkBtnSpan = BX.create('SPAN');
				
					this.sectionLinkBtnI = BX.create('I', {
						props: {
							className: 'icon-arrow-down'
						}
					});
				
					this.sectionLinkBtn = BX.create('DIV', {
						props: {
							className: 'catalog-section-link-btn-container'
						},
						children: [
							BX.create('DIV', {
								props: {
									className: 'catalog-section-link catalog-section-link-btn'
								},
								children: [
									this.sectionLinkBtnSpan,
									this.sectionLinkBtnI
								],
								events: {
									click: BX.proxy(this.showHideSectionLinks, this)
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
		},

		checkSectionPanel: function() {
			this.sectionPanelWrapper = document.body.querySelector('.catalog-section-panel-wrapper');
			this.sectionPanel = !!this.sectionPanelWrapper && this.sectionPanelWrapper.querySelector('.catalog-section-panel');
			
			if(!!this.sectionPanel) {				
				this.sectionPanelFixed = false;
				this.sectionPanelScrolled = false;
				this.lastScrollTop = 0;
				
				BX.bind(window, 'scroll', BX.proxy(this.checkSectionPanelScroll, this));
				BX.bind(window, 'resize', BX.proxy(this.checkSectionPanelResize, this));
			}
		},

		checkSectionPanelScroll: function() {
			var sMenuInt2 = BX.hasClass(document.body, 'slide-menu-interface-2-0-1-inner'),
				topPanel = document.body.querySelector('.top-panel'),
				topPanelHeight = 0,				
				topPanelThead = !!topPanel && topPanel.querySelector('.top-panel__thead'),
				topPanelTfoot = !!topPanel && topPanel.querySelector('.top-panel__tfoot'),				
				sectionPanelWrapperTop = BX.pos(this.sectionPanelWrapper).top,
				sectionPanelHeight = this.sectionPanel.offsetHeight,
				scrollTop = BX.GetWindowScrollPos().scrollTop;
			
			if(window.innerWidth < 1043) {
				if(!!topPanelThead && !!BX.hasClass(topPanelThead, 'fixed')) {
					topPanelHeight = topPanelThead.offsetHeight;
					if(!!topPanelTfoot && !!BX.hasClass(topPanelTfoot, 'visible'))
						topPanelHeight += topPanelTfoot.offsetHeight;
				}
				
				if(scrollTop + topPanelHeight >= sectionPanelWrapperTop) {
					if(!this.sectionPanelFixed) {
						this.sectionPanelFixed = true;
						BX.style(this.sectionPanelWrapper, 'height', sectionPanelHeight + 'px');
						BX.style(this.sectionPanel, 'top', topPanelHeight + 'px');							
						BX.addClass(this.sectionPanel, 'fixed');
					} else if(!sMenuInt2){
						if(!this.sectionPanelScrolled && topPanelHeight > 0 && scrollTop < BX.lastScrollTop) {
							this.sectionPanelScrolled = true;
							new BX.easing({
								duration: 300,
								start: {top: Math.abs(parseInt(BX.style(this.sectionPanel, 'top'), 10))},
								finish: {top: topPanelHeight},
								transition: BX.easing.transitions.linear,
								step: BX.delegate(function(state) {
									if(!!this.sectionPanelScrolled)
										BX.style(this.sectionPanel, 'top', state.top + 'px');								
								}, this)
							}).animate();
						} else if(!!this.sectionPanelScrolled && topPanelHeight > 0 && scrollTop > BX.lastScrollTop) {								
							this.sectionPanelScrolled = false;
							new BX.easing({
								duration: 300,
								start: {top: Math.abs(parseInt(BX.style(this.sectionPanel, 'top'), 10))},
								finish: {top: topPanelHeight},
								transition: BX.easing.transitions.linear,
								step: BX.delegate(function(state) {
									BX.style(this.sectionPanel, 'top', state.top + 'px');								
								}, this)
							}).animate();
						}
					}
				} else if(!!this.sectionPanelFixed && (scrollTop + topPanelHeight < sectionPanelWrapperTop)) {
					this.sectionPanelFixed = false;
					this.sectionPanelScrolled = false;
					this.sectionPanelWrapper.removeAttribute('style');
					this.sectionPanel.removeAttribute('style');
					BX.removeClass(this.sectionPanel, 'fixed');
				}
			} else {
				if(!!topPanel && !!BX.hasClass(topPanel, 'fixed'))
					topPanelHeight = topPanel.offsetHeight;
				
				if(!this.sectionPanelFixed && (scrollTop + topPanelHeight >= sectionPanelWrapperTop)) {
					this.sectionPanelFixed = true;
					BX.style(this.sectionPanelWrapper, 'height', sectionPanelHeight + 'px');
					BX.style(this.sectionPanel, 'top', topPanelHeight + 'px');							
					BX.addClass(this.sectionPanel, 'fixed');
				} else if(!!this.sectionPanelFixed && (scrollTop + topPanelHeight < sectionPanelWrapperTop)) {
					this.sectionPanelFixed = false;
					this.sectionPanelWrapper.removeAttribute('style');
					this.sectionPanel.removeAttribute('style');
					BX.removeClass(this.sectionPanel, 'fixed');
				}
			}
			BX.lastScrollTop = scrollTop;
		},

		checkSectionPanelResize: function() {
			if(!!BX.hasClass(this.sectionPanel, 'fixed')) {
				var topPanel = document.body.querySelector('.top-panel'),
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
					this.sectionPanelScrolled = false;
				}
				
				BX.style(this.sectionPanel, 'top', topPanelHeight + 'px');
			}
		},

		changeSectionToogle: function() {
			var url = new URL(window.location.href);
			
			url.searchParams.set('view', this.toogle.checked ? 'collections' : 'items');
			window.location.href = url.toString();
		},

		adjustSectionToogle: function() {
			var toogle = this.toogle.parentNode,
				tooglePanelWrapper = document.body.querySelector('.catalog-section-toggle-wrapper'),
				sectionPanelFilterToogle = document.body.querySelector('.catalog-section-filter-toggle');
			
			if(window.innerWidth < 1043) {
				if(!this.toogleAdjusted) {
					if(!tooglePanelWrapper)
						tooglePanelWrapper = BX.create('DIV', {props: {className: 'catalog-section-toggle-wrapper'}});
					
					if(!!tooglePanelWrapper && !!this.sectionPanelWrapper) {
						tooglePanelWrapper.appendChild(toogle);
						BX.removeClass(toogle, 'hidden-xs hidden-sm');
						this.sectionPanelWrapper.parentNode.insertBefore(tooglePanelWrapper, this.sectionPanelWrapper);
						this.toogleAdjusted = true;
					}
				}
			} else {
				if(this.toogleAdjusted && !!sectionPanelFilterToogle && !!tooglePanelWrapper) {
					BX.append(toogle, sectionPanelFilterToogle);
					BX.addClass(toogle, 'hidden-xs hidden-sm');
					BX.remove(tooglePanelWrapper);
					this.toogleAdjusted = false;
				}		
			}
		},

		showSectionSortDropDownPopup: function() {
			if(BX.isNodeHidden(this.sectionSortPopup)) {
				BX.style(this.sectionSortPopup, 'display', '');
				BX.addClass(this.sectionSortContainer, 'active');
			} else {
				BX.style(this.sectionSortPopup, 'display', 'none');
				BX.removeClass(this.sectionSortContainer, 'active');
			}
		},

		hideSectionSortDropDownPopup: function(event) {			
			var target = BX.getEventTarget(event);
			if(!BX.findParent(target, {attr: {'data-role': 'catalogSectionSort'}}, false) && target.getAttribute('data-role') != 'catalogSectionSort') {
				BX.style(this.sectionSortPopup, 'display', 'none');
				BX.removeClass(this.sectionSortContainer, 'active');
			}
		},
		
		adjustSectionLinkBtn: function() {
			if(window.innerWidth < 1043) {
				if(BX.pos(this.sectionLinks[Object.keys(this.sectionLinks).length - 1], true).bottom > this.sectionLinksContainerHeight) {
					if(!this.sectionLinkBtnAdjusted) {
						this.sectionLinkBtnAdjusted = true;

						if(!BX.hasClass(this.sectionLinksContainer, 'active'))
							this.sectionLinkBtnSpan.innerHTML = BX.message('SECTION_LINKS_ALL');
						
						this.sectionLinksContainer.appendChild(this.sectionLinkBtn);						
					} else if(!!this.sectionLinkBtnAdjusted && !BX.hasClass(this.sectionLinksContainer, 'active')) {
						this.sectionLinkBtnSpan.innerHTML = BX.message('SECTION_LINKS_ALL');
					}
				} else if((BX.pos(this.sectionLinks[Object.keys(this.sectionLinks).length - 1], true).bottom <= this.sectionLinksContainerHeight) && !!this.sectionLinkBtnAdjusted) {
					this.sectionLinkBtnAdjusted = false;

					this.sectionLinksContainer.removeChild(this.sectionLinkBtn);
				}
			} else {
				if(this.sectionLinksWidth > this.sectionLinksContainer.offsetWidth) {			
					if(!this.sectionLinkBtnAdjusted) {
						this.sectionLinkBtnAdjusted = true;

						if(!BX.hasClass(this.sectionLinksContainer, 'active'))
							this.sectionLinkBtnSpan.innerHTML = BX.message('SECTION_LINKS_SHOW_ALL');
						
						this.sectionLinksContainer.appendChild(this.sectionLinkBtn);						
					} else if(!!this.sectionLinkBtnAdjusted && !BX.hasClass(this.sectionLinksContainer, 'active')) {
						this.sectionLinkBtnSpan.innerHTML = BX.message('SECTION_LINKS_SHOW_ALL');
					}
				} else if((this.sectionLinksWidth <= this.sectionLinksContainer.offsetWidth) && !!this.sectionLinkBtnAdjusted) {
					this.sectionLinkBtnAdjusted = false;

					this.sectionLinksContainer.removeChild(this.sectionLinkBtn);
				}
			}
		},

		showHideSectionLinks: function() {
			if(!BX.hasClass(this.sectionLinksContainer, 'active')) {
				BX.addClass(this.sectionLinksContainer, 'active');		
				this.sectionLinkBtnSpan.innerHTML = BX.message('SECTION_LINKS_HIDE');
				this.sectionLinkBtnI.className = 'icon-arrow-up';
			} else {
				BX.removeClass(this.sectionLinksContainer, 'active');
				this.sectionLinkBtnSpan.innerHTML = window.innerWidth < 1043 ? BX.message('SECTION_LINKS_ALL') : BX.message('SECTION_LINKS_SHOW_ALL');
				this.sectionLinkBtnI.className = 'icon-arrow-down';
			}
		}
	};
})();