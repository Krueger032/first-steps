(function() {
	'use strict';

	if(!!window.JCCatalogMenu)
		return;

	window.JCCatalogMenu = function(params) {
		this.catalogMenu = BX(params.container);
		this.moreMenu = false;
		this.mainMenu = false;
		
		this.catalogIcon = document.body.querySelector('[data-entity="catalog-icon"]');		
		this.catalogMenuTitleIcon = this.catalogMenu.querySelector('.catalog-menu-title-icon');

		this.menuIcon = document.body.querySelector('[data-entity="menu-icon"]');
		
		BX.ready(BX.delegate(this.init, this));
	};

	window.JCCatalogMenu.prototype = {
		init: function() {
			var subMenuAll = this.catalogMenu.querySelectorAll('[data-entity="dropdown-menu"]');
			if(!!subMenuAll) {
				for(var i in subMenuAll) {
					if(subMenuAll.hasOwnProperty(i)) {
						var subMenuLiActive = subMenuAll[i].querySelector('li.active');
						if(!!subMenuLiActive) {						
							var parentMenuLi = BX.findParent(subMenuAll[i], {tagName: 'LI'});
							if(!!parentMenuLi)
								BX.addClass(parentMenuLi, 'active');
						}
					}
				}
			}
			
			this.adjustCatalogMenu();
			BX.bind(window, 'resize', BX.proxy(this.adjustCatalogMenu, this));

			this.resetDropDownAll();
			BX.bind(window, 'resize', BX.proxy(this.resetDropDownAll, this));
		},

		adjustCatalogMenu: function() {
			if(window.innerWidth >= 1043) {
				if(!this.moreMenu) {
					var catalogMenuUl = this.catalogMenu.querySelector('ul');
					if(!!catalogMenuUl) {
						$(catalogMenuUl).moreMenu();
						this.moreMenu = true;
					}
				}

				this.resetMainMenu();

				if(!!this.catalogIcon)
					BX.unbind(this.catalogIcon, 'click', BX.proxy(this.showCatalogMenu, this));

				if(!!this.catalogMenuTitleIcon)
					BX.unbind(this.catalogMenuTitleIcon, 'click', BX.proxy(this.hideCatalogMenu, this));

				if(!!this.menuIcon)
					BX.unbind(this.menuIcon, 'click', BX.proxy(this.showMainMenu, this));

				BX.unbind(document, 'click', BX.proxy(this.checkDropDownMenu, this));
			} else {
				if(!!this.moreMenu) {
					var catalogMenuUl = this.catalogMenu.querySelector('ul');
					if(!!catalogMenuUl) {
						$(catalogMenuUl).moreMenu({'undo': true});
						this.moreMenu = false;
					}
				}
				
				this.adjustMainMenu();
				
				if(!!this.catalogIcon)
					BX.bind(this.catalogIcon, 'click', BX.proxy(this.showCatalogMenu, this));

				if(!!this.catalogMenuTitleIcon)
					BX.bind(this.catalogMenuTitleIcon, 'click', BX.proxy(this.hideCatalogMenu, this));

				if(!!this.menuIcon)
					BX.bind(this.menuIcon, 'click', BX.proxy(this.showMainMenu, this));

				BX.bind(document, 'click', BX.proxy(this.checkDropDownMenu, this));
			}
		},

		resetDropDownAll: function() {
			var dropDownAll = this.catalogMenu.querySelectorAll('[data-entity="dropdown"]');
			if(!!dropDownAll) {
				for(var i in dropDownAll) {
					if(dropDownAll.hasOwnProperty(i)) {
						BX.bind(dropDownAll[i], 'mouseenter', BX.delegate(function() {
							if(window.innerWidth >= 1043) {
								var target = BX.proxy_context;

								BX.addClass(target, 'hover');
								
								var parentMenu = BX.findParent(target, {attrs: {'data-entity': 'dropdown-menu'}});
								if(!!parentMenu && BX.hasClass(parentMenu, 'catalog-menu')) {
									var dropDownMenu = target.querySelector('[data-entity="dropdown-menu"]');
									if(!!dropDownMenu) {
										if(!BX.hasClass(target, 'more')) {
											BX.style(dropDownMenu, 'left', '0');
										} else {
											BX.style(dropDownMenu, 'left', 'auto');
											if(target.getBoundingClientRect().left + dropDownMenu.offsetWidth > document.body.clientWidth)
												BX.style(dropDownMenu, 'right', '0');
										}
									}

									var catalogMenuBack = document.body.querySelector('.catalog-menu__backdrop');
									if(!catalogMenuBack) {
										document.body.appendChild(
											BX.create('DIV', {
												props: {
													className: 'modal-backdrop catalog-menu__backdrop'
												}
											})
										);
									}
								}
							}
						}, this));
						
						BX.bind(dropDownAll[i], 'mouseleave', BX.delegate(function() {
							if(window.innerWidth >= 1043) {
								var target = BX.proxy_context;

								BX.removeClass(target, 'hover');
								
								var parentMenu = BX.findParent(target, {attrs: {'data-entity': 'dropdown-menu'}});
								if(!!parentMenu && BX.hasClass(parentMenu, 'catalog-menu')) {
									var dropDownMenu = target.querySelector('[data-entity="dropdown-menu"]');
									if(!!dropDownMenu) {
										BX.style(dropDownMenu, 'left', '');
										BX.style(dropDownMenu, 'right', '');
									}
									
									var catalogMenuBack = document.body.querySelector('.catalog-menu__backdrop');
									if(!!catalogMenuBack)
										BX.remove(catalogMenuBack);
								}
							}
						}, this));
					}
				}
			}
		},
			
		resetMainMenu: function() {
			if(!!this.mainMenu) {
				BX.remove(this.mainMenu);				
				this.mainMenu = false;
			}
		},

		adjustMainMenu: function() {
			if(!this.mainMenu) {
				var topMenu = document.body.querySelector('.horizontal-multilevel-menu');

				this.mainMenu = !!topMenu && BX.clone(topMenu);
				if(!!this.mainMenu) {				
					this.mainMenu.removeAttribute('id');
					this.mainMenu.removeAttribute('class');
					
					var mainMenuLi = this.mainMenu.querySelectorAll('li');
					if(!!mainMenuLi) {
						for(var i in mainMenuLi) {
							if(mainMenuLi.hasOwnProperty(i)) {
								var mainMenuLiA = mainMenuLi[i].querySelector('a');
								if(!!mainMenuLiA) {
									BX.adjust(mainMenuLiA, {
										html: '<span class="main-menu-text">' + mainMenuLiA.innerText + '</span>'
									});
									if(mainMenuLi[i].getAttribute('data-entity') == 'dropdown') {
										mainMenuLiA.appendChild(BX.create('SPAN', {
											props: {
												className: 'main-menu-arrow'
											},
											children: [
												BX.create('I', {
													props: {
														className: 'icon-arrow-down'
													}
												})
											],
											events: {
												click: BX.proxy(this.showHideMainMenuDropDownMenu, this)
											}
										}));
									}
								}
							}
						}
					}

					var mainMenuUl = this.mainMenu.querySelectorAll('[data-entity="dropdown-menu"]');
					if(!!mainMenuUl) {
						for(var i in mainMenuUl) {
							if(mainMenuUl.hasOwnProperty(i)) {
								BX.removeClass(mainMenuUl[i], 'horizontal-multilevel-dropdown-menu');
								BX.addClass(mainMenuUl[i], 'main-menu-dropdown-menu');
							}
						}
					}

					var mainMenuCatalog = BX.create('LI', {
							attrs: {
								'data-role': 'catalogMenu'
							},
							children: [
								BX.create('A', {
									attrs: {
										'href': 'javascript:void(0);'
									},
									children: [
										BX.create('SPAN', {
											props: {
												className: 'main-menu-text'
											},
											html: BX.message('CATALOG_FULL')
										}),
										BX.create('SPAN', {
											props: {
												className: 'main-menu-arrow'
											},
											children: [
												BX.create('I', {
													props: {
														className: 'icon-arrow-right'
													}
												})
											]
										})
									]
								})
							],
							events: {
								click: BX.proxy(this.showCatalogMenu, this)
							}						
						});

					BX.prepend(mainMenuCatalog, this.mainMenu);

					this.mainMenu = BX.create('DIV', {
						props: {
							className: 'main-menu'
						},
						children: [
							this.mainMenu
						]
					});
					
					var mainMenuTitle = BX.create('DIV', {
							props: {
								className: 'main-menu-title'
							},
							children: [							
								BX.create('DIV', {
									props: {
										className: 'main-menu-title-icon'
									},
									children: [
										BX.create('I', {
											props: {
												className: 'icon-back'
											}
										})
									]
								}),
								BX.create('DIV', {
									props: {
										className: 'main-menu-title-text'
									},
									html: BX.message('MAIN_MENU')
								})
							],
							events: {
								click: BX.proxy(this.hideMainMenu, this)
							}
						});

					BX.prepend(mainMenuTitle, this.mainMenu);
					
					BX.insertBefore(this.mainMenu, this.catalogMenu);
				}
			}
		},
			
		showCatalogMenu: function() {
			if(!BX.hasClass(this.catalogMenu, 'active')) {
				BX.addClass(this.catalogMenu, 'active');
				BX.addClass(document.body, 'slide-menu-interface-2-0-2-active');

				if(!BX.hasClass(this.catalogMenu, 'catalog-menu-mobile-one_level')) {
					var dropDownAll = this.catalogMenu.querySelectorAll('[data-entity="dropdown"]');
					if(!!dropDownAll) {
						BX.addClass(dropDownAll[0], 'hover');
						var dropDownMenu = dropDownAll[0].querySelector('[data-entity="dropdown-menu"]');
						if(!!dropDownMenu)
							BX.addClass(dropDownMenu, 'active');
					}
				}
			}
		},

		hideCatalogMenu: function() {
			if(!!BX.hasClass(this.catalogMenu, 'active')) {
				BX.removeClass(this.catalogMenu, 'active');
				BX.removeClass(document.body, 'slide-menu-interface-2-0-2-active');

				if(!BX.hasClass(this.catalogMenu, 'catalog-menu-mobile-one_level')) {
					var dropDownHover = this.catalogMenu.querySelector('li.hover');
					if(!!dropDownHover)
						BX.removeClass(dropDownHover, 'hover');

					var dropDownMenuActive = this.catalogMenu.querySelector('.catalog-menu-dropdown-menu.active');
					if(!!dropDownMenuActive)
						BX.removeClass(dropDownMenuActive, 'active');
				}
			}
		},

		showMainMenu: function() {
			if(!!this.mainMenu && !BX.hasClass(this.mainMenu, 'active')) {
				BX.addClass(this.mainMenu, 'active');
				BX.addClass(document.body, 'main-menu-active');
			}
		},

		hideMainMenu: function() {
			if(!!this.mainMenu && !!BX.hasClass(this.mainMenu, 'active')) {
				BX.removeClass(this.mainMenu, 'active');
				BX.removeClass(document.body, 'main-menu-active');
			}
		},

		showHideMainMenuDropDownMenu: function(event) {
			var target = BX.proxy_context;

			var mainMenuLi = BX.findParent(target, {attrs: {'data-entity': 'dropdown'}});
			if(!!mainMenuLi) {
				if(!BX.hasClass(mainMenuLi, 'active'))
					BX.addClass(mainMenuLi, 'active');
				else
					BX.removeClass(mainMenuLi, 'active');

				event.preventDefault();
				event.stopPropagation();
			}
		},

		checkDropDownMenu: function(event) {
			if(BX.hasClass(this.catalogMenu, 'active') && !BX.hasClass(this.catalogMenu, 'catalog-menu-mobile-one_level')) {
				var catalogMenuLi = event.target.tagName == 'LI' ? event.target : BX.findParent(event.target, {tagName: 'LI'});
				if(!!catalogMenuLi) {
					if(catalogMenuLi.getAttribute('data-entity') == 'dropdown') {
						var parentMenu = BX.findParent(catalogMenuLi, {attrs: {'data-entity': 'dropdown-menu'}});
						if(!!parentMenu && BX.hasClass(parentMenu, 'catalog-menu')) {
							var dropDownHover = parentMenu.querySelector('li.hover');
							if(!!dropDownHover)
								BX.removeClass(dropDownHover, 'hover');

							BX.addClass(catalogMenuLi, 'hover');

							var dropDownMenuActive = parentMenu.querySelector('.catalog-menu-dropdown-menu.active');
							if(!!dropDownMenuActive) {
								BX.removeClass(dropDownMenuActive, 'active');
							}

							var dropDownMenu = catalogMenuLi.querySelector('[data-entity="dropdown-menu"]');
							if(!!dropDownMenu) {
								BX.addClass(dropDownMenu, 'active');
							}

							event.preventDefault();
							event.stopPropagation();
						}
					}
				}
			}
		}
	}
})();