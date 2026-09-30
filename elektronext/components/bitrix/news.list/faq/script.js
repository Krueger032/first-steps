(function(window) {
	'use strict';

	if(!window.JCNewsListFaq) {
		window.JCNewsListFaq = function(params) {
			this.container = BX(params.container);
			
			BX.ready(BX.delegate(this.init, this));
		};

		window.JCNewsListFaq.prototype = {
			init: function() {
				var items = this.container.querySelectorAll('[data-entity="item"]');
				for(var i in items) {
					if(items.hasOwnProperty(i) && BX.type.isDomNode(items[i])) {
						var title = items[i].querySelector('[data-entity="title"]');
						if(!!title)
							BX.bind(title, 'click', BX.proxy(this.showHideFaqItemDescr, this));
					}
				}
			},

			showHideFaqItemDescr: function() {
				var target = BX.proxy_context,
					icon = target.querySelector('[data-entity="icon"]'),
					item = BX.findParent(target, {attrs: {'data-entity': 'item'}});

				if(!!item) {
					if(!BX.hasClass(item, 'active')) {
						BX.addClass(item, 'active');
						icon && BX.adjust(icon, {props: {className: 'icon-arrow-up'}});
					} else {
						BX.removeClass(item, 'active');
						icon && BX.adjust(icon, {props: {className: 'icon-arrow-down'}});
					}
				}
			}
		}
	}
})(window);