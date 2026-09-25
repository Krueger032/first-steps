(function(window, document) {
    'use strict';

    if (window.__ENEXT_CART_POPUP_SAFE_INIT__) {
        return;
    }
    window.__ENEXT_CART_POPUP_SAFE_INIT__ = true;

    var POPUP_ID = 'elektronext-cart-popup-safe';
    var OVERLAY_ID = 'elektronext-cart-popup-safe-overlay';
    var CART_URL = '/personal/cart/';
    var ORDER_URL_FALLBACK = '/personal/cart/';
    var PATCHED_MARK = '__elektronextCartPopupPatched';
    var INTERNAL_SUPPRESS_KEY = '__ENEXT_CART_POPUP_SUPPRESS_COUNT__';
    var BASKET_COUNT_SELECTORS = [
        '[data-basket-count]',
        '[data-entity="basket-count"]',
        '.basket .count',
        '.basket_block .count',
        '.header-cart .count',
        '.top-btn.basket .count',
        '.basket_fly .count',
        '.js-basket-block .count',
        '.js-basket .count',
        '.cart .count',
        '.header_opener .count',
        '.header-cart-block .count',
        '#basket_count',
        '#cart_count',
        '.wrap_icon .count'
    ];

    var popupState = {
        instance: null,
        name: 'Товар',
        subtitle: '',
        detailUrl: '#',
        imageSrc: '',
        productId: 0,
        quantity: 1,
        unitPriceValue: 0,
        priceText: '',
        busy: false
    };

    function escapeHtml(value) {
        return String(value == null ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function trimText(value) {
        return String(value == null ? '' : value).replace(/\s+/g, ' ').trim();
    }

    function textFromNode(node) {
        return node ? trimText(node.textContent || node.innerText || '') : '';
    }

    function normalizeUrl(url) {
        if (!url) {
            return '';
        }
        try {
            return new URL(url, window.location.origin).href;
        } catch (error) {
            return url;
        }
    }

    function parsePriceValue(priceText) {
        var raw = String(priceText || '')
            .replace(/&nbsp;/gi, ' ')
            .replace(/\u00a0/g, ' ')
            .replace(/[^\d,\.\s]/g, '')
            .trim();

        if (!raw) {
            return 0;
        }

        var normalized = raw.replace(/\s+/g, '').replace(',', '.');
        var match = normalized.match(/\d+(?:\.\d+)?/);
        return match ? parseFloat(match[0]) : 0;
    }

    function formatPrice(value, fallbackText) {
        var amount = Number(value || 0);
        if (!amount || !isFinite(amount)) {
            return trimText(fallbackText || '');
        }

        var hasFraction = Math.abs(amount % 1) > 0.001;
        return new Intl.NumberFormat('ru-RU', {
            minimumFractionDigits: hasFraction ? 2 : 0,
            maximumFractionDigits: hasFraction ? 2 : 0
        }).format(amount) + ' ₽';
    }

    function parseInteger(value, fallbackValue) {
        var parsed = parseInt(String(value == null ? '' : value).replace(/[^\d-]/g, ''), 10);
        return isFinite(parsed) ? parsed : (fallbackValue || 0);
    }

    function getCurrentPriceText(instance) {
        var nodes = [
            instance && instance.obPriceCurrent,
            instance && instance.obPrice,
            instance && instance.obSkuItemPriceCurrent,
            instance && instance.obSkuItemPrice,
            document.querySelector('.product-item-detail-price-current'),
            document.querySelector('.product-item-price-current'),
            document.querySelector('[data-entity="price-current"]')
        ];

        for (var i = 0; i < nodes.length; i++) {
            var text = textFromNode(nodes[i]);
            if (text) {
                return text;
            }
        }

        return '';
    }

    function getCurrentProductId(instance) {
        if (!instance) {
            return 0;
        }

        if (instance.productType === 3 && instance.offers && typeof instance.offerNum !== 'undefined' && instance.offers[instance.offerNum]) {
            return Math.max(0, parseInt(instance.offers[instance.offerNum].ID || 0, 10) || 0);
        }

        return Math.max(0, parseInt(instance.product && instance.product.id ? instance.product.id : 0, 10) || 0);
    }

    function getCurrentQuantity(instance) {
        var quantity = '';
        var quantityNodes = getInstanceQuantityNodes(instance);

        for (var i = 0; i < quantityNodes.length; i++) {
            if (quantityNodes[i] && quantityNodes[i].value) {
                quantity = quantityNodes[i].value;
                break;
            }
        }

        if (!quantity && instance && instance.basketParams && instance.basketData && instance.basketData.quantity) {
            quantity = instance.basketParams[instance.basketData.quantity];
        }

        quantity = trimText(quantity || '1');
        quantity = quantity.replace(',', '.');
        quantity = quantity.replace(/[^\d.]/g, '');

        var parsed = parseFloat(quantity || '1');
        if (!isFinite(parsed) || parsed <= 0) {
            parsed = 1;
        }

        return Math.max(1, Math.round(parsed));
    }

    function getInstanceQuantityNodes(instance) {
        return [
            instance && instance.obQuantity,
            instance && instance.obSkuItemQuantity,
            instance && instance.obPcQuantity,
            instance && instance.obSkuItemPcQuantity,
            instance && instance.obSqMQuantity,
            instance && instance.obSkuItemSqMQuantity
        ].filter(Boolean);
    }

    function setInstanceQuantity(instance, value) {
        var nodes = getInstanceQuantityNodes(instance);
        for (var i = 0; i < nodes.length; i++) {
            try {
                nodes[i].value = value;
                nodes[i].setAttribute('value', value);
                nodes[i].dispatchEvent(new Event('input', { bubbles: true }));
                nodes[i].dispatchEvent(new Event('change', { bubbles: true }));
            } catch (error) {
            }
        }
    }

    function getImageSrc(instance) {
        var src = '';

        if (instance && instance.product && instance.product.pict && instance.product.pict.SRC) {
            src = instance.product.pict.SRC;
        }

        if (!src && instance && instance.offers && typeof instance.offerNum !== 'undefined' && instance.offers[instance.offerNum]) {
            var offer = instance.offers[instance.offerNum];
            if (offer.DETAIL_PICTURE && offer.DETAIL_PICTURE.SRC) {
                src = offer.DETAIL_PICTURE.SRC;
            } else if (offer.PREVIEW_PICTURE && offer.PREVIEW_PICTURE.SRC) {
                src = offer.PREVIEW_PICTURE.SRC;
            }
        }

        if (!src && instance && instance.defaultPict) {
            if (instance.defaultPict.pict && instance.defaultPict.pict.SRC) {
                src = instance.defaultPict.pict.SRC;
            } else if (instance.defaultPict.detail && instance.defaultPict.detail.SRC) {
                src = instance.defaultPict.detail.SRC;
            } else if (instance.defaultPict.preview && instance.defaultPict.preview.SRC) {
                src = instance.defaultPict.preview.SRC;
            }
        }

        if (!src && instance && instance.obProduct) {
            var img = instance.obProduct.querySelector('img');
            if (img) {
                src = img.getAttribute('src') || img.getAttribute('data-src') || '';
            }
        }

        if (!src) {
            var fallback = document.querySelector('.product-item-detail-slider-image.active img, .product-item-detail-slider-image img, .catalog-item img, .product-item-image img');
            if (fallback) {
                src = fallback.getAttribute('src') || fallback.getAttribute('data-src') || '';
            }
        }

        return normalizeUrl(src);
    }

    function getDetailUrl(instance) {
        var url = '';

        if (instance && instance.product && instance.product.detailPageUrl) {
            url = instance.product.detailPageUrl;
        }

        if (!url && instance && instance.product && instance.product.DETAIL_PAGE_URL) {
            url = instance.product.DETAIL_PAGE_URL;
        }

        if (!url && instance && instance.obProduct) {
            var link = instance.obProduct.querySelector('a[href]');
            if (link) {
                url = link.getAttribute('href') || '';
            }
        }

        if (!url) {
            url = window.location.href;
        }

        return normalizeUrl(url);
    }

    function getProductName(instance) {
        var name = '';

        if (instance && instance.product && instance.product.name) {
            name = instance.product.name;
        }

        if (!name && instance && instance.offers && typeof instance.offerNum !== 'undefined' && instance.offers[instance.offerNum]) {
            name = instance.offers[instance.offerNum].NAME || '';
        }

        if (!name && instance && instance.obProduct) {
            var selectors = [
                '[data-entity="name"]',
                '.product-item-title a',
                '.product-item-title',
                '.catalog-item-title',
                'a[itemprop="name"]'
            ];
            for (var i = 0; i < selectors.length; i++) {
                var node = instance.obProduct.querySelector(selectors[i]);
                name = textFromNode(node);
                if (name) {
                    break;
                }
            }
        }

        if (!name) {
            name = textFromNode(document.querySelector('h1')) || document.title || 'Товар';
        }

        return name;
    }

    function getProductSubtitle(instance) {
        if (!instance || !instance.obProduct) {
            return '';
        }

        var selectors = [
            '.catalog-item-articul',
            '.catalog-item-article',
            '.product-item-articul',
            '.product-item-article',
            '.catalog-item-code',
            '.product-item-code',
            '[class*="articul"]',
            '[class*="article"]',
            '[class*="code"]'
        ];

        for (var i = 0; i < selectors.length; i++) {
            var node = instance.obProduct.querySelector(selectors[i]);
            var text = textFromNode(node);
            if (text && /код|артикул/i.test(text)) {
                return text;
            }
        }

        var textBlocks = instance.obProduct.querySelectorAll('div, span, p');
        for (var j = 0; j < textBlocks.length; j++) {
            var blockText = textFromNode(textBlocks[j]);
            if (blockText && blockText.length <= 120 && /код|артикул/i.test(blockText)) {
                return blockText;
            }
        }

        return '';
    }

    function guessOrderUrl() {
        var selectors = [
            'a[href*="/personal/order/make/"]',
            'a[href*="/personal/order/"]',
            'a[href*="/order/make/"]',
            'a[href*="/order/"]'
        ];

        for (var i = 0; i < selectors.length; i++) {
            var node = document.querySelector(selectors[i]);
            if (node && node.getAttribute('href')) {
                return normalizeUrl(node.getAttribute('href'));
            }
        }

        return normalizeUrl(ORDER_URL_FALLBACK);
    }

    function collectData(instance) {
        var priceText = getCurrentPriceText(instance);

        return {
            instance: instance || null,
            id: getCurrentProductId(instance),
            name: getProductName(instance),
            subtitle: getProductSubtitle(instance),
            priceText: priceText,
            unitPriceValue: parsePriceValue(priceText),
            quantity: getCurrentQuantity(instance),
            imageSrc: getImageSrc(instance),
            detailUrl: getDetailUrl(instance),
            orderUrl: guessOrderUrl()
        };
    }

    function getDeliveryIconHtml() {
        return '' +
            '<svg viewBox="0 0 120 54" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">' +
                '<rect x="14" y="18" width="56" height="22" rx="6" fill="#ffffff" opacity=".98" />' +
                '<path d="M70 24h19l11 10v6H70z" fill="#dff8eb" />' +
                '<rect x="74" y="27" width="10" height="7" rx="2" fill="#7dd9ad" />' +
                '<rect x="87" y="27" width="8" height="7" rx="2" fill="#7dd9ad" />' +
                '<circle cx="36" cy="42" r="8" fill="#4b5f88" />' +
                '<circle cx="36" cy="42" r="4" fill="#dce6f7" />' +
                '<circle cx="83" cy="42" r="8" fill="#4b5f88" />' +
                '<circle cx="83" cy="42" r="4" fill="#dce6f7" />' +
                '<circle cx="22" cy="18" r="10" fill="#ffc857" />' +
                '<path d="M17 18h10" stroke="#6b95ea" stroke-width="3" stroke-linecap="round" />' +
                '<path d="M22 13v10" stroke="#6b95ea" stroke-width="3" stroke-linecap="round" />' +
                '<path d="M47 20c8 0 13 3 13 8s-5 8-13 8c-7 0-13-3-13-8s6-8 13-8z" fill="#6b95ea" opacity=".7" />' +
            '</svg>';
    }
 
    function createPopup() {
        if (document.getElementById(POPUP_ID) && document.getElementById(OVERLAY_ID)) {
            return;
        }

        var overlay = document.createElement('div');
        overlay.id = OVERLAY_ID;
        overlay.addEventListener('click', hidePopup);

        var popup = document.createElement('div');
        popup.id = POPUP_ID;
        popup.setAttribute('role', 'dialog');
        popup.setAttribute('aria-modal', 'true');
        popup.setAttribute('aria-label', 'Товар добавлен в корзину');
        popup.innerHTML = '' +
            '<div class="elektronext-cart-popup__card elektronext-cart-popup__card--main">' +
                '<button type="button" class="elektronext-cart-popup__close" aria-label="Закрыть"></button>' +
                '<div class="elektronext-cart-popup__title">Товар добавлен в корзину!</div>' +
                '<div class="elektronext-cart-popup__row">' +
                    '<div class="elektronext-cart-popup__image is-empty"><img src="" alt=""></div>' +
                    '<div class="elektronext-cart-popup__info">' +
                        '<a class="elektronext-cart-popup__name" href="#"></a>' +
                        '<div class="elektronext-cart-popup__subtitle is-hidden"></div>' +
                    '</div>' +
                    '<div class="elektronext-cart-popup__qty-wrap">' +
                        '<div class="elektronext-cart-popup__qty">' +
                            '<button type="button" class="elektronext-cart-popup__qty-btn elektronext-cart-popup__qty-btn--minus" disabled>−</button>' +
                            '<input class="elektronext-cart-popup__qty-input" type="text" value="1" readonly>' +
                            '<button type="button" class="elektronext-cart-popup__qty-btn elektronext-cart-popup__qty-btn--plus">+</button>' +
                        '</div>' +
                    '</div>' +
                    '<div class="elektronext-cart-popup__price"></div>' +
                '</div>' +
                '<div class="elektronext-cart-popup__actions">' +
                    '<button type="button" class="elektronext-cart-popup__btn elektronext-cart-popup__btn--secondary">Продолжить покупки</button>' +
                    '<a class="elektronext-cart-popup__btn elektronext-cart-popup__btn--primary" href="' + escapeHtml(ORDER_URL_FALLBACK) + '">Перейти в корзину</a>' +
                '</div>' +
            '</div>' +
            '<div class="elektronext-cart-popup__card elektronext-cart-popup__card--delivery">' +
                '<div class="elektronext-cart-popup__delivery-title">Доставка вашего товара будет от 1000 руб. для Москвы.</div><div class="popdel" id="popdel" style="display:block;"><div class="product-item-detail-deliveries"><div class="product-item-detail-delivery"><div class="product-item-detail-delivery-icon"><svg width="14" height="19" viewBox="0 0 14 19" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.875 4.25H9.625C8.59075 4.25 7.75 5.0915 7.75 6.125V8.75H6.7135L5.68675 6.69575C5.164 5.6495 4.11175 5 2.9425 5C1.45825 5 0.25 6.20825 0.25 7.703L0.2905 10.538C0.3055 11.5835 0.8485 12.5285 1.744 13.0662L3.637 14.2048C3.86125 14.3398 4 14.5865 4 14.8475V17.7493C4 18.1632 4.33525 18.4993 4.75 18.4993C5.16475 18.4993 5.5 18.1632 5.5 17.7493V14.8475C5.5 14.063 5.08225 13.3243 4.41025 12.9193L2.5165 11.78C2.0695 11.5107 1.798 11.0382 1.7905 10.5155L1.75 7.691C1.75 7.03325 2.28475 6.4985 2.9425 6.4985C3.0475 6.4985 3.15025 6.509 3.25 6.5285V10.2485C3.25 10.6625 3.58525 10.9985 4 10.9985C4.41475 10.9985 4.75 10.6625 4.75 10.2485V8.17475L5.37175 9.41825C5.62675 9.9305 6.1405 10.2477 6.7135 10.2477H11.875C12.9093 10.2477 13.75 9.40625 13.75 8.37275V6.12275C13.75 5.08925 12.9093 4.24775 11.875 4.24775V4.25ZM12.25 8.375C12.25 8.582 12.0813 8.75 11.875 8.75H9.25V6.125C9.25 5.918 9.41875 5.75 9.625 5.75H11.875C12.0813 5.75 12.25 5.918 12.25 6.125V8.375ZM1 2.375C1 1.33925 1.83925 0.5 2.875 0.5C3.91075 0.5 4.75 1.33925 4.75 2.375C4.75 3.41075 3.91075 4.25 2.875 4.25C1.83925 4.25 1 3.41075 1 2.375ZM2.5 15.5V17.75C2.5 18.164 2.16475 18.5 1.75 18.5C1.33525 18.5 1 18.164 1 17.75V15.5C1 15.086 1.33525 14.75 1.75 14.75C2.16475 14.75 2.5 15.086 2.5 15.5Z" fill="#333333"></path></svg></div><div class="product-item-detail-delivery-info"><div class="delivery-info-item"><span>Доставка курьером</span><div class="dot"></div><span>Мск</span><div class="dot"></div><span>от 1000 ₽</span></div><div class="delivery-info-date"><span>С 2 апреля</span></div></div></div><div class="product-item-detail-delivery popup"><div class="product-item-detail-delivery-icon"><svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 12.5V4.25C15 3.65326 15.2371 3.08097 15.659 2.65901C16.081 2.23705 16.6533 2 17.25 2C17.4489 2 17.6397 1.92098 17.7804 1.78033C17.921 1.63968 18 1.44891 18 1.25C18 1.05109 17.921 0.860322 17.7804 0.71967C17.6397 0.579018 17.4489 0.5 17.25 0.5C16.2558 0.501191 15.3027 0.896661 14.5997 1.59966C13.8967 2.30267 13.5012 3.2558 13.5 4.25V12.7092L11.4495 13.3932C11.4533 13.1384 11.4137 12.8848 11.3325 12.6432L10.1678 8.81825C9.98563 8.25035 9.58595 7.77771 9.0562 7.50377C8.52644 7.22982 7.90973 7.17687 7.34103 7.3565L2.31603 8.954C1.75448 9.13584 1.28637 9.53018 1.0118 10.0527C0.73723 10.5752 0.678002 11.1844 0.846779 11.75L2.07228 15.7797C2.15236 16.0045 2.26809 16.215 2.41503 16.403L0.513029 17.0368C0.324062 17.0996 0.167805 17.235 0.0786313 17.413C-0.0105423 17.5911 -0.0253271 17.7973 0.0375292 17.9862C0.100385 18.1752 0.235734 18.3315 0.4138 18.4206C0.591866 18.5098 0.798062 18.5246 0.987029 18.4618L12.075 14.7673C11.9662 15.2093 11.959 15.6702 12.0539 16.1155C12.1488 16.5607 12.3434 16.9786 12.623 17.3379C12.9027 17.6971 13.2601 17.9883 13.6684 18.1896C14.0767 18.3909 14.5253 18.497 14.9805 18.5C18.924 18.398 18.9413 12.6267 15 12.5ZM3.49653 15.311L2.28228 11.3158C2.22641 11.1268 2.24669 10.9235 2.33877 10.7493C2.43085 10.575 2.58743 10.4438 2.77503 10.3835L7.80003 8.786C7.98802 8.72625 8.19198 8.74296 8.36774 8.83253C8.54349 8.92209 8.67689 9.07728 8.73903 9.2645L9.90003 13.094L9.90828 13.1173C9.96714 13.2901 9.96091 13.4785 9.89077 13.6471C9.82062 13.8157 9.69138 13.9529 9.52728 14.033L4.36128 15.758C4.18753 15.7985 4.00502 15.7758 3.84653 15.6939C3.68804 15.6119 3.56395 15.4762 3.49653 15.311ZM14.9805 17C14.5827 17 14.2012 16.842 13.9199 16.5607C13.6386 16.2794 13.4805 15.8978 13.4805 15.5C13.4805 15.1022 13.6386 14.7206 13.9199 14.4393C14.2012 14.158 14.5827 14 14.9805 14C15.3784 14 15.7599 14.158 16.0412 14.4393C16.3225 14.7206 16.4805 15.1022 16.4805 15.5C16.4805 15.8978 16.3225 16.2794 16.0412 16.5607C15.7599 16.842 15.3784 17 14.9805 17ZM7.53753 11C7.56737 11.0939 7.57844 11.1927 7.57009 11.2908C7.56174 11.389 7.53415 11.4845 7.48888 11.572C7.44361 11.6594 7.38155 11.7371 7.30626 11.8006C7.23096 11.8641 7.14389 11.9122 7.05003 11.942L5.20203 12.5308C5.01237 12.591 4.80653 12.5735 4.6298 12.482C4.45307 12.3905 4.31993 12.2325 4.25965 12.0429C4.19938 11.8532 4.21693 11.6474 4.30842 11.4706C4.39991 11.2939 4.55787 11.1608 4.74753 11.1005L6.59628 10.5125C6.78574 10.4525 6.9913 10.4701 7.16778 10.5615C7.34427 10.6529 7.47725 10.8106 7.53753 11Z" fill="#333333"></path></svg></div><div class="product-item-detail-delivery-info"><div class="delivery-info-item"><span>Самовывоз</span><div class="dot"></div><span>Мск</span><div class="dot"></div><span>Бесплатно</span></div><div class="delivery-info-date"><span>С 2 апреля</span></div><div class="delivery-info-address"><address>г. Москва, 41-й км МКАД ТВК «Мельница» 3-я линия Пассаж 21-22</address></div></div></div><div class="product-item-detail-delivery"><div class="product-item-detail-delivery-icon"><svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 12.5V4.25C15 3.65326 15.2371 3.08097 15.659 2.65901C16.081 2.23705 16.6533 2 17.25 2C17.4489 2 17.6397 1.92098 17.7804 1.78033C17.921 1.63968 18 1.44891 18 1.25C18 1.05109 17.921 0.860322 17.7804 0.71967C17.6397 0.579018 17.4489 0.5 17.25 0.5C16.2558 0.501191 15.3027 0.896661 14.5997 1.59966C13.8967 2.30267 13.5012 3.2558 13.5 4.25V12.7092L11.4495 13.3932C11.4533 13.1384 11.4137 12.8848 11.3325 12.6432L10.1678 8.81825C9.98563 8.25035 9.58595 7.77771 9.0562 7.50377C8.52644 7.22982 7.90973 7.17687 7.34103 7.3565L2.31603 8.954C1.75448 9.13584 1.28637 9.53018 1.0118 10.0527C0.73723 10.5752 0.678002 11.1844 0.846779 11.75L2.07228 15.7797C2.15236 16.0045 2.26809 16.215 2.41503 16.403L0.513029 17.0368C0.324062 17.0996 0.167805 17.235 0.0786313 17.413C-0.0105423 17.5911 -0.0253271 17.7973 0.0375292 17.9862C0.100385 18.1752 0.235734 18.3315 0.4138 18.4206C0.591866 18.5098 0.798062 18.5246 0.987029 18.4618L12.075 14.7673C11.9662 15.2093 11.959 15.6702 12.0539 16.1155C12.1488 16.5607 12.3434 16.9786 12.623 17.3379C12.9027 17.6971 13.2601 17.9883 13.6684 18.1896C14.0767 18.3909 14.5253 18.497 14.9805 18.5C18.924 18.398 18.9413 12.6267 15 12.5ZM3.49653 15.311L2.28228 11.3158C2.22641 11.1268 2.24669 10.9235 2.33877 10.7493C2.43085 10.575 2.58743 10.4438 2.77503 10.3835L7.80003 8.786C7.98802 8.72625 8.19198 8.74296 8.36774 8.83253C8.54349 8.92209 8.67689 9.07728 8.73903 9.2645L9.90003 13.094L9.90828 13.1173C9.96714 13.2901 9.96091 13.4785 9.89077 13.6471C9.82062 13.8157 9.69138 13.9529 9.52728 14.033L4.36128 15.758C4.18753 15.7985 4.00502 15.7758 3.84653 15.6939C3.68804 15.6119 3.56395 15.4762 3.49653 15.311ZM14.9805 17C14.5827 17 14.2012 16.842 13.9199 16.5607C13.6386 16.2794 13.4805 15.8978 13.4805 15.5C13.4805 15.1022 13.6386 14.7206 13.9199 14.4393C14.2012 14.158 14.5827 14 14.9805 14C15.3784 14 15.7599 14.158 16.0412 14.4393C16.3225 14.7206 16.4805 15.1022 16.4805 15.5C16.4805 15.8978 16.3225 16.2794 16.0412 16.5607C15.7599 16.842 15.3784 17 14.9805 17ZM7.53753 11C7.56737 11.0939 7.57844 11.1927 7.57009 11.2908C7.56174 11.389 7.53415 11.4845 7.48888 11.572C7.44361 11.6594 7.38155 11.7371 7.30626 11.8006C7.23096 11.8641 7.14389 11.9122 7.05003 11.942L5.20203 12.5308C5.01237 12.591 4.80653 12.5735 4.6298 12.482C4.45307 12.3905 4.31993 12.2325 4.25965 12.0429C4.19938 11.8532 4.21693 11.6474 4.30842 11.4706C4.39991 11.2939 4.55787 11.1608 4.74753 11.1005L6.59628 10.5125C6.78574 10.4525 6.9913 10.4701 7.16778 10.5615C7.34427 10.6529 7.47725 10.8106 7.53753 11Z" fill="#333333"></path></svg></div><div class="product-item-detail-delivery-info"><div class="delivery-info-item"><span>Доставка в регионы</span><div class="dot"></div><span>Расчёт отдельно</span></div><div class="delivery-info-text"><p>Осуществляется транспортными компаниями и рассчитывается отдельно после оформления заказа</p></div></div></div><div class="product-item-detail-delivery" style="align-items:flex-start"><div class="product-item-detail-delivery-icon"><img data-lazyload-src="/upload/ya_delivery.svg" style="width:18px;margin-top:5px" src="/upload/ya_delivery.svg" class="bx-lazyload-success"></div><div class="product-item-detail-delivery-info"><div class="delivery-info-item"><span>Яндекс доставка Москва до двери или в ПВЗ</span></div><div class="delivery-info-text"><p>После подтверждения менеджера Ваш заказ будет отправлен в кратчайшие сроки:<br>Москва и МО - 2-3 дня<br>Регионы - 3-10 дней (зависит от расстояния)<br></p></div></div></div></div></div>' +
                '<div class="elektronext-cart-popup__delivery-bar">' +
                    '<span class="elektronext-cart-popup__delivery-badge">Доставка от 1 000 ₽</span>' +
                    '<span class="elektronext-cart-popup__delivery-icon">' + getDeliveryIconHtml() + '</span>' +
                '</div>' +
            '</div>';

        popup.querySelector('.elektronext-cart-popup__close').addEventListener('click', hidePopup);
        popup.querySelector('.elektronext-cart-popup__btn--secondary').addEventListener('click', hidePopup);
        popup.querySelector('.elektronext-cart-popup__qty-btn--minus').addEventListener('click', onMinusClick);
        popup.querySelector('.elektronext-cart-popup__qty-btn--plus').addEventListener('click', onPlusClick);
 

        document.body.appendChild(overlay);
        document.body.appendChild(popup);
    }

    function getPopupElements() {
        var popup = document.getElementById(POPUP_ID);
        if (!popup) {
            return null;
        }

        return {
            popup: popup,
            overlay: document.getElementById(OVERLAY_ID),
            imageWrap: popup.querySelector('.elektronext-cart-popup__image'),
            image: popup.querySelector('.elektronext-cart-popup__image img'),
            name: popup.querySelector('.elektronext-cart-popup__name'),
            subtitle: popup.querySelector('.elektronext-cart-popup__subtitle'),
            qtyInput: popup.querySelector('.elektronext-cart-popup__qty-input'),
            minus: popup.querySelector('.elektronext-cart-popup__qty-btn--minus'),
            plus: popup.querySelector('.elektronext-cart-popup__qty-btn--plus'),
            price: popup.querySelector('.elektronext-cart-popup__price'),
            primary: popup.querySelector('.elektronext-cart-popup__btn--primary')
        };
    }

    function renderPopupState() {
        var elements = getPopupElements();
        if (!elements) {
            return;
        }

        elements.name.textContent = popupState.name;
        elements.name.setAttribute('href', popupState.detailUrl || '#');
        elements.qtyInput.value = String(popupState.quantity || 1);
        elements.price.textContent = popupState.unitPriceValue
            ? formatPrice(popupState.unitPriceValue * popupState.quantity, popupState.priceText)
            : trimText(popupState.priceText || '');

        if (popupState.subtitle) {
            elements.subtitle.textContent = popupState.subtitle;
            elements.subtitle.classList.remove('is-hidden');
        } else {
            elements.subtitle.textContent = '';
            elements.subtitle.classList.add('is-hidden');
        }

        if (popupState.imageSrc) {
            elements.image.src = popupState.imageSrc;
            elements.image.alt = popupState.name;
            elements.imageWrap.classList.remove('is-empty');
        } else {
            elements.image.removeAttribute('src');
            elements.image.alt = '';
            elements.imageWrap.classList.add('is-empty');
        }

        elements.primary.setAttribute('href', popupState.orderUrl || ORDER_URL_FALLBACK);
        elements.minus.disabled = popupState.busy || popupState.quantity <= 1;
        elements.plus.disabled = !!popupState.busy;

        if (popupState.busy) {
            elements.popup.classList.add('is-busy');
        } else {
            elements.popup.classList.remove('is-busy');
        }
    }

    function showPopup(data) {
        createPopup();

        var elements = getPopupElements();
        if (!elements || !elements.overlay) {
            return;
        }

        popupState = {
            instance: data && data.instance ? data.instance : null,
            name: trimText(data && data.name ? data.name : 'Товар'),
            subtitle: trimText(data && data.subtitle ? data.subtitle : ''),
            detailUrl: data && data.detailUrl ? data.detailUrl : '#',
            imageSrc: data && data.imageSrc ? data.imageSrc : '',
            productId: Math.max(0, parseInt(data && data.id ? data.id : 0, 10) || 0),
            quantity: 1,
            unitPriceValue: Number(data && data.unitPriceValue ? data.unitPriceValue : 0) || 0,
            priceText: trimText(data && data.priceText ? data.priceText : ''),
            orderUrl: data && data.orderUrl ? data.orderUrl : ORDER_URL_FALLBACK,
            busy: false
        };

        renderPopupState();

        elements.overlay.classList.add('is-active');
        elements.popup.classList.add('is-active');
        document.body.classList.add('elektronext-cart-popup-open');

        window.setTimeout(syncPopupQuantityFromBasket, 120);
        window.setTimeout(syncPopupQuantityFromBasket, 520);
    }

    function hidePopup() {
        var popup = document.getElementById(POPUP_ID);
        var overlay = document.getElementById(OVERLAY_ID);

        if (popup) {
            popup.classList.remove('is-active');
        }
        if (overlay) {
            overlay.classList.remove('is-active');
        }
        document.body.classList.remove('elektronext-cart-popup-open');
        popupState.busy = false;
    }

    function waitForAnimationEnd(callback) {
        var started = Date.now();

        function check() {
            var animated = document.querySelector('.animated-image');
            if (!animated || Date.now() - started > 1800) {
                callback();
                return;
            }
            window.setTimeout(check, 60);
        }

        check();
    }

    function findMethod(instance, names) {
        for (var i = 0; i < names.length; i++) {
            if (instance && typeof instance[names[i]] === 'function') {
                return instance[names[i]];
            }
        }
        return null;
    }

    function normalizeComparableUrl(url) {
        var normalized = normalizeUrl(url || '');
        if (!normalized) {
            return '';
        }

        try {
            var parsed = new URL(normalized, window.location.origin);
            var pathname = parsed.pathname || '/';
            pathname = pathname.replace(/\/+$/g, '') || '/';
            pathname = pathname.replace(/\/+/, '/');
            return pathname.toLowerCase();
        } catch (error) {
            return String(normalized)
                .replace(/^https?:\/\/[^/]+/i, '')
                .replace(/[?#].*$/, '')
                .replace(/\/+$/, '') || '/';
        }
    }

    function normalizeComparableText(value) {
        return trimText(value || '')
            .toLowerCase()
            .replace(/ё/g, 'е');
    }

    function isQuantityField(input) {
        if (!input || !input.name || input.disabled) {
            return false;
        }

        var name = String(input.name);
        var entity = String(input.getAttribute('data-entity') || '');
        var className = String(input.className || '');
        var type = String(input.type || '').toLowerCase();

        if (/QUANTITY/i.test(name) || /basket-item-quantity-field/i.test(entity)) {
            return true;
        }

        if ((type === 'text' || type === 'tel' || type === 'number' || type === '') && /quantity/i.test(className + ' ' + name + ' ' + entity)) {
            return true;
        }

        return false;
    }

    function getBasketRowSelector() {
        return [
            '.slide-panel-basket-item-tr[data-id]',
            '[data-entity="row"][data-id]',
            '[data-entity="row"][id]',
            '[data-entity="row"]',
            '[data-entity="basket-item"][data-id]',
            '.basket-items-list-item-container[data-id]',
            '.basket-item-tr[id]',
            '.basket-item-tr',
            '.basket-item[data-id]',
            '.bx_ordercart_item[data-id]',
            '.bx_ordercart_item[id]',
            '.bx_ordercart_order_table_container[data-id]',
            '.bx_ordercart_order_table_container[id]',
            'tr[data-id]',
            'tr[id]',
            'li[data-id]',
            'li[id]'
        ].join(', ');
    }

    function findRowContainer(node) {
        var current = node;
        while (current && current !== document && current !== document.documentElement) {
            if (current.tagName) {
                var tag = current.tagName.toLowerCase();
                if ((tag === 'tr' || tag === 'li' || tag === 'form' || tag === 'section' || tag === 'article' || tag === 'div') && current.querySelector && current.querySelector('input[name*="QUANTITY"], input[data-entity="basket-item-quantity-field"]')) {
                    return current;
                }
            }
            current = current.parentElement;
        }
        return null;
    }

    function extractComparableArticle(value) {
        return normalizeComparableText(value || '').replace(/^(артикул|код)\s*:?\s*/i, '');
    }

    function getBasketRowCandidates(root) {
        if (!root || !root.querySelectorAll) {
            return [];
        }

        var selector = getBasketRowSelector();
        var result = [];
        var directRows = root.querySelectorAll(selector);

        for (var i = 0; i < directRows.length; i++) {
            var row = directRows[i];
            if (!row || !row.querySelector || !row.querySelector('input[name*="QUANTITY"], input[data-entity="basket-item-quantity-field"]')) {
                continue;
            }

            if (result.indexOf(row) === -1) {
                result.push(row);
            }
        }

        return result;
    }

    function getBasketRowId(row) {
        if (!row) {
            return 0;
        }

        var directValues = [
            row.getAttribute && row.getAttribute('data-id'),
            row.getAttribute && row.getAttribute('data-basket-id'),
            row.getAttribute && row.getAttribute('id'),
            row.id || ''
        ];

        for (var i = 0; i < directValues.length; i++) {
            var directId = parseInt(directValues[i], 10);
            if (directId > 0) {
                return directId;
            }
        }

        var inputs = row.querySelectorAll('input[id], input[name]');
        for (var j = 0; j < inputs.length; j++) {
            var tokens = [inputs[j].id || '', inputs[j].name || ''];
            for (var k = 0; k < tokens.length; k++) {
                var match = tokens[k].match(/(?:QUANTITY_INPUT_|QUANTITY_)(\d+)/i);
                if (match && parseInt(match[1], 10) > 0) {
                    return parseInt(match[1], 10);
                }
            }
        }

        return 0;
    }

    function getBasketRowQuantityInput(row) {
        if (!row || !row.querySelectorAll) {
            return null;
        }

        var inputs = row.querySelectorAll('input[name], input[data-entity="basket-item-quantity-field"]');
        for (var i = 0; i < inputs.length; i++) {
            if (isQuantityField(inputs[i])) {
                return inputs[i];
            }
        }

        return null;
    }

    function rowMatchesProduct(row, productId, detailUrl, name, subtitle) {
        if (!row) {
            return false;
        }

        var normalizedUrl = normalizeComparableUrl(detailUrl);
        var normalizedName = normalizeComparableText(name);
        var normalizedSubtitle = extractComparableArticle(subtitle);
        var rowText = normalizeComparableText(textFromNode(row));
        var productMatched = false;
        var urlMatched = false;
        var nameMatched = !!(normalizedName && rowText && rowText.indexOf(normalizedName) !== -1);
        var subtitleMatched = !!(normalizedSubtitle && rowText && rowText.indexOf(normalizedSubtitle) !== -1);

        if (productId > 0) {
            var productSelectors = [
                '[data-product-id]',
                '[data-productid]',
                '[data-offer-id]',
                'input[name="PRODUCT_ID"]',
                'input[name*="PRODUCT_ID"]',
                'input[name*="[PRODUCT_ID]"]'
            ];

            for (var s = 0; s < productSelectors.length && !productMatched; s++) {
                var productNodes = row.querySelectorAll(productSelectors[s]);
                for (var i = 0; i < productNodes.length && !productMatched; i++) {
                    var node = productNodes[i];
                    var values = [
                        node.getAttribute && node.getAttribute('data-product-id'),
                        node.getAttribute && node.getAttribute('data-productid'),
                        node.getAttribute && node.getAttribute('data-offer-id'),
                        node.value,
                        node.getAttribute && node.getAttribute('value')
                    ];

                    for (var j = 0; j < values.length; j++) {
                        if (parseInt(values[j], 10) === productId) {
                            productMatched = true;
                            break;
                        }
                    }
                }
            }
        }

        if (normalizedUrl) {
            var links = row.querySelectorAll('a[href]');
            for (var l = 0; l < links.length; l++) {
                if (normalizeComparableUrl(links[l].getAttribute('href')) === normalizedUrl) {
                    urlMatched = true;
                    break;
                }
            }
        }

        if (productMatched) {
            return true;
        }

        if (urlMatched && (nameMatched || subtitleMatched || (!normalizedName && !normalizedSubtitle))) {
            return true;
        }

        if (nameMatched && subtitleMatched) {
            return true;
        }

        if (!normalizedUrl && nameMatched) {
            return true;
        }

        return false;
    }

    function findBasketRows(root, productId, detailUrl, name, subtitle) {
        if (!root) {
            return [];
        }

        var rows = [];
        var candidates = getBasketRowCandidates(root);

        for (var i = 0; i < candidates.length; i++) {
            var row = candidates[i];
            if (
                rowMatchesProduct(row, productId, detailUrl, name, subtitle) &&
                getBasketRowQuantityInput(row) &&
                rows.indexOf(row) === -1
            ) {
                rows.push(row);
            }
        }

        return rows;
    }

    function getBasketRowsInfo(root, productId, detailUrl, name, subtitle) {
        var matchedRows = findBasketRows(root, productId, detailUrl, name, subtitle);
        var rows = [];
        var seenIds = {};
        var seenNodes = [];
        var totalQuantity = 0;
        var i;

        for (i = 0; i < matchedRows.length; i++) {
            var row = matchedRows[i];
            var basketId = getBasketRowId(row);

            if (basketId > 0) {
                if (seenIds[basketId]) {
                    continue;
                }
                seenIds[basketId] = true;
            } else if (seenNodes.indexOf(row) !== -1) {
                continue;
            } else {
                seenNodes.push(row);
            }

            rows.push(row);
        }

        var primaryRow = rows.length ? rows[rows.length - 1] : null;
        var primaryBasketId = getBasketRowId(primaryRow);
        var primaryInput = getBasketRowQuantityInput(primaryRow);
        var primaryInputId = primaryInput && primaryInput.id ? primaryInput.id : (primaryBasketId > 0 ? 'QUANTITY_INPUT_' + primaryBasketId : '');

        for (i = 0; i < rows.length; i++) {
            var rowQuantity = getBasketRowQuantityValue(rows[i]);
            if (rowQuantity > 0) {
                totalQuantity += rowQuantity;
            }
        }

        var primaryQuantity = getBasketRowQuantityValue(primaryRow);

        return {
            rows: rows,
            totalQuantity: totalQuantity,
            primaryQuantity: primaryQuantity > 0 ? primaryQuantity : 0,
            primaryRow: primaryRow,
            primaryBasketId: primaryBasketId,
            primaryInput: primaryInput,
            primaryInputId: primaryInputId
        };
    }

    function findBasketRow(cartDocument, productId, detailUrl, name, subtitle) {
        var info = getBasketRowsInfo(cartDocument, productId, detailUrl, name, subtitle);
        return info.primaryRow;
    }

    function findBasketForm(cartDocument, row) {
        if (row && row.closest) {
            var closestForm = row.closest('form');
            if (closestForm) {
                return closestForm;
            }
        }

        if (!cartDocument) {
            return null;
        }

        return cartDocument.querySelector('form[action*="/personal/cart"], form[name*="basket"], form[id*="basket"], form');
    }

    function getBasketRowQuantityValue(row) {
        if (!row || !row.querySelectorAll) {
            return null;
        }

        var inputs = row.querySelectorAll('input[name], input[data-entity="basket-item-quantity-field"]');
        for (var i = 0; i < inputs.length; i++) {
            if (isQuantityField(inputs[i])) {
                var value = parseInteger(inputs[i].value || inputs[i].getAttribute('value'), -1);
                if (value > 0) {
                    return value;
                }
            }
        }

        var textSelectors = [
            '[data-entity="basket-item-quantity-field"]',
            '.basket-item-amount-filed',
            '.basket-item-quantity-value',
            '.quantity',
            '.amount'
        ];

        for (var j = 0; j < textSelectors.length; j++) {
            var node = row.querySelector(textSelectors[j]);
            var textValue = parseInteger(textFromNode(node), -1);
            if (textValue > 0) {
                return textValue;
            }
        }

        return null;
    }

    function getLiveBasketRowsInfo() {
        return getBasketRowsInfo(document, popupState.productId, popupState.detailUrl, popupState.name, popupState.subtitle);
    }

    function getBasketComponent() {
        if (window.BX && window.BX.Sale && window.BX.Sale.BasketComponent) {
            return window.BX.Sale.BasketComponent;
        }

        return null;
    }

    function updateBasketQuantityInputs(basketId, input, quantity) {
        if (input) {
            input.value = String(quantity);
            input.setAttribute('value', String(quantity));
        }

        if (basketId > 0) {
            var hiddenInput = document.getElementById('QUANTITY_' + basketId);
            if (hiddenInput) {
                hiddenInput.value = String(quantity);
                hiddenInput.setAttribute('value', String(quantity));
            }
        }
    }

    function waitForLiveBasketQuantity(expectedQuantity, callback) {
        var attempts = 0;

        function check() {
            attempts += 1;
            var liveInfo = getLiveBasketRowsInfo();
            if (liveInfo.rows.length && liveInfo.primaryQuantity === expectedQuantity) {
                callback(true, liveInfo);
                return;
            }

            if (attempts >= 24) {
                callback(false, liveInfo);
                return;
            }

            window.setTimeout(check, 140);
        }

        check();
    }

    function deleteDuplicateLiveBasketRows(rows, keepBasketId, callback) {
        var component = getBasketComponent();
        var rowIds = [];

        for (var i = 0; i < rows.length; i++) {
            var rowId = getBasketRowId(rows[i]);
            if (rowId > 0 && rowId !== keepBasketId && rowIds.indexOf(rowId) === -1) {
                rowIds.push(rowId);
            }
        }

        if (!rowIds.length) {
            callback(true);
            return;
        }

        if (!component || typeof component.deleteItem !== 'function') {
            callback(false);
            return;
        }

        function removeNext() {
            if (!rowIds.length) {
                callback(true);
                return;
            }

            var currentId = rowIds.shift();
            try {
                component.deleteItem(currentId);
                window.setTimeout(removeNext, 260);
            } catch (error) {
                callback(false);
            }
        }

        removeNext();
    }

    function updateLiveBasketQuantity(newQuantity, done) {
        var liveInfo = getLiveBasketRowsInfo();
        var component = getBasketComponent();

        if (!liveInfo.rows.length || !liveInfo.primaryBasketId || !liveInfo.primaryInputId || !component || typeof component.updateQuantity !== 'function') {
            done(false);
            return;
        }

        updateBasketQuantityInputs(liveInfo.primaryBasketId, liveInfo.primaryInput, newQuantity);

        try {
            component.updateQuantity(liveInfo.primaryInputId, liveInfo.primaryBasketId, 1, true);
        } catch (error) {
            done(false);
            return;
        }

        window.setTimeout(function() {
            var refreshedInfo = getLiveBasketRowsInfo();
            if (refreshedInfo.rows.length > 1) {
                deleteDuplicateLiveBasketRows(refreshedInfo.rows, refreshedInfo.primaryBasketId, function(duplicatesRemoved) {
                    if (!duplicatesRemoved) {
                        done(false);
                        return;
                    }

                    waitForLiveBasketQuantity(newQuantity, function(success) {
                        done(success);
                    });
                });
                return;
            }

            waitForLiveBasketQuantity(newQuantity, function(success) {
                done(success);
            });
        }, 240);
    }

    function resolveBasketQuantity(callback) {
        var liveInfo = getLiveBasketRowsInfo();
        if (liveInfo.rows.length && liveInfo.primaryQuantity > 0) {
            callback(true, liveInfo.primaryQuantity);
            return;
        }

        fetchCartDocument(function(success, cartDocument) {
            if (!success || !cartDocument) {
                callback(false, 0);
                return;
            }

            var cartInfo = getBasketRowsInfo(cartDocument, popupState.productId, popupState.detailUrl, popupState.name, popupState.subtitle);
            callback(cartInfo.primaryQuantity > 0, cartInfo.primaryQuantity || 0);
        });
    }

    function syncPopupQuantityFromBasket() {
        if (!popupState.productId || popupState.busy) {
            return;
        }

        resolveBasketQuantity(function(success, quantity) {
            if (!success || quantity < 1 || popupState.busy) {
                return;
            }

            if (popupState.quantity !== quantity) {
                popupState.quantity = quantity;
                renderPopupState();
            }
        });
    }

    function fetchCartDocument(callback) {
        if (!window.DOMParser) {
            callback(false, null);
            return;
        }

        var xhr = new XMLHttpRequest();
        xhr.open('GET', CART_URL, true);
        xhr.withCredentials = true;
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.onreadystatechange = function() {
            if (xhr.readyState !== 4) {
                return;
            }

            if (xhr.status < 200 || xhr.status >= 400 || !xhr.responseText) {
                callback(false, null);
                return;
            }

            try {
                var parser = new DOMParser();
                callback(true, parser.parseFromString(xhr.responseText, 'text/html'));
            } catch (error) {
                callback(false, null);
            }
        };

        xhr.onerror = function() {
            callback(false, null);
        };

        xhr.send(null);
    }

    function appendRefreshAction(form, formData) {
        if (!form || !formData) {
            return;
        }

        var submitCandidates = form.querySelectorAll('button[name], input[type="submit"][name], input[type="image"][name]');
        for (var i = 0; i < submitCandidates.length; i++) {
            var button = submitCandidates[i];
            var text = normalizeComparableText(textFromNode(button) + ' ' + (button.value || '') + ' ' + (button.name || ''));
            if (/пересч|обнов|refresh|recalc|recount|update|save/i.test(text)) {
                formData.set(button.name, button.value || 'Y');
                return;
            }
        }

        if (!formData.has('BasketRefresh')) {
            formData.append('BasketRefresh', 'Y');
        }
    }

    function applyActionVar(form, formData, actionValue) {
        if (!form || !formData || !actionValue) {
            return false;
        }

        var actionVarInput = form.querySelector('input[name="action_var"]');
        if (actionVarInput && actionVarInput.value) {
            formData.set(actionVarInput.value, actionValue);
            return true;
        }

        return false;
    }

    function buildBasketFormData(form, row, newQuantity, variant) {
        if (!form || !row) {
            return null;
        }

        var formData = new FormData(form);
        var inputs = row.querySelectorAll('input[name]');
        var hasQuantityField = false;

        for (var i = 0; i < inputs.length; i++) {
            if (isQuantityField(inputs[i])) {
                formData.set(inputs[i].name, String(newQuantity));
                hasQuantityField = true;
            }
        }

        if (!hasQuantityField) {
            return null;
        }

        appendRefreshAction(form, formData);

        if (variant === 1) {
            formData.set('BasketRefresh', 'Y');
            formData.set('update_cart', 'Y');
            formData.set('refresh', 'Y');
            formData.set('recalc', 'Y');
        } else if (variant === 2) {
            if (!applyActionVar(form, formData, 'refreshAjax')) {
                formData.set('action', 'refreshAjax');
            }
            formData.set('BasketRefresh', 'Y');
        } else if (variant === 3) {
            if (!applyActionVar(form, formData, 'recalculate')) {
                formData.set('action', 'recalculate');
            }
            formData.set('BasketRefresh', 'Y');
            formData.set('update_cart', 'Y');
        }

        return formData;
    }

    function submitBasketForm(form, formData, callback) {
        if (!form || !formData) {
            callback(false);
            return;
        }

        var method = String(form.getAttribute('method') || 'POST').toUpperCase();
        var action = normalizeUrl(form.getAttribute('action') || CART_URL) || CART_URL;
        var xhr = new XMLHttpRequest();

        xhr.open(method === 'GET' ? 'GET' : 'POST', action, true);
        xhr.withCredentials = true;
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.onreadystatechange = function() {
            if (xhr.readyState !== 4) {
                return;
            }

            callback(xhr.status >= 200 && xhr.status < 400, xhr.responseText || '');
        };

        xhr.onerror = function() {
            callback(false, '');
        };

        if (method === 'GET') {
            callback(false, '');
            return;
        }

        xhr.send(formData);
    }

    function dispatchBasketChange() {
        try {
            if (window.BX && typeof window.BX.onCustomEvent === 'function') {
                window.BX.onCustomEvent('OnBasketChange');
                window.BX.onCustomEvent('OnBasketResult');
            }
        } catch (error) {
        }

        try {
            document.dispatchEvent(new CustomEvent('onBasketChange', { bubbles: true }));
        } catch (error) {
        }
    }

    function updateCounterNode(node, nextValue) {
        if (!node || nextValue < 0) {
            return;
        }

        if (node.hasAttribute && node.hasAttribute('data-basket-count')) {
            node.setAttribute('data-basket-count', String(nextValue));
        }

        var textValue = node.textContent || '';
        if (/\d/.test(textValue)) {
            node.textContent = textValue.replace(/\d+/, String(nextValue));
            return;
        }

        if (node.value != null && /\d/.test(String(node.value))) {
            node.value = String(node.value).replace(/\d+/, String(nextValue));
            return;
        }

        node.textContent = String(nextValue);
    }

    function adjustLocalBasketCounters(delta) {
        var unique = [];
        for (var i = 0; i < BASKET_COUNT_SELECTORS.length; i++) {
            var nodes = document.querySelectorAll(BASKET_COUNT_SELECTORS[i]);
            for (var j = 0; j < nodes.length; j++) {
                if (unique.indexOf(nodes[j]) === -1) {
                    unique.push(nodes[j]);
                }
            }
        }

        for (var k = 0; k < unique.length; k++) {
            var node = unique[k];
            var raw = node.getAttribute && node.getAttribute('data-basket-count');
            if (!raw) {
                raw = node.textContent || node.value || '';
            }
            var currentValue = parseInteger(raw, -1);
            if (currentValue >= 0) {
                updateCounterNode(node, Math.max(0, currentValue + delta));
            }
        }
    }

    function confirmCartQuantity(expectedQuantity, callback) {
        fetchCartDocument(function(success, cartDocument) {
            if (!success || !cartDocument) {
                callback(false);
                return;
            }

            var cartInfo = getBasketRowsInfo(cartDocument, popupState.productId, popupState.detailUrl, popupState.name, popupState.subtitle);
            callback(cartInfo.primaryQuantity === expectedQuantity);
        });
    }

    function changeCartQuantityFallback(newQuantity, done) {
        fetchCartDocument(function(success, cartDocument) {
            if (!success || !cartDocument) {
                done(false);
                return;
            }

            var row = findBasketRow(cartDocument, popupState.productId, popupState.detailUrl, popupState.name, popupState.subtitle);
            var form = findBasketForm(cartDocument, row);

            if (!row || !form) {
                done(false);
                return;
            }

            var variantIndex = 0;
            var maxVariants = 4;

            function tryVariant() {
                if (variantIndex >= maxVariants) {
                    done(false);
                    return;
                }

                var formData = buildBasketFormData(form, row, newQuantity, variantIndex);
                variantIndex += 1;

                if (!formData) {
                    tryVariant();
                    return;
                }

                submitBasketForm(form, formData, function(postSuccess) {
                    if (!postSuccess) {
                        tryVariant();
                        return;
                    }

                    window.setTimeout(function() {
                        confirmCartQuantity(newQuantity, function(confirmed) {
                            if (!confirmed) {
                                tryVariant();
                                return;
                            }

                            adjustLocalBasketCounters(newQuantity - popupState.quantity);
                            dispatchBasketChange();
                            done(true);
                        });
                    }, 240);
                });
            }

            tryVariant();
        });
    }

    function changeCartQuantity(newQuantity, done) {
        if (!popupState.productId || newQuantity < 1) {
            done(false);
            return;
        }

        if (getLiveBasketRowsInfo().rows.length) {
            updateLiveBasketQuantity(newQuantity, function(success) {
                if (!success) {
                    changeCartQuantityFallback(newQuantity, done);
                    return;
                }

                dispatchBasketChange();
                done(true);
            });
            return;
        }

        changeCartQuantityFallback(newQuantity, done);
    }

    function onMinusClick() {
        if (popupState.busy || popupState.quantity <= 1) {
            return;
        }

        popupState.busy = true;
        renderPopupState();

        var nextQuantity = Math.max(1, popupState.quantity - 1);
        changeCartQuantity(nextQuantity, function(success) {
            popupState.busy = false;

            if (success) {
                popupState.quantity = nextQuantity;
            }

            renderPopupState();
        });
    }

    function onPlusClick() {
        if (popupState.busy) {
            return;
        }

        popupState.busy = true;
        renderPopupState();

        var nextQuantity = popupState.quantity + 1;
        changeCartQuantity(nextQuantity, function(success) {
            popupState.busy = false;
            if (success) {
                popupState.quantity = nextQuantity;
            }
            renderPopupState();
        });
    }
	
	

    function patchConstructor(constructorName) {
        var Constructor = window[constructorName];
        if (!Constructor || !Constructor.prototype || Constructor.prototype[PATCHED_MARK]) {
            return false;
        }

        var originalBasketResult = Constructor.prototype.basketResult;
        if (typeof originalBasketResult !== 'function') {
            return false;
        }

        Constructor.prototype.basketResult = function(arResult) {
            var suppressCount = window[INTERNAL_SUPPRESS_KEY] || 0;
            var isSuppressed = suppressCount > 0;

            if (isSuppressed) {
                window[INTERNAL_SUPPRESS_KEY] = suppressCount - 1;
            }

            var shouldShow = !isSuppressed && !!(arResult && arResult.STATUS === 'OK' && this && this.basketMode !== 'BUY');
            var popupData = shouldShow ? collectData(this) : null;
            var result = originalBasketResult.apply(this, arguments);

            if (shouldShow) {
                waitForAnimationEnd(function() {
                    showPopup(popupData);
                });
            }

            return result;
        };

        Constructor.prototype[PATCHED_MARK] = true;
        return true;
    }

    function patchAllConstructors() {
        var patched = false;
        patched = patchConstructor('JCCatalogItem') || patched;
        patched = patchConstructor('JCCatalogElementArticle') || patched;
        patched = patchConstructor('JCCatalogElement') || patched;
        return patched;
    }

    function bootstrap() {
        createPopup();
        patchAllConstructors();

        var attempts = 0;
        var timer = window.setInterval(function() {
            attempts += 1;
            patchAllConstructors();
            if (attempts >= 80) {
                window.clearInterval(timer);
            }
        }, 250);

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                hidePopup();
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootstrap);
    } else {
        bootstrap();
    }
})(window, document);
