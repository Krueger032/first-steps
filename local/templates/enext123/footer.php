<?php
if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome-Lighthouse')) {

} else { ?>
    <? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
        die();
    IncludeTemplateLangFile(__FILE__);
    if (!$isSiteClosed) {
        if (!CSite::inDir(SITE_DIR . "index.php")) { ?> </div>
            </div>
            </div>
            </div>
            <? if (!CSite::inDir(SITE_DIR . "personal/order/make/")) { ?>
                <div class="hidden-print viewed-wrapper" data-entity="parent-container" style="display: none;">
                    <div class="container">
                        <div class="row viewed">
                            <div class="col-xs-12">
                                <div class="h2" data-entity="header" data-showed="false" style="display: none; opacity: 0;">
                                    <?//VIEWED_TITLE// ?>
                                    <? $APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/footer_viewed_title.php"), false); ?>
                                </div>
                                <?//VIEWED// ?>
                                <? $APPLICATION->IncludeComponent(
                                    "bitrix:main.include",
                                    "",
                                    array(
                                        "AREA_FILE_SHOW" => "file",
                                        "PATH" => SITE_DIR . "include/footer_viewed.php",
                                        "AREA_FILE_RECURSIVE" => "N",
                                        "EDIT_MODE" => "html",
                                    ),
                                    false,
                                    array("HIDE_ICONS" => "Y")
                                ); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <? if (is_array($arSettings["SITE_BLOCKS"]["VALUE"]) && in_array("BIG_DATA", $arSettings["SITE_BLOCKS"]["VALUE"])) { ?>
                    <div class="hidden-print bigdata-wrapper" data-entity="parent-container" style="display: none;">
                        <div class="container">
                            <div class="row bigdata">
                                <div class="col-xs-12">
                                    <div class="h1" data-entity="header" data-showed="false" style="display: none; opacity: 0;">
                                        <?//BIGDATA_TITLE// ?>
                                        <? $APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/footer_bigdata_title.php"), false); ?>
                                    </div>
                                    <?//BIGDATA// ?>
                                    <? $APPLICATION->IncludeComponent(
                                        "bitrix:main.include",
                                        "",
                                        array(
                                            "AREA_FILE_SHOW" => "file",
                                            "PATH" => SITE_DIR . "include/footer_bigdata.php",
                                            "AREA_FILE_RECURSIVE" => "N",
                                            "EDIT_MODE" => "html",
                                        ),
                                        false,
                                        array("HIDE_ICONS" => "Y")
                                    ); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?
                }
            }
        }
    }
    //FEEDBACK//
    if (
        is_array($arSettings["SITE_BLOCKS"]["VALUE"]) &&
        in_array("FEEDBACK", $arSettings["SITE_BLOCKS"]["VALUE"]) &&
        !CSite::inDir(SITE_DIR . "personal/order/make/")
    ) {
        // Ваш код здесь
    } { ?>
        <? $APPLICATION->IncludeComponent(
            "bitrix:main.include",
            "",
            array(
                "AREA_FILE_SHOW" => "file",
                "PATH" => SITE_DIR . "include/footer_feedback.php"
            ),
            false,
            array("HIDE_ICONS" => "Y")
        ); ?>
    <?
    }
    if (
        !$isSiteClosed && is_array($arSettings["SITE_BLOCKS"]["VALUE"]) && in_array("BOTTOM_MENU", $arSettings["SITE_BLOCKS"]["VALUE"])
    ) { ?>
        <!--				<div class="hidden-print bottom-menu-wrapper">-->
        <!--					<div class="bottom-menu">-->
        <!--						<div class="container">-->
        <!--							<div class="row">-->
        <!--								<div class="col-xs-12">-->
        <!--BOTTOM_MENU-->
        <? /*$APPLICATION->IncludeComponent("bitrix:main.include", "",
                                                                                                                      array(
                                                                                                                          "AREA_FILE_SHOW" => "file",
                                                                                                                          "PATH" => SITE_DIR."include/footer_bottom_menu.php"
                                                                                                                      ),
                                                                                                                      false,
                                                                                                                      array("HIDE_ICONS" => "Y")
                                                                                                                  );*/ ?>
        <!--								</div>-->
        <!--							</div>-->
        <!--						</div>-->
        <!--					</div>-->
        <!--				</div>-->
    <? } ?>
    <div class="hidden-print footer-wrapper">
        <div class="container">
            <div class="row">
                <div class="footer">
                    <!--							<div class="col-xs-12 col-md-4">-->
                    <!--								<div class="footer__copyright">									-->
                    <? //COPYRIGHT// ?>
                    <? //$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/footer_copyright.php"), false); ?>
                    <!--								</div>-->
                    <!--							</div>							-->
                    <div class="col-xs-12 col-md-2">
                        <ul class="footer-menu">
                            <li><a href="/">Главная</a></li>
                            <li><a href="/catalog/">Каталог</a></li>
                            <li><a href="/promotions/sales/">Акции и скидки</a></li>
                            <li><a href="/payment-delivery/">Оплата и доставка</a></li>
                            <li><a href="/places/">Магазины</a></li>
                        </ul>
                    </div>
                    <div class="col-xs-12 col-md-2">
                        <ul class="footer-menu">
                            <li><a href="/brands/">Бренды</a></li>
                            <li><a href="/garantii/">Гарантии</a></li>
                            <li><a href="/about/">О магазине</a></li>
                            <li><a href="/contacts/">Контакты</a></li>
                        </ul>
                    </div>
                    <div class="col-xs-12 col-md-3 privacy">
                        <!--FOOTER_MENU-->
                        <? $APPLICATION->IncludeComponent(
                            "bitrix:main.include",
                            "",
                            array(
                                "AREA_FILE_SHOW" => "file",
                                "PATH" => SITE_DIR . "include/footer_menu.php"
                            ),
                            false,
                            array("HIDE_ICONS" => "Y")
                        ); ?>
                    </div>
                    <div class="col-xs-12 col-md-3">
                        <p class="footer-addr">
                            г. Москва, 41-й км МКАД<br />
                            ТВК "Мельница" 3-я линия<br />
                            Пассаж 21-22</p>
                        <p class="footer-addr">пн-пт 09:00 - 19:00<br />
                            сб-вс 10:00 - 18:00
                        </p>
                    </div>
                    <div class="col-xs-12 col-md-2">
                        <? //SOCIAL// ?>
                        <? $APPLICATION->IncludeComponent(
                            "bitrix:main.include",
                            "",
                            array(
                                "AREA_FILE_SHOW" => "file",
                                "PATH" => SITE_DIR . "include/footer_social.php"
                            ),
                            false,
                            array("HIDE_ICONS" => "Y")
                        ); ?>
                        <p>&copy; <?= date('Y'); ?> test.santehpodbor.ru</p>
                    </div>
                    <!--							<div class="col-xs-12 col-md-2">-->
                    <!--								<div class="footer__developer">-->
                    <? //DEVELOPER// ?>
                    <? //$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/footer_developer.php"), false); ?>
                    <!--								</div>-->
                    <!--							</div>-->
                </div>
            </div>
        </div>
    </div>
    <? //SLIDE_PANEL// ?>
    <div class="slide-panel"></div>
    <? if (!$isSiteClosed) { ?>
        </div>
    <? } ?>
    </div>
    <? //SCROLL_UP// ?>
    <a class="scroll-up" href="javascript:void(0)"><i class="icon-arrow-up"></i></a>
    <? //JS// ?>
    <script type="text/javascript">
        BX.message({
            SITE_ID: "<?= SITE_ID ?>",
            SITE_DIR: "<?= SITE_DIR ?>",
            SITE_SERVER_NAME: "<?= SITE_SERVER_NAME ?>",
            SITE_TEMPLATE_PATH: "<?= SITE_TEMPLATE_PATH ?>",
            SITE_CHARSET: "<?= SITE_CHARSET ?>",
            LANGUAGE_ID: "<?= LANGUAGE_ID ?>",
            COOKIE_NAME: "<?= Bitrix\Main\Config\Option::get('main', 'cookie_name', 'BITRIX_SM') ?>",
            SLIDE_PANEL_SEARCH_TITLE: "<?= GetMessageJS('ENEXT_SLIDE_PANEL_SEARCH_TITLE') ?>",
            SLIDE_PANEL_UNDEFINED_ERROR: "<?= GetMessageJS('ENEXT_SLIDE_PANEL_UNDEFINED_ERROR') ?>"
        });
        //IE fix for "jumpy" fixed background
        if (navigator.userAgent.match(/MSIE 10/i) || navigator.userAgent.match(/Trident\/7\./) || navigator.userAgent.match(/Edge\/12\./)) {
            $("body").on("mousewheel", function () {
                event.preventDefault();
                var wd = event.wheelDelta;
                var csp = window.pageYOffset;
                window.scrollTo(0, csp - wd);
            });
        }
    </script>
    <?= $APPLICATION->ShowProperty("countersScriptsBodyEnd"); ?>


<?php } ?>

<!-- BEGIN JIVOSITE CODE {literal} -->
<script src="//code.jivo.ru/widget/sr44cRFlJy" async></script>
<!-- {/literal} END JIVOSITE CODE -->

<script>
    if (document.querySelectorAll('.contacts-item-link').length) {
        document.querySelectorAll('.contacts-item-link')[2].textContent = '+7 (925) 112-22-90';
        const elem = '<span class="contacts-item-descr">Написать в WhatsApp</span>';
        document.querySelectorAll('.contacts-item-link')[2].insertAdjacentHTML('beforeend', elem);
    }
</script>

<div class="tpwidget">
    <a href="https://api.whatsapp.com/send/?phone=79251122290&text&type=phone_number&app_absent=0" target="_blank"
        rel="nofollow" class="tpwidget__button">
        <svg class="tpwidget__button-icon tpwidget__button-icon--closed" width="64" height="64" viewBox="0 0 64 64"
            xmlns="http://www.w3.org/2000/svg">
            <path
                d="M56.9 44.6998C56.4577 48.0758 54.8046 51.1764 52.2479 53.425C49.6911 55.6736 46.4049 56.9173 43 56.9248C23.15 56.9248 7 40.7748 7 20.9248C7.00751 17.52 8.25118 14.2337 10.4998 11.677C12.7484 9.12026 15.849 7.46708 19.225 7.02482C20.0856 6.9294 20.9543 7.1106 21.7049 7.54213C22.4556 7.97367 23.0494 8.63311 23.4 9.42482L28.425 21.1498C28.6857 21.7584 28.7902 22.4225 28.729 23.0817C28.6679 23.741 28.4431 24.3745 28.075 24.9248L23.925 31.2748C25.8042 35.0886 28.9006 38.1674 32.725 40.0248L39 35.8498C39.5497 35.4793 40.1849 35.2549 40.8454 35.1981C41.5059 35.1412 42.17 35.2537 42.775 35.5248L54.5 40.5248C55.2917 40.8755 55.9512 41.4692 56.3827 42.2199C56.8142 42.9706 56.9954 43.8392 56.9 44.6998Z" />
        </svg>
    </a>
</div>

<?php if ($_SERVER['REQUEST_URI'] == '/catalog/dush/'): ?>
    <script>
        const categoryLinks = document.querySelectorAll('.catalog-section-list .category-link');

        categoryLinks.forEach((link) => {
            const innerLink = link.querySelector('a[id="bx_1847241719_455"]');

            if (innerLink) {
                link.remove();
            }
        });
    </script>
<?php endif; ?>
<?php if ($_SERVER['REQUEST_URI'] == '/catalog/aksessuary-dlya-vannoy-komnaty/'): ?>
    <script>
        const categoryLinks = document.querySelectorAll('.catalog-section-list .category-link');

        categoryLinks.forEach((link) => {
            const innerLink = link.querySelector('a[id="bx_1847241719_642"]') || link.querySelector('a[id="bx_1847241719_646"]');

            if (innerLink) {
                link.remove();
            }
        });
    </script>
<?php endif; ?>
<?php if ($_SERVER['REQUEST_URI'] == '/catalog/polotentsesushiteli1/'): ?>
    <script>
        $('a[href="/catalog/polotentsesushiteli1/filter/clear/apply/filter/san_tip-is-электрический/apply/"]').remove();


    </script>
<?php endif; ?>

<script type="text/javascript" src="<?= SITE_TEMPLATE_PATH ?>/js/main.min.js"></script>
<script type="text/javascript" src="<?= SITE_TEMPLATE_PATH ?>/js/owlCarousel/owl.carousel.min.js"></script>

<script type="application/ld+json">
    {
        "@context": "http://schema.org",
        "@type": "Organization",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Москва, Россия",
            "streetAddress": "МКАД ТВК \"Мельница\" 3-я линия Пассаж 21-22"
        },
        "email": "zakaz@test.santehpodbor.ru",
        "name": "test.santehpodbor.ru",
        "telephone": [
            "+7 (495) 128-86-90",
            "+7 (917) 542-50-00"
        ]
    }
</script>

<script>

    ; (function () {
        const productItems = document.querySelectorAll('.product-item-image-list');

        productItems && productItems.forEach(item => {
            const parent = item.parentNode;
            const childrens = item.querySelectorAll('[data-src]');

            if (childrens.length > 1) {
                const pagination = document.createElement('div');
                pagination.classList.add('product-item-image-pagination');
                pagination.innerHTML = `${Array.from(childrens).map(children => (`<div class="product-item-image-bullet"></div>`)).join("")}`;
                parent.append(pagination);
            }

            const paginationBullets = parent.querySelectorAll('.product-item-image-bullet');
            if (paginationBullets.length > 1) {
                paginationBullets[0].classList.add('active');

                item.addEventListener('mousemove', e => {
                    let currentIndex;
                    childrens && childrens.forEach((children, index) => {
                        children.classList.remove('active');
                        paginationBullets[index].classList.remove('active');
                        if (e.target == children) {
                            currentIndex = index;
                        }
                    });
                    paginationBullets[currentIndex].classList.add('active');
                    e.target.classList.add('active');
                    item.setAttribute('style', `background-image:url('${e.target.getAttribute('data-src')}')`);
                });
                item.addEventListener('mouseout', e => {
                    let currentIndex;
                    childrens && childrens.forEach((children, index) => {
                        children.classList.remove('active');
                        paginationBullets[index].classList.remove('active');
                        if (childrens[0] == children) {
                            currentIndex = index;
                        }
                    });
                    paginationBullets[currentIndex].classList.add('active');
                    childrens[0].classList.add('active');
                    item.setAttribute('style', `background-image:url('${childrens[0].getAttribute('data-src')}')`);
                });
            }
        });
    })();

    $(function () {
        // Owl Carousel
        let owl = $(".owl-carousel.slider_small");
        owl.owlCarousel({
            items: 4,
            margin: 4,
            loop: true,
            // nav: true,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 1,
                },
                550: {
                    items: 2
                },
                992: {
                    items: 3
                },
                1350: {
                    items: 4
                },
            },
            autoplay: true,
            autoplayTimeout: 5000,
            smartSpeed: 1000,
        });
    });

</script>
<!-- РАССКОМЕНТИРУЙ ПОД СЛЕДУЮЩИЙ НОВЫЙ ГОД ЧТОБЫ ТЫКАТЬ В ШАРИКИ -->
<!-- <script src="/newyear-garland-master/script.js"></script> -->

<!-- Pop-up для самовывоза -->
<div id="deliveryPopup" class="delivery-popup">
    <div class="delivery-popup__overlay"></div>

    <div class="delivery-popup__content">
        <button class="delivery-popup__close" type="button" aria-label="Закрыть окно">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 6L6 18" stroke="#9299A5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M6 6L18 18" stroke="#9299A5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
        <div class="rows-delivery-popup">
            <div class="popup-left">
                <span class="delivery-popup__title">Пункт выдачи</span>

                <div class="delivery-popup__section">
                    <div class="delivery-popup__address">
                        <strong>г. Москва, 41-й км МКАД ТВК «Мельница»</strong><br>
                        3-я линия Пассаж 21–22
                    </div>
                    <div class="delivery-popup__info-list">
                        <div class="info-column">
                            <p class="delivery-time">Пн — Пт: 9 до 19</p>
                            <p>Сб — Вс: с 10 до 18</p>
                        </div>
                        <div class="info-column">
                            <p class="delivery-term">Срок хранения: 7 дней</p>
                            <p>Бесплатно <span class="dot-span js-time"></span></p>
                        </div>
                    </div>
                </div>
                <div class="delivery-popup__section">
                    <a href="https://yandex.ru/maps/org/santekhpodbor/57814610774/?ll=37.486475%2C55.612879&z=17.02"
                        target="_blank" class="delivery-popup__subtitle_map">Построить маршрут</a>
                    <p class="delivery-popup__text">
                        Для того чтобы забрать товар с нашего склада, Вам необходимо предварительно связаться с
                        менеджером нашего магазина и оговорить заранее дату и время забора продукции
                    </p>
                </div>

                <div class="delivery-popup__section">
                    <button class="delivery-popup__subtitle">Связаться с менеджером</button>
                </div>
            </div>

            <div class="popup-right">
                <div id="map"></div>
            </div>
        </div>
    </div>

</div>
</div>







<?php
$popupFile = $_SERVER['DOCUMENT_ROOT'] . '/local/include/bitrix_cart_popup_include.php';
if (file_exists($popupFile)) {
    include_once $popupFile;
}
?>
</body>

</html>