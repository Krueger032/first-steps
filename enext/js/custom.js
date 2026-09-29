var santehpodborMapLoading = false;
var santehpodborMapInitialized = false;

function santehpodborLoadYandexMap() {
	var mapNode = document.getElementById('map');
	if(!mapNode || santehpodborMapInitialized) return;

	if(window.ymaps) {
		window.ymaps.ready(init);
		return;
	}

	if(santehpodborMapLoading) return;
	santehpodborMapLoading = true;

	var existingScript = document.querySelector('script[src*="api-maps.yandex.ru/2.1"]');
	if(existingScript) {
		existingScript.addEventListener('load', function() {
			if(window.ymaps) window.ymaps.ready(init);
		}, {once: true});
		return;
	}

	var script = document.createElement('script');
	script.async = true;
	script.src = 'https://api-maps.yandex.ru/2.1/?lang=ru_RU';
	script.onload = function() {
		if(window.ymaps) window.ymaps.ready(init);
	};
	script.onerror = function() {
		santehpodborMapLoading = false;
	};
	document.head.appendChild(script);
}

document.addEventListener('DOMContentLoaded', function() {
	var mapNode = document.getElementById('map');
	if(!mapNode) return;
	if(mapNode.closest && mapNode.closest('#deliveryPopup')) return;

	if('IntersectionObserver' in window) {
		var mapObserver = new IntersectionObserver(function(entries, observer) {
			if(entries.some(function(entry) { return entry.isIntersecting; })) {
				observer.disconnect();
				santehpodborLoadYandexMap();
			}
		}, {rootMargin: '200px'});
		mapObserver.observe(mapNode);
	} else if(mapNode.offsetParent !== null) {
		santehpodborLoadYandexMap();
	}
});
function init() {
	if(santehpodborMapInitialized || !window.ymaps || !document.getElementById('map')) return;
	santehpodborMapInitialized = true;
	var zoom = 0;
	if(window.innerWidth >= 576){
      zoom = 18;
	}else{
		zoom = 16;
	}
	var myMap = new ymaps.Map("map", {
		center: [55.612955, 37.486230],
		zoom: zoom,
		controls: [], 
	}, {
		searchControlProvider: 'yandex#search'
	});


	var myPlacemark = new ymaps.Placemark([55.612955, 37.486230], null, {
		iconLayout: 'default#image',
		iconImageHref: "/local/templates/enext/images/marker.png",
		iconImageSize: [52, 64],
		iconImageOffset: [-15, -44]
	});
	myMap.geoObjects.add(myPlacemark);

}
document.addEventListener('DOMContentLoaded', function () {
	const deliveryBlock = document.querySelector('.product-item-detail-delivery.popup');
	const popup = document.getElementById('deliveryPopup');
	const closeBtn = document.querySelector('.delivery-popup__close');
	const overlay = document.querySelector('.delivery-popup__overlay');
	const contactBtn = document.querySelector('.delivery-popup__subtitle'); // Кнопка "Связаться с менеджером"
	const dateElement = document.querySelector('.product-item-detail-delivery.popup .delivery-info-date');
	const date = dateElement ? dateElement.textContent.trim().replace(/\s+/g, ' ') : '';
	const closeContact = document.querySelector('.slide-panel__close');
	const panel = document.querySelector('.slide-panel ');
	// Открытие pop-up при клике на блок доставки
	if (deliveryBlock) {
		deliveryBlock.addEventListener('click', function (e) {
			e.preventDefault();
			santehpodborLoadYandexMap();
			popup.classList.add('active');
			const timeElement = document.querySelector('.js-time');
			timeElement.textContent = date;
			document.body.style.overflow = 'hidden';
		});
	}

	// Обработчик для кнопки "Связаться с менеджером"
	if (contactBtn) {
		contactBtn.addEventListener('click', function (e) {
			e.preventDefault();

			// Закрываем попап
			closePopup();

			// Ищем элемент .top-panel__contacts-caption и кликаем по нему
			const contactsCaption = document.querySelector('#bx_3218110189_77');
			if (contactsCaption) {
				// Добавляем небольшую задержку для плавности
				setTimeout(() => {
					contactsCaption.click();
				}, 300);
			} else {
				console.warn('Элемент .top-panel__contacts-caption не найден');
			}
		});
	}

	// Закрытие pop-up
	function closePopup() {
		popup.classList.remove('active');
		document.body.style.overflow = '';
	}

	if (closeBtn) {
		closeBtn.addEventListener('click', closePopup);
	}

	if (overlay) {
		overlay.addEventListener('click', closePopup);
	}

	// Закрытие по ESC
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && popup.classList.contains('active')) {
			closePopup();
		}
	});
});
/***CUSTOM JAVASCRIPT FOR YOUR SITE***/
$(document).ready(function () {
	// $('.reviews-slider').owlCarousel({
	//     items: 1,
	//     dots: true,
	// });

});

jQuery(document).ready(function ($) {
	$('.iti').wrap('<noindex/>');
$('a[href="/catalog/installyatsii1/installyatsii-grohe/"]').parent('div').remove();
});

window.onload = function () {
	$('.catalog-section-links.category-link.show_more a').click(function (e) {
		e.preventDefault();
		if ($('.row.catalog-sections.categories-min').hasClass('with-spoiler')) {
			$('.catalog-section-links.category-link.show_more a').text('Скрыть всe');
			$('.row.catalog-sections.categories-min').removeClass('with-spoiler');
		} else {
			$('.catalog-section-links.category-link.show_more a').text('Показать все');
			$('.row.catalog-sections.categories-min').addClass('with-spoiler');
		}
	});
};
document.addEventListener('DOMContentLoaded', function () {
	// Проверяем, приняты ли cookies
	if (localStorage.getItem('cookieAccepted') !== 'true') {
		// Устанавливаем задержку в 20 секунд перед показом уведомления
		setTimeout(function () {
			document.querySelector('.cookie-notice').style.display = 'block';
		}, 20000); // 20000 миллисекунд = 20 секунд
	}

	// Обработчик события для кнопки
	document.querySelector('.cookie-notice button').addEventListener('click', function () {
		localStorage.setItem('cookieAccepted', 'true');
		this.parentElement.style.display = 'none';
	});
});


function copyToClipboard(text) {
  navigator.clipboard.writeText(text).then(() => {
    showNotification('Текст скопирован!');
  }).catch(() => {
    showNotification('Ошибка при копировании');
  });
}

function showNotification(message) {
  const notification = document.getElementById('copyNotification');
  notification.innerText = message;
  notification.style.display = 'block';

  // Можно расположить в центре, или рядом с курсором
  // Для простоты — в центре
  notification.style.top = '10px';
  notification.style.right = '10px';

  setTimeout(() => {
    notification.style.display = 'none';
  }, 2000);
}