(function (window) {
	if(!!window.JCCatalogCompareList)
		return;

	window.JCCatalogCompareList = function (params) {
		this.obCompare = null;
		this.visual = params.VISUAL;
		this.ajax = params.AJAX;
		
		BX.ready(BX.proxy(this.init, this));
	};

	window.JCCatalogCompareList.prototype.init = function() {
		this.obCompare = BX(this.visual.ID);
		if(!!this.obCompare)
			BX.addCustomEvent(window, "OnCompareChange", BX.proxy(this.reload, this));
	};

	window.JCCatalogCompareList.prototype.reload = function() {		
		BX.ajax.post(
			this.ajax.url,
			this.ajax.reload,
			BX.proxy(this.reloadResult, this)
		);
	};

	window.JCCatalogCompareList.prototype.reloadResult = function(result) {
		this.obCompare.innerHTML = result;

		var obCompareCount = this.obCompare.querySelector('[data-entity="count"]');
		if(!!obCompareCount) {
			var count = parseInt(obCompareCount.innerHTML);
			if(count > 0 && !BX.hasClass(this.obCompare, 'active')) {
				BX.addClass(this.obCompare, 'active');
			} else if(count <= 0 && !!BX.hasClass(this.obCompare, 'active')){
				BX.removeClass(this.obCompare, 'active');
			}
		}
	};
})(window);