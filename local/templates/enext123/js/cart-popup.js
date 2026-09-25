(function(window, document) {
    'use strict';

    if (window.ENEXTCartPopup) {
        return;
    }

    var STYLE_ID = 'enext-cart-popup-styles';
    var ROOT_ID = 'enext-cart-popup-root';
    var DELIVERY_TEXT = 'Доставка по Москве при заказе от 1000 ₽ — бесплатно';

    function injectStyles() {
        if (document.getElementById(STYLE_ID)) {
            return;
        }

        var style = document.createElement('style');
        style.id = STYLE_ID;
        style.type = 'text/css';
        style.textContent = [
            '.enext-cart-popup-overlay{position:fixed;inset:0;background:rgba(38,50,56,.45);z-index:9998;opacity:0;transition:opacity .2s ease;}',
            '.enext-cart-popup-overlay.is-visible{opacity:1;}',
            '.enext-cart-popup{position:fixed;left:50%;top:50%;transform:translate(-50%,-50%) scale(.96);width:calc(100% - 32px);max-width:760px;background:#fff;border-radius:14px;box-shadow:0 24px 80px rgba(38,50,56,.24);z-index:9999;opacity:0;transition:opacity .2s ease,transform .2s ease;overflow:hidden;font-family:"Museo Sans Cyrl 300",sans-serif;color:#263238;}',
            '.enext-cart-popup.is-visible{opacity:1;transform:translate(-50%,-50%) scale(1);}',
            '.enext-cart-popup *{box-sizing:border-box;}',
            '.enext-cart-popup__close{position:absolute;top:18px;right:18px;width:40px;height:40px;border:none;border-radius:50%;background:#f1f6f7;cursor:pointer;font-size:20px;line-height:1;color:#566b75;display:flex;align-items:center;justify-content:center;}',
            '.enext-cart-popup__close:hover{background:#e3ecef;}',
            '.enext-cart-popup__body{padding:32px 32px 24px;}',
            '.enext-cart-popup__title{margin:0 56px 24px 0;font-family:"Museo Sans Cyrl 700",sans-serif;font-size:28px;line-height:1.2;color:#263238;}',
            '.enext-cart-popup__content{display:flex;align-items:flex-start;gap:24px;}',
            '.enext-cart-popup__image-wrap{flex:0 0 160px;width:160px;height:160px;border:1px solid #e8edef;border-radius:12px;background:#fff;display:flex;align-items:center;justify-content:center;padding:12px;}',
            '.enext-cart-popup__image{max-width:100%;max-height:100%;object-fit:contain;}',
            '.enext-cart-popup__info{flex:1 1 auto;min-width:0;}',
            '.enext-cart-popup__product-name{margin:0 0 16px;font-family:"Museo Sans Cyrl 500",sans-serif;font-size:22px;line-height:1.35;color:#263238;}',
            '.enext-cart-popup__price-row{display:flex;align-items:center;justify-content:space-between;gap:20px;}',
            '.enext-cart-popup__price{font-family:"Museo Sans Cyrl 700",sans-serif;font-size:28px;line-height:1;color:#263238;white-space:nowrap;}',
            '.enext-cart-popup__qty{display:flex;align-items:center;border:1px solid #e3ecef;border-radius:10px;overflow:hidden;background:#fff;}',
            '.enext-cart-popup__qty-btn{width:42px;height:42px;border:none;background:#fff;color:#263238;font-size:24px;line-height:1;cursor:pointer;display:flex;align-items:center;justify-content:center;}',
            '.enext-cart-popup__qty-btn:hover{background:#f1f6f7;}',
            '.enext-cart-popup__qty-value{min-width:48px;height:42px;padding:0 12px;display:flex;align-items:center;justify-content:center;font-family:"Museo Sans Cyrl 500",sans-serif;font-size:18px;border-left:1px solid #e3ecef;border-right:1px solid #e3ecef;}',
            '.enext-cart-popup__actions{display:flex;gap:14px;margin-top:28px;flex-wrap:wrap;}',
            '.enext-cart-popup__btn{height:52px;padding:0 28px;border-radius:10px;border:1px solid #6639b6;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;font-family:"Museо Sans Cyrl 500",sans-serif;font-size:16px;line-height:1;cursor:pointer;transition:all .15s ease;}',
            '.enext-cart-popup__btn--primary{background:#6639b6;color:#fff;}',
            '.enext-cart-popup__btn--primary:hover{background:#5a31a3;border-color:#5a31a3;color:#fff;}',
            '.enext-cart-popup__btn--secondary{background:#fff;color:#6639b6;}',
            '.enext-cart-popup__btn--secondary:hover{background:#f8f4ff;color:#6639b6;}',
            '.enext-cart-popup__delivery{padding:18px 32px;background:#f8f9fb;border-top:1px solid #e8edef;display:flex;align-items:center;gap:12px;font-size:15px;line-height:1.45;color:#566b75;}',
            '.enext-cart-popup__delivery-icon{flex:0 0 22px;width:22px;height:22px;border-radius:50%;background:#6639b6;color:#fff;display:flex;align-items:center;justify-content:center;font-size:14px;font-family:Arial,sans-serif;}',
            '@media (max-width: 767px){.enext-cart-popup__body{padding:24px 20px 20px;}.enext-cart-popup__title{font-size:22px;margin-right:40px;}.enext-cart-popup__content{flex-direction:column;gap:18px;}.enext-cart-popup__image-wrap{width:120px;height:120px;flex-basis:120px;}.enext-cart-popup__product-name{font-size:18px;}.enext-cart-popup__price-row{align-items:flex-start;flex-direction:column;gap:14px;}.enext-cart-popup__price{font-size:24px;}.enext-cart-popup__actions{flex-direction:column;}.enext-cart-popup__btn{width:100%;}.enext-cart-popup__delivery{padding:16px 20px;align-items:flex-start;}}'
        ].join('');

        document.head.appendChild(style);
    }

    function escapeHtml(value) {
        return String(value == null ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatNumber(value) {
        var number = Number(value || 0);
        if (!isFinite(number)) {
            number = 0;
        }

        return number.toLocaleString('ru-RU');
    }

    function formatPrice(value, currency) {
        var symbol = currency || '₽';
        return formatNumber(value) + ' ' + symbol;
    }

    function normalizeQuantity(value) {
        var quantity = parseFloat(value);
        if (!isFinite(quantity) || quantity <= 0) {
            quantity = 1;
        }

        if (Math.floor(quantity) === quantity) {
            return String(quantity);
        }

        return String(quantity).replace('.', ',');
    }

    function closePopup() {
        var root = document.getElementById(ROOT_ID);
        if (!root) {
            return;
        }

        var overlay = root.querySelector('.enext-cart-popup-overlay');
        var popup = root.querySelector('.enext-cart-popup');

        if (overlay) {
            overlay.classList.remove('is-visible');
        }

        if (popup) {
            popup.classList.remove('is-visible');
        }

        setTimeout(function() {
            if (root.parentNode) {
                root.parentNode.removeChild(root);
            }
            document.removeEventListener('keydown', onKeyDown);
        }, 220);
    }

    function onKeyDown(event) {
        if (event.key === 'Escape') {
            closePopup();
        }
    }

    function bindQuantity(root) {
        var qtyValue = root.querySelector('.enext-cart-popup__qty-value');
        var minus = root.querySelector('.enext-cart-popup__qty-btn--minus');
        var plus = root.querySelector('.enext-cart-popup__qty-btn--plus');

        if (!qtyValue || !minus || !plus) {
            return;
        }

        function getNumericValue() {
            var value = qtyValue.textContent.replace(',', '.');
            var numeric = parseFloat(value);
            return isFinite(numeric) && numeric > 0 ? numeric : 1;
        }

        minus.addEventListener('click', function() {
            var current = getNumericValue();
            current = current > 1 ? current - 1 : 1;
            qtyValue.textContent = normalizeQuantity(current);
        });

        plus.addEventListener('click', function() {
            var current = getNumericValue();
            current += 1;
            qtyValue.textContent = normalizeQuantity(current);
        });
    }

    function buildPopup(data) {
        injectStyles();
        closePopup();

        var root = document.createElement('div');
        root.id = ROOT_ID;

        var image = data.image || '/local/templates/enext/images/no_photo.png';
        var productName = escapeHtml(data.name || 'Товар');
        var priceText = data.priceText || formatPrice(data.price, data.currencySymbol);
        var quantityText = normalizeQuantity(data.quantity);
        var cartUrl = data.cartUrl || '/personal/cart/';
        var continueLabel = escapeHtml(data.continueLabel || 'Продолжить покупки');
        var cartLabel = escapeHtml(data.cartLabel || 'Перейти в корзину');
        var title = escapeHtml(data.title || 'Товар добавлен в корзину');
        var deliveryText = escapeHtml(data.deliveryText || DELIVERY_TEXT);

        root.innerHTML = '' +
            '<div class="enext-cart-popup-overlay"></div>' +
            '<div class="enext-cart-popup" role="dialog" aria-modal="true" aria-label="' + title + '">' +
                '<button class="enext-cart-popup__close" type="button" aria-label="Закрыть">×</button>' +
                '<div class="enext-cart-popup__body">' +
                    '<h3 class="enext-cart-popup__title">' + title + '</h3>' +
                    '<div class="enext-cart-popup__content">' +
                        '<div class="enext-cart-popup__image-wrap">' +
                            '<img class="enext-cart-popup__image" src="' + escapeHtml(image) + '" alt="' + productName + '">' +
                        '</div>' +
                        '<div class="enext-cart-popup__info">' +
                            '<div class="enext-cart-popup__product-name">' + productName + '</div>' +
                            '<div class="enext-cart-popup__price-row">' +
                                '<div class="enext-cart-popup__price">' + escapeHtml(priceText) + '</div>' +
                                '<div class="enext-cart-popup__qty">' +
                                    '<button class="enext-cart-popup__qty-btn enext-cart-popup__qty-btn--minus" type="button" aria-label="Уменьшить">−</button>' +
                                    '<div class="enext-cart-popup__qty-value">' + escapeHtml(quantityText) + '</div>' +
                                    '<button class="enext-cart-popup__qty-btn enext-cart-popup__qty-btn--plus" type="button" aria-label="Увеличить">+</button>' +
                                '</div>' +
                            '</div>' +
                            '<div class="enext-cart-popup__actions">' +
                                '<a class="enext-cart-popup__btn enext-cart-popup__btn--primary" href="' + escapeHtml(cartUrl) + '">' + cartLabel + '</a>' +
                                '<button class="enext-cart-popup__btn enext-cart-popup__btn--secondary enext-cart-popup__continue" type="button">' + continueLabel + '</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="enext-cart-popup__delivery">' +
                    '<span class="enext-cart-popup__delivery-icon">✓</span>' +
                    '<span>' + deliveryText + '</span>' +
                '</div>' +
            '</div>';

        document.body.appendChild(root);

        var overlay = root.querySelector('.enext-cart-popup-overlay');
        var popup = root.querySelector('.enext-cart-popup');
        var closeButton = root.querySelector('.enext-cart-popup__close');
        var continueButton = root.querySelector('.enext-cart-popup__continue');

        if (overlay) {
            overlay.addEventListener('click', closePopup);
        }

        if (closeButton) {
            closeButton.addEventListener('click', closePopup);
        }

        if (continueButton) {
            continueButton.addEventListener('click', closePopup);
        }

        bindQuantity(root);
        document.addEventListener('keydown', onKeyDown);

        setTimeout(function() {
            if (overlay) {
                overlay.classList.add('is-visible');
            }
            if (popup) {
                popup.classList.add('is-visible');
            }
        }, 10);
    }

    window.ENEXTCartPopup = {
        show: function(data) {
            if (!data || typeof data !== 'object') {
                return;
            }
            buildPopup(data);
        },
        close: closePopup
    };
})(window, document);
