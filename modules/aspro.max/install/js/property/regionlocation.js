if (typeof window.JRegionLocation === 'undefined') {
	window.JRegionLocation = function(id, property) {
		let _private = {
			inited: false,
			id: id,
		};

		let _property = JSON.stringify(property);

		Object.defineProperties(this, {
			inited: {
				get: function() {
					return _private.inited;
				},
				set: function(value) {
					if (value) {
						_private.inited = true;
					}
				}
			},
			id: {
				get: function() {
					return _private.id;
				},
			},
			property: {
                get: function() {
                    return JSON.parse(_property);
                },
            },
		});

		this.init();
	}

	window.JRegionLocation.prototype = {
		table: null,
		tbody: null,

		init: function() {
			if (!this.inited) {
				this.table = BX(this.id);
				if (this.table) {
					this.table.regionlocation = this;
					this.tbody = this.table.querySelector('tbody');

					this.debounceRequestItem = BX.debounce(this.requestItem, 500);

					this.initObserver();
					this.bindEvents();

					if (this.dropdownObserver) {
						let dropdowns = Array.prototype.slice.call(this.table.querySelectorAll('.location-results'));
						if (dropdowns.length) {
							for (i in dropdowns) {
								this.dropdownObserver.observe(dropdowns[i]);
							}
						}
					}
				}
			}
		},

		bindEvents: function() {
			if (this.table) {
				if (typeof this.handlers.onButtonDeleteClick === 'function') {
					BX.bindDelegate(this.table, 'click', {class: 'remove'}, BX.proxy(this.handlers.onButtonDeleteClick, this));
				}

				if (typeof this.handlers.onItemInputFocus === 'function') {
					BX.bindDelegate(this.table, 'click', {class: 'location-text__input'}, BX.proxy(this.handlers.onItemInputFocus, this));
				}

				if (typeof this.handlers.onItemInputInput === 'function') {
					BX.bindDelegate(this.table, 'input', {class: 'location-text__input'}, BX.proxy(this.handlers.onItemInputInput, this));
				}

				if (typeof this.handlers.onDocClick === 'function') {
					BX.bind(document, 'click', BX.proxy(this.handlers.onDocClick, this));
				}

				if (typeof this.handlers.onBtnMultiClick === 'function') {
					BX.bindDelegate(this.table, 'click', {class: 'aspro_property_regionlocation_btn--multi'}, BX.proxy(this.handlers.onBtnMultiClick, this));
				}

				// drag rows
				if (typeof Sortable === 'function') {
					if (this.tbody) {
						Sortable.create(this.tbody, {
							handle: '.drag',
							animation: 150,
							forceFallback: true,
							filter: '.no_drag',
							onStart: function(evt){
								window.getSelection().removeAllRanges();
							},
							onMove: function(evt){
								return evt.related.querySelector('.no_drag') === null && evt.related.querySelector('.aspro_property_regionlocation_item') !== null;
							},
							onUpdate: BX.proxy(function(evt) {
								try {
									var keys = [];
									var inputsNames = [];
									var rows = Array.prototype.slice.call(this.tbody.querySelectorAll('tr'));
									for (var j in rows) {
										keys.push(j * 1);

										var names = [];
										var inputs = Array.prototype.slice.call(rows[j].querySelectorAll('input'));
										for (var k in inputs) {
											names.push(inputs[k].getAttribute('name'));
										}
										inputsNames.push(names);
									}

									var k = evt.oldIndex;
									do {
										keys[k] = (k == evt.oldIndex ? evt.newIndex : (evt.newIndex > evt.oldIndex ? k - 1 : k + 1)) ;
										evt.newIndex > evt.oldIndex ? ++k : --k;
									}
									while (evt.newIndex > evt.oldIndex ? k <= evt.newIndex : k >= evt.newIndex);

									for (var j in rows) {
										if (keys[j] != j) {
											var inputs = Array.prototype.slice.call(rows[j].querySelectorAll('input'));
											for (var k in inputs) {
												inputs[k].setAttribute('name', inputsNames[keys[j]][k]);
											}
										}
									}
								}
								catch(e) {
									console.error(e);
								}
							}, this),
						});
					}
				}
			}
		},

		unbindEvents: function() {
			if (this.table) {
				if (typeof this.handlers.onButtonDeleteClick === 'function') {
					BX.unbind(this.table, 'click', BX.delegateEvent({class: 'remove'}, BX.proxy(this.handlers.onButtonDeleteClick, this)));
				}

				if (typeof this.handlers.onItemInputFocus === 'function') {
					BX.unbind(this.table, 'click', BX.delegateEvent({class: 'location-text__input'}, BX.proxy(this.handlers.onItemInputFocus, this)));
				}

				if (typeof this.handlers.onItemInputInput === 'function') {
					BX.unbind(this.table, 'input', BX.delegateEvent({class: 'location-text__input'}, BX.proxy(this.handlers.onItemInputInput, this)));
				}

				if (typeof this.handlers.onDocClick === 'function') {
					BX.unbind(document, 'click', BX.proxy(this.handlers.onDocClick, this));
				}

				if (typeof this.handlers.onBtnMultiClick === 'function') {
					BX.unbind(this.table, 'click', BX.delegateEvent({class: 'aspro_property_regionlocation_btn--multi'}, BX.proxy(this.handlers.onBtnMultiClick, this)));
				}
			}
		},

		initObserver: function() {
			if (!this.dropdownObserver) {
				let that = this;

				this.dropdownObserver = new IntersectionObserver(function(entries) {
					entries.forEach(function(entry) {
						const ratio = entry.intersectionRatio;
						const target = entry.target;
						const grid = that.table.closest('.main-grid-wrapper');
	
						if (ratio > 0) {
							BX.addClass(grid, 'no_overflow');

							if (ratio < 1) {
								BX.addClass(target, 'ontop show');
							}
							else {
								BX.addClass(target, 'show');
							}
						}
						else {
							BX.removeClass(grid, 'no_overflow');
							BX.removeClass(target, 'ontop show');
						}
					})
				}, {
					root: document.querySelector('.bx-core-adm-dialog') || null
				});
			}
		},

		isHasOneRow: function() {
			return this.table.querySelectorAll('tr .aspro_property_regionlocation_item').length === 1;
		},

		closeItemsDropdowns: function() {
			if (this.table) {
				let dropdowns = this.table.querySelectorAll('.location-results.show');
				if (dropdowns.length) {
					for (let i = 0; i < dropdowns.length; ++i) {
						BX.addClass(dropdowns[i], 'hidden');
						BX.removeClass(dropdowns[i], 'ontop show');
					}
				}
			}
		},

		getEmptyItemHtml: function() {
			let result = '';

			if (this.table) {
				if (this.property.MULTIPLE === 'Y') {	
					let clone = this.table.querySelector('.aspro_property_regionlocation_item').parentNode.cloneNode(true);
					clone.querySelector('input.location-text__hidden').setAttribute('value', '');
					clone.querySelector('input.location-text__input').setAttribute('value', '');
					clone.querySelector('.location-path').innerHTML = '';
					BX.addClass(clone.querySelector('.location-results'), 'hidden');
					BX.removeClass(clone.querySelector('.location-results'), 'show top');
					BX.cleanNode(clone.querySelector('.results__table'));
					BX.remove(clone.querySelector('.ui-ctl-icon-loader'));
					result = clone.innerHTML.trim();

					let name = result.match(new RegExp('name="([^"]+)"', ''));
					if (name !== null) {
						let bAdminList = name[1].match(new RegExp('^FIELDS\\[', ''));
						if (bAdminList) {
							result = result.replace(new RegExp('FIELDS\\[([^\\]]*)\\]\\[(PROPERTY_\\d+)\\]\\[(n?\\d+)\\]', 'ig'), 'FIELDS[$1][$2][n0]');
						}
						else {
							result = result.replace(new RegExp('PROP\\[(\\d+)\\]\\[(n?\\d+)\\]', 'ig'), 'PROP[$1][n0]');
						}
					}
				}
			}

			return result;
		},

		updateItemsNames: function() {
			if (this.table) {
				let items = Array.prototype.slice.call(this.table.querySelectorAll('.aspro_property_regionlocation_item'));
				if (items.length) {
					for (i in items) {
						let inputs = Array.prototype.slice.call(items[i].querySelectorAll('[name]'));
						if (inputs.length) {
							for (j in inputs) {
								let name = inputs[j].getAttribute('name');
								if (name !== null) {
									let bAdminList = name.match(new RegExp('^FIELDS\\[', ''));
									if (bAdminList) {
										if (name.match(new RegExp('FIELDS\\[([^\\]]*)\\]\\[(PROPERTY_\\d+)\\]\\[(n\\d+)\\]', 'i'))) {
											name = name.replace(new RegExp('FIELDS\\[([^\\]]*)\\]\\[(PROPERTY_\\d+)\\]\\[(n\\d+)\\]', 'ig'), 'FIELDS[$1][$2][n' + i + ']');
										}
									}
									else {
										if (name.match(new RegExp('PROP\\[(\\d+)\\]\\[(n\\d+)\\]', 'i'))) {
											name = name.replace(new RegExp('PROP\\[(\\d+)\\]\\[(n\\d+)\\]', 'ig'), 'PROP[$1][n' + i + ']');
										}
									}

									inputs[j].setAttribute('name', name);
								}
							}
						}
					}
				}
			}
		},

		requestItem: function(page, item) {
			let that = this;

			if (!item) {
				return;
			}

			let dropdown = item.querySelector('.location-results');
			let dropdownTable = item.querySelector('.results__table');
			if (
				!dropdown ||
				!dropdownTable
			) {
				return;
			}

			const cntPageSize = 10;

			let phrase = item.querySelector('.location-text__input').value;

			let data = {
				select: {
					CODE: 'CODE',
					TYPE_ID: 'TYPE_ID',
					VALUE: 'ID',
					DISPLAY: 'NAME.NAME',
				},
				additionals: ['PATH'],
				filter: {
					'=NAME.LANGUAGE_ID': BX.message('LANGUAGE_ID'),
					'=PHRASE': phrase,
				},
				version: '2',
				PAGE_SIZE: cntPageSize,
				PAGE: page || 0,
			};
		  
			if (phrase) {
				let loader = BX.create('div', {
					attrs: {
						className: 'ui-ctl-ext-after ui-ctl-icon-loader',
					}
				});

				BX.prepend(loader, item.querySelector('.location-text'));

				BX.ajax({
					async: true,
					data: data,
					dataType: 'JSON',
					method: 'POST',
					url: '/bitrix/components/bitrix/sale.location.selector.system/get.php',
					onsuccess: function (response) {
						if (
							response.result &&
							response.data.ITEMS.length
						) {
							if (!page) {
								BX.cleanNode(dropdownTable);
							}
					
							response.data.ITEMS.forEach(function(loc) {
								const locPath = loc.PATH.map(function(path) {
									return response.data.ETC.PATH_ITEMS[path].DISPLAY;
								}).join(', ');
							
								let dropdownItem = BX.create('div', {
									attrs: {
										className: 'adm-list-table-row location-results__item',
									},
									children: [
										BX.create('input', {
											attrs: {
												className: 'adm-designed-radio location-results__item-input',
												id: 'designed_radio_' + loc.VALUE,
												type: 'radio',
												value: loc.VALUE,
												name: 'location',
											},
										}),
										BX.create('label', {
											attrs: {
												className: 'location-results__item-cell',
												for: 'designed_radio_' + loc.VALUE,
											},
											events: {
												click: function() {
													let dropdownItem = this.closest('.location-results__item');
													item.querySelector('.location-text__hidden').value = dropdownItem.querySelector('.location-results__item-input').value;
													item.querySelector('.location-text__input').value = dropdownItem.querySelector('.location-results__item-city-name').innerText;
													item.querySelector('.location-path').innerText = dropdownItem.querySelector('.location-results__item-path').innerText;

													if (
														BX.findNextSibling(dropdownItem, {}) ||
														BX.findPreviousSibling(dropdownItem, {})
													) {
														let dropdown = dropdownItem.closest('.location-results');
														let dropdownTable = dropdownItem.closest('.results__table');
														if (dropdown) {
															BX.addClass(dropdown, 'hidden');
															BX.cleanNode(dropdownTable);
														}
													}
												
													that.closeItemsDropdowns();
												}
											},
											children: [
												BX.create('div', {
													children: [
														BX.create('span', {
															attrs: {
															className: 'location-results__item-city-name'
															},
															html: loc.DISPLAY,
														}),
													]
												}),
												BX.create('div', {
													attrs: {
														className: 'location-results__item-path',
													},
													text: locPath,
												}),
											]
										}),
									]
								});

								dropdownTable.appendChild(dropdownItem);
							});

							if (response.data.ITEMS.length == cntPageSize) {
								const ob = new IntersectionObserver(function(entries) {
									if (entries[0].isIntersecting && entries[0].intersectionRatio) {
										ob.unobserve(entries[0].target);
										that.requestItem(page + 1, item);
									}
								});
								ob.observe(dropdownTable.lastElementChild);
							}
							
							if (!page) {
								BX.removeClass(dropdown, 'hidden');
							}
						}
						else if (!page) {
							that.closeItemsDropdowns();
						}

						BX.remove(loader);
					},
					onfailture: function() {
						BX.remove(loader);
					},
				});		  
			}
			else {
				that.closeItemsDropdowns();
				item.querySelector('.location-text__hidden').value = '';
			}
		},

		handlers: {
			onButtonDeleteClick: function(event) {
				event = event || window.event;
				let target = event.target || event.srcElement;

				BX.PreventDefault(event);

				if (
					typeof target !== 'undefined' &&
					target
				) {
					let item = target.closest('.aspro_property_regionlocation_item');
					if (item) {
						let that = this;
						let row = item.closest('tr');
						if (row) {
							if (this.isHasOneRow()) {
								let inputs = Array.prototype.slice.call(row.querySelectorAll('input'));
								for (let i in inputs) {
									inputs[i].value = '';
								}

								// clean dropdown
								BX.cleanNode(row.querySelector('.results__table'));
								BX.addClass(row.querySelector('.location-results'), 'hidden');
								BX.removeClass(row.querySelector('.location-results'), 'show top');

								// clean path
								BX.cleanNode(row.querySelector('.location-path'));

								// remove loader
								BX.remove(row.querySelector('.ui-ctl-icon-loader'));
							}
							else{
								let wrapper = item.querySelector('.wrapper')
								if (wrapper) {
									BX.addClass(wrapper, 'no_drag');
								}

								BX.addClass(item, 'aspro_property_regionlocation_item--deleted');

								// wait animation 0.5s
								setTimeout(function() {
									BX.remove(row);

									if (that.isHasOneRow()) {
										BX.addClass(that.table.querySelector('tr .aspro_property_regionlocation_item'), 'aspro_property_regionlocation_item--hiddendrag');
									}
								}, 490);
							}
						}
					}
				}
			},

			onItemInputFocus: function(event) {
				event = event || window.event;
				let target = event.target || event.srcElement;

				BX.PreventDefault(event);

				if (
					typeof target !== 'undefined' &&
					target
				) {
					let item = target.closest('.aspro_property_regionlocation_item');
					if (item) {
						let dropdown = item.querySelector('.location-results');
						if (dropdown) {
							if (!dropdown.querySelector('.location-results__item')) {
								this.requestItem(0, item);
							}
							else {
								BX.removeClass(dropdown, 'hidden');
							}
						}

						this.closeItemsDropdowns();
					}
				}
			},

			onItemInputInput: function(event) {
				event = event || window.event;
				let target = event.target || event.srcElement;

				if (
					typeof target !== 'undefined' &&
					target
				) {
					let item = target.closest('.aspro_property_regionlocation_item');
					if (item) {
						this.debounceRequestItem(0, item);
					}
				}
			},

			onDocClick: function(event) {
				event = event || window.event;
				let target = event.target || event.srcElement;

				if (
					typeof target !== 'undefined' &&
					target
				) {
					if (!target.closest('.location-container')) {
						this.closeItemsDropdowns();
					}
				}
			},

			onBtnMultiClick: function(event) {
				event = event || window.event;
				let target = event.target || event.srcElement;

				if (
					typeof target !== 'undefined' &&
					target
				) {
					let that = this;
					let value = [];
					let inputs = Array.prototype.slice.call(this.table.querySelectorAll('input.location-text__hidden'));
					if (inputs.length) {
						for (i in inputs) {
							if (inputs[i].value) {
								value.push(inputs[i].value);
							}
						}
					}

					let locationUrl = '/bitrix/admin/aspro.max_regionlocation.php';
					let lang = BX.message('LANGUAGE_ID');
					let locationDialog = new BX.CAdminDialog({
						content_url: locationUrl,
						content_post: {
							value: value,
							lang: lang,
						},
						title: BX.message('MAX_REGION_LOCATION_DIALOG_TITLE_MULTI_EDIT'),
						draggable: true,
						resizable: true,
						width: 1100,
						min_width: 300,
						height: 700,
						min_height: 300,
						buttons: [
							{
								title: BX.message('MAX_REGION_LOCATION_DIALOG_SELECT'),
								id: 'multilocation_button--select',
								name: 'select',
								className: 'adm-btn-save',
								action: function(){
									let newValue = {};

									let comp = this.parentWindow.DIV.querySelector('.adm-location-popup-wrap');
									if (comp) {
										let selectedItems = Array.prototype.slice.call(comp.querySelectorAll('.adm-loc-right .bx-ui-slss-selected-locations .adm-list-table-row'));
										if (selectedItems.length) {
											for (i in selectedItems) {
												let id = selectedItems[i].querySelector('input.adm-designed-checkbox');
												id = id ? id.value : false;
												if (id) {
													let name = selectedItems[i].querySelector('.adm-list-table-link')
													name = name ? name.innerHTML.replace(/^(.*?)(&nbsp;)?<span\s.*/, '$1').trim() : '';
													if (name) {
														let parents = selectedItems[i].querySelector('.adm-list-table-loc-path');
														parents = parents ? parents.innerText : '';

														newValue[id] = {
															id, 
															name,
															parents,
														};
													}
												}
											}
										}
									}

									let lastItemTr = null;
									let emptyItemTr = null;
									let items = Array.prototype.slice.call(that.table.querySelectorAll('.aspro_property_regionlocation_item'));
									if (items.length) {
										for (i in items) {
											let bRemove = true;

											let tr = items[i].closest('tr');
											let bLast = items[i].isSameNode(items[items.length - 1]);

											let id = items[i].querySelector('input.location-text__hidden');
											id = id ? id.value : false;
											if (id) {
												if (newValue[id]) {
													bRemove = false;
													lastItemTr = tr;
												}
											}

											if (bRemove) {
												if (bLast) {
													items[i].querySelector('input.location-text__hidden').value = '';
													items[i].querySelector('input.location-text__input').value = '';
													items[i].querySelector('.location-path').innerHTML = '';
													BX.addClass(items[i].querySelector('.location-results'), 'hidden');
													BX.removeClass(items[i].querySelector('.location-results'), 'show top');
													BX.cleanNode(items[i].querySelector('.results__table'));
													BX.remove(items[i].querySelector('.ui-ctl-icon-loader'));
													emptyItemTr = tr;
												}
												else {
													BX.remove(tr);
												}
											}
										}
									}

									if (!emptyItemTr) {
										emptyItemTr = BX.create('tr', {
											html: '<td>' + that.getEmptyItemHtml() + '</td>',
										});

										emptyItemTr = lastItemTr.parentNode.insertBefore(emptyItemTr, lastItemTr.nextSibling);

										if (that.dropdownObserver) {
											that.dropdownObserver.observe(emptyItemTr.querySelector('.location-results'));
										}
									}

									if (
										newValue &&
										emptyItemTr
									) {
										let newIds = Object.keys(newValue);
										for (i in newIds) {
											let id = newIds[i];
											if (value.indexOf(id) === -1) {
												let newTr = BX.create('tr', {
													html: '<td>' + that.getEmptyItemHtml() + '</td>',
												});

												newTr.querySelector('input.location-text__hidden').value = id;
												newTr.querySelector('input.location-text__input').value = newValue[id].name;
												newTr.querySelector('.location-path').innerHTML = newValue[id].parents;
												newTr = emptyItemTr.parentNode.insertBefore(newTr, emptyItemTr)

												if (that.dropdownObserver) {
													that.dropdownObserver.observe(newTr.querySelector('.location-results'));
												}
											}
										}
									}

									that.updateItemsNames();

									this.parentWindow.Close();
								}
							},
							{
								title: BX.message('MAX_REGION_LOCATION_DIALOG_CLOSE'),
								id: 'multilocation_button--cancel',
								name: 'close',
								action: function(){
									this.parentWindow.Close();
								}
							}
						]
					});
		
					// unset popup height on show
					BX.addCustomEvent(locationDialog, 'onWindowRegister', function(){
						BX.WindowManager.Get().DIV.querySelector('.bx-core-adm-dialog-content').style.height = '';
					});
		
					// show dialog
					locationDialog.Show();
				}
			},
		},
	}

	BX.ready(function() {
		// add new row
		BX.addCustomEvent(window, 'onAddNewRowBeforeInner', function(htmlObject) {
			if (htmlObject && typeof htmlObject === 'object') {
				if (htmlObject['html'] && htmlObject['html'].length) {
					if (htmlObject['html'].indexOf('aspro_property_regionlocation_item') !== -1) {
						let row = BX.create({
							tag: 'div',
							html: htmlObject['html'],
						});

						// clean dropdown
						BX.cleanNode(row.querySelector('.results__table'));
						BX.addClass(row.querySelector('.location-results'), 'hidden');
						BX.removeClass(row.querySelector('.location-results'), 'show top');

						// clean path
						BX.cleanNode(row.querySelector('.location-path'));

						// remove loader
						BX.remove(row.querySelector('.ui-ctl-icon-loader'));

						// remove inputs value
						let inputs = Array.prototype.slice.call(row.querySelectorAll('input'));
						for (let i in inputs) {
							inputs[i].value = '';
							inputs[i].setAttribute('value', ''); // need!
						}

						htmlObject['html'] = row.innerHTML.replace('aspro_property_regionlocation_item--hiddendrag', '');

						// fix bitrix bug (input name of new row is name of last exist row, without n0/n1/n2..)
						let name = htmlObject['html'].match(new RegExp('name="([^"]+)"', ''));
						if (name !== null) {
							let bAdminList = name[1].match(new RegExp('^FIELDS\\[', ''));
							if (bAdminList) {
								let valueId = name[1].match(new RegExp('FIELDS\\[([^\\]]*)\\]\\[(PROPERTY_\\d+)\\]\\[(n\\d+)\\]', 'i'));
								if (valueId === null) {
									htmlObject['html'] = htmlObject['html'].replace(new RegExp('FIELDS\\[([^\\]]*)\\]\\[(PROPERTY_\\d+)\\]\\[([^\\]]*)\\]', 'ig'), 'FIELDS[$1][$2][n0]');
								}
							}
							else{
								let valueId = name[1].match(new RegExp('PROP\\[\\d+\\]\\[(n\\d+)\\]', 'i'));
								if (valueId === null) {
									htmlObject['html'] = htmlObject['html'].replace(new RegExp('PROP\\[(\\d+)\\]\\[([^\\]]*)\\]', 'ig'), 'PROP[$1][n0]');
								}
							}
						}
						name = htmlObject['html'].match(new RegExp('name="([^"]+)"', ''));

						BX.remove(row);

						setTimeout(function() {
							let input = document.querySelector('input[name="' + name[1] + '"]');
							if (input) {
								let table = input.closest('table');
								if (table) {
									if (typeof table.regionlocation === 'object' && table.regionlocation) {
										BX.removeClass(table.querySelector('tr .aspro_property_regionlocation_item'), 'aspro_property_regionlocation_item--hiddendrag');

										if (table.regionlocation.dropdownObserver) {
											let item = input.closest('.aspro_property_regionlocation_item');
											if (item) {
												let dropdown = item.querySelector('.location-results');
												if (dropdown) {
													table.regionlocation.dropdownObserver.observe(dropdown);
												}
											}
										}
									}
								}
							}
						}, 100);
					}
				}
			}
		});

		// edit property value from admin list
		BX.addCustomEvent(window, 'grid::thereeditedrows', function() {
			let adminRows = BX.Main.gridManager.data[0].instance.rows.getSelected();
			for (let i in adminRows) {
				let adminRow = adminRows[i].node;
				if (adminRow) {
					let items = Array.prototype.slice.call(adminRow.querySelectorAll('.aspro_property_regionlocation_item--admlistedit'));
					for (let i in items) {
						let table = items[i].closest('table');
						if (table) {
							if (typeof table.regionlocation === 'undefined') {
								new JRegionLocation(table.id);
							}
						}
					}
				}
			}
		});
	});
}
