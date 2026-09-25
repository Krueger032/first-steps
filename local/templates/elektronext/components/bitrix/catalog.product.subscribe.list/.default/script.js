(function(window) {

if(!!window.JCCatalogProductSubscribeList) {
	return;
}

window.JCCatalogProductSubscribeList = function(arParams) {
	this.productType = 0;
	this.showAbsent = true;
	this.showSkuProps = false;
	this.visual = {
		ID: '',
		PICT_ID: '',
		DELETE_SUBSCRIBE_ID: ''
	};
	this.product = {
		id: 0,
		name: '',
		pict: {},
		listSubscribeId: {}
	};

	this.defaultPict = {
		pict: null
	};

	this.offers = [];
	this.offerNum = 0;
	this.treeProps = [];
	this.obTreeRows = [];

	this.obProduct = null;
	this.obPict = null;
	this.obTree = null;
	this.deleteSubscribeBtn = null;
	this.obSkuProps = null;

	this.containerHeight = 0;

	this.errorCode = 0;

	this.ajaxUrl = '/bitrix/components/bitrix/catalog.product.subscribe.list/ajax.php';

	if('object' === typeof arParams) {
		this.productType = parseInt(arParams.PRODUCT_TYPE, 10);
		this.showAbsent = arParams.SHOW_ABSENT;
		this.showSkuProps = !!arParams.SHOW_SKU_PROPS;

		this.notifyUser = Boolean(arParams.NOTIFY_USER);
		this.notifyPopupTitle = arParams.NOTIFY_POPUP_TITLE;
		this.notifySuccess = Boolean(arParams.NOTIFY_SUCCESS);
		this.notifyMessage = arParams.NOTIFY_MESSAGE;
		
		if(this.notifyUser) {
			BX.ready(BX.delegate(this.showAlertNotifyingUser,this));
			return;
		}

		this.visual = arParams.VISUAL;

		if(!!this.visual.DELETE_SUBSCRIBE_ID) {
			this.deleteSubscribeBtn = BX(this.visual.DELETE_SUBSCRIBE_ID);
			if(!!this.deleteSubscribeBtn) {
				BX.bind(this.deleteSubscribeBtn, 'click', BX.delegate(this.deleteSubscribe, this));
			}
			this.product.listSubscribeId = arParams.PRODUCT.LIST_SUBSCRIBE_ID;
		}

		switch(this.productType) {
			case 1://product
			case 2://set
				if(!!arParams.PRODUCT && 'object' === typeof(arParams.PRODUCT)) {
					this.product.id = arParams.PRODUCT.ID;
					this.product.name = arParams.PRODUCT.NAME;
					this.product.pict = arParams.PRODUCT.PICT;
				} else {
					this.errorCode = -1;
				}
				break;
			case 3://sku
				if(!!arParams.PRODUCT && 'object' === typeof(arParams.PRODUCT)) {
					this.product.id = arParams.PRODUCT.ID;
					this.product.name = arParams.PRODUCT.NAME;
				}
				if(!!arParams.OFFERS && BX.type.isArray(arParams.OFFERS)) {
					this.offers = arParams.OFFERS;
					this.offerNum = 0;
					if(!!arParams.OFFER_SELECTED) {
						this.offerNum = parseInt(arParams.OFFER_SELECTED, 10);
					}
					if(isNaN(this.offerNum)) {
						this.offerNum = 0;
					}
					if(!!arParams.TREE_PROPS) {
						this.treeProps = arParams.TREE_PROPS;
					}
					if(!!arParams.DEFAULT_PICTURE) {
						this.defaultPict.pict = arParams.DEFAULT_PICTURE.PICTURE;
					}
				} else {
					this.errorCode = -1;
				}
				break;
			default:
				this.errorCode = -1;
		}
	}
	if(0 === this.errorCode) {
		BX.ready(BX.delegate(this.Init,this));
	}
};

window.JCCatalogProductSubscribeList.prototype.Init = function() {
	var strPrefix = '',
		TreeItems = null;

	this.obProduct = BX(this.visual.ID);
	if(!this.obProduct) {
		this.errorCode = -1;
	}
	
	this.obPict = BX(this.visual.PICT_ID);
	if(!this.obPict) {
		this.errorCode = -2;
	}
	
	if(3 === this.productType) {
		if(!!this.visual.TREE_ID) {
			this.obTree = BX(this.visual.TREE_ID);
			if(!this.obTree) {
				this.errorCode = -256;
			}
			strPrefix = this.visual.TREE_ITEM_ID;
			for(var i = 0; i < this.treeProps.length; i++) {
				this.obTreeRows[i] = {
					LIST: BX(strPrefix+this.treeProps[i].ID+'_list'),
					CONT: BX(strPrefix+this.treeProps[i].ID+'_cont')
				};
				if(!this.obTreeRows[i].LIST || !this.obTreeRows[i].CONT) {
					this.errorCode = -512;
					break;
				}
			}
		}
	}

	if(0 === this.errorCode) {
		switch (this.productType) {
			case 1://product
				break;
			case 3://sku
				TreeItems = BX.findChildren(this.obTree, {tagName: 'li'}, true);
				if(!!TreeItems && 0 < TreeItems.length) {
					for(i = 0; i < TreeItems.length; i++) {
						BX.bind(TreeItems[i], 'click', BX.delegate(this.SelectOfferProp, this));
					}
				}
				this.SetCurrent();
				break;
		}
		
		this.containerHeight = parseInt(this.obProduct.offsetHeight, 10);
		if(isNaN(this.containerHeight)) {
			this.containerHeight = 0;
		}
		this.setHeight();
		BX.bind(window, 'resize', BX.delegate(this.checkHeight, this));
		BX.bind(this.obProduct, 'mouseover', BX.delegate(this.setHeight, this));
		BX.bind(this.obProduct, 'mouseout', BX.delegate(this.clearHeight, this));
	}
};

window.JCCatalogProductSubscribeList.prototype.SelectOfferProp = function() {
	var i = 0,
		value = '',
		strTreeValue = '',
		arTreeItem = [],
		RowItems = null,
		target = BX.proxy_context;

	if(!!target && target.hasAttribute('data-treevalue')) {
		strTreeValue = target.getAttribute('data-treevalue');
		arTreeItem = strTreeValue.split('_');
		if(this.SearchOfferPropIndex(arTreeItem[0], arTreeItem[1])) {
			RowItems = BX.findChildren(target.parentNode, {tagName: 'li'}, false);
			if(!!RowItems && 0 < RowItems.length) {
				for(i = 0; i < RowItems.length; i++) {
					value = RowItems[i].getAttribute('data-onevalue');
					if(value === arTreeItem[1]) {
						BX.addClass(RowItems[i], 'bx_active');
					} else {
						BX.removeClass(RowItems[i], 'bx_active');
					}
				}
			}

			var scuContainers = this.obTree.querySelectorAll('.bx_subscribe_item_scu_hidden');
			if(!!scuContainers) {
				for(var k in scuContainers) {
					if(scuContainers.hasOwnProperty(k) && BX.type.isDomNode(scuContainers[k])) {
						var activeItem = scuContainers[k].querySelector('.bx_active');
						if(!!activeItem) {
							var currentOption = scuContainers[k].querySelector('[data-entity="current-option"]');
							if(!!currentOption)
								currentOption.innerHTML = activeItem.getAttribute('title');
						}
					}
				}
			}
		}
	}	
};

window.JCCatalogProductSubscribeList.prototype.SearchOfferPropIndex = function(strPropID, strPropValue) {
	var strName = '',
		arShowValues = false,
		i, j,
		arCanBuyValues = [],
		allValues = [],
		index = -1,
		arFilter = {},
		tmpFilter = [];

	for(i = 0; i < this.treeProps.length; i++) {
		if(this.treeProps[i].ID === strPropID) {
			index = i;
			break;
		}
	}

	if(-1 < index) {
		for(i = 0; i < index; i++) {
			strName = 'PROP_'+this.treeProps[i].ID;
			arFilter[strName] = this.selectedValues[strName];
		}
		strName = 'PROP_'+this.treeProps[index].ID;
		arShowValues = this.GetRowValues(arFilter, strName);
		if(!arShowValues) {
			return false;
		}
		if(!BX.util.in_array(strPropValue, arShowValues)) {
			return false;
		}
		arFilter[strName] = strPropValue;
		for(i = index+1; i < this.treeProps.length; i++) {
			strName = 'PROP_'+this.treeProps[i].ID;
			arShowValues = this.GetRowValues(arFilter, strName);
			if(!arShowValues) {
				return false;
			}
			allValues = [];
			if(this.showAbsent) {
				arCanBuyValues = [];
				tmpFilter = [];
				tmpFilter = BX.clone(arFilter, true);
				for(j = 0; j < arShowValues.length; j++) {
					tmpFilter[strName] = arShowValues[j];
					allValues[allValues.length] = arShowValues[j];
					if(this.GetCanBuy(tmpFilter))
						arCanBuyValues[arCanBuyValues.length] = arShowValues[j];
				}
			} else {
				arCanBuyValues = arShowValues;
			}
			if(!!this.selectedValues[strName] && BX.util.in_array(this.selectedValues[strName], arCanBuyValues)) {
				arFilter[strName] = this.selectedValues[strName];
			} else {
				if(this.showAbsent)
					arFilter[strName] = (arCanBuyValues.length > 0 ? arCanBuyValues[0] : allValues[0]);
				else
					arFilter[strName] = arCanBuyValues[0];
			}
			this.UpdateRow(i, arFilter[strName], arShowValues, arCanBuyValues);
		}
		this.selectedValues = arFilter;
		this.ChangeInfo();
	}
	return true;
};

window.JCCatalogProductSubscribeList.prototype.GetRowValues = function(arFilter, index) {
	var i = 0,
		j,
		arValues = [],
		boolSearch = false,
		boolOneSearch = true;

	if(0 === arFilter.length) {
		for(i = 0; i < this.offers.length; i++) {
			if(!BX.util.in_array(this.offers[i].TREE[index], arValues)) {
				arValues[arValues.length] = this.offers[i].TREE[index];
			}
		}
		boolSearch = true;
	} else {
		for(i = 0; i < this.offers.length; i++) {
			boolOneSearch = true;
			for(j in arFilter) {
				if(arFilter[j] !== this.offers[i].TREE[j]) {
					boolOneSearch = false;
					break;
				}
			}
			if(boolOneSearch) {
				if(!BX.util.in_array(this.offers[i].TREE[index], arValues)) {
					arValues[arValues.length] = this.offers[i].TREE[index];
				}
				boolSearch = true;
			}
		}
	}
	return (boolSearch ? arValues : false);
};

window.JCCatalogProductSubscribeList.prototype.GetCanBuy = function(arFilter) {
	var i = 0,
		j,
		boolSearch = false,
		boolOneSearch = true;

	for(i = 0; i < this.offers.length; i++) {
		boolOneSearch = true;
		for(j in arFilter) {
			if(arFilter[j] !== this.offers[i].TREE[j]) {
				boolOneSearch = false;
				break;
			}
		}
		if(boolOneSearch) {
			if(this.offers[i].CAN_BUY) {
				boolSearch = true;
				break;
			}
		}
	}
	return boolSearch;
};

window.JCCatalogProductSubscribeList.prototype.UpdateRow = function(intNumber, activeID, showID, canBuyID) {
	var i = 0,
		showI = 0,
		value = '',
		countShow = 0,
		obData = {},
		extShowMode = false,
		isCurrent = false,
		selectIndex = 0,
		obLeft = this.treeEnableArrow,
		obRight = this.treeEnableArrow,
		currentShowStart = 0,
		RowItems = null;

	if(-1 < intNumber && intNumber < this.obTreeRows.length) {
		RowItems = BX.findChildren(this.obTreeRows[intNumber].LIST, {tagName: 'li'}, false);
		
		if(!!RowItems && 0 < RowItems.length) {
			countShow = showID.length;
			extShowMode = this.treeRowShowSize < countShow;
			obData = {
				style: {}
			};
			
			for(i = 0; i < RowItems.length; i++) {
				value = RowItems[i].getAttribute('data-onevalue');
				isCurrent = (value === activeID);
				obData.style.display = 'none';
				
				if(BX.util.in_array(value, showID)) {
					obData.style.display = '';
					
					if(isCurrent)
						selectIndex = showI;
					
					showI++;
				}
				BX.adjust(RowItems[i], obData);
				
				if(isCurrent)
					BX.addClass(RowItems[i], 'bx_active');
				else
					BX.removeClass(RowItems[i], 'bx_active');
					
			}
		}
	}
};

window.JCCatalogProductSubscribeList.prototype.ChangeInfo = function() {
	var i = 0,
		j,
		index = -1,
		obData = {},
		boolOneSearch = true,
		strPrice = '';

	for(i = 0; i < this.offers.length; i++) {
		boolOneSearch = true;
		for(j in this.selectedValues) {
			if(this.selectedValues[j] !== this.offers[i].TREE[j]) {
				boolOneSearch = false;
				break;
			}
		}
		if(boolOneSearch) {
			index = i;
			break;
		}
	}
	if(-1 < index) {
		if(!!this.obPict) {			
			this.obPictImg = BX.findChild(this.obPict, {tagName: 'IMG'}, true, false);
			if(this.obPictImg) {
				if(this.offers[index].PREVIEW_PICTURE) {							
					BX.adjust(this.obPictImg, {
						props: {
							src: this.offers[index].PREVIEW_PICTURE.SRC,
							width: this.offers[index].PREVIEW_PICTURE.WIDTH,
							height: this.offers[index].PREVIEW_PICTURE.HEIGHT
						}
					});
				} else {							
					BX.adjust(this.obPictImg, {
						props: {
							src: this.defaultPict.pict.SRC,
							width: this.defaultPict.pict.WIDTH,
							height: this.defaultPict.pict.HEIGHT
						}
					});
				}
			}
		}
		if(this.showSkuProps && !!this.obSkuProps) {
			BX.adjust(this.obSkuProps, {style: {display: 'none'}, html: ''});
		}
		this.offerNum = index;
	}
};

window.JCCatalogProductSubscribeList.prototype.SetCurrent = function() {
	var i = 0,
		j = 0,
		arCanBuyValues = [],
		strName = '',
		arShowValues = false,
		arFilter = {},
		tmpFilter = [],
		current = this.offers[this.offerNum].TREE;
	
	for(i = 0; i < this.treeProps.length; i++) {
		strName = 'PROP_'+this.treeProps[i].ID;
		arShowValues = this.GetRowValues(arFilter, strName);
		if(!arShowValues) {
			break;
		}
		if(BX.util.in_array(current[strName], arShowValues)) {
			arFilter[strName] = current[strName];
		} else {
			arFilter[strName] = arShowValues[0];
			this.offerNum = 0;
		}
		if(this.showAbsent) {
			arCanBuyValues = [];
			tmpFilter = [];
			tmpFilter = BX.clone(arFilter, true);
			for(j = 0; j < arShowValues.length; j++) {
				tmpFilter[strName] = arShowValues[j];
				if(this.GetCanBuy(tmpFilter)) {
					arCanBuyValues[arCanBuyValues.length] = arShowValues[j];
				}
			}
		} else {
			arCanBuyValues = arShowValues;
		}
		this.UpdateRow(i, arFilter[strName], arShowValues, arCanBuyValues);
	}
	this.selectedValues = arFilter;
	this.ChangeInfo();

	var scuContainers = this.obTree.querySelectorAll('.bx_subscribe_item_scu_hidden');
	if(!!scuContainers) {
		for(var k in scuContainers) {
			if(scuContainers.hasOwnProperty(k) && BX.type.isDomNode(scuContainers[k])) {
				var activeItem = scuContainers[k].querySelector('.bx_active');
				if(!!activeItem) {
					var currentOption = scuContainers[k].querySelector('[data-entity="current-option"]');
					if(!!currentOption)
						currentOption.innerHTML = activeItem.getAttribute('title');
				}
			}
		}
	}
};

window.JCCatalogProductSubscribeList.prototype.checkHeight = function() {
	this.containerHeight = parseInt(this.obProduct.offsetHeight, 10);
	if(isNaN(this.containerHeight)) {
		this.containerHeight = 0;
	}
};

window.JCCatalogProductSubscribeList.prototype.setHeight = function() {
	if(0 < this.containerHeight) {
		BX.adjust(this.obProduct, {style: {height: this.containerHeight + 'px'}});
	}
};

window.JCCatalogProductSubscribeList.prototype.clearHeight = function() {
	BX.adjust(this.obProduct, {style: {height: 'auto'}});
};

window.JCCatalogProductSubscribeList.prototype.deleteSubscribe = function() {
	var itemId, offerIndex;
	switch(this.productType) {
		case 1:
		case 2:
			itemId = this.product.id;
			break;
		case 3:
			var i, j, boolSearch;
			if(!this.offers.length) {
				itemId = this.product.id;
				break;
			}
			for(i = 0; i < this.offers.length; i++) {
				boolSearch = true;
				for(j in this.selectedValues) {
					if(this.selectedValues[j] !== this.offers[i].TREE[j]) {
						boolSearch = false;
						break;
					}
				}
				if(boolSearch) {
					offerIndex = i;
					itemId = this.offers[i].ID;
					break;
				}
			}
			break;
	}

	if(!itemId || !this.product.listSubscribeId.hasOwnProperty(itemId))
		return;

	BX.ajax({
		method: 'POST',
		dataType: 'json',
		url: this.ajaxUrl,
		data: {
			sessid: BX.bitrix_sessid(),
			deleteSubscribe: 'Y',
			itemId: itemId,
			listSubscribeId: this.product.listSubscribeId[itemId]
		},
		onsuccess: BX.delegate(function(result) {
			if(result.success) {
				window.location.reload();
			} else {
				this.showAlertWithAnswer({status: 'error', message: result.message});
			}
		}, this)
	});
};

window.JCCatalogProductSubscribeList.prototype.showAlertWithAnswer = function(answer) {
	answer = answer || {};
	var className;
	
	if(!answer.message) {
		if(answer.status == 'success') {
			answer.message = BX.message('CPSL_STATUS_SUCCESS');
			className = 'alert alert-success';
		} else {
			answer.message = BX.message('CPSL_STATUS_ERROR');
			className = 'alert alert-error';
		}
	}
	this.showAlertBlock(answer.message, className);
};

window.JCCatalogProductSubscribeList.prototype.showAlertNotifyingUser = function() {
	var className;
	
	if(this.notifySuccess)
		className = 'alert alert-success';
	else
		className = 'alert alert-error';

	this.showAlertBlock(this.notifyMessage, className);
};

window.JCCatalogProductSubscribeList.prototype.showAlertBlock = function(message, className) {
	message = message || '';
	className = className || '';
	
	if(BX('alertMessage') && !!message && !!className) {
		BX('alertMessage').appendChild(BX.create('DIV', {
			props: {
				className: className,
				style: 'display: block;'
			},
			text: message
		}));
		BX.addClass(BX('alertMessage'), 'bx_subscribe_alert');
	}
};

})(window);