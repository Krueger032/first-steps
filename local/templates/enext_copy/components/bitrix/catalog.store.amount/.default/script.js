(function(window) {
	'use strict';

	if(!window.JCCatalogStore) {		
		window.JCCatalogStore = function(arParams) {
			this.visual = {
				ID: ''
			};

			this.item = {
				timezone: '',
				workingHours: {}
			};

			this.obItem = null;

			this.errorCode = 0;

			if(typeof arParams === 'object') {
				this.visual = arParams.VISUAL;
				this.item.timezone = arParams.ITEM.TIMEZONE;
				this.item.workingHours = arParams.ITEM.WORKING_HOURS;

				BX.ready(BX.delegate(this.init, this));
			}
		};

		window.JCCatalogStore.prototype = {
			init: function() {
				this.obItem = BX(this.visual.ID);
				if(!this.obItem)
					this.errorCode = -1;
				
				if(this.errorCode === 0)
					this.showWorkingHoursToday();
			},

			showWorkingHoursToday: function() {
				var itemHours = this.obItem.querySelector('.catalog-store-amount-item-hours');
				if(!!itemHours) {
					itemHours.innerHTML = '<div class="catalog-store-amount-item-hours-loader"><div><span></span></div></div>' + BX.message('CSA_LOADING');
					BX.removeClass(itemHours, 'catalog-store-amount-item-hours-hidden');
					BX.ajax({
						url: BX.message('CSA_TEMPLATE_PATH') + '/ajax.php',
						method: 'POST',
						dataType: 'json',
						timeout: 60,
						data: {							
							action: 'workingHoursToday',
							siteCharset: BX.message('SITE_CHARSET'),
							timezone: this.item.timezone,
							workingHours: this.item.workingHours
						},
						onsuccess: BX.delegate(function(result) {
							var content = '';
							
							if(!!result.today) {
								this.item.workingHoursToday = result.today;

								for(var i in this.item.workingHoursToday) {
									if(this.item.workingHoursToday.hasOwnProperty(i)) {
										if(this.item.workingHoursToday[i].STATUS) {
											content += '<span class="catalog-store-amount-item-hours-icon catalog-store-amount-item-hours-icon-' + (this.item.workingHoursToday[i].STATUS == 'OPEN' ? 'open' : 'closed') + '"></span>';
										}
										if(this.item.workingHoursToday[i].WORK_START && this.item.workingHoursToday[i].WORK_END) {
											if(this.item.workingHoursToday[i].WORK_START != this.item.workingHoursToday[i].WORK_END) {
												content += this.item.workingHoursToday[i].WORK_START + ' - ' + this.item.workingHoursToday[i].WORK_END;
												if(this.item.workingHoursToday[i].BREAK_START && this.item.workingHoursToday[i].BREAK_END) {
													if(this.item.workingHoursToday[i].BREAK_START != this.item.workingHoursToday[i].BREAK_END) {
														content += '<span class="catalog-store-amount-item-hours-break">';
															content += BX.message('CSA_ITEM_BREAK') + ' ' + this.item.workingHoursToday[i].BREAK_START + ' - ' + this.item.workingHoursToday[i].BREAK_END;
														content += '</span>';
													}
												}
											} else {
												content += BX.message('CSA_ITEM_24_HOURS');
											}
										} else {
											content += BX.message('CSA_ITEM_OFF');
										}
									}
								}
							}
							
							itemHours.innerHTML = content;
							if(content.length == 0)
								BX.addClass(itemHours, 'catalog-store-amount-item-hours-hidden');
						}, this)
					});
				}
			}
		}
	}

	if(!window.JCCatalogStoreSKU) {
		window.JCCatalogStoreSKU = function(arParams) {
			this.config = {
				'id': arParams.ID,
				'showEmptyStore': arParams.SHOW_EMPTY_STORE,
				'useMinAmount': arParams.USE_MIN_AMOUNT,
				'minAmount': arParams.MIN_AMOUNT
			};

			this.messages = arParams.MESSAGES;
			this.sku = arParams.SKU;
			this.stores = arParams.STORES;
				
			this.obStores = {};
			for(var i in this.stores)
				this.obStores[this.stores[i]] = BX(this.config.id + "_" + this.stores[i]);
				
			BX.addCustomEvent(window, "onCatalogStoreProductChange", BX.proxy(this.offerOnChange, this));
		};

		window.JCCatalogStoreSKU.prototype = {
			offerOnChange: function(id) {
				var curSku = this.sku[id],
					icon, value,
					parent;

				for(var k in this.obStores) {
					var icon = BX.create('I', {
						props: {
							className: 'icon-' + (curSku[k] > 0 ? 'ok' : 'close') + '-b catalog-store-amount-item-quantity-icon'
						}
					});

					var value = BX.create('SPAN', {
						props: {
							className: 'catalog-store-amount-item-quantity-val'
						},
						html: curSku[k] > 0 ? BX.message('CSA_AVAILABLE') + ' ' + (!!this.config.useMinAmount ? this.getStringCount(curSku[k]) : curSku[k]) : BX.message('CSA_NOT_AVAILABLE')
					});

					BX.cleanNode(this.obStores[k]);
					BX.append(icon, this.obStores[k]);
					BX.append(value, this.obStores[k]);

					if(curSku[k] > 0)
						BX.removeClass(this.obStores[k], 'catalog-store-amount-item-quantity-not-avl');
					else
						BX.addClass(this.obStores[k], 'catalog-store-amount-item-quantity-not-avl');
					
					parent = BX.findParent(this.obStores[k], {className: 'catalog-store-amount-item'});
					if(!!this.config.showEmptyStore || curSku[k] > 0)
						BX.show(parent);
					else
						BX.hide(parent);
				}
			},

			getStringCount: function(num) {
				if(num >= this.config.minAmount)
					return this.messages['LOT_OF_GOOD'];
				else
					return this.messages['NOT_MUCH_GOOD'];
			}
		}
	}
})(window);