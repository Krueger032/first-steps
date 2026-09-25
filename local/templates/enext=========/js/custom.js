/***CUSTOM JAVASCRIPT FOR YOUR SITE***/
(function(window, document) {
	'use strict';

	function onReady(callback) {
		if(document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', callback);
		} else {
			callback();
		}
	}

	function onLoad(callback) {
		if(document.readyState === 'complete') {
			callback();
		} else {
			window.addEventListener('load', callback);
		}
	}

	function initMap() {
		var mapNode = document.getElementById('map');

		if(!mapNode || !window.ymaps) {
			return;
		}

		window.ymaps.ready(function() {
			var zoom = window.innerWidth >= 576 ? 18 : 16;

			var myMap = new window.ymaps.Map('map', {
				center: [55.612955, 37.486230],
				zoom: zoom,
				controls: []
			}, {
				searchControlProvider: 'yandex#search'
			});

			var myPlacemark = new window.ymaps.Placemark([55.612955, 37.486230], null, {
				iconLayout: 'default#image',
				iconImageHref: '/local/templates/enext/images/marker.png',
				iconImageSize: [52, 64],
				iconImageOffset: [-15, -44]
			});

			myMap.geoObjects.add(myPlacemark);
		});
	}

	function initDeliveryPopup() {
		var deliveryBlock = document.querySelector('.product-item-detail-delivery.popup');
		var popup = document.getElementById('deliveryPopup');
		var closeBtn = document.querySelector('.delivery-popup__close');
		var overlay = document.querySelector('.delivery-popup__overlay');
		var contactBtn = document.querySelector('.delivery-popup__subtitle');
		var dateNode = document.querySelector('.product-item-detail-delivery.popup .delivery-info-date');

		if(!deliveryBlock || !popup) {
			return;
		}

		var date = dateNode ? dateNode.textContent.trim().replace(/\s+/g, ' ') : '';

		function closePopup() {
			popup.classList.remove('active');
			document.body.style.overflow = '';
		}

		deliveryBlock.addEventListener('click', function(e) {
			e.preventDefault();

			popup.classList.add('active');

			var timeElement = document.querySelector('.js-time');
			if(timeElement) {
				timeElement.textContent = date;
			}

			document.body.style.overflow = 'hidden';
		});

		if(contactBtn) {
			contactBtn.addEventListener('click', function(e) {
				e.preventDefault();

				closePopup();

				var contactsCaption = document.querySelector('#bx_3218110189_77');
				if(contactsCaption) {
					window.setTimeout(function() {
						contactsCaption.click();
					}, 300);
				} else if(window.console && window.console.warn) {
					window.console.warn('Элемент .top-panel__contacts-caption не найден');
				}
			});
		}

		if(closeBtn) {
			closeBtn.addEventListener('click', closePopup);
		}

		if(overlay) {
			overlay.addEventListener('click', closePopup);
		}

		document.addEventListener('keydown', function(e) {
			if(e.key === 'Escape' && popup.classList.contains('active')) {
				closePopup();
			}
		});
	}

	function initCategoryShowMore() {
		if(!window.jQuery) {
			return;
		}

		var $ = window.jQuery;
		$('.catalog-section-links.category-link.show_more a').off('click.awebShowMore').on('click.awebShowMore', function(e) {
			e.preventDefault();

			var $categories = $('.row.catalog-sections.categories-min');
			var $button = $('.catalog-section-links.category-link.show_more a');

			if($categories.hasClass('with-spoiler')) {
				$button.text('Скрыть всe');
				$categories.removeClass('with-spoiler');
			} else {
				$button.text('Показать все');
				$categories.addClass('with-spoiler');
			}
		});
	}

	function initJqueryTweaks() {
		if(!window.jQuery) {
			return;
		}

		var $ = window.jQuery;

		$('.iti').wrap('<noindex/>');
		$('a[href="/catalog/installyatsii1/installyatsii-grohe/"]').parent('div').remove();
	}

	function initCookieNotice() {
		var notice = document.querySelector('.cookie-notice');

		if(!notice) {
			return;
		}

		var button = notice.querySelector('button');

		if(window.localStorage && window.localStorage.getItem('cookieAccepted') !== 'true') {
			window.setTimeout(function() {
				notice.style.display = 'block';
			}, 20000);
		}

		if(button) {
			button.addEventListener('click', function() {
				if(window.localStorage) {
					window.localStorage.setItem('cookieAccepted', 'true');
				}
				notice.style.display = 'none';
			});
		}
	}

	window.copyToClipboard = function(text) {
		if(!navigator.clipboard) {
			window.showNotification('Копирование не поддерживается браузером');
			return;
		}

		navigator.clipboard.writeText(text).then(function() {
			window.showNotification('Текст скопирован!');
		}).catch(function() {
			window.showNotification('Ошибка при копировании');
		});
	};

	window.showNotification = function(message) {
		var notification = document.getElementById('copyNotification');

		if(!notification) {
			return;
		}

		notification.innerText = message;
		notification.style.display = 'block';
		notification.style.top = '10px';
		notification.style.right = '10px';

		window.setTimeout(function() {
			notification.style.display = 'none';
		}, 2000);
	};

	onReady(function() {
		initDeliveryPopup();
		initJqueryTweaks();
		initCookieNotice();
	});

	onLoad(function() {
		initMap();
		initCategoryShowMore();
	});
})(window, document);
