(function(window) {
	'use strict';

	if(!window.JCNewsListObjectsComponent) {
		window.JCNewsListObjectsComponent = function(params) {
			this.formPosting = false;
			this.siteId = params.siteId || '';
			this.prefix = params.prefix || '';
			this.template = params.template || '';			
			this.parameters = params.parameters || '';
			
			if(params.navParams) {
				this.navParams = {
					NavNum: params.navParams.NavNum || 1,
					NavPageNomer: parseInt(params.navParams.NavPageNomer) || 1,
					NavPageCount: parseInt(params.navParams.NavPageCount) || 1
				};
			}
			
			this.container = document.querySelector('[data-entity="' + this.prefix + 'container-' + this.navParams.NavNum + '"]');
			this.showMoreButton = null;
			this.showMoreButtonMessage = null;
			this.showMoreButtonContainer = null;		

			if(params.lazyLoad) {
				this.showMoreButton = document.querySelector('[data-use="' + this.prefix + 'objects-show-more-' + this.navParams.NavNum + '"]');
				this.showMoreButtonMessage = this.showMoreButton.innerHTML;
				this.showMoreButtonContainer = document.querySelector('[data-entity="' + this.prefix + 'objects-show-more-container"]');
				BX.bind(this.showMoreButton, 'click', BX.proxy(this.showMore, this));
			}
		};

		window.JCNewsListObjectsComponent.prototype = {
			checkButton: function() {
				if(this.showMoreButton) {
					if(this.navParams.NavPageNomer == this.navParams.NavPageCount) {
						BX.remove(this.showMoreButtonContainer);
					} else {
						BX.insertAfter(this.showMoreButtonContainer, this.container);
					}
				}
			},

			enableButton: function() {
				if(this.showMoreButton) {
					BX.removeClass(this.showMoreButton, 'disabled');
					this.showMoreButton.innerHTML = this.showMoreButtonMessage;
				}
			},

			disableButton: function() {
				if(this.showMoreButton) {
					BX.addClass(this.showMoreButton, 'disabled');
					this.showMoreButton.innerHTML = BX.message('OBJECTS_LOADING');
				}
			},
			
			showMore: function() {
				if(this.navParams.NavPageNomer < this.navParams.NavPageCount) {
					var data = {};
					data['action'] = 'showMoreObjects';
					data['requestUri'] = window.location.pathname;
					data['PAGEN_' + this.navParams.NavNum] = this.navParams.NavPageNomer + 1;

					if(!this.formPosting) {
						this.formPosting = true;
						this.disableButton();
						this.sendRequest(data);
					}
				}
			},

			sendRequest: function(data) {
				var defaultData = {
					siteId: this.siteId,
					template: this.template,
					parameters: this.parameters
				};
				
				BX.ajax({
					url: BX.message('OBJECTS_TEMPLATE_PATH') + '/ajax.php' + (document.location.href.indexOf('clear_cache=Y') !== -1 ? '?clear_cache=Y' : ''),
					method: 'POST',
					dataType: 'json',
					timeout: 60,
					data: BX.merge(defaultData, data),
					onsuccess: BX.delegate(function(result) {
						if(!result || !result.JS)
							return;

						BX.ajax.processScripts(
							BX.processHTML(result.JS).SCRIPT,
							false,
							BX.delegate(function() {
								this.processShowMoreAction(result);
							}, this)
						);
					}, this)
				});
			},

			processShowMoreAction: function(result) {
				this.formPosting = false;
				this.enableButton();

				if(result) {
					this.navParams.NavPageNomer++;
					this.processItems(result.items, result.imgLazyLoad, result.imgWebp);
					this.processPagination(result.pagination);
					this.checkButton();
				}
			},

			processItems: function(itemsHtml, lazyLoadImg, webpImg) {
				if(!itemsHtml)
					return;

				var processed = BX.processHTML(itemsHtml, false),
					temporaryNode = BX.create('DIV'),
					items, srcList = {};

				temporaryNode.innerHTML = processed.HTML;
				items = temporaryNode.querySelectorAll('[data-entity="item"]');

				if(items.length) {
					for(var k in items) {
						if(items.hasOwnProperty(k)) {
							if(webpImg) {
								var images = items[k].querySelectorAll('img');
								if(!!images) {
									for(var i in images) {
										if(images.hasOwnProperty(i)) {
											var imageDataLazyloadSrc = images[i].getAttribute('data-lazyload-src'),
												imageSrc = images[i].getAttribute('src');

											if(!!imageDataLazyloadSrc && imageDataLazyloadSrc.substr(0, 4) !== 'http' && imageDataLazyloadSrc.substr(0, 11) !== 'data:image/' && imageDataLazyloadSrc.indexOf('.webp') == -1) {
												srcList[imageDataLazyloadSrc] = imageDataLazyloadSrc;
											} else if(!!imageSrc && imageSrc.substr(0, 4) !== 'http' && imageSrc.substr(0, 11) !== 'data:image/' && imageSrc.indexOf('.webp') == -1) {
												srcList[imageSrc] = imageSrc;
											}
										}
									}
								}
							}

							items[k].style.opacity = 0;
							this.container.appendChild(items[k]);
						}
					}

					if(webpImg && Object.keys(srcList).length > 0)
						convertImgToWebp(srcList);

					if(lazyLoadImg)
						imgLazyLoad();
					
					new BX.easing({
						duration: 2000,
						start: {opacity: 0},
						finish: {opacity: 100},
						transition: BX.easing.makeEaseOut(BX.easing.transitions.quad),
						step: function(state) {
							for(var k in items) {
								if(items.hasOwnProperty(k)) {
									items[k].style.opacity = state.opacity / 100;
								}
							}
						},
						complete: function() {
							for(var k in items) {
								if(items.hasOwnProperty(k)) {
									items[k].removeAttribute('style');
								}
							}
						}
					}).animate();
				}

				BX.ajax.processScripts(processed.SCRIPT);
			},

			processPagination: function(paginationHtml) {
				if(!paginationHtml)
					return;

				var pagination = document.querySelector('[data-pagination-num="' + this.navParams.NavNum + '"]');
				if(!!pagination)
					pagination.innerHTML = paginationHtml;
			}
		}
	}

	if(!window.JCNewsListObjects) {
		window.JCNewsListObjects = function(arParams) {		
			this.visual = {
				ID: ''
			};
			
			this.item = {
				id: 0,
				name: '',
				address: '',
				timezone: '',
				workingHours: {},				
				phone: {},
				whatsapp: {},
				viber: {},
				telegram: {},
				instagram: {},
				email: {},
				skype: {},
				callbackForm: false,
				btnAddReview: true,
				reviewsPageLink: '',
				ratingValue: '',
				reviewsCount: 0,
				reviesDeclension: ''
			};
			
			this.sPanel = null;
			this.sPanelContent = null;
			this.obItem = null;
			
			this.errorCode = 0;
			
			if(typeof arParams === 'object') {
				this.visual = arParams.VISUAL;
				this.item.id = arParams.ITEM.ID;
				this.item.name = arParams.ITEM.NAME;
				this.item.address = arParams.ITEM.ADDRESS;
				this.item.timezone = arParams.ITEM.TIMEZONE;
				this.item.workingHours = arParams.ITEM.WORKING_HOURS;				
				this.item.phone = arParams.ITEM.PHONE.VALUE;
				this.item.phoneDescription = arParams.ITEM.PHONE.DESCRIPTION;
				this.item.whatsapp = arParams.ITEM.WHATSAPP.VALUE;
				this.item.whatsappDescription = arParams.ITEM.WHATSAPP.DESCRIPTION;
				this.item.viber = arParams.ITEM.VIBER.VALUE;
				this.item.viberDescription = arParams.ITEM.VIBER.DESCRIPTION;
				this.item.telegram = arParams.ITEM.TELEGRAM.VALUE;
				this.item.telegramDescription = arParams.ITEM.TELEGRAM.DESCRIPTION;
				this.item.instagram = arParams.ITEM.INSTAGRAM.VALUE;
				this.item.instagramDescription = arParams.ITEM.INSTAGRAM.DESCRIPTION;
				this.item.email = arParams.ITEM.EMAIL.VALUE;
				this.item.emailDescription = arParams.ITEM.EMAIL.DESCRIPTION;
				this.item.skype = arParams.ITEM.SKYPE.VALUE;
				this.item.skypeDescription = arParams.ITEM.SKYPE.DESCRIPTION;
				this.item.callbackForm = arParams.ITEM.CALLBACK_FORM;
				this.item.btnAddReview = arParams.ITEM.BTN_ADD_REVIEW;
				this.item.reviewsPageLink = arParams.ITEM.REVIEWS_PAGE_LINK;
				this.item.ratingValue = arParams.ITEM.RATING_VALUE;
				this.item.reviewsCount = arParams.ITEM.REVIEWS_COUNT;
				this.item.reviesDeclension = arParams.ITEM.REVIEWS_DECLENSION;
				
				BX.ready(BX.delegate(this.init, this));
			}
		};

		window.JCNewsListObjects.prototype = {
			init: function() {
				this.sPanel = document.body.querySelector('.slide-panel');
				
				this.obItem = BX(this.visual.ID);
				if(!this.obItem) {
					this.errorCode = -1;
				}

				if(this.errorCode === 0) {
					this.showWorkingHoursToday();

					var itemBtn = this.obItem.querySelector('.object-item-btn');
					if(!!itemBtn)
						BX.bind(itemBtn, 'click', BX.proxy(this.item.callbackForm ? this.showContactsWidthForm : this.showContacts, this));

					BX.addCustomEvent(this, 'showContactsWidthFormRequest', BX.proxy(this.showContactsWidthFormRequest, this));
					
					if(!!this.sPanel) {		
						BX.bind(document, 'click', BX.delegate(function(e) {
							if(BX.hasClass(this.sPanel, 'active') && BX.findParent(e.target, {attrs: {id: this.visual.ID + '_contacts'}}) && BX.hasClass(e.target, 'icon-arrow-down')) {
								var workingHoursToday = BX.findParent(e.target, {attrs: {'data-entity': 'working-hours-today'}});
								if(!!workingHoursToday)
									BX.style(workingHoursToday, 'display', 'none');
								
								var workingHours = BX(this.visual.ID + '_contacts').querySelector('[data-entity="working-hours"]');
								if(!!workingHours)
									BX.style(workingHours, 'display', '');
								
								e.stopPropagation();
							}
						}, this));
						BX.bind(document, 'click', BX.delegate(function(e) {
							if(BX.hasClass(this.sPanel, 'active') && BX.findParent(e.target, {attrs: {id: this.visual.ID + '_contacts'}}) && BX.hasClass(e.target, 'icon-arrow-up')) {
								var workingHours = BX.findParent(e.target, {attrs: {'data-entity': 'working-hours'}});
								if(!!workingHours)
									BX.style(workingHours, 'display', 'none');
								
								var workingHoursToday = BX(this.visual.ID + '_contacts').querySelector('[data-entity="working-hours-today"]');
								if(!!workingHoursToday)
									BX.style(workingHoursToday, 'display', '');
								
								e.stopPropagation();
							}
						}, this));
					}
				}
			},

			showWorkingHoursToday: function() {
				var itemHours = this.obItem.querySelector('.object-item-hours');
				if(!!itemHours) {
					itemHours.innerHTML = '<div class="object-item-hours-loader"><div><span></span></div></div>' + BX.message('OBJECTS_LOADING');
					BX.removeClass(itemHours, 'object-item-hours-hidden');
					BX.ajax({
						url: BX.message('OBJECTS_TEMPLATE_PATH') + '/ajax.php',
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
											content += '<span class="object-item-hours-icon object-item-hours-icon-' + (this.item.workingHoursToday[i].STATUS == 'OPEN' ? 'open' : 'closed') + '"></span>';
										}
										if(this.item.workingHoursToday[i].WORK_START && this.item.workingHoursToday[i].WORK_END) {
											if(this.item.workingHoursToday[i].WORK_START != this.item.workingHoursToday[i].WORK_END) {
												content += this.item.workingHoursToday[i].WORK_START + ' - ' + this.item.workingHoursToday[i].WORK_END;
												if(this.item.workingHoursToday[i].BREAK_START && this.item.workingHoursToday[i].BREAK_END) {
													if(this.item.workingHoursToday[i].BREAK_START != this.item.workingHoursToday[i].BREAK_END) {
														content += '<span class="object-item-hours-break">';
															content += BX.message('OBJECT_ITEM_BREAK') + ' ' + this.item.workingHoursToday[i].BREAK_START + ' - ' + this.item.workingHoursToday[i].BREAK_END;
														content += '</span>';
													}
												}
											} else {
												content += BX.message('OBJECT_ITEM_24_HOURS');
											}
										} else {
											content += BX.message('OBJECT_ITEM_OFF');
										}
									}
								}
							}
							
							itemHours.innerHTML = content;
							if(content.length == 0)
								BX.addClass(itemHours, 'object-item-hours-hidden');
						}, this)
					});
				}
			},

			adjustContacts: function() {
				var content = '';
				
				if(this.item.address || this.item.workingHours || this.item.workingHoursToday || this.item.phone || this.item.whatsapp || this.item.viber || this.item.telegram || this.item.instagram || this.item.email || this.item.skype || !!this.item.btnAddReview || this.item.reviewsCount > 0) {
					content += '<div class="slide-panel__contacts" id="' + this.visual.ID + '_contacts">';

						if(this.item.address) {
							content += '<div class="slide-panel__contacts-item">';
								content += '<div class="slide-panel__contacts-item__block">';
									content += '<div class="slide-panel__contacts-item__icon"><i class="icon-map-marker"></i></div>';
									content += '<div class="slide-panel__contacts-item__text">' + this.item.address + '</div>';
								content += '</div>';
							content += '</div>';
						}

						if(this.item.workingHoursToday) {
							for(var i in this.item.workingHoursToday) {
								if(this.item.workingHoursToday.hasOwnProperty(i)) {
									content += '<div class="slide-panel__contacts-item" data-entity="working-hours-today">';
										content += '<div class="slide-panel__contacts-item__hours-today">';
											content += '<div class="slide-panel__contacts-item__today-container">';
												content += '<div class="slide-panel__contacts-item__today">';
													content += '<span class="slide-panel__contacts-item__today-icon"><i class="icon-clock"></i></span>';
													content += '<span class="slide-panel__contacts-item__today-title">' + BX.message('OBJECT_ITEM_TODAY') + '</span>';
													if(this.item.workingHoursToday[i].STATUS) {
														content += '<span class="slide-panel__contacts-item__today-status slide-panel__contacts-item__today-status-' + (this.item.workingHoursToday[i].STATUS == 'OPEN' ? 'open' : 'closed') + '"></span>';
													}
												content += '</div>';
											content += '</div>';
											content += '<div class="slide-panel__contacts-item__hours-break">';
												content += '<div class="slide-panel__contacts-item__hours slide-panel__contacts-item__hours-first">';
													content += '<span class="slide-panel__contacts-item__hours-title">';
														if(this.item.workingHoursToday[i].WORK_START && this.item.workingHoursToday[i].WORK_END) {
															if(this.item.workingHoursToday[i].WORK_START != this.item.workingHoursToday[i].WORK_END) {
																content += this.item.workingHoursToday[i].WORK_START + ' - ' + this.item.workingHoursToday[i].WORK_END;
															} else {
																content += BX.message('OBJECT_ITEM_24_HOURS');
															}
														} else {
															content += BX.message('OBJECT_ITEM_OFF');
														}
													content += '</span>';
													content += '<span class="slide-panel__contacts-item__hours-icon"><i class="icon-arrow-down"></i></span>';
												content += '</div>';
												if(this.item.workingHoursToday[i].WORK_START && this.item.workingHoursToday[i].WORK_END) {
													if(this.item.workingHoursToday[i].WORK_START != this.item.workingHoursToday[i].WORK_END) {
														if(this.item.workingHoursToday[i].BREAK_START && this.item.workingHoursToday[i].BREAK_END) {
															if(this.item.workingHoursToday[i].BREAK_START != this.item.workingHoursToday[i].BREAK_END) {
																content += '<div class="slide-panel__contacts-item__break">';
																	content += BX.message('OBJECT_ITEM_BREAK') + ' ' + this.item.workingHoursToday[i].BREAK_START + ' - ' + this.item.workingHoursToday[i].BREAK_END;
																content += '</div>';
															}
														}
													}
												}
											content += '</div>';
										content += '</div>';
									content += '</div>';
								}
							}
						}

						if(this.item.workingHours) {
							content += '<div class="slide-panel__contacts-item" data-entity="working-hours"' + (this.item.workingHoursToday ? 'style="display: none;"' : '') + '>';								
								var key = 0;
								for(var i in this.item.workingHours) {
									if(this.item.workingHours.hasOwnProperty(i)) {										
										content += '<div class="slide-panel__contacts-item__hours-today">';
											content += '<div class="slide-panel__contacts-item__today-container">';
												content += '<div class="slide-panel__contacts-item__today">';
													if(key == 0) {
														content += '<span class="slide-panel__contacts-item__today-icon"><i class="icon-clock"></i></span>';
													}
													content += '<span class="slide-panel__contacts-item__today-title">' + (this.item.workingHoursToday && this.item.workingHoursToday.hasOwnProperty(i) ? BX.message('OBJECT_ITEM_TODAY') : this.item.workingHours[i].NAME) + '</span>';
													if(this.item.workingHoursToday && this.item.workingHoursToday.hasOwnProperty(i) && this.item.workingHoursToday[i].STATUS) {
														content += '<span class="slide-panel__contacts-item__today-status slide-panel__contacts-item__today-status-' + (this.item.workingHoursToday[i].STATUS == 'OPEN' ? 'open' : 'closed') + '"></span>';
													}
												content += '</div>';
											content += '</div>';
											content += '<div class="slide-panel__contacts-item__hours-break">';
												content += '<div class="slide-panel__contacts-item__hours' + (key == 0 ? ' slide-panel__contacts-item__hours-first' : '') + '">';
													content += '<span class="slide-panel__contacts-item__hours-title">';
														if(this.item.workingHours[i].WORK_START && this.item.workingHours[i].WORK_END) {
															if(this.item.workingHours[i].WORK_START != this.item.workingHours[i].WORK_END) {
																content += this.item.workingHours[i].WORK_START + ' - ' + this.item.workingHours[i].WORK_END;
															} else {
																content += BX.message('OBJECT_ITEM_24_HOURS');
															}
														} else {
															content += BX.message('OBJECT_ITEM_OFF');
														}
													content += '</span>';
													if(this.item.workingHoursToday && key == 0) {
														content += '<span class="slide-panel__contacts-item__hours-icon"><i class="icon-arrow-up"></i></span>';
													}
												content += '</div>';
												if(this.item.workingHours[i].WORK_START && this.item.workingHours[i].WORK_END) {
													if(this.item.workingHours[i].WORK_START != this.item.workingHours[i].WORK_END) {
														if(this.item.workingHours[i].BREAK_START && this.item.workingHours[i].BREAK_END) {
															if(this.item.workingHours[i].BREAK_START != this.item.workingHours[i].BREAK_END) {
																content += '<div class="slide-panel__contacts-item__break">';
																	content += BX.message('OBJECT_ITEM_BREAK') + ' ' + this.item.workingHours[i].BREAK_START + ' - ' + this.item.workingHours[i].BREAK_END;
																content += '</div>';
															}
														}
													}
												}
											content += '</div>';
										content += '</div>';
										key++;
									}
								}
							content += '</div>';
						}
						
						if(this.item.phone) {
							for(var i in this.item.phone) {
								if(this.item.phone.hasOwnProperty(i)) {
									content += '<div class="slide-panel__contacts-item">';
										content += '<div class="slide-panel__contacts-item__block">';
											content += '<div class="slide-panel__contacts-item__icon"><i class="icon-phone"></i></div>';
											content += '<div class="slide-panel__contacts-item__text">';
												content += '<a class="slide-panel__contacts-item__phone slide-panel__contacts-item__link" href="tel:' + this.item.phone[i].replace(/[^\d\+]/g,'') + '">' + this.item.phone[i] + '</a>';
												if(this.item.phoneDescription.hasOwnProperty(i) && this.item.phoneDescription[i].length > 0) {
													content += '<span class="slide-panel__contacts-item__descr">' + this.item.phoneDescription[i] + '</span>';
												}
											content += '</div>';
										content += '</div>';
									content += '</div>';
								}
							}
						}

						if(this.item.whatsapp) {
							for(var i in this.item.whatsapp) {
								if(this.item.whatsapp.hasOwnProperty(i)) {
									content += '<div class="slide-panel__contacts-item">';
										content += '<div class="slide-panel__contacts-item__block">';
											content += '<div class="slide-panel__contacts-item__icon"><i class="fa fa-whatsapp"></i></div>';
											content += '<div class="slide-panel__contacts-item__text">';
												content += '<a target="_blank" class="slide-panel__contacts-item__whatsapp slide-panel__contacts-item__link" href="https://wa.me/' + this.item.whatsapp[i].replace(/[^\d]/g,'') + '">' + this.item.whatsapp[i] + '</a>';
												if(this.item.whatsappDescription.hasOwnProperty(i) && this.item.whatsappDescription[i].length > 0) {
													content += '<span class="slide-panel__contacts-item__descr">' + this.item.whatsappDescription[i] + '</span>';
												}
											content += '</div>';
										content += '</div>';
									content += '</div>';
								}
							}
						}

						if(this.item.viber) {
							var isMobile = BX.hasClass(document.documentElement, 'bx-touch');
							for(var i in this.item.viber) {
								if(this.item.viber.hasOwnProperty(i)) {
									content += '<div class="slide-panel__contacts-item">';
										content += '<div class="slide-panel__contacts-item__block">';
											content += '<div class="slide-panel__contacts-item__icon"><i class="fa fa-phone"></i></div>';
											content += '<div class="slide-panel__contacts-item__text">';
												content += '<a class="slide-panel__contacts-item__viber slide-panel__contacts-item__link" href="viber://' + (!isMobile ? 'chat' : 'add') + '?number=' + (!isMobile ? this.item.viber[i].replace(/[^\d\+]/g,'') : this.item.viber[i].replace(/[^\d]/g,'')) + '">' + this.item.viber[i] + '</a>';
												if(this.item.viberDescription.hasOwnProperty(i) && this.item.viberDescription[i].length > 0) {
													content += '<span class="slide-panel__contacts-item__descr">' + this.item.viberDescription[i] + '</span>';
												}
											content += '</div>';
										content += '</div>';
									content += '</div>';
								}
							}
						}

						if(this.item.telegram) {
							for(var i in this.item.telegram) {
								if(this.item.telegram.hasOwnProperty(i)) {
									content += '<div class="slide-panel__contacts-item">';
										content += '<div class="slide-panel__contacts-item__block">';
											content += '<div class="slide-panel__contacts-item__icon"><i class="fa fa-telegram"></i></div>';
											content += '<div class="slide-panel__contacts-item__text">';
												content += '<a target="_blank" class="slide-panel__contacts-item__link" href="https://t.me/' + this.item.telegram[i] + '">' + this.item.telegram[i] + '</a>';
												if(this.item.telegramDescription.hasOwnProperty(i) && this.item.telegramDescription[i].length > 0) {
													content += '<span class="slide-panel__contacts-item__descr">' + this.item.telegramDescription[i] + '</span>';
												}
											content += '</div>';
										content += '</div>';
									content += '</div>';
								}
							}
						}

						if(this.item.instagram) {
							for(var i in this.item.instagram) {
								if(this.item.instagram.hasOwnProperty(i)) {
									content += '<div class="slide-panel__contacts-item">';
										content += '<div class="slide-panel__contacts-item__block">';
											content += '<div class="slide-panel__contacts-item__icon"><i class="fa fa-instagram"></i></div>';
											content += '<div class="slide-panel__contacts-item__text">';
												content += '<a target="_blank" class="slide-panel__contacts-item__link" href="https://www.instagram.com/' + this.item.instagram[i] + '">' + this.item.instagram[i] + '</a>';
												if(this.item.instagramDescription.hasOwnProperty(i) && this.item.instagramDescription[i].length > 0) {
													content += '<span class="slide-panel__contacts-item__descr">' + this.item.instagramDescription[i] + '</span>';
												}
											content += '</div>';
										content += '</div>';
									content += '</div>';
								}
							}
						}

						if(this.item.email) {
							for(var i in this.item.email) {
								if(this.item.email.hasOwnProperty(i)) {
									content += '<div class="slide-panel__contacts-item">';
										content += '<div class="slide-panel__contacts-item__block">';
											content += '<div class="slide-panel__contacts-item__icon"><i class="icon-mail"></i></div>';
											content += '<div class="slide-panel__contacts-item__text">';
												content += '<a class="slide-panel__contacts-item__link" href="mailto:' + this.item.email[i] + '">' + this.item.email[i] + '</a>';
												if(this.item.emailDescription.hasOwnProperty(i) && this.item.emailDescription[i].length > 0) {
													content += '<span class="slide-panel__contacts-item__descr">' + this.item.emailDescription[i] + '</span>';
												}
											content += '</div>';
										content += '</div>';
									content += '</div>';
								}
							}
						}

						if(this.item.skype) {
							for(var i in this.item.skype) {
								if(this.item.skype.hasOwnProperty(i)) {
									content += '<div class="slide-panel__contacts-item">';
										content += '<div class="slide-panel__contacts-item__block">';
											content += '<div class="slide-panel__contacts-item__icon"><i class="fa fa-skype"></i></div>';
											content += '<div class="slide-panel__contacts-item__text">';
												content += '<a class="slide-panel__contacts-item__link" href="skype:' + this.item.skype[i] + '?chat">' + this.item.skype[i] + '</a>';
												if(this.item.skypeDescription.hasOwnProperty(i) && this.item.skypeDescription[i].length > 0) {
													content += '<span class="slide-panel__contacts-item__descr">' + this.item.skypeDescription[i] + '</span>';
												}
											content += '</div>';
										content += '</div>';
									content += '</div>';
								}
							}
						}

						if(!!this.item.btnAddReview || this.item.reviewsCount > 0) {
							content += '<div class="slide-panel__contacts-item">';
								if(this.item.reviewsCount > 0) {
									content += '<a class="slide-panel__contacts-item__rating-link" href="' + this.item.reviewsPageLink + '">';
										content += '<span class="slide-panel__contacts-item__rating">';
											content += '<span class="slide-panel__contacts-item__rating-val"' + (this.item.ratingValue <= 4.4 ? ' data-rate="' + parseInt(this.item.ratingValue) + '"' : '') + '>' + this.item.ratingValue + '</span>';
											content += '<span class="slide-panel__contacts-item__rating-reviews-count">' + this.item.reviewsCount + ' ' + this.item.reviesDeclension + '</span>';
										content += '</span>';
										content += '<span class="slide-panel__contacts-item__rating-text">' + BX.message('OBJECT_ITEM_SEE_REVIEWS') + '</span>';
									content += '</a>';
								} else {
									content += '<a class="btn btn-default" href="' + this.item.reviewsPageLink + '" role="button"><span>' + BX.message('OBJECT_ITEM_ADD_REVIEW') + '</span></a>';
								}
							content += '</div>';
						}
					
					content += '</div>';
				}

				this.sPanelContent = content;
			},
			
			showContactsWidthFormRequest: function(sPanelContent) {
				this.adjustContacts();

				BX.ajax({
					url: BX.message('SITE_DIR') + 'ajax/slide_panel.php',
					method: 'POST',
					dataType: 'json',
					timeout: 60,
					data: {
						action: 'callback_objects'
					},
					onsuccess: BX.delegate(function(result) {
						if(!result.content || !result.JS) {
							sPanelContent.innerHTML = this.sPanelContent;
						} else {
							BX.ajax.processScripts(
								BX.processHTML(result.JS).SCRIPT,
								false,
								BX.delegate(function() {
									var processed = BX.processHTML(result.content),
										temporaryNode = BX.create('DIV');

									temporaryNode.innerHTML = processed.HTML;

									var sPanelFormObjectIdInput = temporaryNode.querySelector('[name="OBJECT_ID"]');
									if(!!sPanelFormObjectIdInput)
										sPanelFormObjectIdInput.value = this.item.id;
									
									sPanelContent.innerHTML = this.sPanelContent + temporaryNode.innerHTML;
									
									BX.ajax.processScripts(processed.SCRIPT);
								}, this)
							);
						}
						
						$(sPanelContent).scrollbar();
					}, this)
				});
			},
			
			showContactsWidthForm: function(e) {
				if(!!this.sPanel) {
					this.sPanel.appendChild(
						BX.create('DIV', {
							props: {
								className: 'slide-panel__title-wrap'
							},
							children: [
								BX.create('I', {
									props: {
										className: 'icon-phone-call'
									}
								}),						
								BX.create('SPAN', {
									props: {
										className: 'slide-panel__title'
									},
									html: this.item.name
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
						BX.onCustomEvent(this, 'showContactsWidthFormRequest', [sPanelContent]);

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

						var tabsPanel = document.querySelector('[data-entity="tabs"]');
						if(!!tabsPanel && BX.hasClass(tabsPanel, 'fixed'))
							BX.style(tabsPanel, 'padding-right', scrollWidth + 'px');

						var objectsMap = document.querySelector('.objects-map');
						if(!!objectsMap)
							BX.style(objectsMap, 'padding-right', scrollWidth + 'px');

						var catalogCompareList = document.querySelector('.catalog-compare-list');
						if(!!catalogCompareList && BX.hasClass(catalogCompareList, 'active'))
							BX.style(catalogCompareList, 'margin-left', '-' + scrollWidth/2 + 'px');
					}

					var scrollTop = BX.GetWindowScrollPos().scrollTop;
					if(!!scrollTop && scrollTop > 0)
						BX.style(document.body, 'top', '-' + scrollTop + 'px');

					BX.addClass(document.body, 'slide-panel-active')
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
			
			showContacts: function(e) {
				if(!!this.sPanel) {
					this.adjustContacts();

					this.sPanel.appendChild(
						BX.create('DIV', {
							props: {
								className: 'slide-panel__title-wrap'
							},
							children: [
								BX.create('I', {
									props: {
										className: 'icon-phone-call'
									}
								}),						
								BX.create('SPAN', {
									props: {
										className: 'slide-panel__title'
									},
									html: this.item.name
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
							html: this.sPanelContent
						})
					);

					var sPanelContent = this.sPanel.querySelector('.slide-panel__content');
					if(!!sPanelContent)
						$(sPanelContent).scrollbar();

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

						var tabsBlock = document.querySelector('.objects-detail-tabs-block');
						if(!!tabsBlock && BX.hasClass(tabsBlock, 'fixed'))
							BX.style(tabsBlock, 'padding-right', scrollWidth + 'px');

						var objectsMap = document.querySelector('.objects-map');
						if(!!objectsMap)
							BX.style(objectsMap, 'padding-right', scrollWidth + 'px');

						var catalogCompareList = document.querySelector('.catalog-compare-list');
						if(!!catalogCompareList && BX.hasClass(catalogCompareList, 'active'))
							BX.style(catalogCompareList, 'margin-left', '-' + scrollWidth/2 + 'px');
					}

					var scrollTop = BX.GetWindowScrollPos().scrollTop;
					if(!!scrollTop && scrollTop > 0)
						BX.style(document.body, 'top', '-' + scrollTop + 'px');
					
					BX.addClass(document.body, 'slide-panel-active')
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
			}
		}
	}
})(window);