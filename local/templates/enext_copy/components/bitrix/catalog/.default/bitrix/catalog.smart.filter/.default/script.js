function JCSmartFilter(ajaxURL, params) {
	this.ajaxURL = ajaxURL;
	this.form = null;
	this.timer = null;
	this.cacheKey = '';
	this.cache = [];
	this.popups = [];	
	if(params && params.SEF_SET_FILTER_URL) {
		this.bindUrlToButton('set_filter', params.SEF_SET_FILTER_URL);
		this.sef = true;
	}
	if(params && params.SEF_DEL_FILTER_URL) {
		this.bindUrlToButton('del_filter', params.SEF_DEL_FILTER_URL);
	}
	if(params && params.HIDE_DISABLED_PROPS) {
		this.hideDisabledProps = params.HIDE_DISABLED_PROPS == 'Y' ? true : false;
	}
	
	BX.ready(BX.delegate(this.init, this));
}

JCSmartFilter.prototype.init = function() {
	this.filter = document.body.querySelector('.bx-filter');
	if(!!this.filter) {
		this.filterInner = this.filter.querySelector('.bx-filter-inner');
		this.filterButtonBox = this.filter.querySelector('.bx-filter-button-box');
		this.filterButtonBoxFixed = false;
		this.btnShowFilter = document.body.querySelector('[data-entity="showFilter"]');
		this.filterToogle = BX('catalog-section-filter-toggle');
		
		this.adjustFilter();
		BX.bind(window, 'resize', BX.proxy(this.adjustFilter, this));
		
		var filterLinks = document.body.querySelectorAll('.catalog-section-filter-link');
		if(!!filterLinks) {
			var tmpFilter = BX.clone(this.filter);
			for(var i in filterLinks) {
				if(filterLinks.hasOwnProperty(i) && BX.type.isDomNode(filterLinks[i])) {
					BX.bind(filterLinks[i], 'click', BX.delegate(function() {
						var id = BX.proxy_context && BX.proxy_context.id;
						if(!!id) {
							var element = tmpFilter.querySelector('[id="' + id + '"]');
							if(!!element) {
								switch(element.type.toLowerCase()) {
									case 'text':
									case 'textarea':
									case 'password':
									case 'hidden':
									case 'select-one':
										if(element.value.length)
											element.value = '';
										break;
									case 'radio':
									case 'checkbox':
										if(element.checked)
											element.checked = false;
										break;
									case 'select-multiple':
										for(var j = 0; j < element.options.length; j++) {
											if(element.options[j].selected)
												element.options[j].selected = false;
										}
										break;
									default:
										break;
								}
								
								this.reload(element, true);
							}
						}
					}, this));
				}
			}
		}

		var scrollbar = this.filter.querySelectorAll('[data-role="scrollbar"]');
		if(!!scrollbar) {
			for(var i in scrollbar) {
				if(scrollbar.hasOwnProperty(i)) {
					$(scrollbar[i]).scrollbar();
				}
			}			
		}
	}
};

JCSmartFilter.prototype.adjustFilter = function() {
	if(!!this.filterToogle) {
		if(!BX.isNodeHidden(this.filterToogle.parentNode)) {
			if(BX.hasClass(this.filter, 'active'))
				this.hideFilter();
			
			BX.bind(this.filterToogle, 'bxchange', BX.proxy(this.changeFilterToogle, this));

			if(!!this.filterButtonBox) {
				this.checkFilterButtonBoxScroll();
				BX.bind(window, 'scroll', BX.proxy(this.checkFilterButtonBoxScroll, this));
			}
			
			if(!!this.btnShowFilter) {
				BX.unbind(this.btnShowFilter, 'click', BX.proxy(this.showFilter, this));
				BX.unbind(document, 'mousedown', BX.proxy(this.checkFilterClick, this));
				BX.unbind(document, 'touchend', BX.proxy(this.checkFilterClick, this));
				BX.unbind(document, 'keydown', BX.proxy(this.checkFilterKeydown, this));
			}
		} else {
			if(!!this.btnShowFilter) {
				BX.bind(this.btnShowFilter, 'click', BX.proxy(this.showFilter, this));				
				BX.bind(document, 'mousedown', BX.proxy(this.checkFilterClick, this));
				BX.bind(document, 'touchend', BX.proxy(this.checkFilterClick, this));
				BX.bind(document, 'keydown', BX.proxy(this.checkFilterKeydown, this));
			}
			
			BX.unbind(this.filterToogle, 'bxchange', BX.proxy(this.changeFilterToogle, this));

			if(!!this.filterButtonBox && !!this.filterButtonBoxFixed) {
				this.filterButtonBoxFixed = false;
				this.filter.removeAttribute('style');
				BX.removeClass(this.filterButtonBox, 'fixed');
				BX.unbind(window, 'scroll', BX.proxy(this.checkFilterButtonBoxScroll, this));
			}
		}
	} else {
		var isFilterLeftActive = BX.hasClass(document.body, 'smart-filter-view-left-active');
		if(!isFilterLeftActive || (!!isFilterLeftActive && window.innerWidth < 1272)) {
			if(!!this.btnShowFilter) {
				BX.bind(this.btnShowFilter, 'click', BX.proxy(this.showFilter, this));
				BX.bind(document, 'mousedown', BX.proxy(this.checkFilterClick, this));
				BX.bind(document, 'touchend', BX.proxy(this.checkFilterClick, this));
				BX.bind(document, 'keydown', BX.proxy(this.checkFilterKeydown, this));
			}

			if(!!this.filterButtonBox && !!this.filterButtonBoxFixed) {
				this.filterButtonBoxFixed = false;
				this.filter.removeAttribute('style');
				BX.removeClass(this.filterButtonBox, 'fixed');
				BX.unbind(window, 'scroll', BX.proxy(this.checkFilterButtonBoxScroll, this));
			}
		} else if(!!isFilterLeftActive && window.innerWidth >= 1272) {
			if(BX.hasClass(this.filter, 'active'))
				this.hideFilter();

			if(!!this.filterButtonBox) {
				this.checkFilterButtonBoxScroll();
				BX.bind(window, 'scroll', BX.proxy(this.checkFilterButtonBoxScroll, this));
			}
			
			if(!!this.btnShowFilter) {
				BX.unbind(this.btnShowFilter, 'click', BX.proxy(this.showFilter, this));
				BX.unbind(document, 'mousedown', BX.proxy(this.checkFilterClick, this));
				BX.unbind(document, 'touchend', BX.proxy(this.checkFilterClick, this));
				BX.unbind(document, 'keydown', BX.proxy(this.checkFilterKeydown, this));
			}
		}
	}
};

JCSmartFilter.prototype.showFilter = function(event) {
	if(!!this.filterInner) {
		BX.addClass(this.filterInner, 'scrollbar-inner');
		$(this.filterInner).scrollbar();
	}
	
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
		
		var sectionPanel = document.body.querySelector('.catalog-section-panel');
		if(!!sectionPanel && !!BX.hasClass(sectionPanel, 'fixed'))
			BX.style(sectionPanel, 'padding-right', scrollWidth + 'px');

		var catalogCompareList = document.body.querySelector('.catalog-compare-list');
		if(!!catalogCompareList && BX.hasClass(catalogCompareList, 'active'))
			BX.style(catalogCompareList, 'margin-left', '-' + scrollWidth/2 + 'px');
	}

	var scrollTop = BX.GetWindowScrollPos().scrollTop;
	if(!!scrollTop && scrollTop > 0)
		BX.style(document.body, 'top', '-' + scrollTop + 'px');
		
	BX.addClass(document.body, 'slide-panel-active')
	BX.addClass(this.filter, 'active');

	var filterBack = document.body.querySelector('.slide-panel__backdrop');
	if(!filterBack) {
		document.body.appendChild(BX.create('DIV', {
			props: {
				className: 'modal-backdrop slide-panel__backdrop fadeInBig'
			}
		}));
	}
	
	event.stopPropagation();
};

JCSmartFilter.prototype.checkFilterClick = function(event) {
	if(event.type == 'mousedown' && event.button != 0)
		return;

	if(BX.hasClass(this.filter, 'active') && (!BX.findParent(event.target, {className: 'bx-filter'}) || BX.findParent(event.target, {className: 'bx-filter-close'}) || BX.hasClass(event.target, 'bx-filter-close'))) {
		this.hideFilter();
		event.stopPropagation();
	}
};

JCSmartFilter.prototype.checkFilterKeydown = function(event) {	
	if(BX.hasClass(this.filter, 'active') && event.keyCode == 27) {	
		this.hideFilter();
		event.stopPropagation();
	}
};

JCSmartFilter.prototype.hideFilter = function() {
	if(!!this.filterInner && BX.hasClass(this.filterInner, 'scrollbar-inner')) {
		this.filterInner.removeAttribute('style');
		BX.removeClass(this.filterInner, 'scrollbar-inner');
		$(this.filterInner).scrollbar('destroy');
	}
	
	BX.removeClass(this.filter, 'active');

	BX.removeClass(document.body, 'slide-panel-active');
	BX.style(document.body, 'padding-right', '');

	var scrollTop = Math.abs(parseInt(BX.style(document.body, 'top'), 10));
	if(!!scrollTop && scrollTop > 0) {
		window.scrollTo(0, scrollTop);
		BX.style(document.body, 'top', '');
	}
	
	var topPanel = document.body.querySelector('.top-panel');
	if(!!topPanel) {
		BX.style(topPanel, 'padding-right', '');
		
		var topPanelThead = topPanel.querySelector('.top-panel__thead');
		if(!!topPanelThead)
			BX.style(topPanelThead, 'padding-right', '');
		
		var topPanelTfoot = topPanel.querySelector('.top-panel__tfoot');
		if(!!topPanelTfoot)
			BX.style(topPanelTfoot, 'padding-right', '');
	}
	
	var sectionPanel = document.body.querySelector('.catalog-section-panel');
	if(!!sectionPanel)
		BX.style(sectionPanel, 'padding-right', '');

	var catalogCompareList = document.body.querySelector('.catalog-compare-list');
	if(!!catalogCompareList)
		BX.style(catalogCompareList, 'margin-left', '');
	
	var filterBack = document.body.querySelector('.slide-panel__backdrop');
	if(!!filterBack) {
		new BX.easing({
			duration: 300,
			start: {opacity: 100},
			finish: {opacity: 0},
			transition: BX.easing.transitions.linear,
			step: function(state) {
				filterBack.style.opacity = state.opacity / 100;
			},
			complete: function() {
				BX.remove(filterBack);
			}
		}).animate();
	}
};

JCSmartFilter.prototype.changeFilterToogle = function() {
	if(this.filterToogle.checked) {
		BX.addClass(document.body, 'smart-filter-view-left-active');
		BX.setCookie(BX.message('COOKIE_NAME') + '_ENEXT_SMART_FILTER_LEFT', 'OPEN', {expires: 32832000, path: '/', secure: false});
	} else {
		BX.removeClass(document.body, 'smart-filter-view-left-active');
		BX.setCookie(BX.message('COOKIE_NAME') + '_ENEXT_SMART_FILTER_LEFT', 'CLOSED', {expires: 32832000, path: '/', secure: false});
	}

	if(!!this.filterButtonBox)
		this.checkFilterButtonBoxScroll();
};

JCSmartFilter.prototype.checkFilterButtonBoxScroll = function() {
	var filterButtonBoxHeight = this.filterButtonBox.offsetHeight;
	
	if(!this.filterButtonBoxFixed &&
		this.filter.getBoundingClientRect().bottom >= window.innerHeight && 
		this.filter.getBoundingClientRect().top + filterButtonBoxHeight <= window.innerHeight &&
		!!BX.hasClass(document.body, 'smart-filter-view-left-active')
	) {		
		this.filterButtonBoxFixed = true;
		BX.style(this.filter, 'paddingBottom', filterButtonBoxHeight + 'px');
		BX.addClass(this.filterButtonBox, 'fixed');
	} else if(!!this.filterButtonBoxFixed && (
		this.filter.getBoundingClientRect().bottom < window.innerHeight || 
		this.filter.getBoundingClientRect().top + filterButtonBoxHeight > window.innerHeight ||
		!BX.hasClass(document.body, 'smart-filter-view-left-active')
	)) {
		this.filterButtonBoxFixed = false;
		this.filter.removeAttribute('style');
		BX.removeClass(this.filterButtonBox, 'fixed');
	}
};

JCSmartFilter.prototype.keyup = function(input) {
	if(!!this.timer) {
		clearTimeout(this.timer);
	}
	this.timer = setTimeout(BX.delegate(function() {
		this.reload(input, false);
	}, this), 500);
};

JCSmartFilter.prototype.click = function(checkbox) {
	if(!!this.timer) {
		clearTimeout(this.timer);
	}

	this.timer = setTimeout(BX.delegate(function() {
		this.reload(checkbox, false);
	}, this), 500);
};

JCSmartFilter.prototype.reload = function(input, needReload) {
	if(this.cacheKey !== '') {
		//Postprone backend query
		if(!!this.timer) {
			clearTimeout(this.timer);
		}
		this.timer = setTimeout(BX.delegate(function() {
			this.reload(input, needReload);
		}, this), 1000);
		return;
	}
	this.cacheKey = '|';

	this.position = BX.pos(input, true);
	this.form = BX.findParent(input, {'tag':'form'});
	if(this.form) {
		var values = [];
		values[0] = {name: 'ajax', value: 'y'};
		this.gatherInputsValues(values, BX.findChildren(this.form, {'tag': new RegExp('^(input|select)$', 'i')}, true));

		for(var i = 0; i < values.length; i++)
			this.cacheKey += values[i].name + ':' + values[i].value + '|';

		this.needReload = needReload;
		
		if(this.cache[this.cacheKey]) {
			this.curFilterinput = input;
			this.postHandler(this.cache[this.cacheKey], true);
		} else {
			if(this.sef) {
				var set_filter = BX('set_filter');
				set_filter.disabled = true;
			}

			this.curFilterinput = input;
			BX.ajax.loadJSON(
				this.ajaxURL,
				this.values2post(values),
				BX.delegate(this.postHandler, this)
			);
		}
	}
};

JCSmartFilter.prototype.updateItem = function (PID, arItem) {
	if(arItem.PROPERTY_TYPE === 'N' || arItem.PRICE) {
		var trackBar = window['trackBar' + PID];
		if(!trackBar && arItem.ENCODED_ID)
			trackBar = window['trackBar' + arItem.ENCODED_ID];

		if(trackBar && arItem.VALUES) {
			if(arItem.VALUES.MIN) {
				if(arItem.VALUES.MIN.FILTERED_VALUE)
					trackBar.setMinFilteredValue(arItem.VALUES.MIN.FILTERED_VALUE);
				else
					trackBar.setMinFilteredValue(arItem.VALUES.MIN.VALUE);
			}

			if(arItem.VALUES.MAX) {
				if(arItem.VALUES.MAX.FILTERED_VALUE)
					trackBar.setMaxFilteredValue(arItem.VALUES.MAX.FILTERED_VALUE);
				else
					trackBar.setMaxFilteredValue(arItem.VALUES.MAX.VALUE);
			}
		}
	} else if(arItem.VALUES) {
		for(var i in arItem.VALUES) {
			if(arItem.VALUES.hasOwnProperty(i)) {
				var value = arItem.VALUES[i];
				var control = BX(value.CONTROL_ID);

				if(!!control) {
					var label = document.querySelector('[data-role="label_'+value.CONTROL_ID+'"]');
					if(value.DISABLED) {
						if(label)
							BX.addClass(label, this.hideDisabledProps ? 'hidden' : 'disabled');
						else
							BX.addClass(control.parentNode, this.hideDisabledProps ? 'hidden' : 'disabled');

						BX.adjust(control, {props: {disabled: true}});
					} else {
						if(label)
							BX.removeClass(label, this.hideDisabledProps ? 'hidden' : 'disabled');
						else
							BX.removeClass(control.parentNode, this.hideDisabledProps ? 'hidden' : 'disabled');

						BX.adjust(control, {props: {disabled: false}});
					}

					if(value.hasOwnProperty('ELEMENT_COUNT')) {
						label = document.querySelector('[data-role="count_'+value.CONTROL_ID+'"]');
						if(label)
							label.innerHTML = value.ELEMENT_COUNT;
					}
				}
			}
		}
	}
};

JCSmartFilter.prototype.postHandler = function (result, fromCache) {
	var filter_count = document.querySelector('.bx-filter-count'),
		url, node;

	if(!!result && !!result.ITEMS) {
		for(var popupId in this.popups) {
			if(this.popups.hasOwnProperty(popupId)) {
				this.popups[popupId].destroy();
			}
		}
		this.popups = [];

		for(var PID in result.ITEMS) {
			if(result.ITEMS.hasOwnProperty(PID)) {
				this.updateItem(PID, result.ITEMS[PID]);
			}
		}
		
		if(!!filter_count)
			filter_count.innerHTML = result.ELEMENT_COUNT;
		
		if(result.INSTANT_RELOAD && result.COMPONENT_CONTAINER_ID) {
			url = BX.util.htmlspecialcharsback(result.FILTER_AJAX_URL);
			node = BX(result.COMPONENT_CONTAINER_ID);
			if(!!node) {
				var show = BX.showWait(node);
				BX.ajax.get(url, BX.delegate(function(data) {
					node.innerHTML = data;
					this.hideFilter();
					BX.closeWait(node, show);
				}, this));
			}
		} else {
			if(!!filter_count && filter_count.style.display === 'none')
				filter_count.style.display = 'inline-block';
				
			if(result.SEF_SET_FILTER_URL) {
				this.bindUrlToButton('set_filter', result.SEF_SET_FILTER_URL);
				
				if(this.needReload)
					window.location.href = result.SEF_SET_FILTER_URL;
			}
		}
	}

	if(this.sef) {
		var set_filter = BX('set_filter');
		set_filter.disabled = false;
	}

	if(!fromCache && this.cacheKey !== '') {
		this.cache[this.cacheKey] = result;
	}
	this.cacheKey = '';
};

JCSmartFilter.prototype.bindUrlToButton = function (buttonId, url) {
	var button = BX(buttonId);
	if(button) {
		var proxy = function(j, func) {
			return function() {
				return func(j);
			}
		};

		if(button.type == 'submit')
			button.type = 'button';

		BX.bind(button, 'click', proxy(url, function(url) {
			window.location.href = url;
			return false;
		}));
	}
};

JCSmartFilter.prototype.gatherInputsValues = function (values, elements) {
	if(elements) {
		for(var i = 0; i < elements.length; i++) {
			var el = elements[i];
			if(el.disabled || !el.type)
				continue;

			switch(el.type.toLowerCase()) {
				case 'text':
				case 'textarea':
				case 'password':
				case 'hidden':
				case 'select-one':
					if(el.value.length)
						values[values.length] = {name : el.name, value : el.value};
					break;
				case 'radio':
				case 'checkbox':
					if(el.checked)
						values[values.length] = {name : el.name, value : el.value};
					break;
				case 'select-multiple':
					for(var j = 0; j < el.options.length; j++) {
						if(el.options[j].selected)
							values[values.length] = {name : el.name, value : el.options[j].value};
					}
					break;
				default:
					break;
			}
		}
	}
};

JCSmartFilter.prototype.values2post = function (values) {
	var post = [];
	var current = post;
	var i = 0;

	while(i < values.length) {
		var p = values[i].name.indexOf('[');
		if(p == -1) {
			current[values[i].name] = values[i].value;
			current = post;
			i++;
		} else {
			var name = values[i].name.substring(0, p);
			var rest = values[i].name.substring(p+1);
			if(!current[name])
				current[name] = [];

			var pp = rest.indexOf(']');
			if(pp == -1) {
				//Error - not balanced brackets
				current = post;
				i++;
			} else if(pp == 0) {
				//No index specified - so take the next integer
				current = current[name];
				values[i].name = '' + current.length;
			} else {
				//Now index name becomes and name and we go deeper into the array
				current = current[name];
				values[i].name = rest.substring(0, pp) + rest.substring(pp+1);
			}
		}
	}
	return post;
};

JCSmartFilter.prototype.hideFilterProps = function(element) {
	var obj = element.parentNode,
		filterBlock = obj.querySelector("[data-role='bx_filter_block']"),
		propAngle = obj.querySelector("[data-role='prop_angle']");

	if(BX.hasClass(obj, "bx-active")) {
		filterBlock.style.overflow = "hidden";

		new BX.easing({
			duration: 300,
			start: {				
				height: filterBlock.offsetHeight,
				marginTop: 16,
				opacity: 100
			},
			finish: {				
				height: 0,
				marginTop: 0,
				opacity: 0
			},
			transition: BX.easing.transitions.linear,
			step: function(state) {				
				filterBlock.style.height = state.height + "px";
				filterBlock.style.marginTop = state.marginTop + "px";
				filterBlock.style.opacity = state.opacity / 100;
			},
			complete: function() {
				filterBlock.setAttribute("style", "");
				BX.removeClass(obj, "bx-active");
			}
		}).animate();
	} else {
		BX.adjust(filterBlock, {
			style: {
				height: "auto",
				display: "block",
				opacity: 0,
				overflow: "hidden"
			}
		});
		var obj_children_height = filterBlock.offsetHeight;		
		filterBlock.style.height = 0;

		new BX.easing({
			duration: 300,
			start: {				
				height: 0,
				marginTop: 0,
				opacity: 0
			},
			finish: {				
				height: obj_children_height,
				marginTop: 16,
				opacity: 100
			},
			transition: BX.easing.transitions.linear,
			step: function(state) {				
				filterBlock.style.height = state.height + "px";
				filterBlock.style.marginTop = state.marginTop + "px";
				filterBlock.style.opacity = state.opacity / 100;
			},
			complete: function() {
				filterBlock.setAttribute("style", "");
				BX.addClass(obj, "bx-active");
			}
		}).animate();
	}
};

JCSmartFilter.prototype.showDropDownPopup = function(element, popupId) {
	var contentNode = element.querySelector('[data-role="dropdownContent"]');
	this.popups["smartFilterDropDown" + popupId] = BX.PopupWindowManager.create("smartFilterDropDown" + popupId, element, {
		autoHide: true,
		offsetLeft: 0,
		offsetTop: 3,
		overlay : false,
		draggable: {restrict:true},
		closeByEsc: true,
		className: "bx-filter-popup-window",
		content: BX.clone(contentNode)
	});	
	contentNode.parentNode.appendChild(BX("smartFilterDropDown" + popupId));
	this.popups["smartFilterDropDown" + popupId].show();
};

JCSmartFilter.prototype.selectDropDownItem = function(element, controlId) {
	this.keyup(BX(controlId));

	var wrapContainer = BX.findParent(BX(controlId), {className:"bx-filter-select-container"}, false);

	var currentOption = wrapContainer.querySelector('[data-role="currentOption"]');
	currentOption.innerHTML = element.innerHTML;
	BX.PopupWindowManager.getCurrentPopup().close();
};

BX.namespace("BX.Iblock.SmartFilter");
BX.Iblock.SmartFilter = (function() {	
	var SmartFilter = function(arParams) {
		if(typeof arParams === 'object') {
			this.leftSlider = BX(arParams.leftSlider);
			this.rightSlider = BX(arParams.rightSlider);
			this.tracker = BX(arParams.tracker);
			this.trackerWrap = BX(arParams.trackerWrap);

			this.minInput = BX(arParams.minInputId);
			this.maxInput = BX(arParams.maxInputId);

			this.minPrice = parseFloat(arParams.minPrice);
			this.maxPrice = parseFloat(arParams.maxPrice);

			this.curMinPrice = parseFloat(arParams.curMinPrice);
			this.curMaxPrice = parseFloat(arParams.curMaxPrice);

			this.fltMinPrice = arParams.fltMinPrice ? parseFloat(arParams.fltMinPrice) : parseFloat(arParams.curMinPrice);
			this.fltMaxPrice = arParams.fltMaxPrice ? parseFloat(arParams.fltMaxPrice) : parseFloat(arParams.curMaxPrice);

			this.precision = arParams.precision || 0;

			this.priceDiff = this.maxPrice - this.minPrice;

			this.leftPercent = 0;
			this.rightPercent = 0;

			this.fltMinPercent = 0;
			this.fltMaxPercent = 0;

			this.colorUnavailableActive = BX(arParams.colorUnavailableActive);//gray
			this.colorAvailableActive = BX(arParams.colorAvailableActive);//blue
			this.colorAvailableInactive = BX(arParams.colorAvailableInactive);//light blue

			this.isTouch = false;

			this.init();

			if('ontouchstart' in document.documentElement) {
				this.isTouch = true;

				BX.bind(this.leftSlider, "touchstart", BX.proxy(function(event) {
					this.onMoveLeftSlider(event)
				}, this));

				BX.bind(this.rightSlider, "touchstart", BX.proxy(function(event) {
					this.onMoveRightSlider(event)
				}, this));
			} else {
				BX.bind(this.leftSlider, "mousedown", BX.proxy(function(event) {
					this.onMoveLeftSlider(event)
				}, this));

				BX.bind(this.rightSlider, "mousedown", BX.proxy(function(event) {
					this.onMoveRightSlider(event)
				}, this));
			}

			BX.bind(this.minInput, "keyup", BX.proxy(function(event) {
				this.onInputChange();
			}, this));

			BX.bind(this.maxInput, "keyup", BX.proxy(function(event) {
				this.onInputChange();
			}, this));
		}
	};

	SmartFilter.prototype.init = function() {
		var priceDiff;

		if(this.curMinPrice > this.minPrice) {
			priceDiff = this.curMinPrice - this.minPrice;
			this.leftPercent = (priceDiff*100)/this.priceDiff;

			this.leftSlider.style.left = this.leftPercent + "%";
			this.colorUnavailableActive.style.left = this.leftPercent + "%";
		}

		this.setMinFilteredValue(this.fltMinPrice);

		if(this.curMaxPrice < this.maxPrice) {
			priceDiff = this.maxPrice - this.curMaxPrice;
			this.rightPercent = (priceDiff*100)/this.priceDiff;

			this.rightSlider.style.right = this.rightPercent + "%";
			this.colorUnavailableActive.style.right = this.rightPercent + "%";
		}

		this.setMaxFilteredValue(this.fltMaxPrice);
	};
	
	SmartFilter.prototype.setMinFilteredValue = function (fltMinPrice) {
		this.fltMinPrice = parseFloat(fltMinPrice);
		if(this.fltMinPrice >= this.minPrice) {
			var priceDiff = this.fltMinPrice - this.minPrice;
			this.fltMinPercent = (priceDiff*100)/this.priceDiff;

			if(this.leftPercent > this.fltMinPercent)
				this.colorAvailableActive.style.left = this.leftPercent + "%";
			else
				this.colorAvailableActive.style.left = this.fltMinPercent + "%";

			this.colorAvailableInactive.style.left = this.fltMinPercent + "%";
		} else {
			this.colorAvailableActive.style.left = "0%";
			this.colorAvailableInactive.style.left = "0%";
		}
	};

	SmartFilter.prototype.setMaxFilteredValue = function (fltMaxPrice) {
		this.fltMaxPrice = parseFloat(fltMaxPrice);
		if(this.fltMaxPrice <= this.maxPrice) {
			var priceDiff = this.maxPrice - this.fltMaxPrice;
			this.fltMaxPercent = (priceDiff*100)/this.priceDiff;

			if(this.rightPercent > this.fltMaxPercent)
				this.colorAvailableActive.style.right = this.rightPercent + "%";
			else
				this.colorAvailableActive.style.right = this.fltMaxPercent + "%";

			this.colorAvailableInactive.style.right = this.fltMaxPercent + "%";
		} else {
			this.colorAvailableActive.style.right = "0%";
			this.colorAvailableInactive.style.right = "0%";
		}
	};

	SmartFilter.prototype.getXCoord = function(elem) {
		var box = elem.getBoundingClientRect();
		var body = document.body;
		var docElem = document.documentElement;

		var scrollLeft = window.pageXOffset || docElem.scrollLeft || body.scrollLeft;
		var clientLeft = docElem.clientLeft || body.clientLeft || 0;
		var left = box.left + scrollLeft - clientLeft;

		return Math.round(left);
	};

	SmartFilter.prototype.getPageX = function(e) {
		e = e || window.event;
		var pageX = null;

		if(this.isTouch && event.targetTouches[0] != null) {
			pageX = e.targetTouches[0].pageX;
		} else if(e.pageX != null) {
			pageX = e.pageX;
		} else if(e.clientX != null) {
			var html = document.documentElement;
			var body = document.body;

			pageX = e.clientX + (html.scrollLeft || body && body.scrollLeft || 0);
			pageX -= html.clientLeft || 0;
		}

		return pageX;
	};

	SmartFilter.prototype.recountMinPrice = function() {
		var newMinPrice = (this.priceDiff*this.leftPercent)/100;
		newMinPrice = (this.minPrice + newMinPrice).toFixed(this.precision);

		if(newMinPrice != this.minPrice)
			this.minInput.value = newMinPrice;
		else
			this.minInput.value = "";		
		smartFilter.keyup(this.minInput);
	};

	SmartFilter.prototype.recountMaxPrice = function() {
		var newMaxPrice = (this.priceDiff*this.rightPercent)/100;
		newMaxPrice = (this.maxPrice - newMaxPrice).toFixed(this.precision);

		if(newMaxPrice != this.maxPrice)
			this.maxInput.value = newMaxPrice;
		else
			this.maxInput.value = "";		
		smartFilter.keyup(this.maxInput);
	};

	SmartFilter.prototype.onInputChange = function() {
		var priceDiff;
		if(this.minInput.value) {
			var leftInputValue = this.minInput.value;
			if(leftInputValue < this.minPrice)
				leftInputValue = this.minPrice;

			if(leftInputValue > this.maxPrice)
				leftInputValue = this.maxPrice;

			priceDiff = leftInputValue - this.minPrice;
			this.leftPercent = (priceDiff*100)/this.priceDiff;

			this.makeLeftSliderMove(false);
		}

		if(this.maxInput.value) {
			var rightInputValue = this.maxInput.value;
			if(rightInputValue < this.minPrice)
				rightInputValue = this.minPrice;

			if(rightInputValue > this.maxPrice)
				rightInputValue = this.maxPrice;

			priceDiff = this.maxPrice - rightInputValue;
			this.rightPercent = (priceDiff*100)/this.priceDiff;

			this.makeRightSliderMove(false);
		}
	};

	SmartFilter.prototype.makeLeftSliderMove = function(recountPrice) {
		recountPrice = (recountPrice !== false);

		this.leftSlider.style.left = this.leftPercent + "%";
		this.colorUnavailableActive.style.left = this.leftPercent + "%";

		var areBothSlidersMoving = false;
		if(this.leftPercent + this.rightPercent >= 100) {
			areBothSlidersMoving = true;
			this.rightPercent = 100 - this.leftPercent;
			this.rightSlider.style.right = this.rightPercent + "%";
			this.colorUnavailableActive.style.right = this.rightPercent + "%";
		}

		if(this.leftPercent >= this.fltMinPercent && this.leftPercent <= (100-this.fltMaxPercent)) {
			this.colorAvailableActive.style.left = this.leftPercent + "%";
			if(areBothSlidersMoving) {
				this.colorAvailableActive.style.right = 100 - this.leftPercent + "%";
			}
		} else if(this.leftPercent <= this.fltMinPercent) {
			this.colorAvailableActive.style.left = this.fltMinPercent + "%";
			if(areBothSlidersMoving) {
				this.colorAvailableActive.style.right = 100 - this.fltMinPercent + "%";
			}
		} else if(this.leftPercent >= this.fltMaxPercent) {
			this.colorAvailableActive.style.left = 100-this.fltMaxPercent + "%";
			if(areBothSlidersMoving) {
				this.colorAvailableActive.style.right = this.fltMaxPercent + "%";
			}
		}

		if(recountPrice) {
			this.recountMinPrice();
			if(areBothSlidersMoving)
				this.recountMaxPrice();
		}
	};

	SmartFilter.prototype.countNewLeft = function(event) {
		var pageX = this.getPageX(event);

		var trackerXCoord = this.getXCoord(this.trackerWrap);
		var rightEdge = this.trackerWrap.offsetWidth;

		var newLeft = pageX - trackerXCoord;

		if(newLeft < 0)
			newLeft = 0;
		else if(newLeft > rightEdge)
			newLeft = rightEdge;

		return newLeft;
	};

	SmartFilter.prototype.onMoveLeftSlider = function(e) {
		if(!this.isTouch) {
			this.leftSlider.ondragstart = function() {
				return false;
			};
		}

		if(!this.isTouch) {
			document.onmousemove = BX.proxy(function(event) {
				this.leftPercent = ((this.countNewLeft(event)*100)/this.trackerWrap.offsetWidth);
				this.makeLeftSliderMove();
			}, this);

			document.onmouseup = function() {
				document.onmousemove = document.onmouseup = null;
			};
		} else {
			document.ontouchmove = BX.proxy(function(event) {
				this.leftPercent = ((this.countNewLeft(event)*100)/this.trackerWrap.offsetWidth);
				this.makeLeftSliderMove();
			}, this);

			document.ontouchend = function() {
				document.ontouchmove = document.touchend = null;
			};
		}

		return false;
	};

	SmartFilter.prototype.makeRightSliderMove = function(recountPrice) {
		recountPrice = (recountPrice !== false);

		this.rightSlider.style.right = this.rightPercent + "%";
		this.colorUnavailableActive.style.right = this.rightPercent + "%";

		var areBothSlidersMoving = false;
		if(this.leftPercent + this.rightPercent >= 100) {
			areBothSlidersMoving = true;
			this.leftPercent = 100 - this.rightPercent;
			this.leftSlider.style.left = this.leftPercent + "%";
			this.colorUnavailableActive.style.left = this.leftPercent + "%";
		}

		if((100-this.rightPercent) >= this.fltMinPercent && this.rightPercent >= this.fltMaxPercent) {
			this.colorAvailableActive.style.right = this.rightPercent + "%";
			if(areBothSlidersMoving) {
				this.colorAvailableActive.style.left = 100 - this.rightPercent + "%";
			}
		} else if(this.rightPercent <= this.fltMaxPercent) {
			this.colorAvailableActive.style.right = this.fltMaxPercent + "%";
			if(areBothSlidersMoving) {
				this.colorAvailableActive.style.left = 100 - this.fltMaxPercent + "%";
			}
		} else if((100-this.rightPercent) <= this.fltMinPercent) {
			this.colorAvailableActive.style.right = 100-this.fltMinPercent + "%";
			if(areBothSlidersMoving) {
				this.colorAvailableActive.style.left = this.fltMinPercent + "%";
			}
		}

		if(recountPrice) {
			this.recountMaxPrice();
			if(areBothSlidersMoving)
				this.recountMinPrice();
		}
	};

	SmartFilter.prototype.onMoveRightSlider = function(e) {
		if(!this.isTouch) {
			this.rightSlider.ondragstart = function() {
				return false;
			};
		}

		if(!this.isTouch) {
			document.onmousemove = BX.proxy(function(event) {
				this.rightPercent = 100-(((this.countNewLeft(event))*100)/(this.trackerWrap.offsetWidth));
				this.makeRightSliderMove();
			}, this);

			document.onmouseup = function() {
				document.onmousemove = document.onmouseup = null;
			};
		} else {
			document.ontouchmove = BX.proxy(function(event) {
				this.rightPercent = 100-(((this.countNewLeft(event))*100)/(this.trackerWrap.offsetWidth));
				this.makeRightSliderMove();
			}, this);

			document.ontouchend = function() {
				document.ontouchmove = document.ontouchend = null;
			};
		}

		return false;
	};

	return SmartFilter;
})();