<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<style>
    .oai-cart-popup-overlay {
        position: fixed;
        inset: 0;
        z-index: 99999;
        background: rgba(24, 30, 42, 0.56);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity .22s ease, visibility .22s ease;
    }

    .oai-cart-popup-overlay.is-open {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    .oai-cart-popup-wrap {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%) scale(.96);
        width: min(920px, calc(100vw - 32px));
        transition: transform .22s ease;
    }

    .oai-cart-popup-overlay.is-open .oai-cart-popup-wrap {
        transform: translate(-50%, -50%) scale(1);
    }

    .oai-cart-popup-main,
    .oai-cart-popup-delivery {
        background: #fff;
        border-radius: 32px;
        box-shadow: 0 18px 60px rgba(0, 0, 0, .16);
    }

    .oai-cart-popup-main {
        position: relative;
        padding: 36px 40px 34px;
    }

    .oai-cart-popup-close {
        position: absolute;
        top: -12px;
        right: -12px;
        width: 46px;
        height: 46px;
        border: 0;
        border-radius: 50%;
        background: #fff;
        color: #b4b4b4;
        cursor: pointer;
        font-size: 30px;
        line-height: 1;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .10);
    }

    .oai-cart-popup-title {
        margin: 0 0 26px;
        font-size: 22px;
        line-height: 1.2;
        font-weight: 800;
        color: #0f1728;
    }

    .oai-cart-popup-row {
        display: grid;
        grid-template-columns: 76px minmax(0, 1fr) 126px 112px;
        gap: 18px;
        align-items: center;
        margin-bottom: 28px;
    }

    .oai-cart-popup-image,
    .oai-cart-popup-image-placeholder {
        width: 76px;
        height: 76px;
        border-radius: 14px;
        background: #f7f8fb;
    }

    .oai-cart-popup-image {
        object-fit: contain;
    }

    .oai-cart-popup-image-placeholder {
        background: linear-gradient(180deg, #f4f6fb 0%, #edf1f8 100%);
    }

    .oai-cart-popup-name {
        margin: 0 0 6px;
        font-size: 18px;
        line-height: 1.35;
        font-weight: 600;
        color: #182033;
    }

    .oai-cart-popup-meta {
        font-size: 14px;
        line-height: 1.35;
        color: #8e97a7;
    }

    .oai-cart-popup-controls {
        display: contents;
    }

    .oai-cart-popup-qty {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 14px;
        height: 42px;
        min-width: 106px;
        border: 1px solid #d7ddeb;
        border-radius: 10px;
        color: #36435c;
        font-size: 20px;
        line-height: 1;
        user-select: none;
    }

    .oai-cart-popup-qty-btn {
        border: 0;
        background: transparent;
        width: 20px;
        height: 20px;
        padding: 0;
        margin: 0;
        font-size: 20px;
        line-height: 20px;
        color: #62708b;
        cursor: default;
    }

    .oai-cart-popup-qty-value {
        min-width: 12px;
        text-align: center;
        font-size: 18px;
        line-height: 1;
        color: #1f2a44;
    }

    .oai-cart-popup-price {
        text-align: right;
        font-size: 18px;
        line-height: 1.2;
        font-weight: 700;
        color: #182033;
        white-space: nowrap;
    }

    .oai-cart-popup-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .oai-cart-popup-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 56px;
        padding: 0 24px;
        border-radius: 16px;
        border: 0;
        text-decoration: none;
        font-size: 16px;
        line-height: 1;
        font-weight: 700;
        transition: transform .15s ease, opacity .15s ease, background .15s ease;
        cursor: pointer;
        box-sizing: border-box;
    }

    .oai-cart-popup-btn:hover {
        transform: translateY(-1px);
    }

    .oai-cart-popup-btn--secondary {
        background: #eef2fb;
        color: #7a9bee;
    }

    .oai-cart-popup-btn--primary {
        background: #6f96ee;
        color: #fff;
    }

    .oai-cart-popup-delivery {
        margin-top: 16px;
        padding: 18px 18px 16px;
    }

    .oai-cart-popup-delivery-title {
        margin: 0 0 12px;
        font-size: 16px;
        line-height: 1.35;
        color: #394256;
    }

    .oai-cart-popup-delivery-bar {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding: 0 64px 0 20px;
        border-radius: 18px;
        background: linear-gradient(90deg, #3fcb91 0%, #49d58f 100%);
        color: #fff;
        font-size: 14px;
        line-height: 1;
        font-weight: 800;
        text-transform: uppercase;
    }

    .oai-cart-popup-delivery-icon {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 28px;
        line-height: 1;
    }

    body.oai-cart-popup-lock {
        overflow: hidden;
    }

    @media (max-width: 820px) {
        .oai-cart-popup-wrap {
            top: 16px;
            left: 16px;
            right: 16px;
            width: auto;
            transform: none;
        }

        .oai-cart-popup-overlay.is-open .oai-cart-popup-wrap {
            transform: none;
        }

        .oai-cart-popup-main {
            padding: 28px 20px 20px;
            border-radius: 24px;
        }

        .oai-cart-popup-close {
            top: 12px;
            right: 12px;
            width: 40px;
            height: 40px;
            font-size: 26px;
        }

        .oai-cart-popup-title {
            padding-right: 40px;
            margin-bottom: 20px;
            font-size: 20px;
        }

        .oai-cart-popup-row {
            grid-template-columns: 60px minmax(0, 1fr);
            gap: 14px;
            align-items: start;
            margin-bottom: 22px;
        }

        .oai-cart-popup-image,
        .oai-cart-popup-image-placeholder {
            width: 60px;
            height: 60px;
        }

        .oai-cart-popup-info {
            margin-bottom: 12px;
        }

        .oai-cart-popup-controls {
            grid-column: 2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .oai-cart-popup-actions {
            grid-template-columns: 1fr;
        }

        .oai-cart-popup-delivery {
            border-radius: 24px;
        }
    }
</style>

<div class="oai-cart-popup-overlay" id="oaiCartPopupOverlay" aria-hidden="true">
    <div class="oai-cart-popup-wrap">
        <div class="oai-cart-popup-main" role="dialog" aria-modal="true" aria-labelledby="oaiCartPopupTitle">
            <button class="oai-cart-popup-close" type="button" id="oaiCartPopupClose" aria-label="Закрыть">×</button>

            <div class="oai-cart-popup-title" id="oaiCartPopupTitle">Товар добавлен в корзину!</div>

            <div class="oai-cart-popup-row">
                <div id="oaiCartPopupImageBox">
                    <div class="oai-cart-popup-image-placeholder"></div>
                </div>

                <div class="oai-cart-popup-info">
                    <div class="oai-cart-popup-name" id="oaiCartPopupName">Товар</div>
                    <div class="oai-cart-popup-meta" id="oaiCartPopupMeta"></div>
                </div>

                <div class="oai-cart-popup-controls">
                    <div class="oai-cart-popup-qty" aria-label="Количество">
                        <button class="oai-cart-popup-qty-btn" type="button" tabindex="-1" aria-hidden="true">−</button>
                        <span class="oai-cart-popup-qty-value" id="oaiCartPopupQty">1</span>
                        <button class="oai-cart-popup-qty-btn" type="button" tabindex="-1" aria-hidden="true">+</button>
                    </div>

                    <div class="oai-cart-popup-price" id="oaiCartPopupPrice"></div>
                </div>
            </div>

            <div class="oai-cart-popup-actions">
                <button class="oai-cart-popup-btn oai-cart-popup-btn--secondary" type="button" id="oaiCartPopupContinue">Продолжить покупки</button>
                <a class="oai-cart-popup-btn oai-cart-popup-btn--primary" id="oaiCartPopupCheckout" href="/basket/">Оформить заказ</a>
            </div>
        </div>

        <div class="oai-cart-popup-delivery">
            <div class="oai-cart-popup-delivery-title" id="oaiCartPopupDeliveryTitle">Доставка вашего товара будет от 1000 ₽ для Москвы</div>
            <div class="oai-cart-popup-delivery-bar">
                <span id="oaiCartPopupDeliveryBadge">Доставка от 1000 ₽</span>
                <span class="oai-cart-popup-delivery-icon" aria-hidden="true">🚚</span>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    if (window.__oaiBitrixCartPopupInit) {
        return;
    }

    window.__oaiBitrixCartPopupInit = true;

    var config = {
        cartUrl: '/basket/',
        checkoutUrl: '/basket/',
        deliveryTitle: 'Доставка вашего товара будет от 1000 ₽ для Москвы',
        deliveryBadge: 'Доставка от 1000 ₽',
        debug: false
    };

    var state = {
        lastProduct: null,
        waiting: false,
        waitingStartedAt: 0,
        waitingConfidence: 'medium',
        basketSignalSeen: false,
        fallbackTimer: 0,
        basketCountBefore: '',
        popupOpenedAt: 0
    };

    var strongSelectors = [
        '[data-cart-popup-button]',
        '[data-add2basket]',
        '[data-add-to-cart]',
        '[data-item-add-cart]',
        '[data-basket-add]',
        '.to-cart',
        '.ajax_add_to_cart',
        '.btn-cart',
        '.buy',
        '.button_buy',
        '.catalog-item-buy',
        '.product-item-buy-button',
        '.product-item-detail-buy-button',
        '.js-add-to-cart',
        '.js-add2basket',
        '.counter_wrapp .to-cart',
        'a[href*="ADD2BASKET"]',
        'a[href*="action=ADD2BASKET"]',
        'button[onclick*="ADD2BASKET"]',
        'button[onclick*="add2basket"]',
        'a[onclick*="ADD2BASKET"]',
        'a[onclick*="add2basket"]'
    ].join(',');

    var basketCountSelectors = [
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

    var addToCartTexts = [
        'в корзину',
        'добавить в корзину',
        'купить',
        'заказать'
    ];

    var overlay = document.getElementById('oaiCartPopupOverlay');
    if (!overlay) {
        return;
    }

    var closeBtn = document.getElementById('oaiCartPopupClose');
    var continueBtn = document.getElementById('oaiCartPopupContinue');
    var checkoutBtn = document.getElementById('oaiCartPopupCheckout');
    var nameNode = document.getElementById('oaiCartPopupName');
    var metaNode = document.getElementById('oaiCartPopupMeta');
    var qtyNode = document.getElementById('oaiCartPopupQty');
    var priceNode = document.getElementById('oaiCartPopupPrice');
    var imageBoxNode = document.getElementById('oaiCartPopupImageBox');
    var deliveryTitleNode = document.getElementById('oaiCartPopupDeliveryTitle');
    var deliveryBadgeNode = document.getElementById('oaiCartPopupDeliveryBadge');

    function log() {
        if (!config.debug || !window.console || typeof window.console.log !== 'function') {
            return;
        }

        var args = Array.prototype.slice.call(arguments);
        args.unshift('[cart-popup]');
        window.console.log.apply(window.console, args);
    }

    function text(value) {
        return String(value || '').replace(/\s+/g, ' ').trim();
    }

    function normalizeText(value) {
        return text(value).toLowerCase();
    }

    function parseQty(value) {
        var qty = parseInt(String(value || '').replace(/[^\d]/g, ''), 10);
        return qty > 0 ? qty : 1;
    }

    function parseCountValue(value) {
        var match = String(value || '').match(/\d+/);
        return match ? match[0] : '';
    }

    function escapeAttribute(value) {
        return String(value || '').replace(/"/g, '&quot;');
    }

    function getProductForms(button) {
        if (!button) {
            return [];
        }

        var forms = [];
        var form = button.closest('form');
        if (form) {
            forms.push(form);
        }

        var productBox = closestProductBox(button);
        if (productBox) {
            var innerForms = productBox.querySelectorAll('form');
            for (var i = 0; i < innerForms.length; i++) {
                if (forms.indexOf(innerForms[i]) === -1) {
                    forms.push(innerForms[i]);
                }
            }
        }

        return forms;
    }

    function findText(root, selectorList) {
        if (!root) {
            return '';
        }

        for (var i = 0; i < selectorList.length; i++) {
            var node = root.querySelector(selectorList[i]);
            if (node && text(node.textContent)) {
                return text(node.textContent);
            }
        }

        return '';
    }

    function findValue(root, selectorList) {
        if (!root) {
            return '';
        }

        for (var i = 0; i < selectorList.length; i++) {
            var node = root.querySelector(selectorList[i]);
            if (!node) {
                continue;
            }

            var value = text(node.value || node.getAttribute('value') || node.getAttribute('content') || node.textContent);
            if (value) {
                return value;
            }
        }

        return '';
    }

    function findImage(root) {
        if (!root) {
            return '';
        }

        var img = root.querySelector('img');
        if (!img) {
            return '';
        }

        return text(img.getAttribute('data-lazyload-src') || img.getAttribute('data-src') || img.getAttribute('src') || '');
    }

    function findQty(root) {
        if (!root) {
            return 1;
        }

        var input = root.querySelector('input[name="quantity"], input[name="QUANTITY"], input[name="qty"], input[name="QTY"], .ratio input, .product-item-amount input, .counter_block input');
        if (!input) {
            return 1;
        }

        return parseQty(input.value);
    }

    function closestProductBox(node) {
        if (!node || !node.closest) {
            return null;
        }

        return node.closest(
            '[data-entity="item"], ' +
            '[data-entity="items-row"], ' +
            '[data-item], ' +
            '.product-item-container, ' +
            '.product-item, ' +
            '.catalog-item, ' +
            '.catalog-section-item, ' +
            '.js-product, ' +
            '.bx_catalog_item, ' +
            '.bx_item_detail, ' +
            '.product-detail, ' +
            '.catalog-detail, ' +
            '.item_info, ' +
            '.inner_wrap, ' +
            '.catalog_block .item'
        );
    }

    function getBasketCountText() {
        for (var i = 0; i < basketCountSelectors.length; i++) {
            var node = document.querySelector(basketCountSelectors[i]);
            if (!node) {
                continue;
            }

            var value = parseCountValue(node.textContent || node.getAttribute('data-basket-count') || '');
            if (value !== '') {
                return value;
            }
        }

        return '';
    }

    function detectCartLinks() {
        var cartLink = document.querySelector('a[href*="/basket"], a[href*="/cart"], a[href*="/personal/cart"]');
        var checkoutLink = document.querySelector('a[href*="/order"], a[href*="/personal/order"], a[href*="/checkout"], a[href*="/basket"]');

        if (cartLink && cartLink.getAttribute('href')) {
            config.cartUrl = cartLink.getAttribute('href');
        }

        if (checkoutLink && checkoutLink.getAttribute('href')) {
            config.checkoutUrl = checkoutLink.getAttribute('href');
        } else {
            config.checkoutUrl = config.cartUrl;
        }

        checkoutBtn.setAttribute('href', config.checkoutUrl || '/basket/');
    }

    function detectProductData(button) {
        var box = closestProductBox(button);
        var forms = getProductForms(button);
        var pageTitle = document.querySelector('h1');
        var metaTitle = document.querySelector('meta[property="og:title"]');
        var metaPrice = document.querySelector('meta[property="product:price:amount"], meta[itemprop="price"]');
        var article = text(button.getAttribute('data-product-article') || '');

        var data = {
            id: text(button.getAttribute('data-product-id') || (box && box.getAttribute('data-product-id')) || ''),
            name: '',
            price: '',
            image: '',
            qty: 1,
            article: article
        };

        data.name = text(button.getAttribute('data-product-name')) ||
            findText(box, [
                '[data-product-name]',
                '.product-item-title a',
                '.product-item-title',
                '.catalog-item__title',
                '.item-title a',
                '.item-title',
                '.product-title',
                '.product-name',
                '.bx_catalog_item_title a',
                '.bx_catalog_item_title',
                '.detail-title',
                '[itemprop="name"]'
            ]) ||
            text(metaTitle && metaTitle.getAttribute('content')) ||
            text(pageTitle && pageTitle.textContent) ||
            'Товар';

        data.price = text(button.getAttribute('data-product-price')) ||
            findValue(box, [
                '[data-product-price]',
                '[itemprop="price"]',
                'meta[itemprop="price"]',
                '.price',
                '.price_value',
                '.price .value',
                '.product-item-price-current',
                '.product-item-detail-price-current',
                '.catalog-item-price',
                '.current-price',
                '.sale_block .price'
            ]) ||
            text(metaPrice && metaPrice.getAttribute('content'));

        data.image = text(button.getAttribute('data-product-image')) || findImage(box) || text((document.querySelector('meta[property="og:image"]') || {}).content || '');
        data.qty = parseQty(button.getAttribute('data-product-qty')) || findQty(box);

        if (!data.article) {
            data.article = findText(box, [
                '.article',
                '.product-article',
                '.catalog-item-article',
                '[data-product-article]'
            ]);
        }

        for (var i = 0; i < forms.length; i++) {
            var form = forms[i];
            if (!data.id) {
                data.id = findValue(form, ['input[name="id"]', 'input[name="ID"]', 'input[name="PRODUCT_ID"]', 'input[name="product_id"]']);
            }
            if (!data.qty) {
                data.qty = parseQty(findValue(form, ['input[name="quantity"]', 'input[name="QUANTITY"]', 'input[name="qty"]', 'input[name="QTY"]']));
            }
        }

        return data;
    }

    function renderImage(src, title) {
        if (!src) {
            imageBoxNode.innerHTML = '<div class="oai-cart-popup-image-placeholder"></div>';
            return;
        }

        imageBoxNode.innerHTML = '<img class="oai-cart-popup-image" src="' + escapeAttribute(src) + '" alt="' + escapeAttribute(title || 'Товар') + '">';
    }

    function openPopup(product) {
        if (!product) {
            return;
        }

        detectCartLinks();

        nameNode.textContent = product.name || 'Товар';
        metaNode.textContent = product.article || '';
        qtyNode.textContent = String(product.qty || 1);
        priceNode.textContent = product.price || '';
        renderImage(product.image || '', product.name || 'Товар');

        overlay.classList.add('is-open');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.classList.add('oai-cart-popup-lock');

        state.popupOpenedAt = Date.now();
        state.waiting = false;
        clearTimeout(state.fallbackTimer);

        log('popup opened', product);
    }

    function closePopup() {
        overlay.classList.remove('is-open');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('oai-cart-popup-lock');
    }

    function startWaiting(product, confidence) {
        if (!product) {
            return;
        }

        state.lastProduct = product;
        state.waiting = true;
        state.waitingStartedAt = Date.now();
        state.waitingConfidence = confidence || 'medium';
        state.basketSignalSeen = false;
        state.basketCountBefore = getBasketCountText();

        clearTimeout(state.fallbackTimer);
        state.fallbackTimer = window.setTimeout(function () {
            if (!state.waiting || !state.lastProduct) {
                return;
            }

            if (state.waitingConfidence === 'strong' || state.basketSignalSeen) {
                openPopup(state.lastProduct);
            }
        }, 1300);

        log('start waiting', state.lastProduct, state.waitingConfidence, state.basketCountBefore);
    }

    function isStrongAddToCartElement(element) {
        if (!element || !element.matches) {
            return false;
        }

        if (element.matches(strongSelectors)) {
            return true;
        }

        var href = normalizeText(element.getAttribute('href'));
        var onclick = normalizeText(element.getAttribute('onclick'));
        var cls = normalizeText(element.className || '');
        var textValue = normalizeText(element.textContent || element.value || '');

        if (href.indexOf('add2basket') !== -1 || href.indexOf('basket') !== -1 && href.indexOf('add') !== -1) {
            return true;
        }

        if (onclick.indexOf('add2basket') !== -1 || onclick.indexOf('basket') !== -1 && onclick.indexOf('add') !== -1) {
            return true;
        }

        if (cls.indexOf('cart') !== -1 && (textValue.indexOf('в корзину') !== -1 || textValue.indexOf('купить') !== -1)) {
            return true;
        }

        return false;
    }

    function isAddToCartByText(element) {
        if (!element) {
            return false;
        }

        var textValue = normalizeText(element.textContent || element.value || '');
        if (!textValue) {
            return false;
        }

        for (var i = 0; i < addToCartTexts.length; i++) {
            if (textValue.indexOf(addToCartTexts[i]) !== -1) {
                return true;
            }
        }

        return false;
    }

    function getAddToCartElement(target) {
        if (!target || !target.closest) {
            return null;
        }

        var node = target.closest('button, a, input[type="button"], input[type="submit"], .button');
        if (!node || overlay.contains(node)) {
            return null;
        }

        if (isStrongAddToCartElement(node)) {
            return { node: node, confidence: 'strong' };
        }

        if (isAddToCartByText(node)) {
            return { node: node, confidence: 'medium' };
        }

        return null;
    }

    function markBasketSignal(source) {
        if (!state.waiting || !state.lastProduct) {
            return;
        }

        state.basketSignalSeen = true;
        log('basket signal', source);

        window.clearTimeout(state.fallbackTimer);
        state.fallbackTimer = window.setTimeout(function () {
            if (state.waiting && state.lastProduct) {
                openPopup(state.lastProduct);
            }
        }, 120);
    }

    function requestLooksLikeBasket(url) {
        var value = normalizeText(url);
        if (!value) {
            return false;
        }

        return value.indexOf('add2basket') !== -1 ||
            value.indexOf('action=add2basket') !== -1 ||
            value.indexOf('/ajax/basket') !== -1 ||
            value.indexOf('/basket/add') !== -1 ||
            value.indexOf('basket&action=add') !== -1 ||
            value.indexOf('basket') !== -1 && value.indexOf('add') !== -1 ||
            value.indexOf('cart') !== -1 && value.indexOf('add') !== -1;
    }

    function installFetchWatcher() {
        if (typeof window.fetch !== 'function') {
            return;
        }

        var originalFetch = window.fetch;
        window.fetch = function () {
            var args = Array.prototype.slice.call(arguments);
            var url = args[0];
            if (url && typeof url !== 'string' && typeof url.url === 'string') {
                url = url.url;
            }

            var promise = originalFetch.apply(this, args);

            if (state.waiting && requestLooksLikeBasket(url)) {
                promise.then(function (response) {
                    if (response && response.ok) {
                        markBasketSignal('fetch:' + url);
                    }
                }).catch(function () {});
            }

            return promise;
        };
    }

    function installXhrWatcher() {
        if (!window.XMLHttpRequest) {
            return;
        }

        var originalOpen = XMLHttpRequest.prototype.open;
        var originalSend = XMLHttpRequest.prototype.send;

        XMLHttpRequest.prototype.open = function (method, url) {
            this.__oaiCartPopupUrl = url;
            return originalOpen.apply(this, arguments);
        };

        XMLHttpRequest.prototype.send = function () {
            if (state.waiting && requestLooksLikeBasket(this.__oaiCartPopupUrl || '')) {
                this.addEventListener('load', function () {
                    if (this.status >= 200 && this.status < 400) {
                        markBasketSignal('xhr:' + (this.__oaiCartPopupUrl || ''));
                    }
                });
            }

            return originalSend.apply(this, arguments);
        };
    }

    function installJqueryWatcher() {
        if (!window.jQuery || !window.jQuery(document).ajaxComplete) {
            return;
        }

        window.jQuery(document).ajaxComplete(function (event, xhr, settings) {
            if (!state.waiting || !settings || !requestLooksLikeBasket(settings.url || '')) {
                return;
            }

            if (xhr && xhr.status >= 200 && xhr.status < 400) {
                markBasketSignal('jquery:' + settings.url);
            }
        });
    }

    function installBasketCounterWatcher() {
        var observer = new MutationObserver(function () {
            if (!state.waiting) {
                return;
            }

            var currentCount = getBasketCountText();
            if (currentCount !== '' && currentCount !== state.basketCountBefore) {
                markBasketSignal('basket-counter');
            }
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true,
            characterData: true,
            attributes: true
        });
    }

    function installBitrixWatcher() {
        if (!window.BX || typeof window.BX.addCustomEvent !== 'function') {
            return;
        }

        BX.addCustomEvent('OnBasketChange', function () {
            markBasketSignal('BX.OnBasketChange');
        });

        BX.addCustomEvent('OnBasketResult', function () {
            markBasketSignal('BX.OnBasketResult');
        });
    }

    deliveryTitleNode.textContent = config.deliveryTitle;
    deliveryBadgeNode.textContent = config.deliveryBadge;
    detectCartLinks();

    closeBtn.addEventListener('click', closePopup);
    continueBtn.addEventListener('click', closePopup);

    overlay.addEventListener('click', function (event) {
        if (event.target === overlay) {
            closePopup();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && overlay.classList.contains('is-open')) {
            closePopup();
        }
    });

    document.addEventListener('click', function (event) {
        var result = getAddToCartElement(event.target);
        if (!result || !result.node) {
            return;
        }

        startWaiting(detectProductData(result.node), result.confidence);
    }, true);

    installFetchWatcher();
    installXhrWatcher();
    installJqueryWatcher();
    installBasketCounterWatcher();
    installBitrixWatcher();
})();
</script>
