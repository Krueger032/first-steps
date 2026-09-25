(function() {
	'use strict';

	if(!!window.JCYMapObjects)
		return;

	window.JCYMapObjects = function(params) {
		this.mapObjects = BX(params.container);
		
		BX.ready(BX.delegate(this.init, this));
	};

	window.JCYMapObjects.prototype = {
		/** Инлайн left/top из скрипта только для position:fixed; иначе карта «уезжает» */
		isViewportPinned: function() {
			if (!this.mapObjects) {
				return false;
			}
			return window.getComputedStyle(this.mapObjects).position === 'fixed';
		},

		clearPinStyles: function() {
			BX.style(this.mapObjects, 'left', '');
			BX.style(this.mapObjects, 'top', '');
		},

		init: function() {
			this.checkMapObjectsTop();
			BX.bind(window, 'scroll', BX.proxy(this.checkMapObjectsTop, this));

			this.checkMapObjectsLeft();
			BX.bind(window, 'resize', BX.proxy(this.checkMapObjectsLeft, this));

			BX.addCustomEvent(window, 'slideMenu', BX.delegate(function() {
				this.checkMapObjectsLeft();
			}, this));
		},

		checkMapObjectsTop: function() {
			if (!this.isViewportPinned()) {
				this.clearPinStyles();
				return;
			}

			var topPanel = document.body.querySelector('.top-panel'),
				navPanel = document.body.querySelector('.navigation-wrapper');
			
			if(!!topPanel && !!navPanel) {
				if(window.innerWidth >= 1043) {
					var topPanelTop = topPanel.getBoundingClientRect().top,
						topPanelHeight = topPanel.offsetHeight,
						navPanelTop = navPanel.getBoundingClientRect().top,
						navPanelHeight = navPanel.offsetHeight;
					
					if((navPanelTop + navPanelHeight) > (topPanelTop + topPanelHeight)) {
						BX.style(this.mapObjects, 'top', navPanelTop + navPanelHeight + 'px');
					} else {
						BX.style(this.mapObjects, 'top', topPanelTop + topPanelHeight + 'px');
					}
				} else {
					BX.style(this.mapObjects, 'top', '');
				}
			}
		},

		checkMapObjectsLeft: function() {
			if (!this.isViewportPinned()) {
				this.clearPinStyles();
				return;
			}

			var el = this.mapObjects,
				mapObjectsContainer = (el.closest && el.closest('.objects-map-anchor')) || el.parentNode;

			if(!!mapObjectsContainer) {
				if(window.innerWidth >= 1043) {
					BX.style(this.mapObjects, 'left', mapObjectsContainer.getBoundingClientRect().left +
						Math.abs(parseInt(BX.style(mapObjectsContainer, 'padding-left'), 10)) + 'px'
					);
				} else {
					BX.style(this.mapObjects, 'left', '');
				}
			}
		}
	}
})();