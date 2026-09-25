(function() {
	'use strict';

	if(!!window.JCHorizontalMultilevelMenu)
		return;

	window.JCHorizontalMultilevelMenu = function(params) {
		this.menu = BX(params.container);
		
		BX.ready(BX.delegate(this.init, this));
	};

	window.JCHorizontalMultilevelMenu.prototype = {
		init: function() {
			$(this.menu).moreMenu();

			var dropDownMenuAll = this.menu.querySelectorAll('[data-entity="dropdown-menu"]');
			if(!!dropDownMenuAll) {
				for(var i in dropDownMenuAll) {
					if(dropDownMenuAll.hasOwnProperty(i)) {
						var dropDownMenuLiActive = dropDownMenuAll[i].querySelector('.active');
						if(!!dropDownMenuLiActive) {						
							var parentMenuLi = BX.findParent(dropDownMenuAll[i], {tagName: 'LI'});
							if(!!parentMenuLi)
								BX.addClass(parentMenuLi, 'active');
						}
					}
				}
			}
			
			this.resetDropDownAll();
			BX.bind(window, 'resize', BX.proxy(this.resetDropDownAll, this));
		},

		resetDropDownAll: function() {
			var dropDownAll = this.menu.querySelectorAll('[data-entity="dropdown"]');
			if(!!dropDownAll) {
				for(var i in dropDownAll) {
					if(dropDownAll.hasOwnProperty(i))
						this.hoverIntentLi(dropDownAll[i]);
				}
			}
		},

		hoverIntentLi: function(item) {			
			$(item).hoverIntent({
				over: function() {					
					var dropDownMenu = this.querySelector('[data-entity="dropdown-menu"]');
					if(!!dropDownMenu) {
						BX.style(dropDownMenu, 'min-width', this.offsetWidth + 'px');

						var parentDropDownMenu = BX.findParent(this, {attribute: {'data-entity': 'dropdown-menu'}}),
							dropDownMenuLeft = !!parentDropDownMenu ? this.getBoundingClientRect().left + this.offsetWidth : this.getBoundingClientRect().left;
						
						if(dropDownMenuLeft + dropDownMenu.offsetWidth <= document.body.clientWidth) {
							BX.style(dropDownMenu, 'left', !!parentDropDownMenu ? '100%' : 'auto');
						} else {
							BX.style(dropDownMenu, 'left', 'auto');
							BX.style(dropDownMenu, 'right', !!parentDropDownMenu ? '100%' : '0');
						}
						
						new BX.easing({
							duration: 150,
							start: {opacity: 0},
							finish: {opacity: 100},
							transition: BX.easing.transitions.linear,
							step: function(state) {								
								dropDownMenu.style.opacity = state.opacity / 100;
							}
						}).animate();
					}					
				},
				out: function() {					
					var dropDownMenu = this.querySelector('[data-entity="dropdown-menu"]');
					if(!!dropDownMenu) {
						new BX.easing({
							duration: 150,
							start: {opacity: 100},
							finish: {opacity: 0},
							transition: BX.easing.transitions.linear,
							step: function(state){
								dropDownMenu.style.opacity = state.opacity / 100;
							},
							complete: function() {
								BX.style(dropDownMenu, 'min-width', '');
								BX.style(dropDownMenu, 'left', '');
								BX.style(dropDownMenu, 'right', '');
							}
						}).animate();
					}
				},
				timeout: 200
			});			
		}
	}
})();