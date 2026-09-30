(function() {
	'use strict';

	if(!!window.JCCatalogEmptyParent)
		return;

	window.JCCatalogEmptyParent = function(params) {
		this.ajaxUrl = params.ajaxUrl || '';
		this.siteId = params.siteId || '';
		this.signed = params.signed || '';
		this.container = document.querySelector('[data-entity="empty-parent-products"]');
		this.linksContainer = document.querySelector('[data-entity="empty-parent-links"]');
		this.busy = false;

		if(!!this.container && !!this.linksContainer)
			BX.ready(BX.delegate(this.bind, this));
	};

	window.JCCatalogEmptyParent.prototype = {
		bind: function() {
			this.emptyParentLinks = this.linksContainer.querySelectorAll('.catalog-empty-parent-link[data-section-id]');
			this.emptyParentLinksHeight = 92;
			this.emptyParentLinkBtnAdjusted = false;

			var i;
			for(i = 0; i < this.emptyParentLinks.length; i++)
				BX.bind(this.emptyParentLinks[i], 'click', BX.proxy(this.change, this));

			this.emptyParentLinkBtnSpan = BX.create('SPAN');
			this.emptyParentLinkBtnIcon = BX.create('I', {
				props: {
					className: 'icon-arrow-down'
				}
			});
			this.emptyParentLinkBtn = BX.create('DIV', {
				props: {
					className: 'catalog-empty-parent-link-btn-container'
				},
				children: [
					BX.create('DIV', {
						props: {
							className: 'catalog-empty-parent-link catalog-empty-parent-link-btn'
						},
						children: [
							this.emptyParentLinkBtnSpan,
							this.emptyParentLinkBtnIcon
						],
						events: {
							click: BX.proxy(this.showHideEmptyParentLinks, this)
						}
					})
				]
			});

			this.adjustEmptyParentLinks();
			BX.bind(window, 'resize', BX.proxy(this.adjustEmptyParentLinks, this));
			BX.addCustomEvent(window, 'slideMenu', BX.proxy(this.adjustEmptyParentLinks, this));
		},

		adjustEmptyParentLinks: function() {
			if(!this.emptyParentLinks || !this.emptyParentLinks.length)
				return;

			var last = this.emptyParentLinks[this.emptyParentLinks.length - 1];
			if(BX.pos(last, true).bottom > this.emptyParentLinksHeight) {
				if(!this.emptyParentLinkBtnAdjusted) {
					this.emptyParentLinkBtnAdjusted = true;
					if(!BX.hasClass(this.linksContainer, 'active'))
						this.emptyParentLinkBtnSpan.innerHTML = window.innerWidth < 1043 ? BX.message('EMPTY_SECTION_LINKS_ALL') : BX.message('EMPTY_SECTION_LINKS_SHOW_ALL');
					this.linksContainer.appendChild(this.emptyParentLinkBtn);
				} else if(!BX.hasClass(this.linksContainer, 'active')) {
					this.emptyParentLinkBtnSpan.innerHTML = window.innerWidth < 1043 ? BX.message('EMPTY_SECTION_LINKS_ALL') : BX.message('EMPTY_SECTION_LINKS_SHOW_ALL');
				}
			} else if(this.emptyParentLinkBtnAdjusted && BX.pos(last, true).bottom <= this.emptyParentLinksHeight) {
				this.emptyParentLinkBtnAdjusted = false;
				this.linksContainer.removeChild(this.emptyParentLinkBtn);
			}
		},

		showHideEmptyParentLinks: function() {
			if(!BX.hasClass(this.linksContainer, 'active')) {
				BX.addClass(this.linksContainer, 'active');
				this.emptyParentLinkBtnSpan.innerHTML = BX.message('EMPTY_SECTION_LINKS_HIDE');
				this.emptyParentLinkBtnIcon.className = 'icon-arrow-up';
			} else {
				BX.removeClass(this.linksContainer, 'active');
				this.emptyParentLinkBtnSpan.innerHTML = window.innerWidth < 1043 ? BX.message('EMPTY_SECTION_LINKS_ALL') : BX.message('EMPTY_SECTION_LINKS_SHOW_ALL');
				this.emptyParentLinkBtnIcon.className = 'icon-arrow-down';
			}
		},

		change: function(event) {
			BX.PreventDefault(event);

			var link = BX.proxy_context || BX.getEventTarget(event),
				sectionId = link && link.getAttribute('data-section-id'),
				i;

			if(!link || sectionId === null || sectionId === '' || BX.hasClass(link, 'active') || this.busy)
				return;

			this.busy = true;
			this.container.style.opacity = 0.2;

			BX.ajax({
				url: this.ajaxUrl,
				method: 'POST',
				dataType: 'json',
				timeout: 60,
				data: {
					action: 'changeEmptySection',
					siteId: this.siteId,
					signed: this.signed,
					sectionId: sectionId
				},
				onsuccess: BX.delegate(function(result) {
					this.busy = false;
					if(!result || !result.content) {
						this.container.style.opacity = '';
						return;
					}

					var applyContent = BX.delegate(function() {
						var processed = BX.processHTML(result.content, false);

						this.container.innerHTML = processed.HTML;

						if(result.imgWebp) {
							var srcList = {},
								images = this.container.querySelectorAll('img');

							if(!!images) {
								for(var n in images) {
									if(images.hasOwnProperty(n)) {
										var imageDataLazyloadSrc = images[n].getAttribute('data-lazyload-src'),
											imageSrc = images[n].getAttribute('src');

										if(!!imageDataLazyloadSrc && imageDataLazyloadSrc.substr(0, 4) !== 'http' && imageDataLazyloadSrc.substr(0, 11) !== 'data:image/' && imageDataLazyloadSrc.indexOf('.webp') == -1)
											srcList[imageDataLazyloadSrc] = imageDataLazyloadSrc;
										else if(!!imageSrc && imageSrc.substr(0, 4) !== 'http' && imageSrc.substr(0, 11) !== 'data:image/' && imageSrc.indexOf('.webp') == -1)
											srcList[imageSrc] = imageSrc;
									}
								}
							}

							if(Object.keys(srcList).length > 0 && typeof convertImgToWebp === 'function')
								convertImgToWebp(srcList);
						}

						if(result.imgLazyLoad && typeof imgLazyLoad === 'function')
							imgLazyLoad();

						new BX.easing({
							duration: 2000,
							start: {opacity: 20},
							finish: {opacity: 100},
							transition: BX.easing.makeEaseOut(BX.easing.transitions.quad),
							step: BX.delegate(function(state) {
								this.container.style.opacity = state.opacity / 100;
							}, this),
							complete: BX.delegate(function() {
								this.container.removeAttribute('style');
							}, this)
						}).animate();

						BX.ajax.processScripts(processed.SCRIPT);
					}, this);

					if(result.JS)
						BX.ajax.processScripts(BX.processHTML(result.JS).SCRIPT, false, applyContent);
					else
						applyContent();
				}, this),
				onfailure: BX.delegate(function() {
					this.busy = false;
					this.container.style.opacity = '';
				}, this)
			});

			for(i in this.emptyParentLinks) {
				if(this.emptyParentLinks.hasOwnProperty(i) && BX.type.isDomNode(this.emptyParentLinks[i])) {
					if(this.emptyParentLinks[i].getAttribute('data-section-id') === sectionId)
						BX.addClass(this.emptyParentLinks[i], 'active');
					else
						BX.removeClass(this.emptyParentLinks[i], 'active');
				}
			}
		}
	};
})();
