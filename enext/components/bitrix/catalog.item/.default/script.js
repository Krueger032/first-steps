(function (window){
	'use strict';

	if(window.JCCatalogItem)
		return;
	
	window.JCCatalogItem = function (arParams) {
		this.productType = 0;
		this.offersView = '';
		this.showQuantity = true;		
		this.showAbsent = true;		
		this.showOldPrice = false;
		this.showMaxQuantity = 'N';
		this.relativeQuantityFactor = 5;
		this.showPercent = false;
		this.usePriceRanges = false;
		this.basketAction = 'ADD';
		this.useCompare = false;
		this.showSubscription = false;
		this.visual = {
			ID: '',
			PICT_ID: '',
			ARTICLE_ID: '',
			QUANTITY_ID: '',
			QUANTITY_UP_ID: '',
			QUANTITY_DOWN_ID: '',
			PC_QUANTITY_ID: '',
			PC_QUANTITY_UP_ID: '',
			PC_QUANTITY_DOWN_ID: '',
			SQ_M_QUANTITY_ID: '',
			SQ_M_QUANTITY_UP_ID: '',
			SQ_M_QUANTITY_DOWN_ID: '',
			QUANTITY_MEASURE: '',
			QUANTITY_LIMIT: '',
			QUANTITY_LIMIT_NOT_AVAILABLE: '',
			BUY_LINK: '',
			BASKET_ACTIONS_ID: '',
			ASK_PRICE_LINK: '',
			NOT_AVAILABLE_LINK: '',
			MORE_LINK: '',
			SUBSCRIBE_LINK: '',
			DELAY_LINK: '',
			QUICK_VIEW_LINK: '',
			PRICE_ID: '',
			OLD_PRICE_ID: '',
			DISCOUNT_PRICE_ID: '',
			DISCOUNT_PERCENT_ID: '',
			TOTAL_COST_ID: '',
			TREE_ID: '',
			BASKET_PROP_DIV: '',
			DISPLAY_PROP_DIV: ''
		};
		this.product = {
			checkQuantity: false,
			maxQuantity: 0,
			maxPcQuantity: 0,
			maxSqMQuantity: 0,
			stepQuantity: 1,
			stepPcQuantity: 1,
			stepSqMQuantity: 0.01,
			isDblQuantity: false,
			canBuy: true,
			name: '',
			pict: {},
			id: 0,
			iblockId: 0,
			detailPageUrl: '',
			addUrl: '',
			buyUrl: ''
		};

		this.basketMode = '';
		this.basketData = {
			useProps: false,
			emptyProps: false,
			quantity: 'quantity',
			props: 'prop',
			basketUrl: '',
			sku_props: '',
			sku_props_var: 'basket_props',
			add_url: '',
			buy_url: ''
		};
		
		this.ajaxPath = '';

		this.quickViewData = {
			quickViewParameters: '',
			quickViewPrevNext: false
		};

		this.compareData = {			
			compareName: '',
			comparePath: '',
			compareUrl: '',
			compareDeleteUrl: ''
		};

		this.object = {
			id: 0
		};

		this.defaultPict = {
			pict: null
		};
		
		this.checkQuantity = false;
		this.maxQuantity = 0;
		this.maxPcQuantity = 0;
		this.maxSqMQuantity = 0;
		this.minQuantity = 0;
		this.minPcQuantity = 0;
		this.minSqMQuantity = 0;
		this.stepQuantity = 1;
		this.stepPcQuantity = 1;
		this.stepSqMQuantity = 0.01;
		this.isDblQuantity = false;
		this.canBuy = true;
		this.precision = 6;
		this.precisionFactor = Math.pow(10, this.precision);
		this.bigData = false;
		this.fullDisplayMode = false;
		this.viewMode = '';		

		this.currentPriceMode = '';
		this.currentPrices = [];
		this.currentPriceSelected = 0;
		this.currentQuantityRanges = [];
		this.currentQuantityRangeSelected = 0;
		this.currentMeasure = [];

		this.offers = [];
		this.offerNum = 0;
		this.treeProps = [];
		this.selectedValues = {};

		this.obProduct = null;
		this.blockNodes = {};
		this.obQuantity = null;
		this.obQuantityUp = null;
		this.obQuantityDown = null;
		this.obPcQuantity = null;
		this.obPcQuantityUp = null;
		this.obPcQuantityDown = null;
		this.obSqMQuantity = null;
		this.obSqMQuantityUp = null;
		this.obSqMQuantityDown = null;
		this.obQuantityLimit = {};
		this.obQuantityLimitNotAvl = {};
		this.obPict = null;
		this.obArticle = null;
		this.obArticleName = null;
		this.obArticleVal = null;
		this.obTitle = null;
		this.obPrice = null;
		this.obPriceNotSet = null;
		this.obPriceCurrent = null;		
		this.obPriceOld = null;
		this.obPriceDiscount = null;
		this.obPriceRangesIcon = null;
		this.obPriceRanges = null;
		this.obPriceRangesBody = null;
		this.obTotalCost = null;
		this.obTotalCostVal = null;
		this.obTree = null;
		this.obSkuProps = null;
		this.obBuyBtn = null;
		this.obBasketActions = null;
		this.obAskPrice = null;
		this.obNotAvail = null;
		this.obMore = null;
		this.obSubscribe = null;
		this.obDscPerc = null;
		this.obDscPercVal = null;
		this.obMeasure = null;		
		this.obDelay = null;
		this.obCompare = null;
		this.obQuickView = null;

		this.sPanel = null;
		this.sPanelContent = null;
		
		this.obPopupWin = null;
		this.basketUrl = '';
		this.basketParams = {};
		this.isTouchDevice = BX.hasClass(document.documentElement, 'bx-touch');
		this.hoverTimer = null;
		this.hoverStateChangeForbidden = false;
		this.mouseX = null;
		this.mouseY = null;

		this.useEnhancedEcommerce = false;
		this.dataLayerName = 'dataLayer';
		this.brandProperty = false;

		this.errorCode = 0;

		if(typeof arParams == 'object') {
			this.productType = parseInt(arParams.PRODUCT_TYPE, 10);			
			this.showQuantity = arParams.SHOW_QUANTITY;			
			this.showAbsent = arParams.SHOW_ABSENT;			
			this.showOldPrice = arParams.SHOW_OLD_PRICE;
			this.showMaxQuantity = arParams.SHOW_MAX_QUANTITY;
			this.relativeQuantityFactor = parseInt(arParams.RELATIVE_QUANTITY_FACTOR);
			this.showPercent = arParams.SHOW_DISCOUNT_PERCENT;
			this.usePriceRanges = arParams.USE_PRICE_COUNT;
			this.showSubscription = arParams.USE_SUBSCRIBE;

			if(arParams.ADD_TO_BASKET_ACTION) {
				this.basketAction = arParams.ADD_TO_BASKET_ACTION;
			}
			
			this.useCompare = arParams.DISPLAY_COMPARE;
			this.fullDisplayMode = arParams.PRODUCT_DISPLAY_MODE == 'Y';
			this.bigData = arParams.BIG_DATA;
			this.viewMode = arParams.VIEW_MODE || '';			
			this.useEnhancedEcommerce = arParams.USE_ENHANCED_ECOMMERCE == 'Y';
			this.dataLayerName = arParams.DATA_LAYER_NAME;
			this.brandProperty = arParams.BRAND_PROPERTY;

			this.visual = arParams.VISUAL;

			switch(this.productType) {
				case 0: // no catalog
				case 1: // product
				case 2: // set
					if(arParams.PRODUCT && typeof arParams.PRODUCT == 'object') {
						this.currentPriceMode = arParams.PRODUCT.ITEM_PRICE_MODE;
						this.currentPrices = arParams.PRODUCT.ITEM_PRICES;
						this.currentPriceSelected = arParams.PRODUCT.ITEM_PRICE_SELECTED;
						this.currentQuantityRanges = arParams.PRODUCT.ITEM_QUANTITY_RANGES;
						this.currentQuantityRangeSelected = arParams.PRODUCT.ITEM_QUANTITY_RANGE_SELECTED;

						if(this.showQuantity) {
							this.currentMeasure = arParams.PRODUCT.ITEM_MEASURE;

							this.product.checkQuantity = arParams.PRODUCT.CHECK_QUANTITY;
							this.product.isDblQuantity = arParams.PRODUCT.QUANTITY_FLOAT;

							if(this.product.checkQuantity) {
								this.product.maxQuantity = (this.product.isDblQuantity ? parseFloat(arParams.PRODUCT.MAX_QUANTITY) : parseInt(arParams.PRODUCT.MAX_QUANTITY, 10));
								this.product.maxPcQuantity = parseInt(arParams.PRODUCT.PC_MAX_QUANTITY, 10);
								this.product.maxSqMQuantity = parseFloat(arParams.PRODUCT.SQ_M_MAX_QUANTITY);
							}

							this.product.stepQuantity = (this.product.isDblQuantity ? parseFloat(arParams.PRODUCT.STEP_QUANTITY) : parseInt(arParams.PRODUCT.STEP_QUANTITY, 10));
							this.product.stepPcQuantity = parseInt(arParams.PRODUCT.PC_STEP_QUANTITY, 10);
							this.product.stepSqMQuantity = parseFloat(arParams.PRODUCT.SQ_M_STEP_QUANTITY);

							this.checkQuantity = this.product.checkQuantity;
							this.isDblQuantity = this.product.isDblQuantity;
							this.stepQuantity = this.product.stepQuantity;
							this.stepPcQuantity = this.product.stepPcQuantity;
							this.stepSqMQuantity = this.product.stepSqMQuantity;
							this.maxQuantity = this.product.maxQuantity;
							this.maxPcQuantity = this.product.maxPcQuantity;
							this.maxSqMQuantity = this.product.maxSqMQuantity;
							this.minQuantity = this.currentPriceMode == 'Q'
								? parseFloat(this.currentPrices[this.currentPriceSelected].MIN_QUANTITY)
								: this.stepQuantity;
							this.minPcQuantity = this.stepPcQuantity;
							this.minSqMQuantity = this.currentPriceMode == 'Q' 
								? parseFloat(this.currentPrices[this.currentPriceSelected].SQ_M_MIN_QUANTITY) 
								: this.stepSqMQuantity;

							if(this.isDblQuantity) {
								this.stepQuantity = Math.round(this.stepQuantity * this.precisionFactor) / this.precisionFactor;
							}
							this.stepSqMQuantity = Math.round(this.stepSqMQuantity * this.precisionFactor) / this.precisionFactor;
						}

						this.product.canBuy = arParams.PRODUCT.CAN_BUY;						

						if(arParams.PRODUCT.RCM_ID) {
							this.product.rcmId = arParams.PRODUCT.RCM_ID;
						}

						this.canBuy = this.product.canBuy;
						this.product.name = arParams.PRODUCT.NAME;
						this.product.pict = arParams.PRODUCT.PICT;
						this.product.id = arParams.PRODUCT.ID;
						this.product.iblockId = arParams.PRODUCT.IBLOCK_ID;
						this.product.detailPageUrl = arParams.PRODUCT.DETAIL_PAGE_URL;

						if(arParams.PRODUCT.ADD_URL) {
							this.product.addUrl = arParams.PRODUCT.ADD_URL;
						}

						if(arParams.PRODUCT.BUY_URL) {
							this.product.buyUrl = arParams.PRODUCT.BUY_URL;
						}

						if(arParams.BASKET && typeof arParams.BASKET == 'object') {
							this.basketData.useProps = arParams.BASKET.ADD_PROPS;
							this.basketData.emptyProps = arParams.BASKET.EMPTY_PROPS;
						}
					} else {
						this.errorCode = -1;
					}
					break;
				case 3: // sku
					if(arParams.OFFERS_VIEW) {
						this.offersView = arParams.OFFERS_VIEW;
					}
					
					if(arParams.PRODUCT && typeof arParams.PRODUCT == 'object') {
						this.product.name = arParams.PRODUCT.NAME;
						this.product.id = arParams.PRODUCT.ID;
						this.product.iblockId = arParams.PRODUCT.IBLOCK_ID;
						this.product.detailPageUrl = arParams.PRODUCT.DETAIL_PAGE_URL;						

						if(arParams.PRODUCT.RCM_ID) {
							this.product.rcmId = arParams.PRODUCT.RCM_ID;
						}
					}

					if(arParams.OFFERS && BX.type.isArray(arParams.OFFERS)) {
						this.offers = arParams.OFFERS;
						this.offerNum = 0;

						if(arParams.OFFER_SELECTED) {
							this.offerNum = parseInt(arParams.OFFER_SELECTED, 10);
						}

						if(isNaN(this.offerNum)) {
							this.offerNum = 0;
						}

						if(arParams.TREE_PROPS) {
							this.treeProps = arParams.TREE_PROPS;
						}
						
						if(arParams.DEFAULT_PICTURE) {
							this.defaultPict.pict = arParams.DEFAULT_PICTURE.PICTURE;							
						}
					}
					break;
				default:
					this.errorCode = -1;
			}
			
			if(arParams.BASKET && typeof arParams.BASKET == 'object') {
				if(arParams.BASKET.QUANTITY) {
					this.basketData.quantity = arParams.BASKET.QUANTITY;
				}

				if(arParams.BASKET.PROPS) {
					this.basketData.props = arParams.BASKET.PROPS;
				}

				if(arParams.BASKET.BASKET_URL) {
					this.basketData.basketUrl = arParams.BASKET.BASKET_URL;
				}

				if(3 == this.productType) {
					if(arParams.BASKET.SKU_PROPS) {
						this.basketData.sku_props = arParams.BASKET.SKU_PROPS;
					}
				}

				if(arParams.BASKET.ADD_URL_TEMPLATE) {
					this.basketData.add_url = arParams.BASKET.ADD_URL_TEMPLATE;
				}

				if(arParams.BASKET.BUY_URL_TEMPLATE) {
					this.basketData.buy_url = arParams.BASKET.BUY_URL_TEMPLATE;
				}

				if(this.basketData.add_url == '' && this.basketData.buy_url == '') {
					this.errorCode = -1024;
				}
			}
			
			if(arParams.AJAX_PATH) {
				this.ajaxPath = arParams.AJAX_PATH;
			}

			if(arParams.QUICK_VIEW && typeof arParams.QUICK_VIEW == 'object') {
				if(arParams.QUICK_VIEW.QUICK_VIEW_PARAMETERS) {
					this.quickViewData.quickViewParameters = arParams.QUICK_VIEW.QUICK_VIEW_PARAMETERS;
				}

				if(arParams.QUICK_VIEW.QUICK_VIEW_PREV_NEXT) {
					this.quickViewData.quickViewPrevNext = arParams.QUICK_VIEW.QUICK_VIEW_PREV_NEXT;
				}
			}

			if(this.useCompare) {
				if(arParams.COMPARE && typeof arParams.COMPARE == 'object') {
					if(arParams.COMPARE.COMPARE_NAME) {
						this.compareData.compareName = arParams.COMPARE.COMPARE_NAME;
					}

					if(arParams.COMPARE.COMPARE_PATH) {
						this.compareData.comparePath = arParams.COMPARE.COMPARE_PATH;
					}

					if(arParams.COMPARE.COMPARE_URL_TEMPLATE) {
						this.compareData.compareUrl = arParams.COMPARE.COMPARE_URL_TEMPLATE;
					} else {
						this.useCompare = false;
					}

					if(arParams.COMPARE.COMPARE_DELETE_URL_TEMPLATE) {
						this.compareData.compareDeleteUrl = arParams.COMPARE.COMPARE_DELETE_URL_TEMPLATE;
					} else {
						this.useCompare = false;
					}
				} else {
					this.useCompare = false;
				}
			}

			if(arParams.OBJECT && typeof arParams.OBJECT == 'object') {
				if(arParams.OBJECT.ID) {
					this.object.id = arParams.OBJECT.ID;
				}
			}
		}
		
		if(this.errorCode == 0) {
			BX.ready(BX.delegate(this.init,this));
		}
	};

	window.JCCatalogItem.prototype = {
		init: function() {
			var i = 0,
				treeItems = null;

			this.obProduct = BX(this.visual.ID);
			if(!this.obProduct) {
				this.errorCode = -1;
			}

			this.obPict = BX(this.visual.PICT_ID);
			if(!this.obPict) {
				this.errorCode = -2;
			}
			
			this.obArticle = BX(this.visual.ARTICLE_ID);
			this.obArticleName = !!this.obArticle && this.obArticle.querySelector('[data-entity="article-name"]');
			this.obArticleVal = !!this.obArticle && this.obArticle.querySelector('[data-entity="article-val"]');
			
			this.obSkuProps = BX(this.visual.DISPLAY_PROP_DIV);			
			
			this.obPrice = BX(this.visual.PRICE_ID);
			this.obPriceNotSet = !!this.obPrice && this.obPrice.querySelector('[data-entity="price-current-not-set"]');
			this.obPriceCurrent = !!this.obPrice && this.obPrice.querySelector('[data-entity="price-current"]');			
			this.obPriceOld = BX(this.visual.OLD_PRICE_ID);
			this.obPriceDiscount = BX(this.visual.DISCOUNT_PRICE_ID);
			this.obPriceMeasure = !!this.obPrice && this.obPrice.querySelector('[data-entity="price-measure"]');
			if(!this.obPrice) {
				this.errorCode = -16;
			}

			if(this.usePriceRanges) {
				this.obPriceRangesIcon = this.obProduct.querySelector('[data-entity="price-ranges-icon"]');
				this.obPriceRanges = this.obProduct.querySelector('[data-entity="price-ranges-block"]');
				this.obPriceRangesBody = !!this.obPriceRanges && this.obPriceRanges.querySelector('[data-entity="price-ranges-body"]');
			}
			
			if(this.showQuantity) {
				this.blockNodes.quantity = !!this.obProduct && this.obProduct.querySelector('[data-entity="quantity-block"]');

				this.obQuantity = BX(this.visual.QUANTITY_ID);
				if(!this.isTouchDevice) {
					BX.bind(this.obQuantity, 'focus', BX.proxy(this.onFocus, this));
					BX.bind(this.obQuantity, 'blur', BX.proxy(this.onBlur, this));
				}
				if(this.visual.QUANTITY_UP_ID) {
					this.obQuantityUp = BX(this.visual.QUANTITY_UP_ID);
				}
				if(this.visual.QUANTITY_DOWN_ID) {
					this.obQuantityDown = BX(this.visual.QUANTITY_DOWN_ID);
				}

				this.obPcQuantity = BX(this.visual.PC_QUANTITY_ID);				
				if(!this.isTouchDevice) {
					BX.bind(this.obPcQuantity, 'focus', BX.proxy(this.onFocus, this));
					BX.bind(this.obPcQuantity, 'blur', BX.proxy(this.onBlur, this));
				}
				if(this.visual.PC_QUANTITY_UP_ID) {
					this.obPcQuantityUp = BX(this.visual.PC_QUANTITY_UP_ID);
				}
				if(this.visual.PC_QUANTITY_DOWN_ID) {
					this.obPcQuantityDown = BX(this.visual.PC_QUANTITY_DOWN_ID);
				}

				this.obSqMQuantity = BX(this.visual.SQ_M_QUANTITY_ID);				
				if(!this.isTouchDevice) {
					BX.bind(this.obSqMQuantity, 'focus', BX.proxy(this.onFocus, this));
					BX.bind(this.obSqMQuantity, 'blur', BX.proxy(this.onBlur, this));
				}
				if(this.visual.SQ_M_QUANTITY_UP_ID) {
					this.obSqMQuantityUp = BX(this.visual.SQ_M_QUANTITY_UP_ID);
				}
				if(this.visual.SQ_M_QUANTITY_DOWN_ID) {
					this.obSqMQuantityDown = BX(this.visual.SQ_M_QUANTITY_DOWN_ID);
				}

				this.obTotalCost = BX(this.visual.TOTAL_COST_ID);
				this.obTotalCostVal = !!this.obTotalCost && this.obTotalCost.querySelector('[data-entity="total-cost"]');
			}

			if(this.visual.QUANTITY_LIMIT && this.showMaxQuantity !== 'N') {
				this.obQuantityLimit.all = BX(this.visual.QUANTITY_LIMIT);
				if(this.obQuantityLimit.all) {					
					this.obQuantityLimit.value = this.obQuantityLimit.all.querySelector('[data-entity="quantity-limit-value"]');
					if(!this.obQuantityLimit.value) {
						this.obQuantityLimit.all = null;
					}
				}
			}

			if(this.visual.QUANTITY_LIMIT_NOT_AVAILABLE && this.showMaxQuantity !== 'N') {
				this.obQuantityLimitNotAvl.all = BX(this.visual.QUANTITY_LIMIT_NOT_AVAILABLE);
			}

			if(this.productType == 3 && this.fullDisplayMode) {
				if(this.visual.TREE_ID) {
					this.obTree = BX(this.visual.TREE_ID);
					if(!this.obTree) {
						this.errorCode = -256;
					}
				}

				if(this.visual.QUANTITY_MEASURE) {
					this.obMeasure = BX(this.visual.QUANTITY_MEASURE);
				}
			}

			this.obBasketActions = BX(this.visual.BASKET_ACTIONS_ID);
			if(this.obBasketActions) {
				if(this.visual.BUY_LINK) {
					this.obBuyBtn = BX(this.visual.BUY_LINK);
				}
			}

			this.obAskPrice = BX(this.visual.ASK_PRICE_LINK);
			this.obNotAvail = BX(this.visual.NOT_AVAILABLE_LINK);
			
			this.obMore = BX(this.visual.MORE_LINK);

			if(this.showSubscription) {
				this.obSubscribe = BX(this.visual.SUBSCRIBE_LINK);
			}
			
			if(this.showPercent) {
				if(this.visual.DISCOUNT_PERCENT_ID) {
					this.obDscPerc = BX(this.visual.DISCOUNT_PERCENT_ID);
					this.obDscPercVal = !!this.obDscPerc && this.obDscPerc.querySelector('[data-entity="dsc-perc-val"]');
				}				
			}
			
			if(this.errorCode == 0) {
				//product slider events
				if(!this.isTouchDevice) {
					//product hover events
					BX.bind(this.obProduct, 'mouseenter', BX.proxy(this.hoverOn, this));
					BX.bind(this.obProduct, 'mouseleave', BX.proxy(this.hoverOff, this));
				}

				if(this.bigData) {
					var links = BX.findChildren(this.obProduct, {tag:'a'}, true);
					if(links) {
						for(i in links) {
							if(links.hasOwnProperty(i)) {
								if(links[i].getAttribute('href') == this.product.detailPageUrl) {
									BX.bind(links[i], 'click', BX.proxy(this.rememberProductRecommendation, this));
								}
							}
						}
					}
				}

				if(this.showQuantity) {
					if(this.obQuantityUp) {
						BX.bind(this.obQuantityUp, 'click', BX.delegate(this.quantityUp, this));
					}
					if(this.obQuantityDown) {
						BX.bind(this.obQuantityDown, 'click', BX.delegate(this.quantityDown, this));
					}
					if(this.obQuantity) {
						BX.bind(this.obQuantity, 'change', BX.delegate(this.quantityChange, this));
					}

					if(this.obPcQuantityUp) {
						BX.bind(this.obPcQuantityUp, 'click', BX.delegate(this.quantityUp, this));
					}
					if(this.obPcQuantityDown) {
						BX.bind(this.obPcQuantityDown, 'click', BX.delegate(this.quantityDown, this));
					}
					if(this.obPcQuantity) {
						BX.bind(this.obPcQuantity, 'change', BX.delegate(this.pcQuantityChange, this));
					}

					if(this.obSqMQuantityUp) {
						BX.bind(this.obSqMQuantityUp, 'click', BX.delegate(this.quantityUp, this));
					}
					if(this.obSqMQuantityDown) {
						BX.bind(this.obSqMQuantityDown, 'click', BX.delegate(this.quantityDown, this));
					}
					if(this.obSqMQuantity) {
						BX.bind(this.obSqMQuantity, 'change', BX.delegate(this.sqMQuantityChange, this));
					}
				}

				switch(this.productType) {
					case 0: // no catalog
					case 1: // product
					case 2: // set						
						this.checkQuantityControls();
						break;
					case 3: // sku
						if(this.offers.length > 0) {
							if(this.offersView == 'PROPS') {
								treeItems = BX.findChildren(this.obTree, {tagName: 'li'}, true);
								if(treeItems && treeItems.length) {
									for(i = 0; i < treeItems.length; i++) {
										BX.bind(treeItems[i], 'click', BX.delegate(this.selectOfferProp, this));
									}
								}
							}
							this.setCurrent();
						}
						break;
				}

				if(this.obBuyBtn) {
					if(this.basketAction == 'ADD') {
						BX.bind(this.obBuyBtn, 'click', BX.proxy(this.add2Basket, this));
					} else {
						BX.bind(this.obBuyBtn, 'click', BX.proxy(this.buyBasket, this));
					}
				}
				
				this.obDelay = BX(this.visual.DELAY_LINK);
				if(this.obDelay)
					BX.bind(this.obDelay, 'click', BX.proxy(this.delay, this));
				
				if(this.useCompare) {
					this.obCompare = BX(this.visual.COMPARE_LINK);
					if(this.obCompare) {
						BX.bind(this.obCompare, 'click', BX.proxy(this.compare, this));
					}
					BX.addCustomEvent('onCatalogDeleteCompare', BX.proxy(this.checkDeletedCompare, this));
				}

				if(this.obBuyBtn || this.obDelay || this.obCompare || this.obQuantity || this.obPcQuantity || this.obSqMQuantity)
					this.checkComparedDelayedBuyedAddedQuantity();
				
				this.obQuickView = BX(this.visual.QUICK_VIEW_LINK);
				if(this.obQuickView) {
					BX.bind(this.obQuickView, 'click', BX.proxy(this.quickView, this));
				} else {
					this.obQuickView = this.obProduct.querySelectorAll('[data-entity="quickView"]');
					if(this.obQuickView) {
						for(var i in this.obQuickView) {
							if(this.obQuickView.hasOwnProperty(i) && BX.type.isDomNode(this.obQuickView[i])) {
								BX.bind(this.obQuickView[i], 'click', BX.proxy(this.quickView, this));
							}
						}
					}
				}

				if(this.quickViewData.quickViewPrevNext) {
					BX.bind(document, 'keydown', BX.delegate(function(e) {
						var fancybox = document.body.querySelector('.fancybox-overlay');
						if(!fancybox) {
							var popupPanel = document.body.querySelector('.popup-panel');
							if(!!popupPanel) {
								if(e.keyCode == 37) {
									var popupPanelPrev = popupPanel.querySelector('.popup-panel__prev');
									if(!!popupPanelPrev)
										popupPanelPrev.click();
								} else if(e.keyCode == 39) {
									var popupPanelNext = popupPanel.querySelector('.popup-panel__next');
									if(!!popupPanelNext)
										popupPanelNext.click();
								}
							}
						}
					}, this));
				}

				this.sPanel = document.body.querySelector('.slide-panel');

				if(this.obAskPrice)
					BX.bind(this.obAskPrice, 'click', BX.proxy(this.sPanelForm, this));

				if(this.obNotAvail)
					BX.bind(this.obNotAvail, 'click', BX.proxy(this.sPanelForm, this));

				BX.addCustomEvent(this, 'quickViewRequest', BX.proxy(this.quickViewRequest, this));
				BX.addCustomEvent(this, 'sPanelFormRequest', BX.proxy(this.sPanelFormRequest, this));
			}
		},

		checkComparedDelayedBuyedAddedQuantity: function() {
			var data = {};
			data['action'] = 'checkComparedDelayedBuyedAddedQuantity';

			switch(this.productType) {
				case 1: //product
				case 2: //set
					data['productId'] = this.product.id;
					break;
				case 3: //sku
					data['offers'] = this.offers;
					data['offerNum'] = this.offerNum;
					break;
			}

			if(this.obBuyBtn)
				data['checkBuyedAdded'] = true;

			if(this.obDelay)
				data['checkDelayed'] = true;

			if(this.obCompare) {
				data['checkCompared'] = true;
				data['compareName'] = this.compareData.compareName;
				data['iblockId'] = this.product.iblockId;
			}

			if(this.obQuantity || this.obPcQuantity || this.obSqMQuantity)
				data['checkQuantity'] = true;

			BX.ajax({
				url: this.ajaxPath,
				method: 'POST',
				dataType: 'json',
				timeout: 60,
				data: data,
				onsuccess: BX.delegate(function(result) {
					if(this.obCompare) {
						this.setCompared(result.compared);
						if(result.comparedIds.length > 0)
							this.setCompareInfo(result.comparedIds);
					}
					
					if(this.obDelay) {
						this.setDelayed(result.delayed);
						if(result.delayedIds.length > 0)
							this.setDelayInfo(result.delayedIds);
					}
					
					if(this.obBuyBtn) {
						this.setBuyedAdded(result.buyedAdded);
						if(result.buyedAddedIds.length > 0)
							this.setBuyAddInfo(result.buyedAddedIds);
					}

					if(this.obQuantity && !this.obPcQuantity && !this.obSqMQuantity) {
						if(!!result.quantity) {
							this.obQuantity.value = result.quantity;
							this.quantityChange(true);
						}						
						if(Object.keys(result.quantityIds).length > 0)
							this.setQuantityInfo(result.quantityIds);
					} else if(this.obPcQuantity && this.obSqMQuantity) {
						if(!!result.quantity) {
							if(this.currentMeasure.SYMBOL_INTL == 'pc. 1' || this.currentMeasure.SYMBOL_INTL == 'm2') {
								(this.currentPrices[this.currentPriceSelected].SQ_M_PRICE ? this.obPcQuantity : this.obSqMQuantity).value = result.quantity;
								this.currentPrices[this.currentPriceSelected].SQ_M_PRICE ? this.pcQuantityChange(true) : this.sqMQuantityChange(true);
							} else {
								this.obQuantity.value = result.quantity;
								this.quantityChange(true);
							}
						}
						if(Object.keys(result.quantityIds).length > 0)
							this.setQuantityInfo(result.quantityIds);
					}
				}, this)
			});
		},
			
		setAnalyticsDataLayer: function(action) {
			if(!this.useEnhancedEcommerce || !this.dataLayerName)
				return;

			var item = {},
				info = {},
				variants = [],
				i, k, j, propId, skuId, propValues;

			switch(this.productType) {
				case 0: //no catalog
				case 1: //product
				case 2: //set
					item = {
						'id': this.product.id,
						'name': this.product.name,
						'price': this.currentPrices[this.currentPriceSelected] && this.currentPrices[this.currentPriceSelected].PRICE,
						'brand': BX.type.isArray(this.brandProperty) ? this.brandProperty.join('/') : this.brandProperty
					};
					break;
				case 3: //sku
					for(i in this.offers[this.offerNum].TREE) {
						if(this.offers[this.offerNum].TREE.hasOwnProperty(i)) {
							propId = i.substring(5);
							skuId = this.offers[this.offerNum].TREE[i];

							for(k in this.treeProps) {
								if(this.treeProps.hasOwnProperty(k) && this.treeProps[k].ID == propId) {
									for(j in this.treeProps[k].VALUES) {
										propValues = this.treeProps[k].VALUES[j];
										if(propValues.ID == skuId) {
											variants.push(propValues.NAME);
											break;
										}
									}
								}
							}
						}
					}

					item = {
						'id': this.offers[this.offerNum].ID,
						'name': this.offers[this.offerNum].NAME,
						'price': this.currentPrices[this.currentPriceSelected] && this.currentPrices[this.currentPriceSelected].PRICE,
						'brand': BX.type.isArray(this.brandProperty) ? this.brandProperty.join('/') : this.brandProperty,
						'variant': variants.join('/')
					};
					break;
			}

			switch(action) {
				case 'addToCart':
					info = {
						'event': 'addToCart',
						'ecommerce': {
							'currencyCode': this.currentPrices[this.currentPriceSelected] && this.currentPrices[this.currentPriceSelected].CURRENCY || '',
							'add': {
								'products': [{
									'name': item.name || '',
									'id': item.id || '',
									'price': item.price || 0,
									'brand': item.brand || '',
									'category': item.category || '',
									'variant': item.variant || ''
								}]
							}
						}
					};

					if(this.showQuantity) {
						if(this.obQuantity && !this.obPcQuantity && !this.obSqMQuantity) {
							info.ecommerce.add.products[0].quantity = this.obQuantity.value;
						} else if(this.obPcQuantity && this.obSqMQuantity) {
							if(this.currentMeasure.SYMBOL_INTL == 'pc. 1' || this.currentMeasure.SYMBOL_INTL == 'm2') {
								info.ecommerce.add.products[0].quantity = this.currentPrices[this.currentPriceSelected].SQ_M_PRICE ? this.obPcQuantity.value : this.obSqMQuantity.value;
							} else {
								info.ecommerce.add.products[0].quantity = this.obQuantity.value;
							}
						}
					} else {						
						info.ecommerce.add.products[0].quantity = this.currentPrices[this.currentPriceSelected] ? this.currentPrices[this.currentPriceSelected].MIN_QUANTITY : '';
					}
					break;
			}

			window[this.dataLayerName] = window[this.dataLayerName] || [];
			window[this.dataLayerName].push(info);
		},

		hoverOn: function(event) {
			clearTimeout(this.hoverTimer);
			this.obProduct.style.height = getComputedStyle(this.obProduct).height;
			BX.addClass(this.obProduct, 'hover');

			BX.PreventDefault(event);
		},

		hoverOff: function(event) {
			if(this.hoverStateChangeForbidden)
				return;

			BX.removeClass(this.obProduct, 'hover');
			this.hoverTimer = setTimeout(
				BX.delegate(function() {
					this.obProduct.style.height = this.viewMode == 'CARD' ? '100%' : 'auto';
				}, this),
				300
			);
				
			BX.PreventDefault(event);
		},

		onFocus: function() {
			this.hoverStateChangeForbidden = true;
			BX.bind(document, 'mousemove', BX.proxy(this.captureMousePosition, this));
		},

		onBlur: function() {
			this.hoverStateChangeForbidden = false;
			BX.unbind(document, 'mousemove', BX.proxy(this.captureMousePosition, this));

			var cursorElement = document.elementFromPoint(this.mouseX, this.mouseY);
			if(!cursorElement || !this.obProduct.contains(cursorElement)) {
				this.hoverOff();
			}
		},

		captureMousePosition: function(event) {
			this.mouseX = event.clientX;
			this.mouseY = event.clientY;
		},

		getCookie: function(name) {
			var matches = document.cookie.match(new RegExp(
				"(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
			));

			return matches ? decodeURIComponent(matches[1]) : null;
		},

		rememberProductRecommendation: function() {
			//save to RCM_PRODUCT_LOG
			var cookieName = BX.cookie_prefix + '_RCM_PRODUCT_LOG',
				cookie = this.getCookie(cookieName),
				itemFound = false;

			var cItems = [],
				cItem;

			if(cookie) {
				cItems = cookie.split('.');
			}

			var i = cItems.length;

			while (i--) {
				cItem = cItems[i].split('-');

				if(cItem[0] == this.product.id) {
					//it's already in recommendations, update the date
					cItem = cItems[i].split('-');

					//update rcmId and date
					cItem[1] = this.product.rcmId;
					cItem[2] = BX.current_server_time;

					cItems[i] = cItem.join('-');
					itemFound = true;
				} else {
					if((BX.current_server_time - cItem[2]) > 3600 * 24 * 30) {
						cItems.splice(i, 1);
					}
				}
			}

			if(!itemFound) {
				//add recommendation
				cItems.push([this.product.id, this.product.rcmId, BX.current_server_time].join('-'));
			}

			//serialize
			var plNewCookie = cItems.join('.'),
				cookieDate = new Date(new Date().getTime() + 1000 * 3600 * 24 * 365 * 10).toUTCString();

			document.cookie = cookieName + "=" + plNewCookie + "; path=/; expires=" + cookieDate + "; domain=" + BX.cookie_domain;
		},
			
		quantityUp: function() {
			var curValue = 0,
				curPcValue = 0,
				curSqMValue = 0,
				boolSet = true,
				boolPcSet = true,
				boolSqMSet = true;

			if(this.errorCode == 0 && this.showQuantity && this.canBuy) {
				if(this.obQuantity) {
					curValue = (this.isDblQuantity ? parseFloat(this.obQuantity.value) : parseInt(this.obQuantity.value, 10));
					if(!isNaN(curValue)) {
						curValue += this.stepQuantity;
						if(this.checkQuantity) {
							if(curValue > this.maxQuantity) {
								boolSet = false;
							}
						}

						if(boolSet) {
							if(this.isDblQuantity) {
								curValue = Math.round(curValue * this.precisionFactor) / this.precisionFactor;
							}

							this.obQuantity.value = curValue;

							this.setPrice();
							if(!this.obPcQuantity && !this.obSqMQuantity)
								this.updateCartProductQuantity();
						}
					}
				}

				if(this.obPcQuantity && this.obSqMQuantity) {
					curPcValue = parseInt(this.obPcQuantity.value, 10);
					if(!isNaN(curPcValue)) {
						curPcValue += this.stepPcQuantity;
						if(this.checkQuantity) {
							if(curPcValue > this.maxPcQuantity)
								boolPcSet = false;
						}
						
						if(boolPcSet)
							this.obPcQuantity.value = curPcValue;
					}
					
					curSqMValue = parseFloat(this.obSqMQuantity.value);
					if(!isNaN(curSqMValue)) {
						curSqMValue += this.stepSqMQuantity;
						if(this.checkQuantity) {
							if(curSqMValue > this.maxSqMQuantity)
								boolSqMSet = false;
						}
						
						if(boolSqMSet) {
							curSqMValue = Math.round(curSqMValue * this.precisionFactor) / this.precisionFactor;

							this.obSqMQuantity.value = curSqMValue;
						}
					}

					if(boolPcSet && boolSqMSet) {
						this.setPrice();
						this.updateCartProductQuantity();
					}
				}
			}
		},

		quantityDown: function() {
			var curValue = 0,
				curPcValue = 0,
				curSqMValue = 0,
				boolSet = true,
				boolPcSet = true,
				boolSqMSet = true;

			if(this.errorCode == 0 && this.showQuantity && this.canBuy) {
				if(this.obQuantity) {
					curValue = (this.isDblQuantity ? parseFloat(this.obQuantity.value) : parseInt(this.obQuantity.value, 10));
					if(!isNaN(curValue)) {
						curValue -= this.stepQuantity;

						this.checkPriceRange(curValue);

						if(curValue < this.minQuantity) {
							boolSet = false;
						}

						if(boolSet) {
							if(this.isDblQuantity) {
								curValue = Math.round(curValue * this.precisionFactor) / this.precisionFactor;
							}

							this.obQuantity.value = curValue;

							this.setPrice();
							if(!this.obPcQuantity && !this.obSqMQuantity)
								this.updateCartProductQuantity();
						}
					}
				}

				if(this.obPcQuantity && this.obSqMQuantity) {
					curPcValue = parseInt(this.obPcQuantity.value, 10);
					if(!isNaN(curPcValue)) {
						curPcValue -= this.stepPcQuantity;

						if(this.currentPrices[this.currentPriceSelected].SQ_M_PRICE)
							this.checkPriceRange(curPcValue);
						
						if(curPcValue < this.minPcQuantity)
							boolPcSet = false;
						
						if(boolPcSet)
							this.obPcQuantity.value = curPcValue;
					}
				
					curSqMValue = parseFloat(this.obSqMQuantity.value);
					if(!isNaN(curSqMValue)) {
						curSqMValue -= this.stepSqMQuantity;

						if(!this.currentPrices[this.currentPriceSelected].SQ_M_PRICE)
							this.checkPriceRange(curSqMValue);
						
						if(curSqMValue < this.minSqMQuantity)
							boolSqMSet = false;
						
						if(boolSqMSet) {
							curSqMValue = Math.round(curSqMValue * this.precisionFactor) / this.precisionFactor;

							this.obSqMQuantity.value = curSqMValue;
						}
					}

					if(boolPcSet && boolSqMSet) {
						this.setPrice();
						this.updateCartProductQuantity();
					}
				}
			}
		},

		quantityChange: function(disable) {
			var curValue = 0,
				curPcValue = 0,
				curSqMValue = 0,
				intCount,
				intPcCount,
				intSqMCount;

			if(this.errorCode == 0 && this.showQuantity) {
				if(this.canBuy) {
					curValue = this.isDblQuantity ? parseFloat(this.obQuantity.value) : Math.round(this.obQuantity.value);
					if(!isNaN(curValue)) {
						if(this.checkQuantity) {
							if(curValue > this.maxQuantity) {
								curValue = this.maxQuantity;
							}
						}

						this.checkPriceRange(curValue);

						if(curValue < this.minQuantity) {
							curValue = this.minQuantity;
						} else {
							intCount = Math.round(Math.round(curValue * this.precisionFactor / this.stepQuantity) / this.precisionFactor) || 1;
							curValue = (intCount <= 1 ? this.stepQuantity : intCount * this.stepQuantity);
							curValue = Math.round(curValue * this.precisionFactor) / this.precisionFactor;
						}

						this.obQuantity.value = curValue;
					} else {
						this.obQuantity.value = this.minQuantity;
					}

					if(this.obPcQuantity && this.obSqMQuantity) {
						curPcValue = this.currentMeasure.SYMBOL_INTL == 'pc. 1' ? this.obQuantity.value : Math.round(this.obQuantity.value / this.stepQuantity);
						if(!isNaN(curPcValue)) {
							if(this.checkQuantity) {
								if(curPcValue > this.maxPcQuantity)
									curPcValue = this.maxPcQuantity;
							}
							
							if(curPcValue < this.minPcQuantity) {
								curPcValue = this.minPcQuantity;
							} else {
								intPcCount = Math.round(Math.round(curPcValue * this.precisionFactor / this.stepPcQuantity) / this.precisionFactor) || 1;
								curPcValue = (intPcCount <= 1 ? this.stepPcQuantity : intPcCount * this.stepPcQuantity);
								curPcValue = Math.round(curPcValue * this.precisionFactor) / this.precisionFactor;
							}

							this.obPcQuantity.value = curPcValue;
						} else {
							this.obPcQuantity.value = this.minPcQuantity;
						}
						
						curSqMValue = this.currentMeasure.SYMBOL_INTL == 'm2' ? this.obQuantity.value : parseFloat((this.obQuantity.value * this.stepSqMQuantity) / this.stepQuantity);
						if(!isNaN(curSqMValue)) {
							if(this.checkQuantity) {
								if(curSqMValue > this.maxSqMQuantity)
									curSqMValue = this.maxSqMQuantity;
							}
							
							if(curSqMValue < this.minSqMQuantity) {
								curSqMValue = this.minSqMQuantity;
							} else {
								intSqMCount = Math.round(Math.round(curSqMValue * this.precisionFactor / this.stepSqMQuantity) / this.precisionFactor) || 1;
								curSqMValue = (intSqMCount <= 1 ? this.stepSqMQuantity : intSqMCount * this.stepSqMQuantity);
								curSqMValue = Math.round(curSqMValue * this.precisionFactor) / this.precisionFactor;
							}

							this.obSqMQuantity.value = curSqMValue;
						} else {
							this.obSqMQuantity.value = this.minSqMQuantity;
						}
					}
				} else {
					this.obQuantity.value = this.minQuantity;
					if(this.obPcQuantity && this.obSqMQuantity) {
						this.obPcQuantity.value = this.minPcQuantity;
						this.obSqMQuantity.value = this.minSqMQuantity;
					}
				}

				this.setPrice();
				if(!disable)
					this.updateCartProductQuantity();
			}
		},

		pcQuantityChange: function(disable) {
			var curPcValue = 0,
				curSqMValue = 0,
				curValue = 0,
				intPcCount,
				intSqMCount,
				intCount;

			if(this.errorCode == 0 && this.showQuantity) {
				if(this.canBuy) {
					curPcValue = Math.round(this.obPcQuantity.value);
					if(!isNaN(curPcValue)) {
						if(this.checkQuantity) {
							if(curPcValue > this.maxPcQuantity)
								curPcValue = this.maxPcQuantity;
						}

						if(!this.obQuantity && this.currentPrices[this.currentPriceSelected].SQ_M_PRICE)
							this.checkPriceRange(curPcValue);

						if(curPcValue < this.minPcQuantity) {
							curPcValue = this.minPcQuantity;
						} else {
							intPcCount = Math.round(Math.round(curPcValue * this.precisionFactor / this.stepPcQuantity) / this.precisionFactor) || 1;
							curPcValue = (intPcCount <= 1 ? this.stepPcQuantity : intPcCount * this.stepPcQuantity);
							curPcValue = Math.round(curPcValue * this.precisionFactor) / this.precisionFactor;
						}

						this.obPcQuantity.value = curPcValue;
					} else {
						this.obPcQuantity.value = this.minPcQuantity;
					}

					if(this.obSqMQuantity) {
						curSqMValue = parseFloat(this.obPcQuantity.value * this.stepSqMQuantity);
						if(!isNaN(curSqMValue)) {
							if(this.checkQuantity) {
								if(curSqMValue > this.maxSqMQuantity)
									curSqMValue = this.maxSqMQuantity;
							}

							if(!this.obQuantity && !this.currentPrices[this.currentPriceSelected].SQ_M_PRICE)
								this.checkPriceRange(curSqMValue);
							
							if(curSqMValue < this.minSqMQuantity) {
								curSqMValue = this.minSqMQuantity;
							} else {
								intSqMCount = Math.round(Math.round(curSqMValue * this.precisionFactor / this.stepSqMQuantity) / this.precisionFactor) || 1;
								curSqMValue = (intSqMCount <= 1 ? this.stepSqMQuantity : intSqMCount * this.stepSqMQuantity);
								curSqMValue = Math.round(curSqMValue * this.precisionFactor) / this.precisionFactor;
							}

							this.obSqMQuantity.value = curSqMValue;
						} else {
							this.obSqMQuantity.value = this.minSqMQuantity;
						}
					}

					if(this.obQuantity) {
						curValue = this.isDblQuantity ? parseFloat(this.currentPrices[this.currentPriceSelected].SQ_M_PRICE ? this.obPcQuantity.value : this.obSqMQuantity.value) 
							: Math.round(this.currentPrices[this.currentPriceSelected].SQ_M_PRICE ? this.obPcQuantity.value : this.obSqMQuantity.value);
						if(!isNaN(curValue)) {
							if(this.checkQuantity) {
								if(curValue > this.maxQuantity)
									curValue = this.maxQuantity;
							}

							this.checkPriceRange(curValue);

							if(curValue < this.minQuantity) {
								curValue = this.minQuantity;
							} else {
								intCount = Math.round(Math.round(curValue * this.precisionFactor / this.stepQuantity) / this.precisionFactor) || 1;
								curValue = (intCount <= 1 ? this.stepQuantity : intCount * this.stepQuantity);
								curValue = Math.round(curValue * this.precisionFactor) / this.precisionFactor;
							}

							this.obQuantity.value = curValue;
						} else {
							this.obQuantity.value = this.minQuantity;
						}
					}
				} else {
					this.obPcQuantity.value = this.minPcQuantity;
					if(this.obSqMQuantity)
						this.obSqMQuantity.value = this.minSqMQuantity;
					if(this.obQuantity)
						this.obQuantity.value = this.minQuantity;
				}

				this.setPrice();
				if(!disable)
					this.updateCartProductQuantity();
			}
		},

		sqMQuantityChange: function(disable) {
			var curSqMValue = 0,
				curPcValue = 0,
				curValue = 0,
				intSqMCount,
				intPcCount,
				intCount;

			if(this.errorCode == 0 && this.showQuantity) {
				if(this.canBuy) {
					curSqMValue = parseFloat(this.obSqMQuantity.value);
					if(!isNaN(curSqMValue)) {
						if(this.checkQuantity) {
							if(curSqMValue > this.maxSqMQuantity)
								curSqMValue = this.maxSqMQuantity;
						}

						if(!this.obQuantity && !this.currentPrices[this.currentPriceSelected].SQ_M_PRICE)
							this.checkPriceRange(curSqMValue);
						
						if(curSqMValue < this.minSqMQuantity) {
							curSqMValue = this.minSqMQuantity;
						} else {
							intSqMCount = Math.round(Math.round(curSqMValue * this.precisionFactor / this.stepSqMQuantity) / this.precisionFactor) || 1;
							curSqMValue = (intSqMCount <= 1 ? this.stepSqMQuantity : intSqMCount * this.stepSqMQuantity);
							curSqMValue = Math.round(curSqMValue * this.precisionFactor) / this.precisionFactor;
						}

						this.obSqMQuantity.value = curSqMValue;
					} else {
						this.obSqMQuantity.value = this.minSqMQuantity;
					}
					
					if(this.obPcQuantity) {
						curPcValue = Math.round((this.obSqMQuantity.value * this.stepPcQuantity) / this.stepSqMQuantity);
						if(!isNaN(curPcValue)) {
							if(this.checkQuantity) {
								if(curPcValue > this.maxPcQuantity)
									curPcValue = this.maxPcQuantity;
							}

							if(!this.obQuantity && this.currentPrices[this.currentPriceSelected].SQ_M_PRICE)
								this.checkPriceRange(curPcValue);
							
							if(curPcValue < this.minPcQuantity) {
								curPcValue = this.minPcQuantity;
							} else {
								intPcCount = Math.round(Math.round(curPcValue * this.precisionFactor / this.stepPcQuantity) / this.precisionFactor) || 1;
								curPcValue = (intPcCount <= 1 ? this.stepPcQuantity : intPcCount * this.stepPcQuantity);
								curPcValue = Math.round(curPcValue * this.precisionFactor) / this.precisionFactor;
							}

							this.obPcQuantity.value = curPcValue;
						} else {
							this.obPcQuantity.value = this.minPcQuantity;
						}
					}

					if(this.obQuantity) {
						curValue = this.isDblQuantity ? parseFloat(this.currentPrices[this.currentPriceSelected].SQ_M_PRICE ? this.obPcQuantity.value : this.obSqMQuantity.value) 
							: Math.round(this.currentPrices[this.currentPriceSelected].SQ_M_PRICE ? this.obPcQuantity.value : this.obSqMQuantity.value);
						if(!isNaN(curValue)) {
							if(this.checkQuantity) {
								if(curValue > this.maxQuantity)
									curValue = this.maxQuantity;
							}

							this.checkPriceRange(curValue);

							if(curValue < this.minQuantity) {
								curValue = this.minQuantity;
							} else {
								intCount = Math.round(Math.round(curValue * this.precisionFactor / this.stepQuantity) / this.precisionFactor) || 1;
								curValue = (intCount <= 1 ? this.stepQuantity : intCount * this.stepQuantity);
								curValue = Math.round(curValue * this.precisionFactor) / this.precisionFactor;
							}

							this.obQuantity.value = curValue;
						} else {
							this.obQuantity.value = this.minQuantity;
						}
					}
				} else {
					this.obSqMQuantity.value = this.minSqMQuantity;
					if(this.obPcQuantity)
						this.obPcQuantity.value = this.minPcQuantity;
					if(this.obQuantity)
						this.obQuantity.value = this.minQuantity;
				}

				this.setPrice();
				if(!disable)
					this.updateCartProductQuantity();
			}
		},

		quantitySet: function(index) {
			var strLimit, resetQuantity, resetPcQuantity, resetSqMQuantity;
			
			var newOffer = this.offers[index],
				oldOffer = this.offers[this.offerNum];

			if(this.errorCode == 0) {
				this.canBuy = newOffer.CAN_BUY;

				this.currentPriceMode = newOffer.ITEM_PRICE_MODE;
				this.currentPrices = newOffer.ITEM_PRICES;
				this.currentPriceSelected = newOffer.ITEM_PRICE_SELECTED;
				this.currentQuantityRanges = newOffer.ITEM_QUANTITY_RANGES;
				this.currentQuantityRangeSelected = newOffer.ITEM_QUANTITY_RANGE_SELECTED;
				this.currentMeasure = newOffer.ITEM_MEASURE;

				var price = this.currentPrices[this.currentPriceSelected],
					partnersUrl = newOffer.PARTNERS_URL;
				
				if(this.canBuy) {
					if(price && price.RATIO_PRICE > 0) {
						if(!partnersUrl) {
							this.obDelay && BX.style(this.obDelay, 'display', '');
							this.blockNodes.quantity && BX.style(this.blockNodes.quantity, 'display', '');
							this.obBuyBtn && BX.adjust(this.obBuyBtn, {props: {disabled: false}, style: {display: ''}});
							this.obMore && BX.style(this.obMore, 'display', 'none');
						} else {
							this.obDelay && BX.style(this.obDelay, 'display', 'none');
							this.blockNodes.quantity && BX.style(this.blockNodes.quantity, 'display', 'none');
							this.obBuyBtn && BX.style(this.obBuyBtn, 'display', 'none');
							this.obMore && BX.style(this.obMore, 'display', '');
						}
						this.obAskPrice && BX.style(this.obAskPrice, 'display', 'none');
					} else {
						if(!partnersUrl) {
							this.obBuyBtn && BX.adjust(this.obBuyBtn, {props: {disabled: true}, style: {display: this.obAskPrice ? 'none' : ''}});
							this.obAskPrice && BX.style(this.obAskPrice, 'display', '');
							this.obMore && BX.style(this.obMore, 'display', 'none');
						} else {
							this.obBuyBtn && BX.style(this.obBuyBtn, 'display', 'none');
							this.obAskPrice && BX.style(this.obAskPrice, 'display', 'none');
							this.obMore && BX.style(this.obMore, 'display', '');
						}
						this.obDelay && BX.style(this.obDelay, 'display', 'none');
						this.blockNodes.quantity && BX.style(this.blockNodes.quantity, 'display', 'none');
					}
					this.obNotAvail && BX.style(this.obNotAvail, 'display', 'none');
					this.obSubscribe && BX.style(this.obSubscribe, 'display', 'none');
				} else {					
					if(!partnersUrl) {
						this.obBuyBtn && BX.adjust(this.obBuyBtn, {props: {disabled: true}, style: {display: this.obNotAvail || this.obSubscribe ? 'none' : ''}});
						this.obMore && BX.style(this.obMore, 'display', 'none');						
						if(this.obNotAvail) {
							BX.style(this.obNotAvail, 'display', '');
							this.obSubscribe && BX.style(this.obSubscribe, 'display', 'none');
						} else if(this.obSubscribe) {
							if(newOffer.CATALOG_SUBSCRIBE == 'Y') {
								BX.style(this.obSubscribe, 'display', '');
								this.obSubscribe.setAttribute('data-item', newOffer.ID);
								BX(this.visual.SUBSCRIBE_LINK + '_hidden').click();	
							} else {
								BX.style(this.obSubscribe, 'display', 'none');
							}
						}
					} else {
						this.obBuyBtn && BX.style(this.obBuyBtn, 'display', 'none');
						this.obMore && BX.style(this.obMore, 'display', '');
						this.obNotAvail && BX.style(this.obNotAvail, 'display', 'none');
						this.obSubscribe && BX.style(this.obSubscribe, 'display', 'none');
					}
					this.obDelay && BX.style(this.obDelay, 'display', 'none');
					this.blockNodes.quantity && BX.style(this.blockNodes.quantity, 'display', 'none');
					this.obAskPrice && BX.style(this.obAskPrice, 'display', 'none');
				}
				
				this.isDblQuantity = newOffer.QUANTITY_FLOAT;
				this.checkQuantity = newOffer.CHECK_QUANTITY;

				if(this.isDblQuantity) {
					this.stepQuantity = Math.round(parseFloat(newOffer.STEP_QUANTITY) * this.precisionFactor) / this.precisionFactor;
					this.maxQuantity = parseFloat(newOffer.MAX_QUANTITY);
					this.minQuantity = this.currentPriceMode == 'Q' ? parseFloat(this.currentPrices[this.currentPriceSelected].MIN_QUANTITY) : this.stepQuantity;
				} else {
					this.stepQuantity = parseInt(newOffer.STEP_QUANTITY, 10);
					this.maxQuantity = parseInt(newOffer.MAX_QUANTITY, 10);
					this.minQuantity = this.currentPriceMode == 'Q' ? parseInt(this.currentPrices[this.currentPriceSelected].MIN_QUANTITY) : this.stepQuantity;
				}
				this.stepPcQuantity = parseInt(newOffer.PC_STEP_QUANTITY, 10);
				this.maxPcQuantity = parseInt(newOffer.PC_MAX_QUANTITY, 10);
				this.minPcQuantity = this.stepPcQuantity;
				this.stepSqMQuantity = Math.round(parseFloat(newOffer.SQ_M_STEP_QUANTITY) * this.precisionFactor) / this.precisionFactor;
				this.maxSqMQuantity = parseFloat(newOffer.SQ_M_MAX_QUANTITY);
				this.minSqMQuantity = this.currentPriceMode == 'Q' ? parseFloat(this.currentPrices[this.currentPriceSelected].SQ_M_MIN_QUANTITY) : this.stepSqMQuantity;

				if(this.showQuantity) {
					if(this.obQuantity) {
						var isDifferentMinQuantity = oldOffer.ITEM_PRICES.length
							&& oldOffer.ITEM_PRICES[oldOffer.ITEM_PRICE_SELECTED]
							&& oldOffer.ITEM_PRICES[oldOffer.ITEM_PRICE_SELECTED].MIN_QUANTITY != this.minQuantity;

						if(this.isDblQuantity) {
							resetQuantity = Math.round(parseFloat(oldOffer.STEP_QUANTITY) * this.precisionFactor) / this.precisionFactor !== this.stepQuantity
								|| isDifferentMinQuantity
								|| oldOffer.MEASURE !== newOffer.MEASURE
								|| (
									this.checkQuantity
									&& parseFloat(oldOffer.MAX_QUANTITY) > this.maxQuantity
									&& parseFloat(this.obQuantity.value) > this.maxQuantity
								);
						} else {
							resetQuantity = parseInt(oldOffer.STEP_QUANTITY, 10) !== this.stepQuantity
								|| isDifferentMinQuantity
								|| oldOffer.MEASURE !== newOffer.MEASURE
								|| (
									this.checkQuantity
									&& parseInt(oldOffer.MAX_QUANTITY, 10) > this.maxQuantity
									&& parseInt(this.obQuantity.value, 10) > this.maxQuantity
								);
						}

						this.obQuantity.disabled = !this.canBuy;

						if(!!newOffer.QUANTITY) {
							this.obQuantity.value = newOffer.QUANTITY;
							this.quantityChange(true);
						} else if(resetQuantity) {
							this.obQuantity.value = this.minQuantity;
						}
					}

					if(this.obMeasure) {
						if(newOffer.MEASURE) {
							BX.adjust(this.obMeasure, {html: newOffer.MEASURE});
						} else {
							BX.adjust(this.obMeasure, {html: ''});
						}
					}
					
					if(this.obPriceMeasure) {
						if(newOffer.MEASURE) {
							BX.adjust(this.obPriceMeasure, {html: '/' + newOffer.MEASURE});
						} else {
							BX.adjust(this.obPriceMeasure, {html: ''});
						}
					}
					
					if(this.obPcQuantity && this.obSqMQuantity) {
						if(this.currentMeasure.SYMBOL_INTL == 'pc. 1' || this.currentMeasure.SYMBOL_INTL == 'm2') {
							if(price.SQ_M_PRICE) {
								var isDifferentMinPcQuantity = oldOffer.ITEM_PRICES.length
									&& oldOffer.ITEM_PRICES[oldOffer.ITEM_PRICE_SELECTED]
									&& oldOffer.ITEM_PRICES[oldOffer.ITEM_PRICE_SELECTED].PC_MIN_QUANTITY != this.minPcQuantity;

								resetPcQuantity = parseInt(oldOffer.PC_STEP_QUANTITY, 10) !== this.stepPcQuantity
									|| isDifferentMinPcQuantity
									|| oldOffer.MEASURE !== newOffer.MEASURE
									|| (
										this.checkQuantity
										&& parseInt(oldOffer.PC_MAX_QUANTITY, 10) > this.maxPcQuantity
										&& parseInt(this.obPcQuantity.value, 10) > this.maxPcQuantity
									);
							} else {
								var isDifferentMinSqMQuantity = oldOffer.ITEM_PRICES.length
									&& oldOffer.ITEM_PRICES[oldOffer.ITEM_PRICE_SELECTED]
									&& oldOffer.ITEM_PRICES[oldOffer.ITEM_PRICE_SELECTED].SQ_M_MIN_QUANTITY != this.minSqMQuantity;

								resetSqMQuantity = Math.round(parseFloat(oldOffer.SQ_M_STEP_QUANTITY) * this.precisionFactor) / this.precisionFactor !== this.stepSqMQuantity
									|| isDifferentMinSqMQuantity
									|| oldOffer.MEASURE !== newOffer.MEASURE
									|| (
										this.checkQuantity
										&& parseFloat(oldOffer.SQ_M_MAX_QUANTITY) > this.maxSqMQuantity
										&& parseFloat(this.obSqMQuantity.value) > this.maxSqMQuantity
									);
							}

							this.obPcQuantity.disabled = !this.canBuy;
							this.obSqMQuantity.disabled = !this.canBuy;

							if(!!newOffer.QUANTITY) {
								(price.SQ_M_PRICE ? this.obPcQuantity : this.obSqMQuantity).value = newOffer.QUANTITY;
								price.SQ_M_PRICE ? this.pcQuantityChange(true) : this.sqMQuantityChange(true);
							} else if(resetPcQuantity || resetSqMQuantity) {
								this.obPcQuantity.value = this.minPcQuantity;
								this.obSqMQuantity.value = this.minSqMQuantity;
							}
							
							if(this.obPriceMeasure)
								BX.adjust(this.obPriceMeasure, {html: '/' + BX.message('SQ_M_MESSAGE')});
							
							BX.style(this.obPcQuantity.parentNode, 'display', '');
							BX.style(this.obSqMQuantity.parentNode, 'display', '');
							BX.style(this.obQuantity.parentNode, 'display', 'none');
						} else {
							BX.style(this.obPcQuantity.parentNode, 'display', 'none');
							BX.style(this.obSqMQuantity.parentNode, 'display', 'none');
							BX.style(this.obQuantity.parentNode, 'display', '');
						}
					}
				}
				
				if(this.obQuantityLimit.all && this.obQuantityLimitNotAvl.all) {					
					if(this.canBuy) {
						if(!this.checkQuantity) {
							BX.adjust(this.obQuantityLimit.value, {html: ''});												
						} else {
							if(this.showMaxQuantity == 'M') {
								strLimit = (this.maxQuantity / this.stepQuantity >= this.relativeQuantityFactor)
									? BX.message('RELATIVE_QUANTITY_MANY')
									: BX.message('RELATIVE_QUANTITY_FEW');
							} else {
								strLimit = this.maxQuantity;
							}
							BX.adjust(this.obQuantityLimit.value, {html: strLimit});							
						}
						BX.adjust(this.obQuantityLimit.all, {style: {display: ''}});
						BX.adjust(this.obQuantityLimitNotAvl.all, {style: {display: 'none'}});
					} else {
						BX.adjust(this.obQuantityLimit.value, {html: ''});
						BX.adjust(this.obQuantityLimit.all, {style: {display: 'none'}});
						BX.adjust(this.obQuantityLimitNotAvl.all, {style: {display: ''}});
					}
				}

				if(this.usePriceRanges && this.obPriceRanges) {
					if(this.currentPriceMode === 'Q' && newOffer.PRICE_RANGES_HTML) {
						this.obPriceRangesIcon.style.display = '';
						this.obPriceRanges.style.display = '';
						if(this.obPriceRangesBody)
							this.obPriceRangesBody.innerHTML = newOffer.PRICE_RANGES_HTML;
					} else {
						this.obPriceRangesIcon.style.display = 'none';
						this.obPriceRanges.style.display = 'none';
						if(this.obPriceRangesBody)
							this.obPriceRangesBody.innerHTML = '';
					}

				}
			}
		},
		
		selectOfferProp: function(element) {
			var i = 0,
				value = '',
				strTreeValue = '',
				arTreeItem = [],
				lineContainer = null,
				rowItems = null,
				target = this.offersView == 'DROPDOWN_LIST' ? element : BX.proxy_context;

			if(target && target.hasAttribute('data-treevalue')) {
				if(BX.hasClass(target, 'selected'))
					return;

				strTreeValue = target.getAttribute('data-treevalue');
				arTreeItem = strTreeValue.split('_');
				if(this.searchOfferPropIndex(arTreeItem[0], arTreeItem[1])) {
					lineContainer = BX.findParent(target, {attribute: {'data-entity': 'sku-line-block'}});
					rowItems = lineContainer && BX.findChildren(lineContainer, {tagName: 'li'}, true);
					if(rowItems && 0 < rowItems.length) {
						for(i = 0; i < rowItems.length; i++) {
							value = rowItems[i].getAttribute('data-onevalue');
							if(value == arTreeItem[1]) {
								BX.addClass(rowItems[i], 'selected');
							} else {
								BX.removeClass(rowItems[i], 'selected');
							}
						}
					}
					
					var lineContainers = this.obTree.querySelectorAll('[data-entity="sku-line-block"]');
					if(!!lineContainers) {
						for(var k in lineContainers) {
							if(lineContainers.hasOwnProperty(k) && BX.type.isDomNode(lineContainers[k])) {
								var selectedItem = lineContainers[k].querySelector('.selected');
								if(!!selectedItem) {
									var currentOption = lineContainers[k].querySelector('[data-entity="current-option"]');
									if(!!currentOption)
										currentOption.innerHTML = selectedItem.hasAttribute('title') ? selectedItem.getAttribute('title') : selectedItem.innerHTML;
								}
							}
						}
					}
				}
			}
		},

		searchOfferPropIndex: function(strPropID, strPropValue) {
			var strName = '',
				arShowValues = false,
				i, j,
				arCanBuyValues = [],
				allValues = [],
				index = -1,
				arFilter = {},
				tmpFilter = [];

			for(i = 0; i < this.treeProps.length; i++) {
				if(this.treeProps[i].ID == strPropID) {
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
				arShowValues = this.getRowValues(arFilter, strName);
				if(!arShowValues) {
					return false;
				}
				if(!BX.util.in_array(strPropValue, arShowValues)) {
					return false;
				}
				arFilter[strName] = strPropValue;
				for(i = index+1; i < this.treeProps.length; i++) {
					strName = 'PROP_'+this.treeProps[i].ID;
					arShowValues = this.getRowValues(arFilter, strName);
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
							if(this.getCanBuy(tmpFilter))
								arCanBuyValues[arCanBuyValues.length] = arShowValues[j];
						}
					} else {
						arCanBuyValues = arShowValues;
					}
					if(this.selectedValues[strName] && BX.util.in_array(this.selectedValues[strName], arCanBuyValues)) {
						arFilter[strName] = this.selectedValues[strName];
					} else {
						if(this.showAbsent)
							arFilter[strName] = (arCanBuyValues.length > 0 ? arCanBuyValues[0] : allValues[0]);
						else
							arFilter[strName] = arCanBuyValues[0];
					}
					this.updateRow(i, arFilter[strName], arShowValues, arCanBuyValues);
				}
				this.selectedValues = arFilter;
				this.changeInfo();
			}
			return true;
		},

		updateRow: function(intNumber, activeID, showID, canBuyID) {
			var i = 0,
				value = '',
				isCurrent = false,
				rowItems = null;

			var lineContainer = this.obTree.querySelectorAll('[data-entity="sku-line-block"]');

			if(intNumber > -1 && intNumber < lineContainer.length) {
				rowItems = lineContainer[intNumber].querySelectorAll('li');
				for(i = 0; i < rowItems.length; i++) {
					value = rowItems[i].getAttribute('data-onevalue');
					isCurrent = value == activeID;

					if(isCurrent) {
						BX.addClass(rowItems[i], 'selected');
					} else {
						BX.removeClass(rowItems[i], 'selected');
					}

					if(BX.util.in_array(value, canBuyID)) {
						BX.removeClass(rowItems[i], 'notallowed');
					} else {
						BX.addClass(rowItems[i], 'notallowed');
					}

					rowItems[i].style.display = BX.util.in_array(value, showID) ? '' : 'none';

					if(isCurrent) {
						lineContainer[intNumber].style.display = (value == 0 && canBuyID.length == 1) ? 'none' : '';
					}
				}
			}
		},

		getRowValues: function(arFilter, index) {
			var i = 0,
				j,
				arValues = [],
				boolSearch = false,
				boolOneSearch = true;

			if(0 == arFilter.length) {
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
		},

		getCanBuy: function(arFilter) {
			var i, j,
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
		},

		setCurrent: function() {
			var i,
				j = 0,
				arCanBuyValues = [],
				strName = '',
				arShowValues = false,
				arFilter = {},
				tmpFilter = [],
				current = this.offers[this.offerNum].TREE;

			for(i = 0; i < this.treeProps.length; i++) {
				strName = 'PROP_'+this.treeProps[i].ID;
				arShowValues = this.getRowValues(arFilter, strName);
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
						if(this.getCanBuy(tmpFilter)) {
							arCanBuyValues[arCanBuyValues.length] = arShowValues[j];
						}
					}
				} else {
					arCanBuyValues = arShowValues;
				}
				this.updateRow(i, arFilter[strName], arShowValues, arCanBuyValues);
			}
			this.selectedValues = arFilter;
			this.changeInfo();

			var lineContainers = this.obTree.querySelectorAll('[data-entity="sku-line-block"]');
			if(!!lineContainers) {
				for(var k in lineContainers) {
					if(lineContainers.hasOwnProperty(k) && BX.type.isDomNode(lineContainers[k])) {
						var selectedItem = lineContainers[k].querySelector('.selected');
						if(!!selectedItem) {
							var currentOption = lineContainers[k].querySelector('[data-entity="current-option"]');
							if(!!currentOption)
								currentOption.innerHTML = selectedItem.hasAttribute('title') ? selectedItem.getAttribute('title') : selectedItem.innerHTML;
						}
					}
				}
			}
		},

		changeInfo: function() {
			var i, j,
				index = -1,
				boolOneSearch = true,
				quantityChanged;

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
			if(index > -1) {
				//show pict containers
				if(this.obPict) {
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

					this.obPict.style.display = '';
				}

				if(this.obArticle && this.obArticleName && this.obArticleVal) {
					if(this.offers[index].ARTICLE) {
						if(this.viewMode == 'CARD') {
							BX.style(this.obArticleName, 'display', '');
							BX.adjust(this.obArticleVal, {html: this.offers[index].ARTICLE, style: {display: ''}});
						} else {
							BX.style(this.obArticle, 'display', '');
							BX.adjust(this.obArticleVal, {html: this.offers[index].ARTICLE});
						}
					} else {
						if(this.viewMode == 'CARD') {
							BX.style(this.obArticleName, 'display', 'none');
							BX.adjust(this.obArticleVal, {html: '', style: {display: 'none'}});
						} else {
							BX.style(this.obArticle, 'display', 'none');
							BX.adjust(this.obArticleVal, {html: ''});
						}
					}
				}
				
				if(this.obSkuProps) {
					var skuProps = this.obSkuProps.querySelectorAll('[data-entity="sku-props"]');
					for(var i in skuProps) {
						if(skuProps.hasOwnProperty(i)) {
							this.obSkuProps.removeChild(skuProps[i]);
						}
					}
					if(this.offers[index].DISPLAY_PROPERTIES) {
						BX.adjust(this.obSkuProps, {
							html: this.obSkuProps.innerHTML + this.offers[index].DISPLAY_PROPERTIES
						});
					}
				}
				
				this.quantitySet(index);
				this.setPrice();				
				this.setDelayed(this.offers[index].DELAYED);
				this.setBuyedAdded(this.offers[index].BUYED_ADDED);
				this.setCompared(this.offers[index].COMPARED);

				this.offerNum = index;
			}
		},

		checkPriceRange: function(quantity) {
			if(typeof quantity == 'undefined'|| this.currentPriceMode != 'Q')
				return;

			var range, found = false;

			for(var i in this.currentQuantityRanges) {
				if(this.currentQuantityRanges.hasOwnProperty(i)) {
					range = this.currentQuantityRanges[i];

					if(parseInt(quantity) >= parseInt(range.SORT_FROM) && (range.SORT_TO == 'INF' || parseInt(quantity) <= parseInt(range.SORT_TO))) {
						found = true;
						this.currentQuantityRangeSelected = range.HASH;
						break;
					}
				}
			}

			if(!found && (range = this.getMinPriceRange())) {
				this.currentQuantityRangeSelected = range.HASH;
			}

			for(var k in this.currentPrices) {
				if(this.currentPrices.hasOwnProperty(k)) {
					if(this.currentPrices[k].QUANTITY_HASH == this.currentQuantityRangeSelected) {
						this.currentPriceSelected = k;
						break;
					}
				}
			}
		},

		getMinPriceRange: function() {
			var range;

			for(var i in this.currentQuantityRanges) {
				if(this.currentQuantityRanges.hasOwnProperty(i)) {
					if(!range || parseInt(this.currentQuantityRanges[i].SORT_FROM) < parseInt(range.SORT_FROM)) {
						range = this.currentQuantityRanges[i];
					}
				}
			}

			return range;
		},

		checkQuantityControls: function() {
			if(this.obQuantity) {
				var reachedTopLimit = this.checkQuantity && parseFloat(this.obQuantity.value) + this.stepQuantity > this.maxQuantity,
					reachedBottomLimit = parseFloat(this.obQuantity.value) - this.stepQuantity < this.minQuantity;

				if(reachedTopLimit) {
					BX.addClass(this.obQuantityUp, 'product-item-amount-btn-disabled');
				} else if(BX.hasClass(this.obQuantityUp, 'product-item-amount-btn-disabled')) {
					BX.removeClass(this.obQuantityUp, 'product-item-amount-btn-disabled');
				}

				if(reachedBottomLimit) {
					BX.addClass(this.obQuantityDown, 'product-item-amount-btn-disabled');
				} else if(BX.hasClass(this.obQuantityDown, 'product-item-amount-btn-disabled')) {
					BX.removeClass(this.obQuantityDown, 'product-item-amount-btn-disabled');
				}

				if(reachedTopLimit && reachedBottomLimit) {
					this.obQuantity.setAttribute('disabled', 'disabled');
				} else {
					this.obQuantity.removeAttribute('disabled');
				}
			}
			
			if(this.obPcQuantity && this.obSqMQuantity) {
				var reachedPcTopLimit = this.checkQuantity && parseFloat(this.obPcQuantity.value) + this.stepPcQuantity > this.maxPcQuantity,
					reachedPcBottomLimit = parseFloat(this.obPcQuantity.value) - this.stepPcQuantity < this.minPcQuantity;

				if(reachedPcTopLimit) {
					BX.addClass(this.obPcQuantityUp, 'product-item-amount-btn-disabled');
				} else if(BX.hasClass(this.obPcQuantityUp, 'product-item-amount-btn-disabled')) {
					BX.removeClass(this.obPcQuantityUp, 'product-item-amount-btn-disabled');
				}

				if(reachedPcBottomLimit) {
					BX.addClass(this.obPcQuantityDown, 'product-item-amount-btn-disabled');
				} else if(BX.hasClass(this.obPcQuantityDown, 'product-item-amount-btn-disabled')) {
					BX.removeClass(this.obPcQuantityDown, 'product-item-amount-btn-disabled');
				}

				if(reachedPcTopLimit && reachedPcBottomLimit) {
					this.obPcQuantity.setAttribute('disabled', 'disabled');
				} else {
					this.obPcQuantity.removeAttribute('disabled');
				}
			
				var reachedSqMTopLimit = this.checkQuantity && parseFloat(this.obSqMQuantity.value) + this.stepSqMQuantity > this.maxSqMQuantity,
					reachedSqMBottomLimit = parseFloat(this.obSqMQuantity.value) - this.stepSqMQuantity < this.minSqMQuantity;

				if(reachedSqMTopLimit) {
					BX.addClass(this.obSqMQuantityUp, 'product-item-amount-btn-disabled');
				} else if(BX.hasClass(this.obSqMQuantityUp, 'product-item-amount-btn-disabled')) {
					BX.removeClass(this.obSqMQuantityUp, 'product-item-amount-btn-disabled');
				}

				if(reachedSqMBottomLimit) {
					BX.addClass(this.obSqMQuantityDown, 'product-item-amount-btn-disabled');
				} else if(BX.hasClass(this.obSqMQuantityDown, 'product-item-amount-btn-disabled')) {
					BX.removeClass(this.obSqMQuantityDown, 'product-item-amount-btn-disabled');
				}

				if(reachedSqMTopLimit && reachedSqMBottomLimit) {
					this.obSqMQuantity.setAttribute('disabled', 'disabled');
				} else {
					this.obSqMQuantity.removeAttribute('disabled');
				}
			}
		},
			
		setPrice: function() {
			var economyInfo = '',
				price;

			if(this.obQuantity && !this.obPcQuantity && !this.obSqMQuantity) {
				this.checkPriceRange(this.obQuantity.value);
			} else if(this.obPcQuantity && this.obSqMQuantity) {
				if(this.currentMeasure.SYMBOL_INTL == 'pc. 1' || this.currentMeasure.SYMBOL_INTL == 'm2') {
					this.checkPriceRange(this.currentPrices[this.currentPriceSelected].SQ_M_PRICE ? this.obPcQuantity.value : this.obSqMQuantity.value);
				} else {
					this.checkPriceRange(this.obQuantity.value);
				}
			}
			
			this.checkQuantityControls();

			price = this.currentPrices[this.currentPriceSelected];
			
			if(this.obPrice) {
				if(price) {
					if(this.obPriceCurrent) {
						if(price.SQ_M_PRICE) {
							BX.adjust(this.obPriceCurrent, {
								html: BX.Currency.currencyFormat(price.SQ_M_PRICE, price.CURRENCY, true),
								style: {display: price.SQ_M_PRICE > 0 ? '' : 'none'}
							});
						} else {
							BX.adjust(this.obPriceCurrent, {
								html: BX.Currency.currencyFormat(price.PRICE, price.CURRENCY, true),
								style: {display: price.PRICE > 0 ? '' : 'none'}
							});
						}
					}
					if(this.obPriceNotSet) {
						if(price.SQ_M_PRICE)
							BX.adjust(this.obPriceNotSet, {style: {display: price.SQ_M_PRICE > 0 ? 'none' : ''}});
						else
							BX.adjust(this.obPriceNotSet, {style: {display: price.PRICE > 0 ? 'none' : ''}});
					}
				} else {
					if(this.obPriceCurrent)
						BX.adjust(this.obPriceCurrent, {html: '', style: {display: 'none'}});
					if(this.obPriceNotSet)
						BX.adjust(this.obPriceNotSet, {style: {display: 'none'}});
				}

				if(price && price.PRICE !== price.BASE_PRICE) {
					if(this.showOldPrice) {
						this.obPriceOld && BX.adjust(this.obPriceOld, {
							style: {display: ''},
							html: BX.Currency.currencyFormat(price.SQ_M_BASE_PRICE ? price.SQ_M_BASE_PRICE : price.BASE_PRICE, price.CURRENCY, true)
						});

						if(this.obPriceDiscount) {
							economyInfo = BX.message('ECONOMY_INFO_MESSAGE');
							economyInfo = economyInfo.replace('#ECONOMY#', BX.Currency.currencyFormat(price.SQ_M_DISCOUNT ? price.SQ_M_DISCOUNT : price.DISCOUNT, price.CURRENCY, true));
							BX.adjust(this.obPriceDiscount, {style: {display: ''}, html: economyInfo});
						}
					}

					if(this.showPercent) {						
						this.obDscPerc && BX.removeClass(this.obDscPerc, 'product-item-marker-container-hidden');
						this.obDscPercVal && BX.adjust(this.obDscPercVal, {html: -price.PERCENT + '%'});
					}
				} else {
					if(this.showOldPrice) {
						this.obPriceOld && BX.adjust(this.obPriceOld, {style: {display: 'none'}, html: ''});						
						this.obPriceDiscount && BX.adjust(this.obPriceDiscount, {style: {display: 'none'}, html: ''});
					}

					if(this.showPercent) {
						this.obDscPerc && BX.addClass(this.obDscPerc, 'product-item-marker-container-hidden');
						this.obDscPercVal && BX.adjust(this.obDscPercVal, {html: ''});
					}
				}
			}

			if(this.obTotalCost) {
				if(this.obQuantity && !this.obPcQuantity && !this.obSqMQuantity) {
					if(price && price.PRICE > 0) {
						if(this.obQuantity.value != 1) {
							BX.adjust(this.obTotalCost, {style: {display: ''}});
							this.obTotalCostVal && BX.adjust(this.obTotalCostVal, {html: BX.Currency.currencyFormat(price.PRICE * this.obQuantity.value, price.CURRENCY, true)});
						} else {
							BX.adjust(this.obTotalCost, {style: {display: 'none'}});
							this.obTotalCostVal && BX.adjust(this.obTotalCostVal, {html: ''});
						}
					} else {
						BX.adjust(this.obTotalCost, {style: {display: 'none'}});
						this.obTotalCostVal && BX.adjust(this.obTotalCostVal, {html: ''});
					}
				} else if(this.obPcQuantity && this.obSqMQuantity) {
					if(price && price.PRICE > 0) {
						if(this.currentMeasure.SYMBOL_INTL == 'pc. 1' || this.currentMeasure.SYMBOL_INTL == 'm2') {
							if(this.obPcQuantity.value != 1 || this.obSqMQuantity.value != 1) {
								BX.adjust(this.obTotalCost, {style: {display: ''}});
								this.obTotalCostVal && BX.adjust(this.obTotalCostVal, {html: BX.Currency.currencyFormat(price.PRICE * (price.SQ_M_PRICE ? this.obPcQuantity.value : this.obSqMQuantity.value), price.CURRENCY, true)});
							} else {
								BX.adjust(this.obTotalCost, {style: {display: 'none'}});
								this.obTotalCostVal && BX.adjust(this.obTotalCostVal, {html: ''});
							}
						} else {
							if(this.obQuantity.value != 1) {
								BX.adjust(this.obTotalCost, {style: {display: ''}});
								this.obTotalCostVal && BX.adjust(this.obTotalCostVal, {html: BX.Currency.currencyFormat(price.PRICE * this.obQuantity.value, price.CURRENCY, true)});
							} else {
								BX.adjust(this.obTotalCost, {style: {display: 'none'}});
								this.obTotalCostVal && BX.adjust(this.obTotalCostVal, {html: ''});
							}
						}
					} else {
						BX.adjust(this.obTotalCost, {style: {display: 'none'}});
						this.obTotalCostVal && BX.adjust(this.obTotalCostVal, {html: ''});
					}
				}
			}
		},

		updateCartProductQuantity: function() {
			var isBuyedAdded = BX.hasClass(this.obBuyBtn, 'btn-buy-ok'),
				productId,
				quantity;
			
			switch(this.productType) {
				case 0: //no catalog
				case 1: //product
				case 2: //set
					productId = this.product.id;
					break;
				case 3: //sku
					productId = this.offers[this.offerNum].ID;
					break;
			}

			if(this.obQuantity && !this.obPcQuantity && !this.obSqMQuantity) {
				quantity = this.obQuantity.value;
			} else if(this.obPcQuantity && this.obSqMQuantity) {
				if(this.currentMeasure.SYMBOL_INTL == 'pc. 1' || this.currentMeasure.SYMBOL_INTL == 'm2') {
					quantity = this.currentPrices[this.currentPriceSelected].SQ_M_PRICE ? this.obPcQuantity.value : this.obSqMQuantity.value;
				} else {
					quantity = this.obQuantity.value;
				}
			}
			
			if(!!isBuyedAdded) {
				BX.ajax({
					method: 'POST',				
					dataType: 'json',				
					url: this.ajaxPath,
					data: {					
						action: 'updateCartProductQuantity',
						siteId: BX.message('SITE_ID'),
						productId: productId,
						quantity: quantity
					},
					onsuccess: BX.delegate(function(result) {
						if(!result || !result.STATUS)
							return;
						
						if(result.STATUS == 'UPDATED') {
							if(this.offers.length > 0)
								this.offers[this.offerNum].QUANTITY = quantity;
							BX.onCustomEvent('OnBasketChange');
						}
					}, this)
				});
			}
		},
			
		delay: function(e) {
			BX.adjust(this.obDelay, {
				html: '<div class="product-item-delay-loader"><div><span></span></div></div>'
			});

			var isDelayed = BX.hasClass(this.obDelay, 'product-item-delayed'),
				productId,
				quantity;
			
			switch(this.productType) {
				case 0: // no catalog
				case 1: // product
				case 2: // set
					productId = this.product.id;
					break;
				case 3: // sku
					productId = this.offers[this.offerNum].ID;
					break;
			}

			if(this.showQuantity) {
				if(this.obQuantity && !this.obPcQuantity && !this.obSqMQuantity) {
					quantity = this.obQuantity.value;
				} else if(this.obPcQuantity && this.obSqMQuantity) {
					if(this.currentMeasure.SYMBOL_INTL == 'pc. 1' || this.currentMeasure.SYMBOL_INTL == 'm2') {
						quantity = this.currentPrices[this.currentPriceSelected].SQ_M_PRICE ? this.obPcQuantity.value : this.obSqMQuantity.value;
					} else {
						quantity = this.obQuantity.value;
					}
				}
			} else {						
				quantity = this.currentPrices[this.currentPriceSelected] ? this.currentPrices[this.currentPriceSelected].MIN_QUANTITY : '';
			}

			BX.ajax({
				method: 'POST',				
				dataType: 'json',				
				url: this.ajaxPath,
				data: {
					siteId: BX.message('SITE_ID'),
					action: !isDelayed ? 'ADD_TO_DELAY' : 'DELETE_FROM_DELAY',
					productId: productId,
					quantity: quantity
				},
				onsuccess: BX.proxy(this.delayResult, this)
			});

			var popupPanel = document.body.querySelector('.popup-panel');
			if(!!popupPanel)
				e.stopPropagation();
		},

		delayResult: function(arResult) {
			if(!!arResult.STATUS) {
				if(arResult.STATUS == 'ADDED') {				
					this.setDelayed(true);
					this.setBuyedAdded(false);
					if(this.offers.length > 0) {
						this.offers[this.offerNum].DELAYED = true;
						this.offers[this.offerNum].BUYED_ADDED = false;
						this.offers[this.offerNum].QUANTITY = false;
					}
				} else if(arResult.STATUS == 'DELETED') {
					this.setDelayed(false);
					if(this.offers.length > 0) {
						this.offers[this.offerNum].DELAYED = false;
					}
				}
				BX.onCustomEvent('OnBasketDelayChange');
			} else {
				var isDelayed = BX.hasClass(this.obDelay, 'product-item-delayed');
				this.setDelayed(!isDelayed ? false : true);
			}
		},
		
		setDelayed: function(state) {
			if(!this.obDelay)
				return;
			
			BX.adjust(this.obDelay, {
				props: {
					className: 'product-item-delay' + (state ? 'ed' : ''),
					title: state ? BX.message('DELAY_OK_MESSAGE') : BX.message('DELAY_MESSAGE')
				},
				html: '<i class="' + (state ? 'icon-heart-s' : 'icon-heart') + '"></i>'
			});
		},

		setDelayInfo: function(delayedIds) {
			if(!BX.type.isArray(delayedIds))
				return;

			for(var i in this.offers) {
				if(this.offers.hasOwnProperty(i)) {
					this.offers[i].DELAYED = BX.util.in_array(this.offers[i].ID, delayedIds);
				}
			}
		},

		setBuyedAdded: function(state) {
			if(!this.obBuyBtn)
				return;
			
			if(state) {
				BX.adjust(this.obBuyBtn, {
					props: {
						className: 'btn btn-buy-ok',
						title: BX.message('ADD_BASKET_OK_MESSAGE')
					},
					html: '<i class="icon-ok-b"></i>' + (this.viewMode == 'LIST' ? '<span>' + BX.message('ADD_BASKET_OK_MESSAGE') + '</span>' : '')
				});
				BX.unbindAll(this.obBuyBtn);
				BX.bind(this.obBuyBtn, "click", BX.delegate(this.basketRedirect, this));				
			} else {
				BX.adjust(this.obBuyBtn, {
					props: {
						className: 'btn btn-buy',
						title: BX.message('ADD_BASKET_MESSAGE')
					},
					html: '<i class="icon-cart"></i>' + (this.viewMode == 'LIST' ? '<span>' + BX.message('ADD_BASKET_MESSAGE') + '</span>' : '')
				});
				BX.unbindAll(this.obBuyBtn);
				BX.bind(this.obBuyBtn, "click", BX.proxy(this.basketAction == 'BUY' ? this.buyBasket : this.add2Basket, this));
			}
		},

		setBuyAddInfo: function(buyedAddedIds) {
			if(!BX.type.isArray(buyedAddedIds))
				return;

			for(var i in this.offers) {
				if(this.offers.hasOwnProperty(i)) {
					this.offers[i].BUYED_ADDED = BX.util.in_array(this.offers[i].ID, buyedAddedIds);
				}
			}
		},

		setQuantityInfo: function(quantityIds) {
			if(typeof quantityIds != 'object')
				return;
			
			for(var i in this.offers) {
				if(this.offers.hasOwnProperty(i)) {
					this.offers[i].QUANTITY = !!quantityIds[this.offers[i].ID] ? quantityIds[this.offers[i].ID] : false;
				}
			}
		},

		compare: function(event) {
			var checkbox = this.obCompare.querySelector('[data-entity="compare-checkbox"]'),
				target = BX.getEventTarget(event),
				checked = true;
			
			if(!!checkbox)
				checked = target == checkbox ? checkbox.checked : !checkbox.checked;
			
			var url = checked ? this.compareData.compareUrl : this.compareData.compareDeleteUrl,
				compareLink;
			
			if(!!url) {
				if(target !== checkbox) {
					BX.PreventDefault(event);
					this.setCompared(checked);
				}
				
				switch(this.productType) {
					case 0: // no catalog
					case 1: // product
					case 2: // set
						compareLink = url.replace('#ID#', this.product.id.toString());
						break;
					case 3: // sku
						compareLink = url.replace('#ID#', this.offers[this.offerNum].ID);
						break;
				}

				BX.ajax({
					method: 'POST',
					dataType: checked ? 'json' : 'html',
					url: compareLink + (compareLink.indexOf('?') !== -1 ? '&' : '?') + 'ajax_action=Y',
					onsuccess: checked ? BX.proxy(this.compareResult, this) : BX.proxy(this.compareDeleteResult, this)
				});
			}
		},

		compareResult: function(result) {
			if(!BX.type.isPlainObject(result))
				return;
			
			if(this.offers.length > 0)
				this.offers[this.offerNum].COMPARED = result.STATUS == 'OK';
			
			if(result.STATUS == 'OK')
				BX.onCustomEvent('OnCompareChange');
		},

		compareDeleteResult: function() {
			BX.onCustomEvent('OnCompareChange');

			if(this.offers && this.offers.length)
				this.offers[this.offerNum].COMPARED = false;
		},

		setCompared: function(state) {
			if(!this.obCompare)
				return;

			var checkbox = this.obCompare.querySelector('[data-entity="compare-checkbox"]');
			if(!!checkbox)
				checkbox.checked = state;

			var title = this.obCompare.querySelector('[data-entity="compare-title"]');
			if(!!title)
				title.innerHTML = state ? BX.message('COMPARE_OK_MESSAGE') : BX.message('COMPARE_MESSAGE');
		},

		setCompareInfo: function(comparedIds) {
			if(!BX.type.isArray(comparedIds))
				return;

			for(var i in this.offers) {
				if(this.offers.hasOwnProperty(i))
					this.offers[i].COMPARED = BX.util.in_array(this.offers[i].ID, comparedIds);
			}
		},

		checkDeletedCompare: function(id) {
			switch(this.productType) {
				case 0: // no catalog
				case 1: // product
				case 2: // set
					if(this.product.id == id)
						this.setCompared(false);
					break;
				case 3: // sku
					var i = this.offers.length;
					while(i--) {
						if(this.offers[i].ID == id) {
							this.offers[i].COMPARED = false;
							if(this.offerNum == i)
								this.setCompared(false);
							break;
						}
					}
			}
		},
			
		quickViewRequest: function(action, popupPanelContent) {
			var urls = [
				'/bitrix/components/altop/quick.order.electronext/templates/.default/style.min.css',
				BX.message('SITE_TEMPLATE_PATH') + '/components/bitrix/sale.prediction.product.detail/.default/style.min.css'
			];
			if(action == 'quickView') {
				urls.push(BX.message('SITE_TEMPLATE_PATH') + '/components/bitrix/catalog.element/article/style.min.css');
			} else {
				urls.push(BX.message('SITE_TEMPLATE_PATH') + '/components/bitrix/catalog.element/.default/style.min.css');
				urls.push(BX.message('SITE_TEMPLATE_PATH') + '/js/owlCarousel/owl.carousel.min.css');
				urls.push('/bitrix/components/altop/geo.delivery.electronext/templates/slide_panel/style.min.css');
				urls.push(BX.message('SITE_TEMPLATE_PATH') + '/components/bitrix/sale.location.selector.search/slide_panel/style.min.css');
				urls.push('/bitrix/components/altop/catalog.set.constructor.electronext/templates/.default/style.min.css');
				urls.push(BX.message('SITE_TEMPLATE_PATH') + '/components/bitrix/sale.products.gift/.default/style.min.css');
				urls.push(BX.message('SITE_TEMPLATE_PATH') + '/components/bitrix/catalog.store.amount/.default/style.min.css');
				urls.push(BX.message('SITE_TEMPLATE_PATH') + '/components/bitrix/news.list/articles/style.min.css');
				urls.push(BX.message('SITE_TEMPLATE_PATH') + '/components/bitrix/news.list/reviews/style.min.css');
				urls.push('/bitrix/components/altop/add.review.electronext/templates/slide_panel/style.min.css');
				urls.push(BX.message('SITE_TEMPLATE_PATH') + '/js/jquery.filer/jquery.filer.min.css');
				urls.push(BX.message('SITE_TEMPLATE_PATH') + '/js/fancybox/jquery.fancybox.css');
			}
			
			for(var i = 0; i < urls.length; i++) {
				var url = urls[i];
				let xhReq = new XMLHttpRequest();
				xhReq.open("GET", url);
				xhReq.onreadystatechange = function() {
					if(xhReq.readyState === XMLHttpRequest.DONE && xhReq.status === 200) {
						BX.loadCSS(xhReq.responseURL + '?' + Date.parse(xhReq.getResponseHeader('Last-Modified')));
					}
				}
				xhReq.send();
			}
			
			BX.ajax({
				url: this.ajaxPath,
				method: 'POST',
				dataType: 'json',
				timeout: 60,
				data: {
					action: action,
					siteId: BX.message('SITE_ID'), //PRO_WS//
					productId: this.product.id,
					parameters: this.quickViewData.quickViewParameters,
					prevNext: this.quickViewData.quickViewPrevNext && this.canBuy ? 'Y' : 'N',
					rcmId: this.product.rcmId ? this.product.rcmId : ''
				},
				onsuccess: BX.delegate(function(result) {
					if(!result.content || !result.JS) {
						BX.cleanNode(popupPanelContent);
						
						popupPanelContent.appendChild(BX.create('DIV', {							
							props: {
								className: 'alert alert-error'
							},
							html: BX.message('SLIDE_PANEL_UNDEFINED_ERROR')
						}));
					} else {
						BX.ajax.processScripts(
							BX.processHTML(result.JS).SCRIPT,
							false,
							BX.delegate(function() {
								var processed = BX.processHTML(result.content);
								
								popupPanelContent.innerHTML = processed.HTML;
								
								BX.ajax.processScripts(processed.SCRIPT);
							}, this)
						);
					}
					
					if(action == 'quickView') {
						this.popupPanel.appendChild(
							BX.create('DIV', {
								props: {
									className: 'popup-panel__footer'
								},
								children: [
									BX.create('A', {
										attrs: {
											'href': this.product.detailPageUrl,
											'target': '_blank'
										},
										props: {														
											className: 'btn btn-more'
										},
										html: BX.message('BTN_MESSAGE_DETAIL_ITEM')
									})
								]
							})
						);
					}

					if(this.quickViewData.quickViewPrevNext) {
						this.popupPanel.prepend(
							BX.create('DIV', {
								props: {
									className: 'popup-panel__prev' + (!result.prevProductId ? ' disabled' : '')
								},
								children: [
									BX.create('I', {
										props: {
											className: 'icon-arrow-left'
										}
									})
								],
								events: {
									click: BX.delegate(function() {
										this.changeQuickViewContent(action, popupPanelContent, result.prevProductId);
									}, this)
								}
							})
						);

						this.popupPanel.appendChild(
							BX.create('DIV', {
								props: {
									className: 'popup-panel__next' + (!result.nextProductId ? ' disabled' : '')
								},
								children: [
									BX.create('I', {
										props: {
											className: 'icon-arrow-right'
										}
									})
								],
								events: {
									click: BX.delegate(function() {
										this.changeQuickViewContent(action, popupPanelContent, result.nextProductId);
									}, this)
								}
							})
						);
					}
					
					if(!!this.popupPanelShare) {
						var shareContainer = document.body.querySelector('.navigation-share');
						if(!!shareContainer) {
							this.popupPanelShare.innerHTML = shareContainer.innerHTML;
							
							var popupPanelShareLinks = this.popupPanelShare.querySelectorAll('a');
							if(!!popupPanelShareLinks) {
								for(var i in popupPanelShareLinks) {
									if(popupPanelShareLinks.hasOwnProperty(i) && BX.type.isDomNode(popupPanelShareLinks[i])) {
										popupPanelShareLinks[i].setAttribute('href', popupPanelShareLinks[i].getAttribute('href').replace(encodeURIComponent(window.location.pathname), encodeURIComponent(this.product.detailPageUrl)));
										popupPanelShareLinks[i].setAttribute('href', popupPanelShareLinks[i].getAttribute('href').replace(encodeURIComponent(document.title), encodeURIComponent(this.product.name)));
									}
								}
							}

							var popupPanelShareIcon = this.popupPanelShare.querySelector('[data-entity="showShare"]'),
								popupPanelShareContent = this.popupPanelShare.querySelector('[data-entity="shareContent"]');
							
							if(!!popupPanelShareIcon && !!popupPanelShareContent) {
								BX.bind(popupPanelShareIcon, 'click', function() {
									if(BX.isNodeHidden(popupPanelShareContent)) {
										BX.style(popupPanelShareContent, 'display', 'flex');
										BX.addClass(popupPanelShareIcon, 'active');
									} else {
										BX.style(popupPanelShareContent, 'display', 'none');
										BX.removeClass(popupPanelShareIcon, 'active');
									}
								});
								
								BX.bind(document, 'click', function(event) {
									if(!BX.isNodeHidden(popupPanelShareContent) &&
										!BX.findParent(event.target, {attr: {'data-entity': 'showShare'}}, false) && event.target.getAttribute('data-entity') != 'showShare' &&
										!BX.findParent(event.target, {attr: {'data-entity': 'shareContent'}}, false) && event.target.getAttribute('data-entity') != 'shareContent'
									) {
										BX.style(popupPanelShareContent, 'display', 'none');
										BX.removeClass(popupPanelShareIcon, 'active');
										event.stopPropagation();
									}			
								});
							}
						}
					}
					
					if(action == 'quickViewFull') {
						if(history.scrollRestoration) {
							history.scrollRestoration = 'manual';
						}
						window.history.pushState('', document.title, this.product.detailPageUrl);
					}
						
					$(popupPanelContent).scrollbar();
				}, this)
			});
		},
			
		quickView: function(e) {
			var target = BX.proxy_context,
				action = target.hasAttribute('data-entity') ? 'quickViewFull' : 'quickView';

			this.popupPanel = BX.create('DIV', {props: {className: 'popup-panel' + (action == 'quickViewFull' ? ' popup-panel-full' : '') + ' fadeInBig'}});

			this.popupPanelShare = false;

			if(action == 'quickViewFull') {
				this.popupPanel.setAttribute('data-location-href', window.location.href);
				
				this.popupPanelShare = BX.create('SPAN', {props: {className: 'popup-panel__share'}});
			}
			
			this.popupPanel.appendChild(
				BX.create('DIV', {
					props: {
						className: 'popup-panel__title-wrap'
					},
					children: [
						BX.create('SPAN', {
							props: {
								className: 'popup-panel__title'
							},
							html: this.product.name
						}),
						this.popupPanelShare,
						BX.create('SPAN', {
							props: {
								className: 'popup-panel__close'
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

			this.popupPanel.appendChild(
				BX.create('DIV', {
					props: {
						className: 'popup-panel__content scrollbar-inner'
					},
					children: [
						BX.create('DIV', {
							props: {
								className: 'popup-panel__loader'
							},
							html: '<div><span></span></div>'
						})
					]
				})
			);
			
			var popupPanelContent = this.popupPanel.querySelector('.popup-panel__content');
			if(!!popupPanelContent)
				BX.onCustomEvent(this, 'quickViewRequest', [action, popupPanelContent]);
			
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
				
				var sectionPanel = document.querySelector('.catalog-section-panel');
				if(!!sectionPanel && BX.hasClass(sectionPanel, 'fixed'))
					BX.style(sectionPanel, 'padding-right', scrollWidth + 'px');

				var catalogCompareList = document.querySelector('.catalog-compare-list');
				if(!!catalogCompareList && BX.hasClass(catalogCompareList, 'active'))
					BX.style(catalogCompareList, 'margin-left', '-' + scrollWidth/2 + 'px');
			}

			var scrollTop = BX.GetWindowScrollPos().scrollTop;
			if(!!scrollTop && scrollTop > 0)
				BX.style(document.body, 'top', '-' + scrollTop + 'px');
			
			BX.addClass(document.body, 'popup-panel-active');
			
			document.body.appendChild(this.popupPanel);
			
			document.body.appendChild(
				BX.create('DIV', {
					props: {
						className: 'modal-backdrop popup-panel__backdrop fadeInBig'
					}
				})
			);
			
			if(action == 'quickViewFull')
				e.preventDefault();
			
			e.stopPropagation();
		},

		changeQuickViewContent: function(action, popupPanelContent, productId) {
			if(!productId)
				return;
			
			BX.cleanNode(popupPanelContent);
			if(BX.hasClass(popupPanelContent, 'scroll-content'))
				$(popupPanelContent).scrollbar('destroy');
			
			popupPanelContent.appendChild(
				BX.create('DIV', {
					props: {
						className: 'popup-panel__loader'
					},
					html: '<div><span></span></div>'
				})
			);

			var popupPanelFooter = this.popupPanel.querySelector('.popup-panel__footer');
			if(!!popupPanelFooter) {
				var popupPanelFooterBtn = popupPanelFooter.querySelector('.btn');
				if(!!popupPanelFooterBtn) {
					BX.addClass(popupPanelFooterBtn, 'disabled');
					popupPanelFooterBtn.setAttribute('href', 'javascript:void(0)');
					popupPanelFooterBtn.setAttribute('target', '_self');
				}
			}

			var popupPanelPrev = this.popupPanel.querySelector('.popup-panel__prev');
			if(!!popupPanelPrev)
				BX.unbindAll(popupPanelPrev);
			
			var popupPanelNext = this.popupPanel.querySelector('.popup-panel__next');
			if(!!popupPanelNext)
				BX.unbindAll(popupPanelNext);

			if(!!this.popupPanelShare) {
				var popupPanelShareLinks = this.popupPanelShare.querySelectorAll('a');
				if(!!popupPanelShareLinks) {
					for(var i in popupPanelShareLinks) {
						if(popupPanelShareLinks.hasOwnProperty(i) && BX.type.isDomNode(popupPanelShareLinks[i])) {
							popupPanelShareLinks[i].setAttribute('data-href', popupPanelShareLinks[i].getAttribute('href'));
							popupPanelShareLinks[i].setAttribute('href', 'javascript:void(0)');
							popupPanelShareLinks[i].setAttribute('target', '_self');
						}
					}
				}
			}
			
			BX.ajax({
				url: this.ajaxPath,
				method: 'POST',
				dataType: 'json',
				timeout: 60,
				data: {
					action: action,
					productId: productId,
					parameters: this.quickViewData.quickViewParameters,
					prevNext: 'Y',
					needProductInfo: 'Y'
				},
				onsuccess: BX.delegate(function(result) {
					if(!!result.productName) {
						var popupPanelTitle = this.popupPanel.querySelector('.popup-panel__title');
						if(!!popupPanelTitle) {
							if(!!popupPanelShareLinks) {
								for(var i in popupPanelShareLinks) {
									if(popupPanelShareLinks.hasOwnProperty(i) && BX.type.isDomNode(popupPanelShareLinks[i])) {
										popupPanelShareLinks[i].setAttribute('data-href', popupPanelShareLinks[i].getAttribute('data-href').replace(encodeURIComponent(popupPanelTitle.innerHTML), encodeURIComponent(result.productName)));
									}
								}
							}
							popupPanelTitle.innerHTML = result.productName;
						}
					}
					
					if(!result.content || !result.JS) {
						BX.cleanNode(popupPanelContent);
						
						popupPanelContent.appendChild(BX.create('DIV', {							
							props: {
								className: 'alert alert-error'
							},
							html: BX.message('SLIDE_PANEL_UNDEFINED_ERROR')
						}));
					} else {
						BX.ajax.processScripts(
							BX.processHTML(result.JS).SCRIPT,
							false,
							BX.delegate(function() {
								var processed = BX.processHTML(result.content);
								
								popupPanelContent.innerHTML = processed.HTML;
								
								BX.ajax.processScripts(processed.SCRIPT);
							}, this)
						);
					}

					if(!!result.productUrl) {
						if(!!popupPanelFooterBtn) {
							BX.removeClass(popupPanelFooterBtn, 'disabled');
							popupPanelFooterBtn.setAttribute('href', result.productUrl);
							popupPanelFooterBtn.setAttribute('target', '_blank');
						}

						if(!!popupPanelShareLinks) {
							for(var i in popupPanelShareLinks) {
								if(popupPanelShareLinks.hasOwnProperty(i) && BX.type.isDomNode(popupPanelShareLinks[i])) {
									popupPanelShareLinks[i].setAttribute('href', popupPanelShareLinks[i].getAttribute('data-href'));
									popupPanelShareLinks[i].setAttribute('href', popupPanelShareLinks[i].getAttribute('href').replace(encodeURIComponent(window.location.pathname), encodeURIComponent(result.productUrl)));
									popupPanelShareLinks[i].setAttribute('data-href', '');
									popupPanelShareLinks[i].setAttribute('target', '_blank');
								}
							}
						}
						
						if(action == 'quickViewFull')
							window.history.pushState('', document.title, result.productUrl);
					}
					
					if(!!popupPanelPrev) {
						if(result.prevProductId) {							
							BX.removeClass(popupPanelPrev, 'disabled');
							BX.bind(popupPanelPrev, 'click', BX.delegate(function() {
								this.changeQuickViewContent(action, popupPanelContent, result.prevProductId);
							}, this));
						} else {							
							BX.addClass(popupPanelPrev, 'disabled');
						}
					}
					
					if(!!popupPanelNext) {
						if(result.nextProductId) {							
							BX.removeClass(popupPanelNext, 'disabled');
							BX.bind(popupPanelNext, 'click', BX.delegate(function() {
								this.changeQuickViewContent(action, popupPanelContent, result.nextProductId);
							}, this));
						} else {							
							BX.addClass(popupPanelNext, 'disabled');
						}
					}
					
					$(popupPanelContent).scrollbar();
				}, this)
			});
		},

		sPanelFormRequest: function(action, productId, offerId, productLink, objectId, sPanelContent) {
			BX.ajax({
				url: BX.message('SITE_DIR') + 'ajax/slide_panel.php',
				method: 'POST',
				dataType: 'json',
				timeout: 60,
				data: {
					action: action
				},
				onsuccess: BX.delegate(function(result) {
					if(!result.content || !result.JS) {
						BX.cleanNode(sPanelContent);
						sPanelContent.appendChild(BX.create('DIV', {
							props: {
								className: 'slide-panel__form'
							},
							children: [
								BX.create('DIV', {							
									props: {
										className: 'alert alert-error'
									},
									html: BX.message('SLIDE_PANEL_UNDEFINED_ERROR')
								})
							]
						}));
					} else {
						BX.ajax.processScripts(
							BX.processHTML(result.JS).SCRIPT,
							false,
							BX.delegate(function() {
								var processed = BX.processHTML(result.content),
									temporaryNode = BX.create('DIV');

								temporaryNode.innerHTML = processed.HTML;
								
								var sPanelTitle = this.sPanel.querySelector('.slide-panel__title'),
									sPanelFormTitle = temporaryNode.querySelector('.slide-panel__form-title'),
									sPanelFormBtn = temporaryNode.querySelector('[type="submit"]');
								if(!!sPanelFormTitle) {
									sPanelTitle.innerHTML = sPanelFormTitle.innerHTML;
									if(!!sPanelFormBtn)
										sPanelFormBtn.innerHTML = '<span>' + sPanelFormTitle.innerHTML + '</span>';
									BX.remove(sPanelFormTitle);
								}

								var sPanelFormObjectIdInput = temporaryNode.querySelector('[name="OBJECT_ID"]');
								if(!!sPanelFormObjectIdInput && !!objectId)
									sPanelFormObjectIdInput.value = objectId;
								
								var sPanelFormProductIdInput = temporaryNode.querySelector('[name="PRODUCT_ID"]');
								if(!!sPanelFormProductIdInput && !!productId)
									sPanelFormProductIdInput.value = productId;

								var sPanelFormOfferIdInput = temporaryNode.querySelector('[name="OFFER_ID"]');
								if(!!sPanelFormOfferIdInput && !!offerId)
									sPanelFormOfferIdInput.value = offerId;

								var sPanelFormProductLinkInput = temporaryNode.querySelector('[name="PRODUCT_LINK"]');
								if(!!sPanelFormProductLinkInput && !!productLink)
									sPanelFormProductLinkInput.value = productLink;
								
								sPanelContent.innerHTML = temporaryNode.innerHTML;
								
								BX.ajax.processScripts(processed.SCRIPT);
							}, this)
						);
					}
					
					$(sPanelContent).scrollbar();
				}, this)
			});
		},

		sPanelForm: function(e) {
			if(!!this.sPanel) {
				var target = BX.proxy_context && BX.proxy_context.getAttribute('id'),
					action,
					iconClass,
					productId = this.product.id,
					offerId,
					productLink = window.location.protocol + '//' + window.location.hostname + this.product.detailPageUrl,
					objectId = this.object.id;

				if(target == this.visual.ASK_PRICE_LINK) {
					action = !!objectId ? 'ask_price_objects' : 'ask_price';
					iconClass = 'icon-comment';
				} else if(target == this.visual.NOT_AVAILABLE_LINK) {
					action = !!objectId ? 'not_available_objects' : 'not_available';
					iconClass = 'icon-clock';
				}

				if(this.productType == 3) {
					offerId = this.offers[this.offerNum].ID;
					productLink += '?OFFER_ID=' + offerId; 
				}
				
				this.sPanel.appendChild(
					BX.create('DIV', {
						props: {
							className: 'slide-panel__title-wrap'
						},
						children: [
							BX.create('I', {
								props: {
									className: iconClass
								}
							}),						
							BX.create('SPAN', {
								props: {
									className: 'slide-panel__title'
								}
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
					BX.onCustomEvent(this, 'sPanelFormRequest', [action, productId, offerId, productLink, objectId, sPanelContent]);
				
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
				
				BX.addClass(document.body, 'slide-panel-active');
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
		
		initBasketUrl: function() {
			this.basketUrl = (this.basketMode == 'ADD' ? this.basketData.add_url : this.basketData.buy_url);
			switch(this.productType) {
				case 1: // product
				case 2: // set
					this.basketUrl = this.basketUrl.replace('#ID#', this.product.id.toString());
					break;
				case 3: // sku
					this.basketUrl = this.basketUrl.replace('#ID#', this.offers[this.offerNum].ID);
					break;
			}
			this.basketParams = {
				'ajax_basket': 'Y'
			};

			if(this.showQuantity) {
				if(this.obQuantity && !this.obPcQuantity && !this.obSqMQuantity) {
					this.basketParams[this.basketData.quantity] = this.obQuantity.value;
				} else if(this.obPcQuantity && this.obSqMQuantity) {
					if(this.currentMeasure.SYMBOL_INTL == 'pc. 1' || this.currentMeasure.SYMBOL_INTL == 'm2') {
						this.basketParams[this.basketData.quantity] = this.currentPrices[this.currentPriceSelected].SQ_M_PRICE ? this.obPcQuantity.value : this.obSqMQuantity.value;
					} else {
						this.basketParams[this.basketData.quantity] = this.obQuantity.value;
					}
				}
			} else {						
				this.basketParams[this.basketData.quantity] = this.currentPrices[this.currentPriceSelected] ? this.currentPrices[this.currentPriceSelected].MIN_QUANTITY : '';
			}
			
			if(this.basketData.sku_props) {
				this.basketParams[this.basketData.sku_props_var] = this.basketData.sku_props;
			}
		},

		fillBasketProps: function() {
			if(!this.visual.BASKET_PROP_DIV)
				return;
			
			var i = 0,
				propCollection = null,
				foundValues = false,
				obBasketProps = null;
			
			obBasketProps = BX(this.visual.BASKET_PROP_DIV);
			if(obBasketProps) {
				propCollection = obBasketProps.getElementsByTagName('input');
				if(propCollection && propCollection.length) {
					for(i = 0; i < propCollection.length; i++) {
						if(!propCollection[i].disabled) {
							switch(propCollection[i].type.toLowerCase()) {
								case 'hidden':
									this.basketParams[propCollection[i].name] = propCollection[i].value;
									foundValues = true;
									break;
								case 'radio':
									if(propCollection[i].checked) {
										this.basketParams[propCollection[i].name] = propCollection[i].value;
										foundValues = true;
									}
									break;
								default:
									break;
							}
						}
					}
				}
			}
			if(!foundValues) {
				this.basketParams[this.basketData.props] = [];
				this.basketParams[this.basketData.props][0] = 0;
			}
		},

		showBasketPropsDropDownPopup: function(element, popupId) {
			var contentNode = element.querySelector('[data-entity="dropdownContent"]');

			if(!!this.obPopupWin)
				this.obPopupWin.close();

			this.obPopupWin = BX.PopupWindowManager.create('basketPropsDropDown_' + popupId + '_' + this.visual.ID, element, {
				autoHide: true,
				offsetLeft: 0,
				offsetTop: 3,
				overlay : false,
				draggable: {restrict: true},
				closeByEsc: true,
				className: 'bx-drop-down-popup-window',
				content: BX.clone(contentNode)
			});	
			
			contentNode.parentNode.appendChild(BX('basketPropsDropDown_' + popupId + '_' + this.visual.ID));
			
			this.obPopupWin.show();
		},
			
		selectBasketPropsDropDownPopupItem: function(element, valueId) {
			var wrapContainer = BX.findParent(element, {className: 'product-item-basket-props-drop-down'}, false),
				currentValue = wrapContainer.querySelector('INPUT'),
				currentOption = wrapContainer.querySelector('[data-entity="current-option"]');
			
			currentValue.value = valueId;
			currentOption.innerHTML = element.innerHTML;
			
			BX.PopupWindowManager.getCurrentPopup().close();
		},

		add2Basket: function(e) {
			this.basketMode = 'ADD';
			this.basket();

			var popupPanel = document.body.querySelector('.popup-panel');
			if(!!popupPanel)
				e.stopPropagation();
		},

		buyBasket: function(e) {
			this.basketMode = 'BUY';
			this.basket();

			var popupPanel = document.body.querySelector('.popup-panel');
			if(!!popupPanel)
				e.stopPropagation();
		},

		sendToBasket: function() {
			if(!this.canBuy)
				return;

			var productId,
				quantity;

			switch(this.productType) {
				case 0: //no catalog
				case 1: //product
				case 2: //set
					productId = this.product.id;
					break;
				case 3: //sku
					productId = this.offers[this.offerNum].ID;
					break;
			}

			if(this.showQuantity) {
				if(this.obQuantity && !this.obPcQuantity && !this.obSqMQuantity) {
					quantity = this.obQuantity.value;
				} else if(this.obPcQuantity && this.obSqMQuantity) {
					if(this.currentMeasure.SYMBOL_INTL == 'pc. 1' || this.currentMeasure.SYMBOL_INTL == 'm2') {
						quantity = this.currentPrices[this.currentPriceSelected].SQ_M_PRICE ? this.obPcQuantity.value : this.obSqMQuantity.value;
					} else {
						quantity = this.obQuantity.value;
					}
				} else {
					quantity = this.currentPrices[this.currentPriceSelected] ? this.currentPrices[this.currentPriceSelected].MIN_QUANTITY : '';
				}
			} else {						
				quantity = this.currentPrices[this.currentPriceSelected] ? this.currentPrices[this.currentPriceSelected].MIN_QUANTITY : '';
			}
			
			BX.ajax({
				method: 'POST',				
				dataType: 'json',				
				url: this.ajaxPath,
				data: {					
					action: 'ADD_TO_CART',
					siteId: BX.message('SITE_ID'),
					productId: productId,
					quantity: quantity
				},
				onsuccess: BX.delegate(function(result) {
					if(result.STATUS == 'OK') {
						this.basketResult(result);
					} else if(result.STATUS == 'ADD_TO_CART') {
						this.initBasketUrl();
						this.fillBasketProps();
						BX.ajax({
							method: 'POST',
							dataType: 'json',
							url: this.basketUrl,
							data: this.basketParams,
							onsuccess: BX.proxy(this.basketResult, this)
						});
					}
				}, this)
			});
		},

		basket: function() {			
			if(!this.canBuy)
				return;

			this.obBuyBtn.innerHTML = '<span class="btn-loader"><span><span></span></span></span>';
			
			this.sendToBasket();
		},

		basketResult: function(arResult) {
			if(arResult.STATUS == 'OK') {
				if(this.basketMode == 'BUY') {
					this.basketRedirect();
				} else {
					var strPict,
						strPictWidth,
						strPictContainer = this.obProduct.querySelector('[data-entity="image-wrapper"]');
					
					switch(this.productType) {
						case 1: // product
						case 2: // set
							strPict = this.product.pict.SRC;
							strPictWidth = this.product.pict.WIDTH;
							break;
						case 3: // sku
							strPict = this.offers[this.offerNum].PREVIEW_PICTURE
								? this.offers[this.offerNum].PREVIEW_PICTURE.SRC
								: this.defaultPict.pict.SRC;
							strPictWidth = this.offers[this.offerNum].PREVIEW_PICTURE
								? this.offers[this.offerNum].PREVIEW_PICTURE.WIDTH
								: this.defaultPict.pict.WIDTH;
							break;
					}
					
					if(!!strPict) {
						document.body.appendChild(
							BX.create('IMG', {
								props: {
									className: 'animated-image'
								},
								style: {
									width: strPictWidth + 'px',								
									position: 'absolute',
									'z-index': '1100'
								},
								attrs: {
									src: strPict
								}
							})
						);
					}

					var animatedImg = document.body.querySelector('.animated-image');
					if(!!animatedImg) {
						var topPanel = document.querySelector('.top-panel'),
							miniCart = topPanel.querySelector('.mini-cart__cart');
						
						new BX.easing({
							duration: 500,
							start: {
								width: Number(strPictWidth),
								left: BX.pos(strPictContainer).left,
								top: BX.pos(strPictContainer).top
							},
							finish: {
								width: 70,							
								left: BX.pos(miniCart).left,
								top: BX.pos(miniCart).top
							},
							transition: BX.easing.transitions.linear,
							step: BX.delegate(function(state) {
								animatedImg.style.width = state.width + 'px';							
								animatedImg.style.left = state.left + 'px';
								animatedImg.style.top = state.top + 'px';
							}, this),
							complete: BX.delegate(function() {
								BX.remove(animatedImg);
								this.setBuyedAdded(true);
								this.setDelayed(false);
								if(this.offers.length > 0) {
									this.offers[this.offerNum].BUYED_ADDED = true;
									this.offers[this.offerNum].DELAYED = false;
									if(this.showQuantity) {
										if(this.obQuantity && !this.obPcQuantity && !this.obSqMQuantity) {
											this.offers[this.offerNum].QUANTITY = this.obQuantity.value;
										} else if(this.obPcQuantity && this.obSqMQuantity) {
											if(this.currentMeasure.SYMBOL_INTL == 'pc. 1' || this.currentMeasure.SYMBOL_INTL == 'm2') {
												this.offers[this.offerNum].QUANTITY = this.currentPrices[this.currentPriceSelected].SQ_M_PRICE ? this.obPcQuantity.value : this.obSqMQuantity.value;
											} else {
												this.offers[this.offerNum].QUANTITY = this.obQuantity.value;
											}
										}
									}
								}
								BX.onCustomEvent('OnBasketChange');
								this.setAnalyticsDataLayer('addToCart');
								
								var popupPanel = document.body.querySelector('.popup-panel');
								if(!popupPanel || (!!popupPanel && !BX.hasClass(popupPanel, 'popup-panel-full'))) {
									if(window.location.pathname == '/personal/cart/') {
										setTimeout(function() {
											window.location.reload(true);
										}, 1000);
									}
								} else {
									if(popupPanel.hasAttribute('data-location-href') && 
										popupPanel.getAttribute('data-location-href').indexOf('/personal/cart/') > -1
									) {
										setTimeout(function() {
											window.location.href = popupPanel.getAttribute('data-location-href');
										}, 1000);
									}
								}
							}, this)
						}).animate();
					}
				}
			} else {
				this.setBuyedAdded(false);
			}
		},

		showOfferBasketPropsDropDownPopup: function(element, popupId) {
			var contentNode = element.querySelector('[data-entity="dropdownContent"]');

			if(!!this.obPopupWin)
				this.obPopupWin.close();

			this.obPopupWin = BX.PopupWindowManager.create('offerBasketPropsDropDown_' + popupId + '_' + this.visual.ID, element, {
				autoHide: true,
				offsetLeft: 0,
				offsetTop: 3,
				overlay : false,
				draggable: {restrict: true},
				closeByEsc: true,
				className: 'bx-drop-down-popup-window',
				content: BX.clone(contentNode)
			});	
			
			contentNode.parentNode.appendChild(BX('offerBasketPropsDropDown_' + popupId + '_' + this.visual.ID));
			
			this.obPopupWin.show();
		},

		selectOfferBasketPropsDropDownPopupItem: function(element) {
			BX.PopupWindowManager.getCurrentPopup().close();
			
			this.selectOfferProp(element);
		},

		basketRedirect: function() {
			window.location.href = (this.basketData.basketUrl ? this.basketData.basketUrl : BX.message('BASKET_URL'));
		}
	};
})(window);