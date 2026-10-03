(function(window) {
	'use strict';

	if(!!window.JCNewsListHeaderContacts)
		return;

	window.JCNewsListHeaderContacts = function(arParams) {		
		this.visual = {
			ID: ''
		};
		
		this.item = {
			name: '',
			previewPicture: '',
			address: '',
			map: '',
			timezone: '',
			workingHours: {},			
			phone: {},
			whatsapp: {},
			viber: {},
			telegram: {},
			instagram: {},
			email: {},
			skype: {}
		};

		this.btnAddReview = true;
		this.reviewsPageLink = '';
		this.ratingValue = '';
		this.reviewsCount = 0;
		this.reviesDeclension = '';
		
		this.parameters = {};
		
		this.btnCallback = false;
		
		this.sPanelContacts = null;		
		this.obItem = null;
		
		this.errorCode = 0;
		
		if(typeof arParams === 'object') {
			this.visual = arParams.VISUAL;
			this.item.name = arParams.ITEM.NAME;
			this.item.previewPicture = arParams.ITEM.PREVIEW_PICTURE;
			this.item.address = arParams.ITEM.ADDRESS;
			this.item.map = arParams.ITEM.MAP;
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
			this.btnAddReview = arParams.BTN_ADD_REVIEW;
			this.reviewsPageLink = arParams.REVIEWS_PAGE_LINK;
			this.ratingValue = arParams.RATING_VALUE;
			this.reviewsCount = arParams.REVIEWS_COUNT;
			this.reviesDeclension = arParams.REVIEWS_DECLENSION;
			this.parameters = arParams.PARAMETERS;
			this.btnCallback = arParams.BTN_CALLBACK;

			BX.ready(BX.delegate(this.init, this));
		}
	};

	window.JCNewsListHeaderContacts.prototype = {
		init: function() {
			this.obItem = BX(this.visual.ID);
			if(!this.obItem) {
				this.errorCode = -1;
			}

			if(this.errorCode === 0) {
				BX.bind(this.obItem, 'click', BX.proxy(this.showContacts, this));

				BX.addCustomEvent(this, 'getWorkingHoursToday', BX.proxy(this.getWorkingHoursToday, this));
				BX.addCustomEvent(this, 'workingHoursTodayReceived', BX.proxy(this.adjustContacts, this));
				BX.addCustomEvent(this, 'contactsAdjusted', BX.proxy(this.showContactsRequest, this));
				
				BX.bind(document, 'click', BX.delegate(function(e) {
					if(!!this.sPanelContacts && BX.findParent(e.target, {attrs: {id: this.visual.ID + '_contacts'}}) && BX.hasClass(e.target, 'icon-arrow-down')) {
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
					if(!!this.sPanelContacts && BX.findParent(e.target, {attrs: {id: this.visual.ID + '_contacts'}}) && BX.hasClass(e.target, 'icon-arrow-up')) {
						var workingHours = BX.findParent(e.target, {attrs: {'data-entity': 'working-hours'}});
						if(!!workingHours)
							BX.style(workingHours, 'display', 'none');
						
						var workingHoursToday = BX(this.visual.ID + '_contacts').querySelector('[data-entity="working-hours-today"]');
						if(!!workingHoursToday)
							BX.style(workingHoursToday, 'display', '');
						
						e.stopPropagation();
					}
				}, this));
				BX.bind(document, 'click', BX.delegate(function(e) {
					if(!!this.sPanelContacts && (e.target.getAttribute('data-entity') == 'callback' || BX.findParent(e.target, {attr: {'data-entity': 'callback'}}))) {
						this.callback();

						e.stopPropagation();
					}
				}, this));

				BX.addCustomEvent(this, 'callbackRequest', BX.proxy(this.callbackRequest, this));
			}
		},
			
		getWorkingHoursToday: function(sPanelContactsContent) {			
			BX.ajax({
				url: BX.message('HEADER_CONTACTS_TEMPLATE_PATH') + '/ajax.php',
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
					if(!!result.today)
						this.item.workingHoursToday = result.today;
					BX.onCustomEvent(this, 'workingHoursTodayReceived', [sPanelContactsContent]);
				}, this)
			});
		},

		adjustContacts: function(sPanelContactsContent) {
			var content = '';
			
			if(this.item.address || this.item.workingHours || this.item.workingHoursToday || this.item.phone || this.item.whatsapp || this.item.viber || this.item.telegram || this.item.instagram || this.item.email || this.item.skype || !!this.btnAddReview || this.reviewsCount > 0 || !!this.btnCallback) {
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
												content += '<span class="slide-panel__contacts-item__today-title">' + BX.message('HEADER_CONTACTS_ITEM_TODAY') + '</span>';
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
															content += BX.message('HEADER_CONTACTS_ITEM_24_HOURS');
														}
													} else {
														content += BX.message('HEADER_CONTACTS_ITEM_OFF');
													}
												content += '</span>';
												content += '<span class="slide-panel__contacts-item__hours-icon"><i class="icon-arrow-down"></i></span>';
											content += '</div>';
											if(this.item.workingHoursToday[i].WORK_START && this.item.workingHoursToday[i].WORK_END) {
												if(this.item.workingHoursToday[i].WORK_START != this.item.workingHoursToday[i].WORK_END) {
													if(this.item.workingHoursToday[i].BREAK_START && this.item.workingHoursToday[i].BREAK_END) {
														if(this.item.workingHoursToday[i].BREAK_START != this.item.workingHoursToday[i].BREAK_END) {
															content += '<div class="slide-panel__contacts-item__break">';
																content += BX.message('HEADER_CONTACTS_ITEM_BREAK') + ' ' + this.item.workingHoursToday[i].BREAK_START + ' - ' + this.item.workingHoursToday[i].BREAK_END;
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
												content += '<span class="slide-panel__contacts-item__today-title">' + (this.item.workingHoursToday && this.item.workingHoursToday.hasOwnProperty(i) ? BX.message('HEADER_CONTACTS_ITEM_TODAY') : this.item.workingHours[i].NAME) + '</span>';
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
															content += BX.message('HEADER_CONTACTS_ITEM_24_HOURS');
														}
													} else {
														content += BX.message('HEADER_CONTACTS_ITEM_OFF');
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
																content += BX.message('HEADER_CONTACTS_ITEM_BREAK') + ' ' + this.item.workingHours[i].BREAK_START + ' - ' + this.item.workingHours[i].BREAK_END;
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

					if(!!this.btnAddReview || this.reviewsCount > 0) {
						content += '<div class="slide-panel__contacts-item">';
							if(this.reviewsCount > 0) {
								content += '<a class="slide-panel__contacts-item__rating-link" href="' + this.reviewsPageLink + '">';
									content += '<span class="slide-panel__contacts-item__rating">';
										content += '<span class="slide-panel__contacts-item__rating-val"' + (this.ratingValue <= 4.4 ? ' data-rate="' + parseInt(this.ratingValue) + '"' : '') + '>' + this.ratingValue + '</span>';
										content += '<span class="slide-panel__contacts-item__rating-reviews-count">' + this.reviewsCount + ' ' + this.reviesDeclension + '</span>';
									content += '</span>';
									content += '<span class="slide-panel__contacts-item__rating-text">' + BX.message('HEADER_CONTACTS_SEE_REVIEWS') + '</span>';
								content += '</a>';
							} else {
								content += '<a class="btn btn-default" href="' + this.reviewsPageLink + '" role="button"><span>' + BX.message('HEADER_CONTACTS_ADD_REVIEW') + '</span></a>';
							}
						content += '</div>';
					}

					if(!!this.btnCallback) {
						content += '<div class="slide-panel__contacts-item">';
							content += '<button type="button" class="btn btn-primary" data-entity="callback"><i class="icon-phone"></i><span>' + BX.message('HEADER_CONTACTS_CALLBACK') + '</span></button>';
						content += '</div>';
					}
					
				content += '</div>';
			}

			BX.onCustomEvent(this, 'contactsAdjusted', [content, sPanelContactsContent]);
		},

		showContactsRequest: function(contacts, sPanelContactsContent) {
			var urls = [
				'/bitrix/components/altop/map.yandex.view.enext/templates/.default/style.min.css',
				BX.message('SITE_TEMPLATE_PATH') + '/components/bitrix/news.list/objects/style.min.css'
			];
			
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
				url: BX.message('HEADER_CONTACTS_TEMPLATE_PATH') + '/ajax.php',
				method: 'POST',
				dataType: 'json',
				timeout: 60,
				data: {
					action: 'mapObjectsCallback',
					siteCharset: BX.message('SITE_CHARSET'),
					parameters: this.parameters,
					item: {
						'NAME': this.item.name,
						'PREVIEW_PICTURE': this.item.previewPicture,
						'ADDRESS': this.item.address,
						'MAP': this.item.map
					}
				},
				onsuccess: BX.delegate(function(result) {
					if(!result.content || !result.JS) {
						sPanelContactsContent.innerHTML = contacts;
					} else {
						BX.ajax.processScripts(
							BX.processHTML(result.JS).SCRIPT,
							false,
							BX.delegate(function() {
								var processed = BX.processHTML(result.content);
								
								sPanelContactsContent.innerHTML = contacts + processed.HTML;
								
								BX.ajax.processScripts(processed.SCRIPT);
							}, this)
						);
					}
					
					$(sPanelContactsContent).scrollbar();
				}, this)
			});
		},
			
		showContacts: function(e) {
			this.sPanelContacts = BX.create('DIV', {props: {className: 'slide-panel-contacts slidePanelContactsRightIn'}});			
			this.sPanelContacts.appendChild(
				BX.create('DIV', {
					props: {
						className: 'slide-panel-contacts__title-wrap'
					},
					children: [
						BX.create('I', {
							props: {
								className: 'icon-phone-call'
							}
						}),						
						BX.create('SPAN', {
							props: {
								className: 'slide-panel-contacts__title'
							},
							html: BX.message('HEADER_CONTACTS_TITLE')
						}),
						BX.create('SPAN', {
							props: {
								className: 'slide-panel-contacts__close'
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

			this.sPanelContacts.appendChild(
				BX.create('DIV', {
					props: {
						className: 'slide-panel-contacts__content scrollbar-inner'
					},
					children: [
						BX.create('DIV', {
							props: {
								className: 'slide-panel-contacts__loader'
							},
							html: '<div><span></span></div>'
						})
					]
				})
			);

			var sPanelContactsContent = this.sPanelContacts.querySelector('.slide-panel-contacts__content');
			if(!!sPanelContactsContent)
				BX.onCustomEvent(this, 'getWorkingHoursToday', [sPanelContactsContent]);

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

			BX.addClass(document.body, 'slide-panel-contacts-active');

			document.body.appendChild(this.sPanelContacts);

			document.body.appendChild(
				BX.create('DIV', {
					props: {
						className: 'modal-backdrop slide-panel-contacts__backdrop fadeInBig'
					}
				})
			);

			e.stopPropagation();
		},

		callbackRequest: function(sPanel, sPanelContent) {
			BX.ajax({
				url: BX.message('SITE_DIR') + 'ajax/slide_panel.php',
				method: 'POST',
				dataType: 'json',
				timeout: 60,
				data: {
					action: 'callback'
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

								var sPanelTitle = sPanel.querySelector('.slide-panel__title'),
									sPanelFormTitle = temporaryNode.querySelector('.slide-panel__form-title');
								if(!!sPanelFormTitle) {
									sPanelTitle.innerHTML = sPanelFormTitle.innerHTML;
									BX.remove(sPanelFormTitle);
								}
								
								sPanelContent.innerHTML = temporaryNode.innerHTML;
								
								BX.ajax.processScripts(processed.SCRIPT);
							}, this)
						);
					}
					
					$(sPanelContent).scrollbar();
				}, this)
			});
		},
			
		callback: function() {
			var sPanel = document.body.querySelector('.slide-panel');
			if(!!sPanel) {
				sPanel.appendChild(
					BX.create('DIV', {
						props: {
							className: 'slide-panel__title-wrap'
						},
						children: [
							BX.create('I', {
								props: {
									className: 'icon-phone'
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

				sPanel.appendChild(
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
							
				var sPanelContent = sPanel.querySelector('.slide-panel__content');
				if(!!sPanelContent)
					BX.onCustomEvent(this, 'callbackRequest', [sPanel, sPanelContent]);
				
				BX.addClass(document.body, 'slide-panel-active')
				BX.addClass(sPanel, 'active');

				document.body.appendChild(
					BX.create('DIV', {
						props: {
							className: 'modal-backdrop slide-panel__backdrop fadeInBig'
						}
					})
				);
			}
		}		
	};
})(window);