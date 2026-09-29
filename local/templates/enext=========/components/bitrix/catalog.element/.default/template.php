<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\Localization\Loc;
use Itb\Helpers\DeliveryDateCalc;

$this->setFrameMode(true);

$arSettings = CEnext::GetFrontParametrsValues(SITE_ID);
$minOrderSum = intval($arSettings["MIN_ORDER_SUM"]);

$this->addExternalCss(SITE_TEMPLATE_PATH . "/js/owlCarousel/owl.carousel.min.css");
$this->addExternalJS(SITE_TEMPLATE_PATH . "/js/owlCarousel/owl.carousel.min.js");

$templateLibrary = array("popup", "fx");
$currencyList = "";

if (!empty($arResult["CURRENCIES"])) {
    $templateLibrary[] = "currency";
    $currencyList = CUtil::PhpToJSObject($arResult["CURRENCIES"], false, true, true);
}

$templateData = array(
    "TEMPLATE_LIBRARY" => $templateLibrary,
    "CURRENCIES" => $currencyList
);
unset($currencyList, $templateLibrary);

$mainId = $this->GetEditAreaId($arResult["ID"] . (!empty($arParams["RCM_ID"]) ? "_" . md5($arParams["RCM_ID"]) : ""));
$obName = $templateData["JS_OBJ"] = "ob" . preg_replace("/[^a-zA-Z0-9_]/", "x", $mainId);
$itemIds = array(
    "ID" => $mainId,
    "JS_NAME" => $obName,
    "DISCOUNT_PERCENT_ID" => $mainId . "_dsc_pict",
    "BIG_SLIDER_ID" => $mainId . "_big_slider",
    "SLIDER_CONT_ID" => $mainId . "_slider_cont",
    "ARTICLE_ID" => $mainId . "_article",
    "OLD_PRICE_ID" => $mainId . "_old_price",
    "PRICE_ID" => $mainId . "_price",
    "DISCOUNT_PRICE_ID" => $mainId . "_price_discount",
    "SLIDER_CONT_OF_ID" => $mainId . "_slider_cont_",
    "QUANTITY_ID" => $mainId . "_quantity",
    "QUANTITY_DOWN_ID" => $mainId . "_quant_down",
    "QUANTITY_UP_ID" => $mainId . "_quant_up",
    "PC_QUANTITY_ID" => $mainId . "_pc_quantity",
    "PC_QUANTITY_DOWN_ID" => $mainId . "_pc_quant_down",
    "PC_QUANTITY_UP_ID" => $mainId . "_pc_quant_up",
    "SQ_M_QUANTITY_ID" => $mainId . "_sq_m_quantity",
    "SQ_M_QUANTITY_DOWN_ID" => $mainId . "_sq_m_quant_down",
    "SQ_M_QUANTITY_UP_ID" => $mainId . "_sq_m_quant_up",
    "QUANTITY_MEASURE" => $mainId . "_quant_measure",
    "QUANTITY_LIMIT" => $mainId . "_quant_limit",
    "QUANTITY_LIMIT_NOT_AVAILABLE" => $mainId . "_quant_limit_not_avl",
    "TOTAL_COST_ID" => $mainId . "_total_cost",
    "BUY_LINK" => $mainId . "_buy_link",
    "ADD_BASKET_LINK" => $mainId . "_add_basket_link",
    "BASKET_ACTIONS_ID" => $mainId . "_basket_actions",
    "PARTNERS_LINK" => $mainId . "_partners_link",
    "PARTNERS_ID" => $mainId . "_partners",
    "ASK_PRICE_LINK" => $mainId . "_ask_price",
    "NOT_AVAILABLE_MESS" => $mainId . "_not_avail",
    "COMPARE_LINK" => $mainId . "_compare_link",
    "QUICK_ORDER_LINK" => $mainId . "_quick_order",
    "DELAY_LINK" => $mainId . "_delay_link",
    "SELECT_SKU_LINK" => $mainId . "_select_sku_link",
    "TREE_ID" => $mainId . "_skudiv",
    "DISPLAY_PROP_DIV" => $mainId . "_sku_prop",
    "DISPLAY_MAIN_PROP_DIV" => $mainId . "_main_sku_prop",
    "BASKET_PROP_DIV" => $mainId . "_basket_prop",
    "SUBSCRIBE_LINK" => $mainId . "_subscribe",
    "TABS_ID" => $mainId . "_tabs",
    "TAB_CONTAINERS_ID" => $mainId . "_tab_containers",
    "SKU_ITEMS_ID" => $mainId . "_sku_items",
    "CONSTRUCTOR_ID" => $mainId . "_constructor",
    "GEO_DELIVERY_ID" => $mainId . "_geo_delivery"
);

$name = !empty($arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"])
    ? $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]
    : $arResult["NAME"];

$title = !empty($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"])
    ? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"]
    : $arResult["NAME"];

$alt = !empty($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"])
    ? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"]
    : $arResult["NAME"];

$haveOffers = !empty($arResult["OFFERS"]);

if ($arSettings["AUTO_DELIVERY_CALCULATION"] == "Y" && (!$haveOffers || ($haveOffers && $arParams["OFFERS_VIEW"] != "LIST")))
    $this->addExternalCss("/bitrix/components/altop/geo.delivery.enext/templates/slide_panel/style.min.css");

if ($arSettings["OFFERS_ON_MAP"] == "Y" && $haveOffers && $arParams["OFFERS_VIEW"] == "OBJECTS")
    $this->addExternalCss("/bitrix/components/altop/map.yandex.view.enext/templates/.default/style.min.css");

if ($haveOffers) {
    $actualItem = isset($arResult["OFFERS"][$arResult["OFFERS_SELECTED"]]) ? $arResult["OFFERS"][$arResult["OFFERS_SELECTED"]] : reset($arResult["OFFERS"]);

    $showSliderControls = false;
    if ($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST") {
        foreach ($arResult["OFFERS"] as $offer) {
            if ($offer["MORE_PHOTO_COUNT"] > 1) {
                $showSliderControls = true;
                break;
            }
        }
        unset($offer);
    } else {
        $showSliderControls = $actualItem["MORE_PHOTO_COUNT"] > 1;
    }
} else {
    $actualItem = $arResult;
    $showSliderControls = $arResult["MORE_PHOTO_COUNT"] > 1;
}

$skuProps = array();
$price = $actualItem["ITEM_PRICES"][$actualItem["ITEM_PRICE_SELECTED"]];
$measureRatio = $actualItem["ITEM_MEASURE_RATIOS"][$actualItem["ITEM_MEASURE_RATIO_SELECTED"]]["RATIO"];
$showDiscount = $price["PERCENT"] > 0;

$isMeasurePc = $isMeasureSqM = false;
if ($actualItem["ITEM_MEASURE"]["SYMBOL_INTL"] == "pc. 1")
    $isMeasurePc = true;
elseif ($actualItem["ITEM_MEASURE"]["SYMBOL_INTL"] == "m2")
    $isMeasureSqM = true;

$showDescription = !empty($arResult["DETAIL_TEXT"]);
$showBuyBtn = in_array("BUY", $arParams["ADD_TO_BASKET_ACTION"]);
$showAddBtn = in_array("ADD", $arParams["ADD_TO_BASKET_ACTION"]);
$showSubscribe = $arParams["PRODUCT_SUBSCRIPTION"] === "Y" && ($arResult["CATALOG_SUBSCRIBE"] === "Y" || $haveOffers);

$object = !empty($arResult["PROPERTIES"]["OBJECT"]["FULL_VALUE"]) ? $arResult["PROPERTIES"]["OBJECT"]["FULL_VALUE"] : false;
$objectContacts = $object["PHONE_SMS"] || $object["EMAIL_EMAIL"] ? true : false;

if (!$haveOffers || $arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST")
    $partnersUrl = !empty($actualItem["PROPERTIES"]["PARTNERS_URL"]["VALUE"]) ? true : false;
else
    $partnersUrl = !empty($arResult["PROPERTIES"]["PARTNERS_URL"]["VALUE"]) ? true : false;

$moreProductsIds = !empty($arResult["PROPERTIES"]["MORE_PRODUCTS"]["VALUE"]) ? $arResult["PROPERTIES"]["MORE_PRODUCTS"]["VALUE"] : false;

$arParams["MESS_BTN_BUY"] = $arParams["MESS_BTN_BUY"] ?: Loc::getMessage("CT_BCE_CATALOG_BUY");
$arParams["MESS_BTN_ADD_TO_BASKET"] = $arParams["MESS_BTN_ADD_TO_BASKET"] ?: Loc::getMessage("CT_BCE_CATALOG_ADD");
$arParams["MESS_NOT_AVAILABLE"] = $arParams["MESS_NOT_AVAILABLE"] ?: Loc::getMessage("CT_BCE_CATALOG_NOT_AVAILABLE");
$arParams["MESS_BTN_COMPARE"] = $arParams["MESS_BTN_COMPARE"] ?: Loc::getMessage("CT_BCE_CATALOG_COMPARE");
$arParams["MESS_BTN_DELAY"] = $arParams["MESS_BTN_DELAY"] ?: Loc::getMessage("CT_BCE_CATALOG_DELAY");
$arParams["MESS_PRICE_RANGES_TITLE"] = $arParams["MESS_PRICE_RANGES_TITLE"] ?: Loc::getMessage("CT_BCE_CATALOG_PRICE_RANGES_TITLE");
$arParams["MESS_DESCRIPTION_TAB"] = $arParams["MESS_DESCRIPTION_TAB"] ?: Loc::getMessage("CT_BCE_CATALOG_DESCRIPTION_TAB");
$arParams["MESS_PROPERTIES_TAB"] = $arParams["MESS_PROPERTIES_TAB"] ?: Loc::getMessage("CT_BCE_CATALOG_PROPERTIES_TAB");
$arParams["MESS_REVIEWS_TAB"] = $arParams["MESS_REVIEWS_TAB"] ?: Loc::getMessage("CT_BCE_CATALOG_REVIEWS_TAB");
$arParams["MESS_SHOW_MAX_QUANTITY"] = $arParams["MESS_SHOW_MAX_QUANTITY"] ?: Loc::getMessage("CT_BCE_CATALOG_SHOW_MAX_QUANTITY");
$arParams["MESS_RELATIVE_QUANTITY_MANY"] = $arParams["MESS_RELATIVE_QUANTITY_MANY"] ?: Loc::getMessage("CT_BCE_CATALOG_RELATIVE_QUANTITY_MANY");
$arParams["MESS_RELATIVE_QUANTITY_FEW"] = $arParams["MESS_RELATIVE_QUANTITY_FEW"] ?: Loc::getMessage("CT_BCE_CATALOG_RELATIVE_QUANTITY_FEW"); ?>


<div class="bx-catalog-element" id="<?= $itemIds['ID'] ?>" itemscope itemtype="http://schema.org/Product">
    <?//TABS// ?>
    <div class="product-item-detail-tabs-container" id="<?= $itemIds['TABS_ID'] ?>">
        <div class="product-item-detail-tabs-block" data-entity="tabs">
            <div class="product-item-detail-tabs-scroll">
                <ul class="product-item-detail-tabs-list">
                    <?//TAB_DESCRIPTION// ?>
                    <li class="product-item-detail-tab active" data-entity="tab" data-value="description">
                        <?= $arParams["MESS_DESCRIPTION_TAB"] ?></li>
                    <?//TAB_PROPERTIES//
                    if ($arSettings["TAB_PROPERTIES"] == "Y" && (!empty($arResult["DISPLAY_PROPERTIES"]) || (($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST") && $arResult["SHOW_OFFERS_PROPS"]))) { ?>
                        <li class="product-item-detail-tab" data-entity="tab" data-value="properties">
                            <?= $arParams["MESS_PROPERTIES_TAB"] ?></li>
                    <? }
                    //TAB_FREE_TAB//
                    if (!empty($arResult["PROPERTIES"]["FREE_TAB"]["VALUE"])) { ?>
                        <li class="product-item-detail-tab" data-entity="tab" data-value="free-tab">
                            <?= $arResult["PROPERTIES"]["FREE_TAB"]["NAME"] ?></li>
                    <? }
                    //TAB_FILES_DOCS//
                    if (!empty($arResult["PROPERTIES"]["FILES_DOCS"]["FULL_VALUE"])) { ?>
                        <li class="product-item-detail-tab" data-entity="tab" data-value="files-docs">
                            <?= $arResult["PROPERTIES"]["FILES_DOCS"]["NAME"] ?><span><?= count($arResult["PROPERTIES"]["FILES_DOCS"]["FULL_VALUE"]) ?></span>
                        </li>
                    <? }
                    //TAB_STORES
                    if ($arParams["USE_STORE"] == "Y" && (!$haveOffers || ($haveOffers && ($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST")))) { ?>
                        <li class="product-item-detail-tab" data-entity="tab" data-value="stores">
                            <?= $arParams["MAIN_TITLE"] ?></li>
                    <? }
                    //TAB_ARTICLES//
                    if (!empty($arResult["PROPERTIES"]["ARTICLES"]["VALUE"])) { ?>
                        <li class="product-item-detail-tab" data-entity="tab" data-value="articles">
                            <?= $arResult["PROPERTIES"]["ARTICLES"]["NAME"] ?><span><?= count($arResult["PROPERTIES"]["ARTICLES"]["VALUE"]) ?></span>
                        </li>
                    <? }
                    //TAB_MORE_PRODUCTS//
                    if ($moreProductsIds) { ?>
                        <li class="product-item-detail-tab" data-entity="tab" data-value="more-products">
                            <?= $arResult["PROPERTIES"]["MORE_PRODUCTS"]["NAME"] ?><span><?= count($moreProductsIds) ?></span>
                        </li>
                    <? }
                    //TAB_REVIEWS//
                    if (isset($arResult["REVIEWS_COUNT"])) { ?>
                        <li class="product-item-detail-tab" data-entity="tab" data-value="reviews">
                            <?= $arParams["MESS_REVIEWS_TAB"] ?><span><?= $arResult["REVIEWS_COUNT"] ?></span></li>
                    <? } ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="product-item-detail-tabs-content" id="<?= $itemIds['TAB_CONTAINERS_ID'] ?>">
        <div class="row">
            <div class="col-xs-12 col-md-9" data-entity="product-container">

































                <div class="row" data-entity="tab-container" data-value="description">
                    <div class="col-xs-12 col-md-7">
                        <?//SLIDER// ?>
                        <div class="product-item-detail-slider-container<?= ($showSliderControls ? ' full' : '') ?>"
                            id="<?= $itemIds['BIG_SLIDER_ID'] ?>">
                            <span class="product-item-detail-slider-close" data-entity="close-popup"><i
                                    class="icon-close"></i></span>
                            <div
                                class="product-item-detail-slider-block<?= ($arParams['IMAGE_RESOLUTION'] === '1by1' ? ' product-item-detail-slider-block-square' : '') ?>">
                                <span class="product-item-detail-slider-left" data-entity="slider-control-left"
                                    style="display: none;"><i class="icon-arrow-left"></i></span>
                                <span class="product-item-detail-slider-right" data-entity="slider-control-right"
                                    style="display: none;"><i class="icon-arrow-right"></i></span>
                                <?//MARKERS// ?>
                                <div class="product-item-detail-markers">
                                    <? if ($arParams["SHOW_DISCOUNT_PERCENT"] === "Y") { ?>
                                        <span
                                            class="product-item-detail-marker-container<?= ($showDiscount ? '' : ' product-item-detail-marker-container-hidden') ?>"
                                            id="<?= $itemIds['DISCOUNT_PERCENT_ID'] ?>">
                                            <span
                                                class="product-item-detail-marker product-item-detail-marker-discount product-item-detail-marker-14px"><span
                                                    data-entity="dsc-perc-val"><?= -$price["PERCENT"] ?>%</span></span>
                                        </span>
                                    <? }
                                    if (!empty($arResult["PROPERTIES"]["MARKER"]["FULL_VALUE"])) {
                                        foreach ($arResult["PROPERTIES"]["MARKER"]["FULL_VALUE"] as $key => $arMarker) {
                                            if ($key <= 4) { ?>
                                                <span class="product-item-detail-marker-container">
                                                    <span
                                                        class="product-item-detail-marker<?= (!empty($arMarker['FONT_SIZE']) ? ' product-item-detail-marker-' . $arMarker['FONT_SIZE'] : '') ?>"
                                                        <?= (!empty($arMarker["BACKGROUND_1"]) && !empty($arMarker["BACKGROUND_2"]) ? " style='background: " . $arMarker["BACKGROUND_2"] . "; background: -webkit-linear-gradient(left, " . $arMarker["BACKGROUND_1"] . ", " . $arMarker["BACKGROUND_2"] . "); background: -moz-linear-gradient(left, " . $arMarker["BACKGROUND_1"] . ", " . $arMarker["BACKGROUND_2"] . "); background: -o-linear-gradient(left, " . $arMarker["BACKGROUND_1"] . ", " . $arMarker["BACKGROUND_2"] . "); background: -ms-linear-gradient(left, " . $arMarker["BACKGROUND_1"] . ", " . $arMarker["BACKGROUND_2"] . "); background: linear-gradient(to right, " . $arMarker["BACKGROUND_1"] . ", " . $arMarker["BACKGROUND_2"] . ");'" : (!empty($arMarker["BACKGROUND_1"]) && empty($arMarker["BACKGROUND_2"]) ? " style='background: " . $arMarker["BACKGROUND_1"] . ";'" : (empty($arMarker["BACKGROUND_1"]) && !empty($arMarker["BACKGROUND_2"]) ? " style='background: " . $arMarker["BACKGROUND_2"] . ";'" : ""))) ?>><?= (!empty($arMarker["ICON"]) ? "<i class='" . $arMarker["ICON"] . "'></i>" : "") ?><span><?= $arMarker["NAME"] ?></span></span>
                                                </span>
                                            <? } else {
                                                break;
                                            }
                                        }
                                        unset($key, $arMarker);
                                    } ?>
                                </div>
                                <?//MAGNIFIER_DELAY//
                                if (in_array("POPUP", $arParams["DETAIL_PICTURE_MODE"]) || (!$arParams["DISABLE_DELAY"] && (!$object || ($object && $objectContacts)) && (!$haveOffers || ($haveOffers && ($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST"))))) { ?>
                                    <div class="product-item-detail-icons-container">
                                        <? if (in_array("POPUP", $arParams["DETAIL_PICTURE_MODE"])) { ?>
                                            <div class="product-item-detail-magnifier">
                                                <i class="icon-scale-plus" data-entity="slider-magnifier"></i>
                                            </div>
                                        <? }
                                        if (!$arParams["DISABLE_DELAY"] && (!$object || ($object && $objectContacts)) && (!$haveOffers || ($haveOffers && ($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST")))) { ?>
                                            <div class="product-item-detail-delay" id="<?= $itemIds['DELAY_LINK'] ?>"
                                                title="<?= $arParams['MESS_BTN_DELAY'] ?>"
                                                style="display: <?= (!$partnersUrl && $actualItem['CAN_BUY'] && $price['PRICE'] > 0 ? '' : 'none') ?>;">
                                                <i class="icon-heart"></i></div>
                                        <? } ?>
                                    </div>
                                <? }
                                //SLIDER_IMAGES// ?>
                                <div class="product-item-detail-slider-videos-images-container"
                                    data-entity="videos-images-container">
                                    <? if (!empty($actualItem["MORE_PHOTO"])) {
                                        $activeKey = 0;
                                        foreach ($actualItem["MORE_PHOTO"] as $key => $photo) {
                                            if (!empty($photo["VALUE"])) {
                                                $activeKey++; ?>
                                                <div class="product-item-detail-slider-video" data-entity="video"
                                                    data-id="<?= $photo['ID'] ?>">
                                                    <iframe width="640" height="480"
                                                        src="<?= $arResult['SCHEME'] ?>://www.youtube.com/embed/<?= $photo['VALUE'] ?>?rel=0&showinfo=0&enablejsapi=1"
                                                        frameborder="0" allowfullscreen></iframe>
                                                </div>
                                            <? } else { ?>
                                                <div class="product-item-detail-slider-image<?= ($key == $activeKey ? ' active' : '') ?>"
                                                    data-entity="image" data-id="<?= $photo['ID'] ?>">
                                                    <img src="<?= $photo['SRC'] ?>" width="<?= $photo['WIDTH'] ?>"
                                                        height="<?= $photo['HEIGHT'] ?>" alt="<?= $alt ?>" title="<?= $title ?>">
                                                </div>
                                                <? if ($key == $activeKey) { ?>
                                                    <a style="display:none" href="<?= $photo['SRC'] ?>"
                                                        class="product-item-detail-slider-image<?= ($key == $activeKey ? ' active' : '') ?>"
                                                        data-entity="image" data-id="<?= $photo['ID'] ?>" itemprop="image">
                                                        <img src="<?= $photo['SRC'] ?>" width="<?= $photo['WIDTH'] ?>"
                                                            height="<?= $photo['HEIGHT'] ?>" alt="<?= $alt ?>" title="<?= $title ?>">
                                                    </a><? } ?>
                                            <? }
                                        }
                                        unset($key, $photo, $activeKey);
                                    }
                                    //SLIDER_PROGRESS//
                                    if ($arParams["SLIDER_PROGRESS"] === "Y") { ?>
                                        <div class="product-item-detail-slider-progress-bar"
                                            data-entity="slider-progress-bar" style="width: 0;"></div>
                                    <? } ?>
                                </div>
                                <?//BRAND//
                                if (!empty($arResult["PROPERTIES"]["BRAND"]["FULL_VALUE"]["PREVIEW_PICTURE"])) { ?>
                                    <div class="product-item-detail-brand">
                                        <img src="<?= $arResult['PROPERTIES']['BRAND']['FULL_VALUE']['PREVIEW_PICTURE']['SRC'] ?>"
                                            width="<?= $arResult['PROPERTIES']['BRAND']['FULL_VALUE']['PREVIEW_PICTURE']['WIDTH'] ?>"
                                            height="<?= $arResult['PROPERTIES']['BRAND']['FULL_VALUE']['PREVIEW_PICTURE']['HEIGHT'] ?>"
                                            alt="<?= $arResult['PROPERTIES']['BRAND']['FULL_VALUE']['NAME'] ?>"
                                            title="<?= $arResult['PROPERTIES']['BRAND']['FULL_VALUE']['NAME'] ?>" />
                                    </div>
                                <? } ?>
                            </div>
                            <?//SLIDER_CONTROLS//
                            if ($showSliderControls) {
                                if ($haveOffers) {
                                    if ($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST") {
                                        foreach ($arResult["OFFERS"] as $keyOffer => $offer) {
                                            if (!isset($offer["MORE_PHOTO_COUNT"]) || $offer["MORE_PHOTO_COUNT"] <= 0)
                                                continue;
                                            $strVisible = $arResult["OFFERS_SELECTED"] == $keyOffer ? "" : "none"; ?>
                                            <div class="hidden-xs hidden-sm product-item-detail-slider-controls-block"
                                                id="<?= $itemIds['SLIDER_CONT_OF_ID'] . $offer['ID'] ?>" style="display: <?= $strVisible ?>;">
                                                <? $activeKeyPhoto = 0;
                                                foreach ($offer["MORE_PHOTO"] as $keyPhoto => $photo) {
                                                    if (!empty($photo["VALUE"])) {
                                                        $activeKeyPhoto++; ?>
                                                        <div class="product-item-detail-slider-controls-video" data-entity="slider-control"
                                                            data-value="<?= $offer['ID'] . '_' . $photo['ID'] ?>">
                                                            <div class="product-item-detail-slider-controls-video-image">
                                                                <img src="<?= $arResult['SCHEME'] ?>://img.youtube.com/vi/<?= $photo['VALUE'] ?>/default.jpg"
                                                                    alt="<?= $alt ?>" title="<?= $title ?>" />
                                                            </div>
                                                            <div class="product-item-detail-slider-controls-video-play"><i
                                                                    class="icon-play-s"></i></div>
                                                        </div>
                                                    <? } else { ?>
                                                        <div class="product-item-detail-slider-controls-image<?= ($keyPhoto == $activeKeyPhoto ? ' active' : '') ?>"
                                                            data-entity="slider-control" data-value="<?= $offer['ID'] . '_' . $photo['ID'] ?>">
                                                            <img src="<?= $photo['PREVIEW']['SRC'] ?>" width="<?= $photo['PREVIEW']['WIDTH'] ?>"
                                                                height="<?= $photo['PREVIEW']['HEIGHT'] ?>" alt="<?= $alt ?>" title="<?= $title ?>" />
                                                        </div>
                                                    <? }
                                                }
                                                unset($keyPhoto, $photo, $activeKeyPhoto); ?>
                                            </div>
                                        <? }
                                        unset($keyOffer, $offer);
                                    } else {
                                        $offer = isset($arResult["OFFERS"][$arResult["OFFERS_SELECTED"]]) ? $arResult["OFFERS"][$arResult["OFFERS_SELECTED"]] : reset($arResult["OFFERS"]);
                                        if ($offer["MORE_PHOTO_COUNT"] > 0) { ?>
                                            <div class="hidden-xs hidden-sm product-item-detail-slider-controls-block"
                                                id="<?= $itemIds['SLIDER_CONT_OF_ID'] . $offer['ID'] ?>">
                                                <? $activeKeyPhoto = 0;
                                                foreach ($offer["MORE_PHOTO"] as $keyPhoto => $photo) {
                                                    if (!empty($photo["VALUE"])) {
                                                        $activeKeyPhoto++; ?>
                                                        <div class="product-item-detail-slider-controls-video" data-entity="slider-control"
                                                            data-value="<?= $offer['ID'] . '_' . $photo['ID'] ?>">
                                                            <div class="product-item-detail-slider-controls-video-image">
                                                                <img src="<?= $arResult['SCHEME'] ?>://img.youtube.com/vi/<?= $photo['VALUE'] ?>/default.jpg"
                                                                    alt="<?= $alt ?>" title="<?= $title ?>" />
                                                            </div>
                                                            <div class="product-item-detail-slider-controls-video-play"><i
                                                                    class="icon-play-s"></i></div>
                                                        </div>
                                                    <? } else { ?>
                                                        <div class="product-item-detail-slider-controls-image<?= ($keyPhoto == $activeKeyPhoto ? ' active' : '') ?>"
                                                            data-entity="slider-control" data-value="<?= $offer['ID'] . '_' . $photo['ID'] ?>">
                                                            <img src="<?= $photo['PREVIEW']['SRC'] ?>" width="<?= $photo['PREVIEW']['WIDTH'] ?>"
                                                                height="<?= $photo['PREVIEW']['HEIGHT'] ?>" alt="<?= $alt ?>" title="<?= $title ?>" />
                                                        </div>
                                                    <? }
                                                }
                                                unset($keyPhoto, $photo, $activeKeyPhoto); ?>
                                            </div>
                                        <? }
                                    }
                                } else { ?>
                                    <div class="hidden-xs hidden-sm product-item-detail-slider-controls-block"
                                        id="<?= $itemIds['SLIDER_CONT_ID'] ?>">
                                        <? if (!empty($actualItem["MORE_PHOTO"])) {
                                            $activeKey = 0;
                                            foreach ($actualItem["MORE_PHOTO"] as $key => $photo) {
                                                if (!empty($photo["VALUE"])) {
                                                    $activeKey++; ?>
                                                    <div class="product-item-detail-slider-controls-video" data-entity="slider-control"
                                                        data-value="<?= $photo['ID'] ?>">
                                                        <div class="product-item-detail-slider-controls-video-image">
                                                            <img src="<?= $arResult['SCHEME'] ?>://img.youtube.com/vi/<?= $photo['VALUE'] ?>/default.jpg"
                                                                alt="<?= $alt ?>" title="<?= $title ?>" />
                                                        </div>
                                                        <div class="product-item-detail-slider-controls-video-play"><i
                                                                class="icon-play-s"></i></div>
                                                    </div>
                                                <? } else { ?>
                                                    <div class="product-item-detail-slider-controls-image<?= ($key == $activeKey ? ' active' : '') ?>"
                                                        data-entity="slider-control" data-value="<?= $photo['ID'] ?>">
                                                        <img src="<?= $photo['PREVIEW']['SRC'] ?>" width="<?= $photo['PREVIEW']['WIDTH'] ?>"
                                                            height="<?= $photo['PREVIEW']['HEIGHT'] ?>" alt="<?= $alt ?>" title="<?= $title ?>" />
                                                    </div>
                                                <? }
                                            }
                                            unset($key, $photo, $activeKey);
                                        } ?>
                                    </div>
                                <? }
                            } ?>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-5 product-item-detail-blocks">
                        <?//ARTICLE//
                        if ($haveOffers && ($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST")) { ?>
                            <div class="product-item-detail-article" id="<?= $itemIds['ARTICLE_ID'] ?>">
                                <?= Loc::getMessage("CT_BCE_CATALOG_ARTICLE") ?>
                                <span data-entity="article-value"></span>
                            </div>
                        <? } else { ?>
                           <?/* <div class="product-item-detail-article">
                                <?= Loc::getMessage("CT_BCE_CATALOG_ARTICLE");
                                $article = $arResult["PROPERTIES"]["ARTNUMBER"]["VALUE"]; ?>
                                <span><?= (!empty($article) ? $article : "-"); ?>&nbsp;&nbsp;Код:&nbsp;<?= $arResult["ID"]; ?></span>
                            </div>*/?>
                            <div id="copyNotification" style="display:none; position:fixed; top:10px; right:10px; background:#333; color:#fff; padding:10px 20px; border-radius:5px; z-index:1000; font-family:Arial, sans-serif; font-size:14px;">
							</div>


                            <div class="product-item-detail-article">
							  <div class="article-and-code">
							    <div class="article-block">
							      <?= Loc::getMessage("CT_BCE_CATALOG_ARTICLE"); ?>
							      <?php $article = $arResult["PROPERTIES"]["ARTNUMBER"]["VALUE"]; ?>
							      <span><?= (!empty($article) ? $article : "-"); ?></span>
							      <button class="copy-button" onclick="copyToClipboard('<?= htmlspecialchars($article) ?>')">
							        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#000" viewBox="0 0 24 24">
							          <path d="M16 1H4a2 2 0 0 0-2 2v14h2V3h12V1zm3 4H8a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2zm0 16H8V7h11v14z"/>
							        </svg>
							      </button>
							    </div>
							    <div class="code-block">
							      <span>Код: <?= $arResult["ID"]; ?></span>
							      <button class="copy-button" onclick="copyToClipboard('<?= htmlspecialchars($arResult["ID"]) ?>')">
							        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#000" viewBox="0 0 24 24">
							          <path d="M16 1H4a2 2 0 0 0-2 2v14h2V3h12V1zm3 4H8a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2zm0 16H8V7h11v14z"/>
							        </svg>
							      </button>
							    </div>
							  </div>
							</div>
                        <? }
                        //RATING//
                        if (isset($arResult["REVIEWS_COUNT"])) { ?>
                            <div class="product-item-detail-rating">
                                <? if ($arResult["RATING_VALUE"] > 0) { ?>
                                    <div class="product-item-detail-rating-val" <?= ($arResult["RATING_VALUE"] <= 4.4 ? " data-rate='" . intval($arResult["RATING_VALUE"]) . "'" : "") ?>>
                                        <?= $arResult["RATING_VALUE"] ?></div>
                                <? }
                                $arReviewsDeclension = new Bitrix\Main\Grid\Declension(Loc::getMessage("CT_BCE_CATALOG_REVIEW"), Loc::getMessage("CT_BCE_CATALOG_REVIEWS_1"), Loc::getMessage("CT_BCE_CATALOG_REVIEWS_2")); ?>
                                <div class="product-item-detail-rating-reviews-count">
                                    <!--noindex--><?= ($arResult["REVIEWS_COUNT"] > 0 ? $arResult["REVIEWS_COUNT"] . " " . $arReviewsDeclension->get($arResult["REVIEWS_COUNT"]) : Loc::getMessage("CT_BCE_CATALOG_NO_REVIEWS")) ?><!--/noindex-->
                                </div>
                                <? unset($arReviewsDeclension); ?>
                            </div>
                            <? if ($arResult["REVIEWS_COUNT"] > 0) { ?>
                                <span itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
                                    <meta itemprop="ratingValue" content="<?= $arResult['RATING_VALUE'] ?>" />
                                    <meta itemprop="reviewCount" content="<?= $arResult['REVIEWS_COUNT'] ?>" />
                                </span>
                            <? }
                        }
                        //PREVIEW_TEXT//
                        if (!empty($arResult["PREVIEW_TEXT"])) { ?>
                            <div class="product-item-detail-preview"><?= $arResult["PREVIEW_TEXT"] ?></div>
                        <? }
                        //PROPERTIES//
                        $isMainProps = false;
                        if (!empty($arResult["DISPLAY_PROPERTIES"])) {
                            foreach ($arResult["DISPLAY_PROPERTIES"] as $property) {
                                if (isset($arParams["MAIN_BLOCK_PROPERTY_CODE"][$property["CODE"]])) {
                                    $isMainProps = true;
                                    break;
                                }
                            }
                            unset($property);
                        }
                        if (!!$isMainProps || (($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST") && $arResult["SHOW_OFFERS_PROPS"])) { ?>
                            <div class="product-item-detail-main-properties-container">
                                <div class="product-item-detail-properties-block" <?= (($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST") && $arResult["SHOW_OFFERS_PROPS"] ? " id='" . $itemIds["DISPLAY_MAIN_PROP_DIV"] . "'" : ""); ?>>
                                    <? if (!empty($arResult["DISPLAY_PROPERTIES"])) {
                                        foreach ($arResult["DISPLAY_PROPERTIES"] as $property) {
                                            if (isset($arParams["MAIN_BLOCK_PROPERTY_CODE"][$property["CODE"]])) { ?>
                                                <div class="product-item-detail-properties">
                                                    <div class="product-item-detail-properties-name"><?= $property["NAME"] ?></div>
                                                    <div class="product-item-detail-properties-val 1234"><?= $property["DISPLAY_VALUE"] ?>
                                                    </div>
                                                    <script>
                                                        console.log(<?= json_encode($property, JSON_PRETTY_PRINT); ?>);
                                                    </script>

                                                </div>
                                            <? }
                                        }
                                        unset($property);
                                    } ?>
                                </div>
                            </div>
                        <? }
                        unset($isMainProps);
                        ?>
                        <div class="product-item-detail-jivo-tel">
                            <div class="detail_inner">
                                <div class="detail_inner_wrap">
                                    <img src="/local/templates/enext/images/transformed_v_1.png" alt="Помощь эксперта"
                                        title="Помощь эксперта">
                                    <div>
                                        <p class="header">Появились вопросы о товаре?</p>
                                        <p class="text">Ответим на все вопросы и поможем подобрать товар</p>
                                    </div>
                                </div>
                                <div class="detail_inner_buttons">
                                    <a href="https://wa.me/79251122290" target="_blank"
                                        style="padding:0;background:none;width:24px;"><img
                                            src="/local/templates/enext/images/soc-wa.svg" alt="whatsapp"
                                            title="whatsapp"></a>
                                    <a href="https://t.me/Santehpodbor_bot" target="_blank"
                                        style="padding:0;background:none;width:24px;"><img
                                            src="/local/templates/enext/images/soc-tg.svg" alt="telegramm"
                                            title="telegramm"></a>
                                    <a href="javascript:jivo_api.open()"
                                        style="padding: 2px 12px;border-radius: 10px;font-size: 12px;">Чат на сайте</a>
                                </div>
                                <div class="detail_inner_buttons" style="margin-top: 10px;">
                                    <a href="tel:+79175425000"
                                        style="display:block;width: 100%;text-align: center;">Позвонить</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?//SKU_ITEMS//
                    if ($haveOffers && $arParams["OFFERS_VIEW"] != "PROPS" && $arParams["OFFERS_VIEW"] != "DROPDOWN_LIST") { ?>
                        <div class="col-xs-12 product-item-detail-scu-items-container" id="<?= $itemIds['SKU_ITEMS_ID'] ?>">
                            <div class="h2"><?= Loc::getMessage("CT_BCE_CATALOG_SKU_ITEMS") ?></div>
                            <? if ($arParams["OFFERS_VIEW"] != "LIST" && $arSettings["OFFERS_ON_MAP"] == "Y") { ?>
                                <div class="product-item-detail-scu-items-links">
                                    <div class="product-item-detail-scu-items-link active">
                                        <?= Loc::getMessage("CT_BCE_CATALOG_SKU_ITEMS_VIEW_LIST") ?></div>
                                    <div class="product-item-detail-scu-items-link" data-entity="offersOnMap">
                                        <?= Loc::getMessage("CT_BCE_CATALOG_SKU_ITEMS_VIEW_MAP") ?></div>
                                </div>
                            <? } ?>
                            <div class="product-item-detail-scu-items">
                                <?//SKU_ITEMS_HEAD// ?>
                                <div class="product-item-detail-scu-item">
                                    <div
                                        class="hidden-xs hidden-sm product-item-detail-scu-item-col product-item-detail-scu-item-col-non-left-pad">
                                        <?= ($arParams["OFFERS_VIEW"] == "LIST" ? Loc::getMessage("CT_BCE_CATALOG_SKU_ITEMS_ITEM") : $arResult["OFFERS"][0]["PROPERTIES"]["OBJECT"]["NAME"]) ?>
                                    </div>
                                    <div class="hidden-xs hidden-sm product-item-detail-scu-item-col"></div>
                                    <? if (!empty($arResult["OFFERS_PROP"])) {
                                        foreach ($arResult["SKU_PROPS"] as $skuProperty) {
                                            if (!isset($arResult["OFFERS_PROP"][$skuProperty["CODE"]]))
                                                continue; ?>

                                            <div class="hidden-xs hidden-sm product-item-detail-scu-item-col">
                                                <?= htmlspecialcharsEx($skuProperty["NAME"]) ?></div>
                                        <? }
                                        unset($skuProperty);
                                    }
                                    if ($arParams["OFFERS_VIEW"] != "LIST") {
                                        $offersDeliveryMethods = false;
                                        foreach ($arResult["OFFERS"] as $arOffer) {
                                            if (!empty($arOffer["PROPERTIES"]["OBJECT"]["FULL_VALUE"]["DELIVERY_METHODS"]) || ($arSettings["AUTO_DELIVERY_CALCULATION"] == "Y" && !empty($arOffer["PROPERTIES"]["OBJECT"]["FULL_VALUE"]["SITE_ID"]) && $arOffer["PROPERTIES"]["OBJECT"]["FULL_VALUE"]["SITE_ID"] != SITE_ID)) {
                                                $offersDeliveryMethods = true;
                                                break;
                                            }
                                        }
                                        unset($arOffer);
                                        if ($offersDeliveryMethods) { ?>
                                            <div
                                                class="product-item-detail-scu-item-col product-item-detail-scu-item-col-delivery-methods">
                                                <span
                                                    class="product-item-detail-scu-item-object-geo-location product-item-detail-scu-item-object-geo-location-hidden"><?= Loc::getMessage("CT_BCE_CATALOG_SKU_ITEMS_DELIVERY_TO") ?><span
                                                        data-entity="city"></span></span>
                                            </div>
                                        <? }
                                    } ?>
                                    <div
                                        class="hidden-xs hidden-sm product-item-detail-scu-item-col product-item-detail-scu-item-col-price">
                                        <?= Loc::getMessage("CT_BCE_CATALOG_SKU_ITEMS_PRICE") ?></div>
                                    <? if ($arParams["USE_PRODUCT_QUANTITY"] && $arParams["OFFERS_VIEW"] == "LIST" && (!$object || ($object && $objectContacts))) { ?>
                                        <div class="hidden-xs hidden-sm product-item-detail-scu-item-col"></div>
                                    <? } ?>
                                    <div
                                        class="hidden-xs hidden-sm product-item-detail-scu-item-col product-item-detail-scu-item-col-buttons">
                                    </div>
                                    <? if (!$arParams["DISABLE_DELAY"] && $arParams["OFFERS_VIEW"] == "LIST" && (!$object || ($object && $objectContacts)) && !$partnersUrl) {
                                        $numOffersPartnersUrl = 0;
                                        foreach ($arResult["OFFERS"] as $arOffer) {
                                            if (!empty($arOffer["PROPERTIES"]["PARTNERS_URL"]["VALUE"]))
                                                $numOffersPartnersUrl++;
                                        }
                                        unset($arOffer);
                                        if ($numOffersPartnersUrl != count($arResult["OFFERS"])) { ?>
                                            <div
                                                class="hidden-xs hidden-sm product-item-detail-scu-item-col product-item-detail-scu-item-col-delay">
                                            </div>
                                        <? }
                                    } ?>
                                </div>
                                <?//SKU_ITEMS_BODY//
                                    foreach ($arResult["OFFERS"] as $key => $arOffer) {
                                        $offerName = !empty($arOffer["NAME"]) ? $arOffer["NAME"] : $name;
                                        $offerTitle = !empty($arOffer["NAME"]) ? $arOffer["NAME"] : $title;
                                        $offerAlt = !empty($arOffer["NAME"]) ? $arOffer["NAME"] : $alt;

                                        $offerPrice = $arOffer["ITEM_PRICES"][$arOffer["ITEM_PRICE_SELECTED"]];
                                        $offerMeasureRatio = $arOffer["ITEM_MEASURE_RATIOS"][$arOffer["ITEM_MEASURE_RATIO_SELECTED"]]["RATIO"];

                                        $offerObject = !empty($arOffer["PROPERTIES"]["OBJECT"]["FULL_VALUE"]) ? $arOffer["PROPERTIES"]["OBJECT"]["FULL_VALUE"] : false;
                                        $offerObjectContacts = $offerObject["PHONE_SMS"] || $offerObject["EMAIL_EMAIL"] ? true : false;
                                        $offerPartnersUrl = !empty($arOffer["PROPERTIES"]["PARTNERS_URL"]["VALUE"]) ? true : false; ?>

                                    <div class="product-item-detail-scu-item<?= ($arParams['OFFERS_VIEW'] == 'LIST' && $arParams['DISPLAY_COMPARE'] ? ' product-item-detail-scu-item-width-compare' : '') ?>"
                                        data-entity="sku-item" data-num="<?= $key ?>">
                                        <div
                                            class="product-item-detail-scu-item-col<?= ($arParams["OFFERS_VIEW"] != "LIST" ? ' product-item-detail-scu-item-col-non-left-pad' : '') ?>">
                                            <?//SKU_ITEMS_LIST//
                                                    if ($arParams["OFFERS_VIEW"] == "LIST") { ?>
                                                <div class="product-item-detail-scu-item-image">
                                                    <?//SKU_ITEMS_LIST_IMAGE//
                                                                if (is_array($arOffer["PREVIEW_PICTURE"])) { ?>
                                                        <img src="<?= $arOffer['PREVIEW_PICTURE']['SRC'] ?>"
                                                            width="<?= $arOffer['PREVIEW_PICTURE']['WIDTH'] ?>"
                                                            height="<?= $arOffer['PREVIEW_PICTURE']['HEIGHT'] ?>" alt="<?= $offerAlt ?>"
                                                            title="<?= $offerTitle ?>" />
                                                    <? } else { ?>
                                                        <img src="<?= SITE_TEMPLATE_PATH ?>/images/no_photo.png" width="222" height="222"
                                                            alt="<?= $offerAlt ?>" title="<?= $offerTitle ?>" />
                                                    <? }
                                                                //SKU_ITEMS_LIST_COMPARE//
                                                                if ($arParams["DISPLAY_COMPARE"]) { ?>
                                                        <div class="product-item-detail-compare">
                                                            <label title="<?= $arParams['MESS_BTN_COMPARE'] ?>" data-entity="compare">
                                                                <input type="checkbox" data-entity="compare-checkbox">
                                                                <span class="product-item-detail-compare-checkbox"><i
                                                                        class="icon-ok-b"></i></span>
                                                                <span class="visible-xs visible-sm product-item-detail-compare-title"
                                                                    data-entity="compare-title"><?= $arParams["MESS_BTN_COMPARE"] ?></span>
                                                            </label>
                                                        </div>
                                                    <? } ?>
                                                </div>
                                            <?//SKU_ITEMS_OBJECTS//
                                                    } else { ?>
                                                <<?= ($offerObject ? "a target='_blank' href='" . $offerObject["DETAIL_PAGE_URL"] . "'" : "div") ?> class="product-item-detail-scu-item-object-image">
                                                    <?//SKU_ITEMS_OBJECTS_IMAGE//
                                                                if (is_array($offerObject["PREVIEW_PICTURE"])) { ?>
                                                        <img src="<?= $offerObject['PREVIEW_PICTURE']['SRC'] ?>"
                                                            width="<?= $offerObject['PREVIEW_PICTURE']['WIDTH'] ?>"
                                                            height="<?= $offerObject['PREVIEW_PICTURE']['HEIGHT'] ?>"
                                                            alt="<?= $offerObject['NAME'] ?>" />
                                                    <? } else { ?>
                                                        <img src="<?= SITE_TEMPLATE_PATH ?>/images/no_photo.png" width="222" height="222"
                                                            alt="<?= $offerObject['NAME'] ?>" />
                                                    <? } ?>
                                                </<?= ($offerObject ? "a" : "div") ?>>
                                            <? } ?>
                                        </div>
                                        <div
                                            class="product-item-detail-scu-item-col product-item-detail-scu-item-col<?= ($arParams['OFFERS_VIEW'] != 'LIST' ? '-object' : '') ?>-info">
                                            <?//SKU_ITEMS_LIST//
                                                    if ($arParams["OFFERS_VIEW"] == "LIST") {
                                                        //SKU_ITEMS_LIST_ARTICLE// ?>
                                                <div class="hidden-xs hidden-sm product-item-detail-article">
                                                    <?= Loc::getMessage("CT_BCE_CATALOG_ARTICLE");
                                                    $offerArticle = $arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"]; ?>
                                                    <span><?= (!empty($offerArticle) ? $offerArticle : "-"); ?></span>
                                                </div>
                                                <?//SKU_ITEMS_LIST_TITLE// ?>
                                                <div class="product-item-detail-scu-item-title"><?= $offerName ?></div>
                                            <?//SKU_ITEMS_OBJECTS//
                                                    } else {
                                                        //SKU_ITEMS_OBJECTS_TITLE//
                                                        if ($offerObject) { ?>
                                                    <a target="_blank" class="product-item-detail-scu-item-object-title"
                                                        href="<?= $offerObject['DETAIL_PAGE_URL'] ?>"><?= $offerObject["NAME"] ?></a>
                                                <? }
                                                        if (isset($offerObject["REVIEWS_COUNT"]) && $offerObject["REVIEWS_COUNT"] > 0) { ?>
                                                    <div class="product-item-detail-scu-item-object-rating">
                                                        <div class="product-item-detail-scu-item-object-rating-val"
                                                            <?= ($offerObject["RATING_VALUE"] <= 4.4 ? " data-rate='" . intval($offerObject["RATING_VALUE"]) . "'" : "") ?>>
                                                            <?= $offerObject["RATING_VALUE"] ?></div>
                                                        <? $arReviewsDeclension = new Bitrix\Main\Grid\Declension(Loc::getMessage("CT_BCE_CATALOG_REVIEW"), Loc::getMessage("CT_BCE_CATALOG_REVIEWS_1"), Loc::getMessage("CT_BCE_CATALOG_REVIEWS_2")); ?>
                                                        <div class="product-item-detail-scu-item-object-rating-reviews-count">
                                                            <?= $offerObject["REVIEWS_COUNT"] . " " . $arReviewsDeclension->get($offerObject["REVIEWS_COUNT"]) ?>
                                                        </div>
                                                        <? unset($arReviewsDeclension); ?>
                                                    </div>
                                                <? } ?>
                                                <div class="product-item-detail-scu-item-object-hours product-item-detail-scu-item-object-hours-hidden"
                                                    data-entity="hours"></div>
                                            <? } ?>
                                        </div>
                                        <?//SKU_ITEMS_PROPS//
                                                if (!empty($arResult["OFFERS_PROP"])) {
                                                    foreach ($arResult["SKU_PROPS"] as $skuProperty) {
                                                        if (!isset($arResult["OFFERS_PROP"][$skuProperty["CODE"]]))
                                                            continue; ?>

                                                <div class="product-item-detail-scu-item-col product-item-detail-scu-item-col-prop">
                                                    <div class="visible-xs visible-sm product-item-scu-title">
                                                        <?= htmlspecialcharsEx($skuProperty["NAME"]) ?></div>
                                                    <? if (array_key_exists("PROP_" . $skuProperty["ID"], $arOffer["TREE"])) {
                                                        $value = $skuProperty["VALUES"][$arOffer["TREE"]["PROP_" . $skuProperty["ID"]]];
                                                        if ($skuProperty["SHOW_MODE"] === "PICT") { ?>
                                                            <div class="product-item-detail-scu-item-color" title="<?= $value['NAME'] ?>"
                                                                style="<?= (!empty($value['CODE']) ? 'background-color: #' . $value['CODE'] . ';' : (!empty($value['PICT']) ? 'background-image: url(' . $value['PICT']['SRC'] . ');' : '')); ?>">
                                                            </div>
                                                        <? } else { ?>
                                                            <div class="product-item-detail-scu-item-text" title="<?= $value['NAME'] ?>">
                                                                <?= $value["NAME"] ?></div>
                                                        <? }
                                                        unset($value);
                                                    } ?>
                                                </div>
                                            <? }
                                                    unset($skuProperty);
                                                }
                                                //SKU_ITEMS_OBJECTS_DELIVERY_METHODS//
                                                if ($arParams["OFFERS_VIEW"] != "LIST" && $offersDeliveryMethods) { ?>
                                            <div
                                                class="product-item-detail-scu-item-col product-item-detail-scu-item-col-delivery-methods">
                                                <? if ($arSettings["AUTO_DELIVERY_CALCULATION"] == "Y" && !empty($offerObject["SITE_ID"]) && $offerObject["SITE_ID"] != SITE_ID) { ?>
                                                    <div class="product-item-detail-scu-item-object-geo-delivery product-item-detail-scu-item-object-geo-delivery-hidden"
                                                        data-entity="geoDelivery"></div>
                                                <? } elseif (!empty($offerObject["DELIVERY_METHODS"])) { ?>
                                                    <div class="product-item-detail-scu-item-object-delivery-method">
                                                        <?= $offerObject["DELIVERY_METHODS"] ?></div>
                                                <? } ?>
                                            </div>
                                        <? } ?>
                                        <div class="product-item-detail-scu-item-col product-item-detail-scu-item-col-price">
                                            <?//SKU_ITEMS_PRICE// ?>
                                            <div class="product-item-detail-scu-item-price">
                                                <? if (($arParams["OFFERS_VIEW"] == "LIST" && $offerPrice["SQ_M_PRICE"] > 0) || $offerPrice["PRICE"] > 0) { ?>
                                                    <div class="product-item-detail-price">
                                                        <span class="product-item-detail-scu-item-price-current"
                                                            data-entity="price-current"><?= ($arParams["OFFERS_VIEW"] == "LIST" && $offerPrice["SQ_M_PRICE"] > 0 ? $offerPrice["SQ_M_PRINT_PRICE"] : $offerPrice["PRINT_PRICE"]) ?></span>
                                                        <? if ($arParams["OFFERS_VIEW"] == "LIST") { ?>
                                                            <span
                                                                class="product-item-detail-price-measure">/<?= ($offerPrice["SQ_M_PRICE"] > 0 ? Loc::getMessage("CT_BCE_CATALOG_MEASURE_SQ_M") : $arOffer["ITEM_MEASURE"]["TITLE"]) ?></span>
                                                        <? }
                                                        if ($arParams["USE_PRICE_COUNT"] && count($arOffer["ITEM_QUANTITY_RANGES"]) > 1) { ?>
                                                            <span class="product-item-detail-scu-item-price-ranges-icon"><i
                                                                    class="icon-question"></i></span>
                                                        <? } ?>
                                                    </div>
                                                <? } else { ?>
                                                    <div class="product-item-detail-price-not-set">
                                                        <?= Loc::getMessage("CT_BCE_CATALOG_PRICE_NOT_SET") ?></div>
                                                <? }
                                                if ($arParams["SHOW_OLD_PRICE"] === "Y" && $offerPrice["PERCENT"] > 0) { ?>
                                                    <div class="product-item-detail-price-old" data-entity="price-old">
                                                        <?= ($offerPrice["SQ_M_BASE_PRICE"] > 0 ? $offerPrice["SQ_M_PRINT_BASE_PRICE"] : $offerPrice["PRINT_BASE_PRICE"]) ?>
                                                    </div>
                                                    <div class="product-item-detail-price-economy" data-entity="price-economy">
                                                        <?= Loc::getMessage("CT_BCE_CATALOG_ECONOMY_INFO2", array("#ECONOMY#" => ($offerPrice["SQ_M_DISCOUNT"] > 0 ? $offerPrice["SQ_M_PRINT_DISCOUNT"] : $offerPrice["PRINT_DISCOUNT"]))) ?>
                                                    </div>
                                                <? }
                                                //SKU_ITEMS_PRICE_RANGES//
                                                if ($arParams["USE_PRICE_COUNT"] && count($arOffer["ITEM_QUANTITY_RANGES"]) > 1) { ?>
                                                    <div class="product-item-detail-scu-item-ranges-container">
                                                        <div class="product-item-detail-scu-item-ranges">
                                                            <? foreach ($arOffer["ITEM_QUANTITY_RANGES"] as $range) {
                                                                if ($range["HASH"] != "ZERO-INF") {
                                                                    $itemPrice = false;
                                                                    foreach ($arOffer["ITEM_PRICES"] as $itemPrice) {
                                                                        if ($itemPrice["QUANTITY_HASH"] == $range["HASH"]) {
                                                                            break;
                                                                        }
                                                                    }
                                                                    if ($itemPrice) { ?>
                                                                        <div class="product-item-detail-properties">
                                                                            <div class="product-item-detail-properties-name">
                                                                                <? if (is_infinite($range["SORT_TO"])) {
                                                                                    echo Loc::getMessage("CT_BCE_CATALOG_RANGE_FROM", array("#FROM#" => $range["SORT_FROM"] . " " . $arOffer["ITEM_MEASURE"]["TITLE"]));
                                                                                } else {
                                                                                    echo $range["SORT_FROM"] . ($range["SORT_TO"] != $range["SORT_FROM"] ? " - " . $range["SORT_TO"] : "") . " " . $arOffer["ITEM_MEASURE"]["TITLE"];
                                                                                } ?>
                                                                            </div>
                                                                            <div class="product-item-detail-properties-val">
                                                                                <?= ($arParams["USE_RATIO_IN_RANGES"] == "Y" ? $itemPrice["PRINT_RATIO_PRICE"] : $itemPrice["PRINT_PRICE"]) ?>
                                                                            </div>
                                                                        </div>
                                                                    <? }
                                                                    unset($itemPrice);
                                                                }
                                                            }
                                                            unset($range); ?>
                                                        </div>
                                                    </div>
                                                <? } ?>
                                            </div>
                                            <?//SKU_ITEMS_QUANTITY_LIMIT//
                                                    if ($arParams["SHOW_MAX_QUANTITY"] !== "N") { ?>
                                                <div
                                                    class="product-item-detail-quantity<?= ($arOffer['CAN_BUY'] ? '' : ' product-item-detail-quantity-not-avl') ?>">
                                                    <i
                                                        class="icon-<?= ($arOffer['CAN_BUY'] ? 'ok' : 'close') ?>-b product-item-detail-quantity-icon"></i>
                                                    <span class="product-item-detail-quantity-val">
                                                        <? if ($arOffer["CAN_BUY"]) {
                                                            echo $arParams["MESS_SHOW_MAX_QUANTITY"] . "&nbsp;";
                                                            if ($offerMeasureRatio && (float) $arOffer["CATALOG_QUANTITY"] > 0 && $arOffer["CATALOG_QUANTITY_TRACE"] === "Y" && $arOffer["CATALOG_CAN_BUY_ZERO"] === "N") {
                                                                if ($arParams["SHOW_MAX_QUANTITY"] === "M") {
                                                                    if ((float) $arOffer["CATALOG_QUANTITY"] / $offerMeasureRatio >= $arParams["RELATIVE_QUANTITY_FACTOR"]) {
                                                                        echo $arParams["MESS_RELATIVE_QUANTITY_MANY"];
                                                                    } else {
                                                                        echo $arParams["MESS_RELATIVE_QUANTITY_FEW"];
                                                                    }
                                                                } else {
                                                                    echo $arOffer["CATALOG_QUANTITY"];
                                                                }
                                                            }
                                                        } else {
                                                            echo $arParams["MESS_NOT_AVAILABLE"];
                                                        } ?>
                                                    </span>
                                                </div>
                                            <? } ?>
                                        </div>
                                        <?//SKU_ITEMS_QUANTITY//
                                                if ($arParams["USE_PRODUCT_QUANTITY"] && $arParams["OFFERS_VIEW"] == "LIST" && (!$object || ($object && $objectContacts))) { ?>
                                            <div class="product-item-detail-scu-item-col">
                                                <? if ($arOffer["CAN_BUY"] && $offerPrice["PRICE"] > 0) {
                                                    if (!empty($arResult["PROPERTIES"]["M2_COUNT"]["VALUE"]) && ($arOffer["ITEM_MEASURE"]["SYMBOL_INTL"] == "pc. 1" || $arOffer["ITEM_MEASURE"]["SYMBOL_INTL"] == "m2")) { ?>
                                                        <div class="product-item-detail-amount">
                                                            <a class="product-item-detail-amount-btn-minus" href="javascript:void(0)"
                                                                rel="nofollow" data-entity="pc-quantity-down">-</a>
                                                            <input class="product-item-detail-amount-input" type="tel"
                                                                value="<?= $offerPrice['PC_MIN_QUANTITY'] ?>" data-entity="pc-quantity" />
                                                            <a class="product-item-detail-amount-btn-plus" href="javascript:void(0)"
                                                                rel="nofollow" data-entity="pc-quantity-up">+</a>
                                                            <div class="product-item-detail-amount-measure">
                                                                <?= Loc::getMessage("CT_BCE_CATALOG_MEASURE_PC") ?></div>
                                                        </div>
                                                        <div class="product-item-detail-amount">
                                                            <a class="product-item-detail-amount-btn-minus" href="javascript:void(0)"
                                                                rel="nofollow" data-entity="sq-m-quantity-down">-</a>
                                                            <input class="product-item-detail-amount-input" type="tel"
                                                                value="<?= $offerPrice['SQ_M_MIN_QUANTITY'] ?>" data-entity="sq-m-quantity" />
                                                            <a class="product-item-detail-amount-btn-plus" href="javascript:void(0)"
                                                                rel="nofollow" data-entity="sq-m-quantity-up">+</a>
                                                            <div class="product-item-detail-amount-measure">
                                                                <?= Loc::getMessage("CT_BCE_CATALOG_MEASURE_SQ_M") ?></div>
                                                        </div>
                                                    <? } else { ?>
                                                        <div class="product-item-detail-amount">
                                                            <a class="product-item-detail-amount-btn-minus" href="javascript:void(0)"
                                                                rel="nofollow" data-entity="quantity-down">-</a>
                                                            <input class="product-item-detail-amount-input" type="tel"
                                                                value="<?= $offerPrice['MIN_QUANTITY'] ?>" data-entity="quantity" />
                                                            <a class="product-item-detail-amount-btn-plus" href="javascript:void(0)"
                                                                rel="nofollow" data-entity="quantity-up">+</a>
                                                            <div class="product-item-detail-amount-measure">
                                                                <?= $arOffer["ITEM_MEASURE"]["TITLE"] ?></div>
                                                        </div>
                                                    <? }
                                                } ?>
                                            </div>
                                        <? } ?>
                                        <div class="product-item-detail-scu-item-col product-item-detail-scu-item-col-buttons">
                                            <?//SKU_ITEMS_BUTTONS//
                                                    if ($arOffer["CAN_BUY"]) {
                                                        if ($offerPrice["PRICE"] > 0) {
                                                            if (!$arParams["DISABLE_BASKET"] && (($arParams["OFFERS_VIEW"] == "LIST" && !$partnersUrl && !$offerPartnersUrl) || ($arParams["OFFERS_VIEW"] == "OBJECTS" && !$offerPartnersUrl))) {
                                                                if (($arParams["OFFERS_VIEW"] == "LIST" && (!$object || ($object && $objectContacts))) || ($arParams["OFFERS_VIEW"] == "OBJECTS" && $offerObjectContacts)) {
                                                                    if ($showAddBtn) { ?>
                                                                <button type="button" class="btn btn-buy" data-entity="add"><i
                                                                        class="icon-cart"></i><span><?= $arParams["MESS_BTN_ADD_TO_BASKET"] ?></span></button>
                                                            <? }
                                                                    if ($showBuyBtn) { ?>
                                                                <button type="button" class="btn btn-buy" data-entity="buy"><i
                                                                        class="icon-cart"></i><span><?= $arParams["MESS_BTN_BUY"] ?></span></button>
                                                            <? }
                                                                }
                                                                if (($arParams["OFFERS_VIEW"] == "LIST" && $object) || ($arParams["OFFERS_VIEW"] == "OBJECTS" && $offerObject)) { ?>
                                                            <button type="button" class="btn btn-default" data-entity="object"><i
                                                                    class="icon-phone-call"></i><span><?= Loc::getMessage("CT_BCE_CATALOG_CONTACTS") ?></span></button>
                                                        <? }
                                                            } elseif (($arParams["OFFERS_VIEW"] == "LIST" && ($partnersUrl || $offerPartnersUrl)) || ($arParams["OFFERS_VIEW"] == "OBJECTS" && $offerPartnersUrl)) { ?>
                                                        <button type="button" class="btn btn-buy" data-entity="partner-link"><i
                                                                class="icon-cart"></i><span><?= $arParams["MESS_BTN_BUY"] ?></span></button>
                                                        <? if (!empty($arSettings["PARTNERS_INFO_MESSAGE"])) { ?>
                                                            <div class="hidden-xs hidden-sm product-item-detail-info-message">
                                                                <?= $arSettings["PARTNERS_INFO_MESSAGE"] ?></div>
                                                        <? }
                                                            }
                                                        } else {
                                                            if (($arParams["OFFERS_VIEW"] == "LIST" || ($arParams["OFFERS_VIEW"] == "OBJECTS" && $offerObject)) && $arParams["ASK_PRICE"]) { ?>
                                                        <button type="button" class="btn btn-default" data-entity="ask-price"><i
                                                                class="icon-comment"></i><span><?= Loc::getMessage("CT_BCE_CATALOG_ASK_PRICE") ?></span></button>
                                                    <? }
                                                        }
                                                    } else {
                                                        if ($arParams["OFFERS_VIEW"] == "LIST" || ($arParams["OFFERS_VIEW"] == "OBJECTS" && $offerObject)) {
                                                            if ($arParams["UNDER_ORDER"]) { ?>
                                                        <button type="button" class="btn btn-default" data-entity="not-available"><i
                                                                class="icon-clock"></i><span><?= Loc::getMessage("CT_BCE_CATALOG_UNDER_ORDER") ?></span></button>
                                                    <? }
                                                            if ($arParams["PRODUCT_SUBSCRIPTION"] === "Y" && $arOffer["CATALOG_SUBSCRIBE"] === "Y") { ?>
                                                        <? $APPLICATION->IncludeComponent(
                                                            "bitrix:catalog.product.subscribe",
                                                            "",
                                                            array(
                                                                "PRODUCT_ID" => $arOffer["ID"],
                                                                "BUTTON_ID" => $itemIds["SUBSCRIBE_LINK"] . "_" . $this->GetEditAreaId($arOffer["ID"]),
                                                                "BUTTON_CLASS" => "btn btn-default",
                                                                "DEFAULT_DISPLAY" => true,
                                                                "MESS_BTN_SUBSCRIBE" => $arParams["~MESS_BTN_SUBSCRIBE"]
                                                            ),
                                                            $component,
                                                            array("HIDE_ICONS" => "Y")
                                                        ); ?>
                                                    <? }
                                                        }
                                                    } ?>
                                        </div>
                                        <?//SKU_ITEMS_DELAY//
                                                if (!$arParams["DISABLE_DELAY"] && $arParams["OFFERS_VIEW"] == "LIST" && (!$object || ($object && $objectContacts)) && !$partnersUrl && $numOffersPartnersUrl != count($arResult["OFFERS"])) { ?>
                                            <div
                                                class="hidden-xs hidden-sm product-item-detail-scu-item-col product-item-detail-scu-item-col-delay">
                                                <? if (!$offerPartnersUrl && $arOffer["CAN_BUY"] && $offerPrice["PRICE"] > 0) { ?>
                                                    <div class="product-item-detail-delay" title="<?= $arParams['MESS_BTN_DELAY'] ?>"
                                                        data-entity="delay"><i class="icon-heart"></i></div>
                                                <? } ?>
                                            </div>
                                        <? } ?>
                                    </div>
                                    <? unset($offerName, $offerTitle, $offerAlt, $offerPrice, $offerMeasureRatio, $offerArticle, $offerObject, $offerObjectContacts, $offerPartnersUrl);
                                    }
                                    unset($key, $arOffer, $offersDeliveryMethods, $numOffersPartnersUrl); ?>
                            </div>
                        </div>
                    <? } ?>
                    <?//SET_ITEMS//
                    /*if(!empty($arResult["SET_ITEMS"])) {?>
<div class="col-xs-12 product-item-detail-set-items-container">
    <div class="h2"><?=Loc::getMessage("CT_BCE_CATALOG_SET_ITEMS")?></div>
    <div class="row product-item-detail-set-items <?=strtolower($arSettings['PRODUCTS_LIST_VIEW_MOBILE'])?>">
        <?foreach($arResult["SET_ITEMS"] as $arSetItem) {?>
        <div class="<?=($arSettings['PRODUCTS_LIST_VIEW_MOBILE'] == 'TWO_IN_ROW' ? 'col-xs-6' : 'col-xs-12 ')?> col-md-4">
            <a class="product-item-detail-set-item" href="<?=$arSetItem['DETAIL_PAGE_URL']?>" title="<?=$arSetItem['NAME']?>">
                <?//SET_ITEMS_IMAGE//?>
                <span class="product-item-detail-set-item-image">
                                                <?if(is_array($arSetItem["PREVIEW_PICTURE"])) {?>
                    <img src="<?=$arSetItem['PREVIEW_PICTURE']['SRC']?>" width="<?=$arSetItem['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arSetItem['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arSetItem['NAME']?>" title="<?=$arSetItem['NAME']?>" />
                    <?} else {?>
                    <img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo.png" width="222" height="222" alt="<?=$arSetItem['NAME']?>" title="<?=$arSetItem['NAME']?>" />
                    <?}
                                                if(!empty($arSetItem["BRAND"]["PREVIEW_PICTURE"])) {?>
                    <span class="product-item-detail-set-item-brand">
                                                        <img src="<?=$arSetItem['BRAND']['PREVIEW_PICTURE']['SRC']?>" width="<?=$arSetItem['BRAND']['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arSetItem['BRAND']['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arSetItem['BRAND']['NAME']?>" title="<?=$arSetItem['BRAND']['NAME']?>" />
                                                    </span>
                    <?}?>
                                            </span>
                <?//SET_ITEMS_TITLE//?>
                <span class="product-item-detail-set-item-title"><?=$arSetItem["NAME"]?></span>
                <?//SET_ITEMS_QUANTITY//?>
                <span class="product-item-detail-set-item-quantity"><?=$arSetItem["QUANTITY"]." ".$arSetItem["MEASURE"]?></span>
            </a>
        </div>
        <?}
                                unset($arSetItem);?>
    </div>
</div>
<?} */ ?>

                    <?//SET_CONSTRUCTOR//
                    if ($arResult["MODULES"]["catalog"] && $arResult["OFFER_GROUP"] && (!$haveOffers || ($haveOffers && ($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST")))) { ?>
                       
                       
                       
                       
                       
                       
                       
                       
                       
                       
                       
                       
                       
                       
                       
                       
                       
                       
                       
                       
                       
                       
                       
                       
                       
                       <div class="vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv col-xs-12 product-item-detail-set-constructor" id="<?= $itemIds['CONSTRUCTOR_ID'] ?>">
                            <? $APPLICATION->IncludeComponent(
                                "altop:catalog.set.constructor.enext",
                                ".default",
                                array(
                                    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                                    "ELEMENT_ID" => $actualItem["ID"],
                                    "BASKET_URL" => $arParams["BASKET_URL"],
                                    "PRICE_CODE" => $arParams["PRICE_CODE"],
                                    "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
                                    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                                    "CACHE_TIME" => $arParams["CACHE_TIME"],
                                    "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                                    "BUNDLE_ITEMS_COUNT" => !empty($arResult["PROPERTIES"]["SET_ITEMS_COUNT"]["VALUE"]) ? $arResult["PROPERTIES"]["SET_ITEMS_COUNT"]["VALUE"] : $arParams["SET_ITEMS_COUNT"],
                                    "CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
                                    "CURRENCY_ID" => $arParams["CURRENCY_ID"],
                                    "ADD_PROPERTIES_TO_BASKET" => $arParams["ADD_PROPERTIES_TO_BASKET"],
                                    "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
                                    "PARTIAL_PRODUCT_PROPERTIES" => $arParams["PARTIAL_PRODUCT_PROPERTIES"],
                                    "PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
                                    "OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"]
                                ),
                                $component,
                                array("HIDE_ICONS" => "Y")
                            ); ?>
                        </div>



















                    <? } ?>

                    <?//GIFTS//
                    if ($arResult["CATALOG"] && $arParams["USE_GIFTS_DETAIL"] == "Y" && (!$haveOffers || ($haveOffers && ($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST"))) && Bitrix\Main\ModuleManager::isModuleInstalled("sale")) { ?>
                        <div class="col-xs-12 product-item-detail-gifts" data-entity="parent-container"
                            style="display: none;">
                            <? if ($arParams["GIFTS_DETAIL_HIDE_BLOCK_TITLE"] !== "Y") { ?>
                                <div class="h2" data-entity="header" data-showed="false" style="display: none; opacity: 0;">
                                    <?= ($arParams["GIFTS_DETAIL_BLOCK_TITLE"] ?: Loc::getMessage("CT_BCE_CATALOG_GIFTS")) ?>
                                </div>
                            <? }
                            CBitrixComponent::includeComponentClass("bitrix:sale.products.gift"); ?>
                            <? $APPLICATION->IncludeComponent(
                                "bitrix:sale.products.gift",
                                ".default",
                                array(
                                    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                                    "ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
                                    "ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
                                    "ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
                                    "ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
                                    "SHOW_PRODUCTS_" . $arParams["IBLOCK_ID"] => "Y",
                                    "PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
                                    "PROPERTY_CODE_" . $arParams["IBLOCK_ID"] => $arParams["LIST_PROPERTY_CODE"],
                                    "PROPERTY_CODE_" . $arResult["OFFERS_IBLOCK"] => $arParams["OFFER_TREE_PROPS"],
                                    "INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
                                    "BASKET_URL" => $arParams["BASKET_URL"],
                                    "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
                                    "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
                                    "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
                                    "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                                    "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
                                    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                                    "CACHE_TIME" => $arParams["CACHE_TIME"],
                                    "CACHE_FILTER" => $arParams["CACHE_FILTER"],
                                    "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                                    "DISPLAY_COMPARE" => $arParams["DISPLAY_COMPARE"] ? "Y" : "N",
                                    "PAGE_ELEMENT_COUNT" => 0,
                                    "DEFERRED_PAGE_ELEMENT_COUNT" => $arParams["GIFTS_DETAIL_PAGE_ELEMENT_COUNT"],
                                    "PRICE_CODE" => $arParams["PRICE_CODE"],
                                    "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"] ? "Y" : "N",
                                    "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"] ? "Y" : "N",
                                    "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"] ? "Y" : "N",
                                    "USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"] ? "Y" : "N",
                                    "ADD_PROPERTIES_TO_BASKET" => $arParams["ADD_PROPERTIES_TO_BASKET"],
                                    "PARTIAL_PRODUCT_PROPERTIES" => $arParams["PARTIAL_PRODUCT_PROPERTIES"],
                                    "PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
                                    "OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
                                    "CART_PROPERTIES_" . $arResult["OFFERS_IBLOCK"] => $arParams["OFFERS_CART_PROPERTIES"],
                                    "OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
                                    "OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
                                    "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
                                    "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
                                    "OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
                                    "OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
                                    "OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],
                                    "SECTION_ID" => "",
                                    "SECTION_CODE" => "",
                                    "SECTION_URL" => "",
                                    "DETAIL_URL" => "",
                                    "USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"] ? "Y" : "N",
                                    "CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
                                    "CURRENCY_ID" => $arParams["CURRENCY_ID"],
                                    "HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
                                    "HIDE_NOT_AVAILABLE_OFFERS" => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
                                    "TEXT_LABEL_GIFT" => $arParams["GIFTS_DETAIL_TEXT_LABEL_GIFT"],
                                    "PRODUCT_DISPLAY_MODE" => $arParams["PRODUCT_DISPLAY_MODE"],
                                    "PRODUCT_ROW_VARIANTS" => "",
                                    "DEFERRED_PRODUCT_ROW_VARIANTS" => Bitrix\Main\Web\Json::encode(SaleProductsGiftComponent::predictRowVariants(3, $arParams["GIFTS_DETAIL_PAGE_ELEMENT_COUNT"])),
                                    "ADDITIONAL_PICT_PROP_" . $arParams["IBLOCK_ID"] => $arParams["ADD_PICT_PROP"],
                                    "ADDITIONAL_PICT_PROP_" . $arResult["OFFERS_IBLOCK"] => $arParams["OFFER_ADD_PICT_PROP"],
                                    "OFFER_TREE_PROPS" => $arParams["OFFER_TREE_PROPS"],
                                    "OFFER_TREE_PROPS_" . $arResult["OFFERS_IBLOCK"] => $arParams["OFFER_TREE_PROPS"],
                                    "PRODUCT_SUBSCRIPTION" => $arParams["PRODUCT_SUBSCRIPTION"],
                                    "SHOW_DISCOUNT_PERCENT" => $arParams["SHOW_DISCOUNT_PERCENT"],
                                    "SHOW_OLD_PRICE" => $arParams["SHOW_OLD_PRICE"],
                                    "SHOW_MAX_QUANTITY" => $arParams["SHOW_MAX_QUANTITY"],
                                    "MESS_SHOW_MAX_QUANTITY" => $arParams["~MESS_SHOW_MAX_QUANTITY"],
                                    "RELATIVE_QUANTITY_FACTOR" => $arParams["RELATIVE_QUANTITY_FACTOR"],
                                    "MESS_RELATIVE_QUANTITY_MANY" => $arParams["~MESS_RELATIVE_QUANTITY_MANY"],
                                    "MESS_RELATIVE_QUANTITY_FEW" => $arParams["~MESS_RELATIVE_QUANTITY_FEW"],
                                    "MESS_BTN_BUY" => $arParams["~MESS_BTN_BUY"],
                                    "MESS_BTN_ADD_TO_BASKET" => $arParams["~MESS_BTN_ADD_TO_BASKET"],
                                    "GIFTS_MESS_BTN_BUY" => $arParams["~GIFTS_MESS_BTN_BUY"],
                                    "GIFTS_MESS_BTN_ADD_TO_BASKET" => $arParams["~GIFTS_MESS_BTN_BUY"],
                                    "MESS_BTN_SUBSCRIBE" => $arParams["~MESS_BTN_SUBSCRIBE"],
                                    "MESS_BTN_DETAIL" => $arParams["~MESS_BTN_DETAIL"],
                                    "MESS_NOT_AVAILABLE" => $arParams["~MESS_NOT_AVAILABLE"],
                                    "MESS_BTN_COMPARE" => $arParams["~MESS_BTN_COMPARE"],
                                    "POTENTIAL_PRODUCT_TO_BUY" => array(
                                        "ID" => isset($arResult["ID"]) ? $arResult["ID"] : null,
                                        "MODULE" => isset($arResult["MODULE"]) ? $arResult["MODULE"] : "catalog",
                                        "PRODUCT_PROVIDER_CLASS" => isset($arResult["~PRODUCT_PROVIDER_CLASS"]) ? $arResult["~PRODUCT_PROVIDER_CLASS"] : "\Bitrix\Catalog\Product\CatalogProvider",
                                        "QUANTITY" => isset($arResult["QUANTITY"]) ? $arResult["QUANTITY"] : null,
                                        "IBLOCK_ID" => isset($arResult["IBLOCK_ID"]) ? $arResult["IBLOCK_ID"] : null,
                                        "PRIMARY_OFFER_ID" => isset($arResult["OFFERS"][$arResult["OFFERS_SELECTED"]]["ID"]) ? $arResult["OFFERS"][$arResult["OFFERS_SELECTED"]]["ID"] : null,
                                        "SECTION" => array(
                                            "ID" => isset($arResult["SECTION"]["ID"]) ? $arResult["SECTION"]["ID"] : null,
                                            "IBLOCK_ID" => isset($arResult["SECTION"]["IBLOCK_ID"]) ? $arResult["SECTION"]["IBLOCK_ID"] : null,
                                            "LEFT_MARGIN" => isset($arResult["SECTION"]["LEFT_MARGIN"]) ? $arResult["SECTION"]["LEFT_MARGIN"] : null,
                                            "RIGHT_MARGIN" => isset($arResult["SECTION"]["RIGHT_MARGIN"]) ? $arResult["SECTION"]["RIGHT_MARGIN"] : null,
                                        )
                                    ),
                                    "USE_ENHANCED_ECOMMERCE" => $arParams["USE_ENHANCED_ECOMMERCE"],
                                    "DATA_LAYER_NAME" => $arParams["DATA_LAYER_NAME"],
                                    "BRAND_PROPERTY" => $arParams["BRAND_PROPERTY"],
                                    "ADD_TO_BASKET_ACTION" => $arParams["ADD_TO_BASKET_ACTION"],
                                    "COMPARE_PATH" => $arParams["COMPARE_PATH"],
                                    "COMPARE_NAME" => $arParams["COMPARE_NAME"],
                                    "DETAIL_ADD_PICT_PROP" => $arParams["ADD_PICT_PROP"],
                                    "DETAIL_OFFER_ADD_PICT_PROP" => $arParams["OFFER_ADD_PICT_PROP"],
                                    "DETAIL_USE_RATIO_IN_RANGES" => $arParams["USE_RATIO_IN_RANGES"],
                                    "DETAIL_PROPERTY_CODE" => $arParams["PROPERTY_CODE"],
                                    "DETAIL_OFFERS_FIELD_CODE" => $arParams["OFFERS_FIELD_CODE"],
                                    "DETAIL_OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
                                    "DETAIL_MAIN_BLOCK_PROPERTY_CODE" => $arParams["MAIN_BLOCK_PROPERTY_CODE"],
                                    "DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE" => $arParams["MAIN_BLOCK_OFFERS_PROPERTY_CODE"],
                                    "DETAIL_IMAGE_RESOLUTION" => $arParams["IMAGE_RESOLUTION"],
                                    "DETAIL_ADD_DETAIL_TO_SLIDER" => $arParams["ADD_DETAIL_TO_SLIDER"],
                                    "DETAIL_DETAIL_PICTURE_MODE" => $arParams["DETAIL_PICTURE_MODE"],
                                    "DETAIL_SHOW_SLIDER" => $arParams["SHOW_SLIDER"],
                                    "DETAIL_SLIDER_INTERVAL" => $arParams["SLIDER_INTERVAL"],
                                    "DETAIL_SLIDER_PROGRESS" => $arParams["SLIDER_PROGRESS"],
                                    "USE_GIFTS_DETAIL" => $arParams["USE_GIFTS_DETAIL"],
                                    "GIFTS_DETAIL_PAGE_ELEMENT_COUNT" => $arParams["GIFTS_DETAIL_PAGE_ELEMENT_COUNT"],
                                    "GIFTS_DETAIL_HIDE_BLOCK_TITLE" => $arParams["GIFTS_DETAIL_HIDE_BLOCK_TITLE"],
                                    "GIFTS_DETAIL_BLOCK_TITLE" => $arParams["GIFTS_DETAIL_BLOCK_TITLE"],
                                    "GIFTS_DETAIL_TEXT_LABEL_GIFT" => $arParams["GIFTS_DETAIL_TEXT_LABEL_GIFT"],
                                    "GIFTS_MESS_BTN_BUY" => $arParams["~GIFTS_MESS_BTN_BUY"],
                                    "USE_STORE" => $arParams["USE_STORE"],
                                    "STORE_PATH" => $arParams["STORE_PATH"],
                                    "STORES" => $arParams["STORES"],
                                    "USE_MIN_AMOUNT" => $arParams["USE_MIN_AMOUNT"],
                                    "USER_FIELDS" => $arParams["USER_FIELDS"],
                                    "FIELDS" => $arParams["FIELDS"],
                                    "MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
                                    "SHOW_EMPTY_STORE" => $arParams["SHOW_EMPTY_STORE"],
                                    "SHOW_GENERAL_STORE_INFORMATION" => $arParams["SHOW_GENERAL_STORE_INFORMATION"],
                                    "MAIN_TITLE" => $arParams["~MAIN_TITLE"],
                                    "USE_REVIEW" => $arParams["USE_REVIEW"],
                                    "REVIEWS_IBLOCK_TYPE" => $arParams["REVIEWS_IBLOCK_TYPE"],
                                    "REVIEWS_IBLOCK_ID" => $arParams["REVIEWS_IBLOCK_ID"],
                                    "REVIEWS_NEWS_COUNT" => $arParams["REVIEWS_NEWS_COUNT"],
                                    "REVIEWS_SORT_BY1" => $arParams["REVIEWS_SORT_BY1"],
                                    "REVIEWS_SORT_ORDER1" => $arParams["REVIEWS_SORT_ORDER1"],
                                    "REVIEWS_SORT_BY2" => $arParams["REVIEWS_SORT_BY2"],
                                    "REVIEWS_SORT_ORDER2" => $arParams["REVIEWS_SORT_ORDER2"],
                                    "REVIEWS_ACTIVE_DATE_FORMAT" => $arParams["REVIEWS_ACTIVE_DATE_FORMAT"],
                                    "REVIEWS_PROPERTY_CODE" => $arParams["REVIEWS_PROPERTY_CODE"],
                                    "MESS_REVIEWS_TAB" => $arParams["MESS_REVIEWS_TAB"],
                                    "SET_ITEMS_COUNT" => $arParams["SET_ITEMS_COUNT"],
                                    "REINIT_ADD_BUY_URL_TEMPLATE" => $arParams["REINIT_ADD_BUY_URL_TEMPLATE"],
                                    "OBJECTS_USE_REVIEW" => $arParams["OBJECTS_USE_REVIEW"],
                                    "OBJECTS_REVIEWS_IBLOCK_ID" => $arParams["OBJECTS_REVIEWS_IBLOCK_ID"],
                                    "CONTACTS_IBLOCK_ID" => $arParams["CONTACTS_IBLOCK_ID"],
                                    "CONTACTS_USE_REVIEW" => $arParams["CONTACTS_USE_REVIEW"],
                                    "CONTACTS_REVIEWS_IBLOCK_ID" => $arParams["CONTACTS_REVIEWS_IBLOCK_ID"],
                                    "POPUP_MODE" => $arParams["POPUP_MODE"],
                                    "QUICK_VIEW" => isset($arParams["POPUP_MODE"]) && $arParams["POPUP_MODE"] == "Y" ? "OFF" : $arSettings["QUICK_VIEW"]
                                ),
                                $component,
                                array("HIDE_ICONS" => "Y")
                            ); ?>
                        </div>
                    <? }
                    //DETAIL_TEXT
                    if ($showDescription) { ?>
                        <div class="col-xs-8 product-item-detail-description" itemprop="description">
                            <div class="h2"><?= $arParams["MESS_DESCRIPTION_TAB"] ?></div>
                            <!-- PATTERN -->
                            <?= $arResult["DETAIL_TEXT"] ?>
                            <!-- END_PATTERN -->
                        <? } else { ?>
                            <div itemprop="description" style="display:none">
                                <?= $arResult["NAME"] . " в интернет-магазине сантехники Santehpodbor" ?>
                            <? } ?>
                        </div>
                        <?//BRAND//
                        /*if(!empty($arResult["PROPERTIES"]["BRAND"]["FULL_VALUE"]["CERTIFICATE"]) && strpos($arResult["PROPERTIES"]["BRAND"]["FULL_VALUE"]["CERTIFICATE"], "/upload/")===0 ) {?>
    <div class="col-xs-4 product-item-detail-description" itemprop="description">
        <img src="<?=$arResult['PROPERTIES']['BRAND']['FULL_VALUE']['CERTIFICATE']?>" alt="<?=$arResult['PROPERTIES']['BRAND']['FULL_VALUE']['NAME']?>" title="<?=$arResult['PROPERTIES']['BRAND']['FULL_VALUE']['NAME']?>" />
    </div>
    <?}*/ ?>
                    </div>

                    <?//PROPERTIES
                    $spDimensionValues = [];
                    $spDimensionMap = [
                        'D' => ['длина'],
                        'W' => ['ширина'],
                        'G' => ['глубина']
                    ];
                    $spTrimPropertyName = function ($name) {
                        $parts = explode(' - ', $name, 2);
                        return isset($parts[1]) ? trim($parts[1]) : $name;
                    };
                    $spPrepareProperty = function ($property) use ($spTrimPropertyName, $spDimensionMap, &$spDimensionValues) {
                        $originalName = $property["NAME"];
                        $property["NAME"] = $spTrimPropertyName($originalName);
                        if (is_array($property["DISPLAY_VALUE"])) {
                            $property["DISPLAY_VALUE"] = implode(' / ', $property["DISPLAY_VALUE"]);
                        }
                        $displayValue = $property["DISPLAY_VALUE"];

                        foreach ($spDimensionMap as $dimension => $keywords) {
                            foreach ($keywords as $keyword) {
                                if (mb_stripos($property["NAME"], $keyword) !== false || mb_stripos($originalName, $keyword) !== false) {
                                    $spDimensionValues[$dimension] = $displayValue;
                                    return [null, true];
                                }
                            }
                        }

                        return [$property, false];
                    };

                    if (!empty($arResult["PROPS_GROUPS"])) {
                        foreach ($arResult["PROPS_GROUPS"] as $groupIndex => &$group) {
                            if (!empty($group["PROPERTIES"])) {
                                foreach ($group["PROPERTIES"] as $propertyIndex => $property) {
                                    list($preparedProperty, $isDimension) = $spPrepareProperty($property);
                                    if ($isDimension) {
                                        unset($group["PROPERTIES"][$propertyIndex]);
                                        continue;
                                    }
                                    $group["PROPERTIES"][$propertyIndex] = $preparedProperty;
                                }
                                $group["PROPERTIES"] = array_values($group["PROPERTIES"]);
                                if (empty($group["PROPERTIES"])) {
                                    unset($arResult["PROPS_GROUPS"][$groupIndex]);
                                }
                            }
                        }
                        unset($group);
                    }

                    if (!empty($arResult["PROPS_UNGROUPS"])) {
                        foreach ($arResult["PROPS_UNGROUPS"] as $propertyIndex => $property) {
                            list($preparedProperty, $isDimension) = $spPrepareProperty($property);
                            if ($isDimension) {
                                unset($arResult["PROPS_UNGROUPS"][$propertyIndex]);
                                continue;
                            }
                            $arResult["PROPS_UNGROUPS"][$propertyIndex] = $preparedProperty;
                        }
                        $arResult["PROPS_UNGROUPS"] = array_values($arResult["PROPS_UNGROUPS"]);
                    }

                    if (!empty($arResult["DISPLAY_PROPERTIES"])) {
                        foreach ($arResult["DISPLAY_PROPERTIES"] as $propertyCode => $property) {
                            list($preparedProperty, $isDimension) = $spPrepareProperty($property);
                            if ($isDimension) {
                                unset($arResult["DISPLAY_PROPERTIES"][$propertyCode]);
                                continue;
                            }
                            $arResult["DISPLAY_PROPERTIES"][$propertyCode] = $preparedProperty;
                        }
                    }

                    $dimensionOrder = ['D', 'W', 'G'];
                    $dimensionParts = [];
                    foreach ($dimensionOrder as $dimensionKey) {
                        if (!empty($spDimensionValues[$dimensionKey])) {
                            $dimensionParts[] = $spDimensionValues[$dimensionKey];
                        }
                    }
                    if (!empty($dimensionParts)) {
                        $dimensionProperty = [
                            "NAME" => "Габариты (Д Ш Г)",
                            "DISPLAY_VALUE" => implode('х', $dimensionParts)
                        ];
                        if (empty($arResult["PROPS_UNGROUPS"])) {
                            $arResult["PROPS_UNGROUPS"] = [];
                        }
                        array_unshift($arResult["PROPS_UNGROUPS"], $dimensionProperty);
                        if (empty($arResult["DISPLAY_PROPERTIES"])) {
                            $arResult["DISPLAY_PROPERTIES"] = [];
                        }
                        $arResult["DISPLAY_PROPERTIES"] = array_merge(["GABARITS" => $dimensionProperty], $arResult["DISPLAY_PROPERTIES"]);
                    }

                    if ($arSettings["TAB_PROPERTIES"] == "Y" && (!empty($arResult["DISPLAY_PROPERTIES"]) || (($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST") && $arResult["SHOW_OFFERS_PROPS"]))) { ?>
                        <div class="product-item-detail-tab-content" data-entity="tab-container" data-value="properties">
                            <div class="product-item-detail-properties-container">
                                <div class="h2"><?= Loc::getMessage("CT_BCE_CATALOG_PROPERTIES") ?></div>
                                <div class="product-item-detail-properties-block" <?= (($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST") && $arResult["SHOW_OFFERS_PROPS"] ? " id='" . $itemIds["DISPLAY_PROP_DIV"] . "'" : ""); ?>>
                                    <? if (!empty($arResult["PROPS_GROUPS"])) {
                                        foreach ($arResult["PROPS_GROUPS"] as $group) { ?>
                                            <div class="product-item-detail-properties-group">
                                                <div class="product-item-detail-properties-group-name"><?= $group["NAME"] ?></div>
                                                <div class="product-item-detail-properties-group-val"></div>
                                            </div>
                                            <? foreach ($group["PROPERTIES"] as $property) { ?>
                                                <div class="product-item-detail-properties-group-property">
                                                    <div class="product-item-detail-properties-group-property-name">
                                                        <?= $property["NAME"] ?></div>
                                                    <div class="product-item-detail-properties-group-property-val">
                                                        <?= $property["DISPLAY_VALUE"] ?></div>
                                                </div>
                                            <? }
                                            unset($property);
                                        }
                                        unset($group);
                                        if (!empty($arResult["PROPS_UNGROUPS"])) { ?>
                                            <div class="product-item-detail-properties-group">
                                                <div class="product-item-detail-properties-group-name">
                                                    <?= $arSettings["PROPS_UNGROUPS_NAME"] ?></div>
                                                <div class="product-item-detail-properties-group-val"></div>
                                            </div>
                                            <? foreach ($arResult["PROPS_UNGROUPS"] as $property) { ?>
                                                <div class="product-item-detail-properties-group-property">
                                                    <div class="product-item-detail-properties-group-property-name">
                                                        <?= $property["NAME"] ?></div>
                                                    <div class="product-item-detail-properties-group-property-val">
                                                        <?= $property["DISPLAY_VALUE"] ?></div>
                                                </div>
                                            <? }
                                            unset($property);
                                        }
                                    } elseif (!empty($arResult["DISPLAY_PROPERTIES"])) {
                                        foreach ($arResult["DISPLAY_PROPERTIES"] as $property) { ?>
                                            <div class="product-item-detail-properties">
                                                <div class="product-item-detail-properties-name"><?= $property["NAME"] ?></div>
                                                <div class="product-item-detail-properties-val"><?= $property["DISPLAY_VALUE"] ?>
                                                </div>
                                            </div>
                                        <? }
                                        unset($property);
                                    } ?>
                                </div>
                            </div>
                        </div>
                    <? }
                    //FREE_TAB
                    if (!empty($arResult["PROPERTIES"]["FREE_TAB"]["VALUE"])) { ?>
                        <div class="product-item-detail-tab-content" data-entity="tab-container" data-value="free-tab">
                            <div class="h2"><?= $arResult["PROPERTIES"]["FREE_TAB"]["NAME"] ?></div>
                            <?= $arResult["PROPERTIES"]["FREE_TAB"]["~VALUE"]["TEXT"]; ?>
                        </div>
                    <? }
                    //FILES_DOCS
                    if (!empty($arResult["PROPERTIES"]["FILES_DOCS"]["FULL_VALUE"])) { ?>
                        <div class="product-item-detail-tab-content" data-entity="tab-container" data-value="files-docs">
                            <div class="h2"><?= $arResult["PROPERTIES"]["FILES_DOCS"]["NAME"] ?></div>
                            <div class="row product-item-detail-files-docs">
                                <? foreach ($arResult["PROPERTIES"]["FILES_DOCS"]["FULL_VALUE"] as $key => $arDoc) { ?><!--
                             -->
                                    <div class="col-xs-12 col-md-4">
                                        <a class="product-item-detail-files-docs-item" href="<?= $arDoc['SRC'] ?>"
                                            target="_blank">
                                            <div class="product-item-detail-files-docs-icon" data-type="<?= $arDoc['TYPE'] ?>">
                                            </div>
                                            <div class="product-item-detail-files-docs-block">
                                                <span
                                                    class="product-item-detail-files-docs-name"><?= !empty($arDoc["DESCRIPTION"]) ? $arDoc["DESCRIPTION"] : $arDoc["NAME"] ?></span>
                                                <span
                                                    class="product-item-detail-files-docs-size"><?= Loc::getMessage("CT_BCE_CATALOG_SIZE") . $arDoc["SIZE"] ?></span>
                                            </div>
                                        </a>
                                    </div><!--
                         -->
                                <? }
                                unset($key, $arDoc); ?>
                            </div>
                        </div>
                    <? }
                    //STORES//
                    if ($arParams["USE_STORE"] == "Y" && (!$haveOffers || ($haveOffers && ($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST")))) { ?>
                        <div class="product-item-detail-tab-content" data-entity="tab-container" data-value="stores">
                            <? $APPLICATION->IncludeComponent(
                                "bitrix:catalog.store.amount",
                                ".default",
                                array(
                                    "ELEMENT_ID" => $arResult["ID"],
                                    "STORE_PATH" => $arParams["STORE_PATH"],
                                    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                                    "CACHE_TIME" => $arParams["CACHE_TIME"],
                                    "MAIN_TITLE" => $arParams["MAIN_TITLE"],
                                    "USE_MIN_AMOUNT" => $arParams["USE_MIN_AMOUNT"],
                                    "MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
                                    "STORES" => $arParams["STORES"],
                                    "SHOW_EMPTY_STORE" => $arParams["SHOW_EMPTY_STORE"],
                                    "SHOW_GENERAL_STORE_INFORMATION" => $arParams["SHOW_GENERAL_STORE_INFORMATION"],
                                    "USER_FIELDS" => $arParams["USER_FIELDS"],
                                    "FIELDS" => $arParams["FIELDS"]
                                ),
                                $component,
                                array("HIDE_ICONS" => "Y")
                            ); ?>
                        </div>
                    <? } ?>
                </div>

                <div class="col-xs-12 col-md-3">
                    <div class="product-item-detail-ghost-top"></div>
                    <div class="product-item-detail-pay-block">
                        <?//SHORT_CARD// ?>
                        <div class="product-item-detail-short-card">
                            <div class="product-item-detail-short-card-image">
                                <img src="" data-entity="short-card-picture" />
                            </div>
                            <div class="product-item-detail-short-card-title"><?= $name ?></div>
                        </div>
                        <?//SET_ITEMS//
                        if (!empty($arResult["SET_ITEMS"])) { ?>
                            <div class="product-item-detail-pay-complect-container mb-3">
                                <?
                                function getCorrectWordForm($number, $wordForOne, $wordForTwoToFour, $wordForMany)
                                {
                                    $number = abs($number) % 100;
                                    $lastDigit = $number % 10;

                                    if ($number > 10 && $number < 20) {
                                        return $wordForMany;
                                    }

                                    if ($lastDigit > 1 && $lastDigit < 5) {
                                        return $wordForTwoToFour;
                                    }

                                    if ($lastDigit == 1) {
                                        return $wordForOne;
                                    }

                                    return $wordForMany;
                                }
                                ?>
                                <div class="h5">В комплект входит <strong><?= count($arResult["SET_ITEMS"]) ?>
                                        <?= getCorrectWordForm(count($arResult["SET_ITEMS"]), "товар", "товара", "товаров") ?></strong>
                                </div>
                                <div
                                    class="row product-item-detail-pay-complect-items <?= strtolower($arSettings['PRODUCTS_LIST_VIEW_MOBILE']) ?>">
                                    <? foreach ($arResult["SET_ITEMS"] as $arSetItem) { ?>
                                        <div class="col-xs-2 col-md-3">
                                            <a class="product-item-detail-pay-complect-item"
                                                href="<?= $arSetItem['DETAIL_PAGE_URL'] ?>" title="<?= $arSetItem['NAME'] ?>">
                                                <?//SET_ITEMS_IMAGE// ?>
                                                <span class="product-item-detail-pay-complect-item-image">
                                                    <? if (is_array($arSetItem["PREVIEW_PICTURE"])) { ?>
                                                        <img src="<?= $arSetItem['PREVIEW_PICTURE']['SRC'] ?>"
                                                            width="<?= $arSetItem['PREVIEW_PICTURE']['WIDTH'] ?>"
                                                            height="<?= $arSetItem['PREVIEW_PICTURE']['HEIGHT'] ?>"
                                                            alt="<?= $arSetItem['NAME'] ?>" title="<?= $arSetItem['NAME'] ?>" />
                                                    <? } else { ?>
                                                        <img src="<?= SITE_TEMPLATE_PATH ?>/images/no_photo.png" width="222"
                                                            height="222" alt="<?= $arSetItem['NAME'] ?>"
                                                            title="<?= $arSetItem['NAME'] ?>" />
                                                    <? } ?>
                                                </span>
                                            </a>
                                        </div>
                                    <? }
                                    unset($arSetItem); ?>
                                </div>
                            </div>
                        <? } ?>
                        <?//PRICE// ?>
                        <div class="product-item-detail-info-container">
                            <div id="<?= $itemIds['PRICE_ID'] ?>">
                                <? if ($haveOffers && $arParams["OFFERS_VIEW"] != "PROPS" && $arParams["OFFERS_VIEW"] != "DROPDOWN_LIST") {
                                    if (($arParams["OFFERS_VIEW"] == "LIST" && $price["SQ_M_PRICE"] > 0) || $price["PRICE"] > 0) { ?>
                                        <span
                                            class="product-item-detail-price-from"><?= Loc::getMessage("CT_BCE_CATALOG_PRICE_FROM") ?></span>
                                        <span
                                            class="product-item-detail-price-current"><?= ($arParams["OFFERS_VIEW"] == "LIST" && $price["SQ_M_PRICE"] > 0 ? $price["SQ_M_PRINT_PRICE"] : $price["PRINT_PRICE"]) ?></span>
                                        <? if ($arParams["OFFERS_VIEW"] == "LIST") { ?>
                                            <span
                                                class="product-item-detail-price-measure">/<?= ($price["SQ_M_PRICE"] > 0 ? Loc::getMessage("CT_BCE_CATALOG_MEASURE_SQ_M") : $actualItem["ITEM_MEASURE"]["TITLE"]) ?></span>
                                        <? }
                                    } else { ?>
                                        <span
                                            class="product-item-detail-price-not-set"><?= Loc::getMessage("CT_BCE_CATALOG_PRICE_NOT_SET") ?></span>
                                    <? }
                                } else { ?>
                                    <span class="product-item-detail-price-not-set" data-entity="price-current-not-set"
                                        <?= ($price["SQ_M_PRICE"] > 0 ? " style='display:none;'" : ($price["PRICE"] > 0 ? " style='display:none;'" : "")) ?>><?= Loc::getMessage("CT_BCE_CATALOG_PRICE_NOT_SET") ?></span>
                                    <span class="product-item-detail-price-current" data-entity="price-current"
                                        <?= ($price["SQ_M_PRICE"] > 0 ? "" : ($price["PRICE"] > 0 ? "" : " style='display:none;'")) ?>><?= ($price["SQ_M_PRICE"] > 0 ? $price["SQ_M_PRINT_PRICE"] : $price["PRINT_PRICE"]) ?></span>
                                    <span class="product-item-detail-price-measure" data-entity="price-measure"
                                        <?= ($price["SQ_M_PRICE"] > 0 ? "" : ($price["PRICE"] > 0 ? "" : " style='display:none;'")) ?>>/<?= ($price["SQ_M_PRICE"] > 0 ? Loc::getMessage("CT_BCE_CATALOG_MEASURE_SQ_M") : $actualItem["ITEM_MEASURE"]["TITLE"]) ?></span>
                                <? } ?>
                            </div>
                            <? if ($arParams["SHOW_OLD_PRICE"] === "Y") { ?>
                                <div class="product-item-detail-price-old" id="<?= $itemIds['OLD_PRICE_ID'] ?>"
                                    <?= ($showDiscount ? "" : " style='display:none;'") ?>>
                                    <?= ($showDiscount ? ($price["SQ_M_BASE_PRICE"] > 0 ? $price["SQ_M_PRINT_BASE_PRICE"] : $price["PRINT_BASE_PRICE"]) : "") ?>
                                </div>
                                <div class="product-item-detail-price-economy" id="<?= $itemIds['DISCOUNT_PRICE_ID'] ?>"
                                    <?= ($showDiscount ? "" : " style='display:none;'") ?>>
                                    <?= ($showDiscount ? Loc::getMessage("CT_BCE_CATALOG_ECONOMY_INFO2", array("#ECONOMY#" => ($price["SQ_M_DISCOUNT"] > 0 ? $price["SQ_M_PRINT_DISCOUNT"] : $price["PRINT_DISCOUNT"]))) : "") ?>
                                </div>
                            <? } ?>
                        </div>
                        <?//QUANTITY_LIMIT
                        if ($arParams["SHOW_MAX_QUANTITY"] !== "N") {
                            if ($haveOffers) {
                                if ($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST") { ?>
                                    <div class="product-item-detail-info-container" id="<?= $itemIds['QUANTITY_LIMIT'] ?>"
                                        style="display: none;">
                                        <div class="product-item-detail-quantity">
                                            <i class="icon-ok-b product-item-detail-quantity-icon"></i>
                                            <span class="product-item-detail-quantity-val">
                                                <?= $arParams["MESS_SHOW_MAX_QUANTITY"] . "&nbsp;" ?>
                                                <span data-entity="quantity-limit-value"></span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="product-item-detail-info-container"
                                        id="<?= $itemIds['QUANTITY_LIMIT_NOT_AVAILABLE'] ?>" style="display: none;">
                                        <div class="product-item-detail-quantity product-item-detail-quantity-not-avl">
                                            <i class="icon-close-b product-item-detail-quantity-icon"></i>
                                            <span
                                                class="product-item-detail-quantity-val"><?= $arParams["MESS_NOT_AVAILABLE"] ?></span>
                                        </div>
                                    </div>
                                <? } else { ?>
                                    <div class="product-item-detail-info-container">
                                        <!--noindex-->
                                        <div
                                            class="product-item-detail-quantity<?= ($arResult['CATALOG_QUANTITY_TRACE'] === 'N' || $arResult['CATALOG_CAN_BUY_ZERO'] === 'Y' || $arResult['OFFERS_QUANTITY'] > 0 ? '' : ' product-item-detail-quantity-not-avl') ?>">
                                            <i
                                                class="icon-<?= ($arResult['CATALOG_QUANTITY_TRACE'] === 'N' || $arResult['CATALOG_CAN_BUY_ZERO'] === 'Y' || $arResult['OFFERS_QUANTITY'] > 0 ? 'ok' : 'close') ?>-b product-item-detail-quantity-icon"></i>
                                            <span class="product-item-detail-quantity-val">
                                                <? if ($arResult["CATALOG_QUANTITY_TRACE"] === "N" || $arResult["CATALOG_CAN_BUY_ZERO"] === "Y" || $arResult["OFFERS_QUANTITY"] > 0) {
                                                    echo $arParams["MESS_SHOW_MAX_QUANTITY"] . "&nbsp;";
                                                    if ($arResult["CATALOG_QUANTITY_TRACE"] === "Y" && $arResult["CATALOG_CAN_BUY_ZERO"] === "N") {
                                                        if ($arParams["SHOW_MAX_QUANTITY"] === "M") {
                                                            if ($arResult["OFFERS_QUANTITY"] >= $arParams["RELATIVE_QUANTITY_FACTOR"]) {
                                                                echo $arParams["MESS_RELATIVE_QUANTITY_MANY"];
                                                            } else {
                                                                echo $arParams["MESS_RELATIVE_QUANTITY_FEW"];
                                                            }
                                                        } else {
                                                            echo $arResult["OFFERS_QUANTITY"];
                                                        }
                                                    }
                                                } else {
                                                    echo $arParams["MESS_NOT_AVAILABLE"];
                                                } ?>
                                            </span>
                                        </div>
                                        <!--/noindex-->
                                    </div>
                                <? }
                            } else { ?>
                                <div class="product-item-detail-info-container" id="<?= $itemIds['QUANTITY_LIMIT'] ?>">
                                    <!--noindex-->
                                    <div
                                        class="product-item-detail-quantity<?= ($actualItem['CAN_BUY'] ? '' : ' product-item-detail-quantity-not-avl') ?>">
                                        <i
                                            class="icon-<?= ($actualItem['CAN_BUY'] ? 'ok' : 'close') ?>-b product-item-detail-quantity-icon"></i>
                                        <span class="product-item-detail-quantity-val">
                                        <?if($arResult['PROPERTIES']['PRODUCT_STATUS']['VALUE']) {
                                            echo $arResult['PROPERTIES']['PRODUCT_STATUS']['VALUE'];
                                        }else{?>
                                            <? if ($actualItem["CAN_BUY"]) {
                                                echo $arParams["MESS_SHOW_MAX_QUANTITY"] . "&nbsp;";
                                                if ($measureRatio && (float) $actualItem["CATALOG_QUANTITY"] > 0 && $actualItem["CATALOG_QUANTITY_TRACE"] === "Y" && $actualItem["CATALOG_CAN_BUY_ZERO"] === "N") {
                                                    if ($arParams["SHOW_MAX_QUANTITY"] === "M") {
                                                        if ((float) $actualItem["CATALOG_QUANTITY"] / $measureRatio >= $arParams["RELATIVE_QUANTITY_FACTOR"]) {
                                                            echo $arParams["MESS_RELATIVE_QUANTITY_MANY"];
                                                        } else {
                                                            echo $arParams["MESS_RELATIVE_QUANTITY_FEW"];
                                                        }
                                                    } else {
                                                        echo $actualItem["CATALOG_QUANTITY"];
                                                    }
                                                }
                                            } else {
                                                echo $arParams["MESS_NOT_AVAILABLE"];
                                            } ?>
                                        <?} ?>
                                        </span>
                                    </div>
                                    <!--/noindex-->
                                </div>
                            <? }
                        }
                        //PRICE_RANGES//
                        if ($arParams["USE_PRICE_COUNT"] && (!$haveOffers || ($haveOffers && ($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST")))) {
                            $showRanges = !$haveOffers && count($actualItem["ITEM_QUANTITY_RANGES"]) > 1;
                            $useRatio = $arParams["USE_RATIO_IN_RANGES"] === "Y"; ?>
                            <div class="product-item-detail-info-container" <?= ($showRanges ? "" : " style='display: none;'"); ?> data-entity="price-ranges-block">
                                <div class="product-item-detail-properties-block" data-entity="price-ranges-body">
                                    <? if ($showRanges) {
                                        foreach ($actualItem["ITEM_QUANTITY_RANGES"] as $range) {
                                            if ($range["HASH"] !== "ZERO-INF") {
                                                $itemPrice = false;
                                                foreach ($arResult["ITEM_PRICES"] as $itemPrice) {
                                                    if ($itemPrice["QUANTITY_HASH"] === $range["HASH"]) {
                                                        break;
                                                    }
                                                }
                                                if ($itemPrice) { ?>
                                                    <div class="product-item-detail-properties">
                                                        <div class="product-item-detail-properties-name">
                                                            <? if (is_infinite($range["SORT_TO"])) {
                                                                echo Loc::getMessage("CT_BCE_CATALOG_RANGE_FROM", array("#FROM#" => $range["SORT_FROM"] . " " . $actualItem["ITEM_MEASURE"]["TITLE"]));
                                                            } else {
                                                                echo $range["SORT_FROM"] . ($range["SORT_TO"] != $range["SORT_FROM"] ? " - " . $range["SORT_TO"] : "") . " " . $actualItem["ITEM_MEASURE"]["TITLE"];
                                                            } ?>
                                                        </div>
                                                        <div class="product-item-detail-properties-val">
                                                            <?= ($useRatio ? $itemPrice["PRINT_RATIO_PRICE"] : $itemPrice["PRINT_PRICE"]) ?>
                                                        </div>
                                                    </div>
                                                <? }
                                                unset($itemPrice);
                                            }
                                        }
                                        unset($range);
                                    } ?>
                                </div>
                            </div>
                            <? unset($showRanges, $useRatio);
                        }
                        //SKU//
                        if ($haveOffers && !empty($arResult["OFFERS_PROP"])) {
                            if ($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST") { ?>
                                <div class="product-item-detail-scu-container" id="<?= $itemIds['TREE_ID'] ?>">
                                <? }
                            foreach ($arResult["SKU_PROPS"] as $skuProperty) {
                                if (!isset($arResult["OFFERS_PROP"][$skuProperty["CODE"]]))
                                    continue;
                                $propertyId = $skuProperty["ID"];
                                $skuProps[] = array(
                                    "ID" => $propertyId,
                                    "SHOW_MODE" => $skuProperty["SHOW_MODE"],
                                    "VALUES" => $skuProperty["VALUES"],
                                    "VALUES_COUNT" => $skuProperty["VALUES_COUNT"]
                                );
                                if ($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST") { ?>
                                        <div class="product-item-detail-info-container" data-entity="sku-line-block">
                                            <div class="product-item-detail-scu-title">
                                                <?= htmlspecialcharsEx($skuProperty["NAME"]) . ($arParams["OFFERS_VIEW"] == "PROPS" && $skuProperty["SHOW_MODE"] === "PICT" ? "<span data-entity='current-option'></span>" : "") ?>
                                            </div>
                                            <? if ($arParams["OFFERS_VIEW"] == "PROPS") { ?>
                                                <div class="product-item-detail-scu-block">
                                                    <div class="product-item-detail-scu-list">
                                                        <ul class="product-item-detail-scu-item-list">
                                                            <? foreach ($skuProperty["VALUES"] as &$value) {
                                                                $value["NAME"] = htmlspecialcharsbx($value["NAME"]);
                                                                if ($skuProperty["SHOW_MODE"] === "PICT") { ?>
                                                                    <li class="product-item-detail-scu-item-color" title="<?= $value['NAME'] ?>"
                                                                        data-treevalue="<?= $propertyId ?>_<?= $value['ID'] ?>"
                                                                        data-onevalue="<?= $value['ID'] ?>"
                                                                        style="<?= (!empty($value['CODE']) ? 'background-color: #' . $value['CODE'] . ';' : (!empty($value['PICT']) ? 'background-image: url(' . $value['PICT']['SRC'] . ');' : '')); ?>">
                                                                    </li>
                                                                <? } else { ?>
                                                                    <li class="product-item-detail-scu-item-text" title="<?= $value['NAME'] ?>"
                                                                        data-treevalue="<?= $propertyId ?>_<?= $value['ID'] ?>"
                                                                        data-onevalue="<?= $value['ID'] ?>">
                                                                        <?= $value["NAME"] ?>
                                                                    </li>
                                                                <? }
                                                            }
                                                            unset($value); ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            <? } else { ?>
                                                <div class="product-item-detail-basket-props-block">
                                                    <div class="product-item-detail-basket-props-drop-down"
                                                        onclick="<?= $obName ?>.showOfferBasketPropsDropDownPopup(this, '<?= $propertyId ?>');">
                                                        <div class="drop-down-text" data-entity="current-option">-</div>
                                                        <div class="drop-down-arrow"><i class="icon-arrow-down"></i></div>
                                                        <div class="drop-down-popup" data-entity="dropdownContent"
                                                            style="display: none;">
                                                            <ul>
                                                                <? foreach ($skuProperty["VALUES"] as $value) { ?>
                                                                    <li data-treevalue="<?= $propertyId ?>_<?= $value['ID'] ?>"
                                                                        data-onevalue="<?= $value['ID'] ?>"
                                                                        onclick="<?= $obName ?>.selectOfferBasketPropsDropDownPopupItem(this);">
                                                                        <span><?= $value["NAME"] ?></span></li>
                                                                <? }
                                                                unset($value); ?>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            <? } ?>
                                        </div>
                                    <? }
                            }
                            unset($skuProperty);
                            if ($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST") { ?>
                                </div>
                            <? }
                        }
                        //BASKET_PROPERTIES//
                        if (!$haveOffers) {
                            $emptyProductProperties = empty($arResult["PRODUCT_PROPERTIES"]);
                            if ($arParams["ADD_PROPERTIES_TO_BASKET"] === "Y" && !$emptyProductProperties) { ?>
                                <div class="product-item-detail-info-container" id="<?= $itemIds['BASKET_PROP_DIV'] ?>">
                                    <? if (!empty($arResult["PRODUCT_PROPERTIES_FILL"])) {
                                        foreach ($arResult["PRODUCT_PROPERTIES_FILL"] as $propId => $propInfo) { ?>
                                            <input type="hidden" name="<?= $arParams['PRODUCT_PROPS_VARIABLE'] ?>[<?= $propId ?>]"
                                                value="<?= htmlspecialcharsbx($propInfo['ID']) ?>" />
                                            <? unset($arResult["PRODUCT_PROPERTIES"][$propId]);
                                        }
                                        unset($propId, $propInfo);
                                    }
                                 
                                    $emptyProductProperties = empty($arResult["PRODUCT_PROPERTIES"]);
                                    if (!$emptyProductProperties) {
                                        foreach ($arResult["PRODUCT_PROPERTIES"] as $propId => $propInfo) { ?>
                                            <div class="product-item-detail-basket-props-container">
                                                <div class="product-item-detail-basket-props-title">
                                                    <?= $arResult["PROPERTIES"][$propId]["NAME"] ?></div>
                                                <div class="product-item-detail-basket-props-block">
                                                    <? if ($arResult["PROPERTIES"][$propId]["PROPERTY_TYPE"] === "L" && $arResult["PROPERTIES"][$propId]["LIST_TYPE"] === "C") { ?>
                                                        <div class="product-item-detail-basket-props-input-radio">
                                                            <? foreach ($propInfo["VALUES"] as $valueId => $value) { ?>
                                                                <label>
                                                                    <input type="radio"
                                                                        name="<?= $arParams['PRODUCT_PROPS_VARIABLE'] ?>[<?= $propId ?>]"
                                                                        value="<?= $valueId ?>" <?= ($valueId == $propInfo["SELECTED"] ? " checked='checked'" : ""); ?> />
                                                                    <span class="check-container">
                                                                        <span class="check"><i class="icon-ok-b"></i></span>
                                                                    </span>
                                                                    <span class="text" title="<?= $value ?>"><?= $value ?></span>
                                                                </label>
                                                            <? }
                                                            unset($valueId, $value); ?>
                                                        </div>
                                                    <? } else { ?>
                                                        <div class="product-item-detail-basket-props-drop-down"
                                                            onclick="<?= $obName ?>.showBasketPropsDropDownPopup(this, '<?= $propId ?>');">
                                                            <? $currId = $currVal = false;
                                                            foreach ($propInfo["VALUES"] as $valueId => $value) {
                                                                if ($valueId == $propInfo["SELECTED"]) {
                                                                    $currId = $valueId;
                                                                    $currVal = $value;
                                                                }
                                                            }
                                                            unset($valueId, $value); ?>
                                                            <input type="hidden"
                                                                name="<?= $arParams['PRODUCT_PROPS_VARIABLE'] ?>[<?= $propId ?>]"
                                                                value="<?= (!empty($currId) ? $currId : ''); ?>" />
                                                            <div class="drop-down-text" data-entity="current-option">
                                                                <?= (!empty($currVal) ? $currVal : ""); ?></div>
                                                            <? unset($currVal, $currId); ?>
                                                            <div class="drop-down-arrow"><i class="icon-arrow-down"></i></div>
                                                            <div class="drop-down-popup" data-entity="dropdownContent"
                                                                style="display: none;">
                                                                <ul>
                                                                    <? foreach ($propInfo["VALUES"] as $valueId => $value) { ?>
                                                                        <li><span
                                                                                onclick="<?= $obName ?>.selectBasketPropsDropDownPopupItem(this, '<?= $valueId ?>');"><?= $value ?></span>
                                                                        </li>
                                                                    <? }
                                                                    unset($valueId, $value); ?>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    <? } ?>
                                                </div>
                                            </div>
                                        <? }
                                        unset($propId, $propInfo);
                                    } ?>
                                </div>
                            <? }
                            unset($emptyProductProperties);
                        }
                        //QUANTITY//
                        if ($arParams["USE_PRODUCT_QUANTITY"] && (!$object || ($object && $objectContacts)) && (!$haveOffers || ($haveOffers && ($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST")))) { ?>
                            <div class="product-item-detail-info-container"
                                style="<?= (!$actualItem['CAN_BUY'] ? 'display: none;' : '') ?>" data-entity="quantity-block">
                                <? if (!empty($arResult["PROPERTIES"]["M2_COUNT"]["VALUE"])) { ?>
                                    <div class="product-item-detail-amount" <?= ($isMeasurePc || $isMeasureSqM ? "" : " style='display: none;'") ?>>
                                        <a class="product-item-detail-amount-btn-minus"
                                            id="<?= $itemIds['PC_QUANTITY_DOWN_ID'] ?>" href="javascript:void(0)"
                                            rel="nofollow">-</a>
                                        <input class="product-item-detail-amount-input" id="<?= $itemIds['PC_QUANTITY_ID'] ?>"
                                            type="tel" value="<?= $price['PC_MIN_QUANTITY'] ?>" />
                                        <a class="product-item-detail-amount-btn-plus" id="<?= $itemIds['PC_QUANTITY_UP_ID'] ?>"
                                            href="javascript:void(0)" rel="nofollow">+</a>
                                        <div class="product-item-detail-amount-measure">
                                            <?= Loc::getMessage("CT_BCE_CATALOG_MEASURE_PC") ?></div>
                                    </div>
                                    <div class="product-item-detail-amount" <?= ($isMeasurePc || $isMeasureSqM ? "" : " style='display: none;'") ?>>
                                        <a class="product-item-detail-amount-btn-minus"
                                            id="<?= $itemIds['SQ_M_QUANTITY_DOWN_ID'] ?>" href="javascript:void(0)"
                                            rel="nofollow">-</a>
                                        <input class="product-item-detail-amount-input" id="<?= $itemIds['SQ_M_QUANTITY_ID'] ?>"
                                            type="tel" value="<?= $price['SQ_M_MIN_QUANTITY'] ?>" />
                                        <a class="product-item-detail-amount-btn-plus" id="<?= $itemIds['SQ_M_QUANTITY_UP_ID'] ?>"
                                            href="javascript:void(0)" rel="nofollow">+</a>
                                        <div class="product-item-detail-amount-measure">
                                            <?= Loc::getMessage("CT_BCE_CATALOG_MEASURE_SQ_M") ?></div>
                                    </div>
                                    <? if ($haveOffers) { ?>
                                        <div class="product-item-detail-amount" <?= ($isMeasurePc || $isMeasureSqM ? " style='display: none;'" : "") ?>>
                                            <a class="product-item-detail-amount-btn-minus" id="<?= $itemIds['QUANTITY_DOWN_ID'] ?>"
                                                href="javascript:void(0)" rel="nofollow">-</a>
                                            <input class="product-item-detail-amount-input" id="<?= $itemIds['QUANTITY_ID'] ?>"
                                                type="tel" value="<?= $price['MIN_QUANTITY'] ?>" />
                                            <a class="product-item-detail-amount-btn-plus" id="<?= $itemIds['QUANTITY_UP_ID'] ?>"
                                                href="javascript:void(0)" rel="nofollow">+</a>
                                            <div class="product-item-detail-amount-measure" id="<?= $itemIds['QUANTITY_MEASURE'] ?>">
                                                <?= $actualItem["ITEM_MEASURE"]["TITLE"] ?></div>
                                        </div>
                                    <? } ?>
                                    <div class="product-item-detail-total-cost" id="<?= $itemIds['TOTAL_COST_ID'] ?>"
                                        <?= ($price["MIN_QUANTITY"] != 1 || $price["PC_MIN_QUANTITY"] != 1 || $price["SQ_M_MIN_QUANTITY"] != 1 ? "" : " style='display:none;'") ?>>
                                        <?= Loc::getMessage("CT_BCE_CATALOG_TOTAL_COST") ?><span
                                            data-entity="total-cost"><?= ($price["MIN_QUANTITY"] != 1 || $price["PC_MIN_QUANTITY"] != 1 || $price["SQ_M_MIN_QUANTITY"] != 1 ? $price["PRINT_RATIO_PRICE"] : "") ?></span>
                                    </div>
                                <? } else { ?>
                                    <div class="product-item-detail-amount">
                                        <a class="product-item-detail-amount-btn-minus" id="<?= $itemIds['QUANTITY_DOWN_ID'] ?>"
                                            href="javascript:void(0)" rel="nofollow">-</a>
                                        <input class="product-item-detail-amount-input" id="<?= $itemIds['QUANTITY_ID'] ?>"
                                            type="tel" value="<?= $price['MIN_QUANTITY'] ?>" />
                                        <a class="product-item-detail-amount-btn-plus" id="<?= $itemIds['QUANTITY_UP_ID'] ?>"
                                            href="javascript:void(0)" rel="nofollow">+</a>
                                        <div class="product-item-detail-amount-measure" id="<?= $itemIds['QUANTITY_MEASURE'] ?>">
                                            <?= $actualItem["ITEM_MEASURE"]["TITLE"] ?></div>
                                    </div>
                                    <div class="product-item-detail-total-cost" id="<?= $itemIds['TOTAL_COST_ID'] ?>"
                                        <?= ($price["MIN_QUANTITY"] != 1 ? "" : " style='display:none;'") ?>>
                                        <?= Loc::getMessage("CT_BCE_CATALOG_TOTAL_COST") ?><span
                                            data-entity="total-cost"><?= ($price["MIN_QUANTITY"] != 1 ? $price["PRINT_RATIO_PRICE"] : "") ?></span>
                                    </div>
                                <? } ?>
                            </div>
                        <? }
                        //BUTTONS// ?>
                        <div class="product-item-detail-button-container" data-entity="main-button-container">
                            <!--noindex-->
                            <? if ($haveOffers && $arParams["OFFERS_VIEW"] != "PROPS" && $arParams["OFFERS_VIEW"] != "DROPDOWN_LIST") {
                                //SELECT_SKU// ?>
                                <button type="button" class="btn btn-default"
                                    id="<?= $itemIds['SELECT_SKU_LINK'] ?>"><span><?= Loc::getMessage("CT_BCE_CATALOG_SELECT_SKU_" . $arParams["OFFERS_VIEW"]) ?></span></button>
                                <?//LIST_URL//
                                    if (!empty($arResult["PROPERTIES"]["LIST_URL"]["VALUE"])) {
                                        foreach ($arResult["PROPERTIES"]["LIST_URL"]["VALUE"] as $key => $val) { ?>
                                        <a rel="nofollow" target="_blank" class="btn btn-default" href="<?= $val ?>"
                                            role="button"><?= (!empty($arResult["PROPERTIES"]["LIST_URL"]["DESCRIPTION"][$key]) ? $arResult["PROPERTIES"]["LIST_URL"]["DESCRIPTION"][$key] : "") ?></a>
                                    <? }
                                        unset($key, $val);
                                    }
                                    //BUY_INFO_MESSAGE//
                                    if (!empty($arSettings["BUY_INFO_MESSAGE"])) { ?>
                                    <div class="product-item-detail-info-message"><?= $arSettings["BUY_INFO_MESSAGE"] ?></div>
                                <? }
                            } else {
                                //BUY//
                                if (!$arParams["DISABLE_BASKET"] && (!$object || ($object && $objectContacts))) { ?>
                                    <div id="<?= $itemIds['BASKET_ACTIONS_ID'] ?>">
                                        <? if ($showAddBtn) { ?>
                                            <button type="button" class="btn btn-buy" id="<?= $itemIds['ADD_BASKET_LINK'] ?>"
                                                style="display: <?= (!$partnersUrl ? '' : 'none') ?>;" <?= ($actualItem["CAN_BUY"] && $price["PRICE"] > 0 ? "" : " disabled='disabled'") ?>><i
                                                    class="icon-cart"></i><span><?= $arParams["MESS_BTN_ADD_TO_BASKET"] ?></span></button>
                                        <? }
                                        if ($showBuyBtn) { ?>
                                            <button type="button" class="btn btn-buy" id="<?= $itemIds['BUY_LINK'] ?>"
                                                style="display: <?= (!$partnersUrl ? '' : 'none') ?>;" <?= ($actualItem["CAN_BUY"] && $price["PRICE"] > 0 ? "" : " disabled='disabled'") ?>><i
                                                    class="icon-cart"></i><span><?= $arParams["MESS_BTN_BUY"] ?></span></button>
                                        <? } ?>
                                    </div>
                                <? }
                                //PARTNERS_LINK// ?>
                                <div id="<?= $itemIds['PARTNERS_ID'] ?>">
                                    <button type="button" class="btn btn-buy" id="<?= $itemIds['PARTNERS_LINK'] ?>"
                                        style="display: <?= ($partnersUrl ? '' : 'none') ?>;" <?= ($actualItem["CAN_BUY"] && $price["PRICE"] > 0 ? "" : " disabled='disabled'") ?>><i
                                            class="icon-cart"></i><span><?= $arParams["MESS_BTN_BUY"] ?></span></button>
                                    <? if (!empty($arSettings["PARTNERS_INFO_MESSAGE"])) { ?>
                                        <div class="product-item-detail-info-message"
                                            style="display: <?= ($partnersUrl ? '' : 'none') ?>;" data-entity="partners-message">
                                            <?= $arSettings["PARTNERS_INFO_MESSAGE"] ?></div>
                                    <? } ?>
                                </div>
                                <?//LIST_URL//
                                    if (!empty($arResult["PROPERTIES"]["LIST_URL"]["VALUE"])) {
                                        foreach ($arResult["PROPERTIES"]["LIST_URL"]["VALUE"] as $key => $val) { ?>
                                        <a rel="nofollow" target="_blank" class="btn btn-default" href="<?= $val ?>"
                                            role="button"><?= (!empty($arResult["PROPERTIES"]["LIST_URL"]["DESCRIPTION"][$key]) ? $arResult["PROPERTIES"]["LIST_URL"]["DESCRIPTION"][$key] : "") ?></a>
                                    <? }
                                        unset($key, $val);
                                    }
                                    //BUY_INFO_MESSAGE//
                                    if (!empty($arSettings["BUY_INFO_MESSAGE"])) { ?>
                                    <div class="product-item-detail-info-message"><?= $arSettings["BUY_INFO_MESSAGE"] ?></div>
                                <? }
                                    //ASK_PRICE//
                                    if ($arParams["ASK_PRICE"]) { ?>
                                    <button type="button" class="btn btn-default" id="<?= $itemIds['ASK_PRICE_LINK'] ?>"
                                        style="display: <?= ($actualItem['CAN_BUY'] && $price['PRICE'] <= 0 ? '' : 'none') ?>;"><i
                                            class="icon-comment"></i><span><?= Loc::getMessage("CT_BCE_CATALOG_ASK_PRICE") ?></span></button>
                                <? }
                                    //UNDER_ORDER//
                                    if ($arParams["UNDER_ORDER"]) { ?>
                                    <button type="button" class="btn btn-default" id="<?= $itemIds['NOT_AVAILABLE_MESS'] ?>"
                                        style="display: <?= (!$actualItem['CAN_BUY'] ? '' : 'none') ?>;"><i
                                            class="icon-clock"></i><span><?= Loc::getMessage("CT_BCE_CATALOG_UNDER_ORDER") ?></span></button>
                                <? }
                                    //SUBSCRIBE//
                                    if ($showSubscribe) { ?>
                                    <? $APPLICATION->IncludeComponent(
                                        "bitrix:catalog.product.subscribe",
                                        "",
                                        array(
                                            "PRODUCT_ID" => $actualItem["ID"],
                                            "BUTTON_ID" => $itemIds["SUBSCRIBE_LINK"],
                                            "BUTTON_CLASS" => "btn btn-default",
                                            "DEFAULT_DISPLAY" => !$actualItem["CAN_BUY"],
                                            "MESS_BTN_SUBSCRIBE" => $arParams["~MESS_BTN_SUBSCRIBE"]
                                        ),
                                        $component,
                                        array("HIDE_ICONS" => "Y")
                                    ); ?>
                                <? }
                            } ?>
                            <!--/noindex-->
                        </div>
                        <?//COMPARE//
                        if ($arParams["DISPLAY_COMPARE"] && (!$haveOffers || ($haveOffers && ($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST")))) { ?>
                            <div class="product-item-detail-compare">
                                <!--noindex-->
                                <label id="<?= $itemIds['COMPARE_LINK'] ?>">
                                    <input type="checkbox" data-entity="compare-checkbox">
                                    <span class="product-item-detail-compare-checkbox"><i class="icon-ok-b"></i></span>
                                    <span class="product-item-detail-compare-title"
                                        data-entity="compare-title"><?= $arParams["MESS_BTN_COMPARE"] ?></span>
                                </label>
                                <!--/noindex-->
                            </div>
                        <? }
                        //DELIVERY_PAYMENT_METHODS//
/*
						if($arParams["OFFERS_VIEW"] != "OBJECTS") {
							if(($arSettings["AUTO_DELIVERY_CALCULATION"] == "Y" && (!$haveOffers || ($haveOffers && $arParams["OFFERS_VIEW"] != "LIST")) && (!$object || ($object && !empty($object["SITE_ID"]) && $object["SITE_ID"] != SITE_ID))) || ($object && (!empty($object["DELIVERY_METHODS"]) || !empty($object["PAYMENT_METHODS"]))) || !empty($arResult["CONTACTS"]["DELIVERY_METHODS"]) || !empty($arResult["CONTACTS"]["PAYMENT_METHODS"])) {?>
<div class="product-item-detail-methods">
    <!--noindex-->
    <?if($arSettings["AUTO_DELIVERY_CALCULATION"] == "Y" && (!$haveOffers || ($haveOffers && $arParams["OFFERS_VIEW"] != "LIST")) && (!$object || ($object && !empty($object["SITE_ID"]) && $object["SITE_ID"] != SITE_ID))) {?>
    <div class="product-item-detail-geo-delivery" id="<?=$itemIds['GEO_DELIVERY_ID']?>">
        <div class="product-item-detail-geo-delivery-icon"><i class="icon-delivery"></i></div>
        <div class="product-item-detail-geo-delivery-info">
            <div class="product-item-detail-geo-delivery-city"><span data-entity="city"></span></div>
            <div class="product-item-detail-geo-delivery-from"><?=Loc::getMessage("CT_BCE_CATALOG_GEO_DELIVERY_DETERMINE")?></div>
        </div>
        <div class="product-item-detail-geo-delivery-price"><div class="product-item-detail-geo-delivery-loader"><div><span></span></div></div></div>
    </div>
    <?} elseif(($object && !empty($object["DELIVERY_METHODS"])) || !empty($arResult["CONTACTS"]["DELIVERY_METHODS"])) {?>
    <div class="product-item-detail-method"><i class="icon-delivery"></i><span><?=($object && !empty($object["DELIVERY_METHODS"]) ? $object["DELIVERY_METHODS"] : $arResult["CONTACTS"]["DELIVERY_METHODS"])?></span></div>
    <?}
									if(($object && !empty($object["PAYMENT_METHODS"])) || !empty($arResult["CONTACTS"]["PAYMENT_METHODS"])) {?>
    <div class="product-item-detail-method"><i class="icon-cards"></i><span><?=($object && !empty($object["PAYMENT_METHODS"]) ? $object["PAYMENT_METHODS"] : $arResult["CONTACTS"]["PAYMENT_METHODS"])?></span></div>
    <?}?>
    <!--/noindex-->
</div>
<?}
						}*/
                        ?>

                        <div class="divide-line"></div>
                        <div class="product-item-detail-deliveries">
                            <div class="product-item-detail-delivery">
                                <div class="product-item-detail-delivery-icon">
                                    <svg width="14" height="19" viewBox="0 0 14 19" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M11.875 4.25H9.625C8.59075 4.25 7.75 5.0915 7.75 6.125V8.75H6.7135L5.68675 6.69575C5.164 5.6495 4.11175 5 2.9425 5C1.45825 5 0.25 6.20825 0.25 7.703L0.2905 10.538C0.3055 11.5835 0.8485 12.5285 1.744 13.0662L3.637 14.2048C3.86125 14.3398 4 14.5865 4 14.8475V17.7493C4 18.1632 4.33525 18.4993 4.75 18.4993C5.16475 18.4993 5.5 18.1632 5.5 17.7493V14.8475C5.5 14.063 5.08225 13.3243 4.41025 12.9193L2.5165 11.78C2.0695 11.5107 1.798 11.0382 1.7905 10.5155L1.75 7.691C1.75 7.03325 2.28475 6.4985 2.9425 6.4985C3.0475 6.4985 3.15025 6.509 3.25 6.5285V10.2485C3.25 10.6625 3.58525 10.9985 4 10.9985C4.41475 10.9985 4.75 10.6625 4.75 10.2485V8.17475L5.37175 9.41825C5.62675 9.9305 6.1405 10.2477 6.7135 10.2477H11.875C12.9093 10.2477 13.75 9.40625 13.75 8.37275V6.12275C13.75 5.08925 12.9093 4.24775 11.875 4.24775V4.25ZM12.25 8.375C12.25 8.582 12.0813 8.75 11.875 8.75H9.25V6.125C9.25 5.918 9.41875 5.75 9.625 5.75H11.875C12.0813 5.75 12.25 5.918 12.25 6.125V8.375ZM1 2.375C1 1.33925 1.83925 0.5 2.875 0.5C3.91075 0.5 4.75 1.33925 4.75 2.375C4.75 3.41075 3.91075 4.25 2.875 4.25C1.83925 4.25 1 3.41075 1 2.375ZM2.5 15.5V17.75C2.5 18.164 2.16475 18.5 1.75 18.5C1.33525 18.5 1 18.164 1 17.75V15.5C1 15.086 1.33525 14.75 1.75 14.75C2.16475 14.75 2.5 15.086 2.5 15.5Z"
                                            fill="#333333" />
                                    </svg>
                                </div>
                                <div class="product-item-detail-delivery-info">
                                    <div class="delivery-info-item">
                                        <span>Доставка курьером</span>
                                        <div class="dot"></div><span>Мск</span>
                                        <div class="dot"></div><span>от 1000 ₽</span>
                                    </div>
                                    <div class="delivery-info-date">
                                        <span><?= DeliveryDateCalc::deliveryDate(); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="product-item-detail-delivery popup">
                                <div class="product-item-detail-delivery-icon">
                                    <svg width="18" height="19" viewBox="0 0 18 19" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M15 12.5V4.25C15 3.65326 15.2371 3.08097 15.659 2.65901C16.081 2.23705 16.6533 2 17.25 2C17.4489 2 17.6397 1.92098 17.7804 1.78033C17.921 1.63968 18 1.44891 18 1.25C18 1.05109 17.921 0.860322 17.7804 0.71967C17.6397 0.579018 17.4489 0.5 17.25 0.5C16.2558 0.501191 15.3027 0.896661 14.5997 1.59966C13.8967 2.30267 13.5012 3.2558 13.5 4.25V12.7092L11.4495 13.3932C11.4533 13.1384 11.4137 12.8848 11.3325 12.6432L10.1678 8.81825C9.98563 8.25035 9.58595 7.77771 9.0562 7.50377C8.52644 7.22982 7.90973 7.17687 7.34103 7.3565L2.31603 8.954C1.75448 9.13584 1.28637 9.53018 1.0118 10.0527C0.73723 10.5752 0.678002 11.1844 0.846779 11.75L2.07228 15.7797C2.15236 16.0045 2.26809 16.215 2.41503 16.403L0.513029 17.0368C0.324062 17.0996 0.167805 17.235 0.0786313 17.413C-0.0105423 17.5911 -0.0253271 17.7973 0.0375292 17.9862C0.100385 18.1752 0.235734 18.3315 0.4138 18.4206C0.591866 18.5098 0.798062 18.5246 0.987029 18.4618L12.075 14.7673C11.9662 15.2093 11.959 15.6702 12.0539 16.1155C12.1488 16.5607 12.3434 16.9786 12.623 17.3379C12.9027 17.6971 13.2601 17.9883 13.6684 18.1896C14.0767 18.3909 14.5253 18.497 14.9805 18.5C18.924 18.398 18.9413 12.6267 15 12.5ZM3.49653 15.311L2.28228 11.3158C2.22641 11.1268 2.24669 10.9235 2.33877 10.7493C2.43085 10.575 2.58743 10.4438 2.77503 10.3835L7.80003 8.786C7.98802 8.72625 8.19198 8.74296 8.36774 8.83253C8.54349 8.92209 8.67689 9.07728 8.73903 9.2645L9.90003 13.094L9.90828 13.1173C9.96714 13.2901 9.96091 13.4785 9.89077 13.6471C9.82062 13.8157 9.69138 13.9529 9.52728 14.033L4.36128 15.758C4.18753 15.7985 4.00502 15.7758 3.84653 15.6939C3.68804 15.6119 3.56395 15.4762 3.49653 15.311ZM14.9805 17C14.5827 17 14.2012 16.842 13.9199 16.5607C13.6386 16.2794 13.4805 15.8978 13.4805 15.5C13.4805 15.1022 13.6386 14.7206 13.9199 14.4393C14.2012 14.158 14.5827 14 14.9805 14C15.3784 14 15.7599 14.158 16.0412 14.4393C16.3225 14.7206 16.4805 15.1022 16.4805 15.5C16.4805 15.8978 16.3225 16.2794 16.0412 16.5607C15.7599 16.842 15.3784 17 14.9805 17ZM7.53753 11C7.56737 11.0939 7.57844 11.1927 7.57009 11.2908C7.56174 11.389 7.53415 11.4845 7.48888 11.572C7.44361 11.6594 7.38155 11.7371 7.30626 11.8006C7.23096 11.8641 7.14389 11.9122 7.05003 11.942L5.20203 12.5308C5.01237 12.591 4.80653 12.5735 4.6298 12.482C4.45307 12.3905 4.31993 12.2325 4.25965 12.0429C4.19938 11.8532 4.21693 11.6474 4.30842 11.4706C4.39991 11.2939 4.55787 11.1608 4.74753 11.1005L6.59628 10.5125C6.78574 10.4525 6.9913 10.4701 7.16778 10.5615C7.34427 10.6529 7.47725 10.8106 7.53753 11Z"
                                            fill="#333333" />
                                    </svg>
                                </div>
                                <div class="product-item-detail-delivery-info">
                                    <div class="delivery-info-item">
                                        <span>Самовывоз</span>
                                        <div class="dot"></div><span>Мск</span>
                                        <div class="dot"></div><span>Бесплатно</span>
                                    </div>
                                    <div class="delivery-info-date">
                                        <span><?= DeliveryDateCalc::deliveryDate(); ?></span>
                                    </div>
                                    <div class="delivery-info-address">
                                        <address>г. Москва, 41-й км МКАД ТВК «Мельница» 3-я линия Пассаж 21-22</address>
                                    </div>
                                </div>
                            </div>
                            <div class="product-item-detail-delivery">
                                <div class="product-item-detail-delivery-icon">
                                    <svg width="18" height="19" viewBox="0 0 18 19" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M15 12.5V4.25C15 3.65326 15.2371 3.08097 15.659 2.65901C16.081 2.23705 16.6533 2 17.25 2C17.4489 2 17.6397 1.92098 17.7804 1.78033C17.921 1.63968 18 1.44891 18 1.25C18 1.05109 17.921 0.860322 17.7804 0.71967C17.6397 0.579018 17.4489 0.5 17.25 0.5C16.2558 0.501191 15.3027 0.896661 14.5997 1.59966C13.8967 2.30267 13.5012 3.2558 13.5 4.25V12.7092L11.4495 13.3932C11.4533 13.1384 11.4137 12.8848 11.3325 12.6432L10.1678 8.81825C9.98563 8.25035 9.58595 7.77771 9.0562 7.50377C8.52644 7.22982 7.90973 7.17687 7.34103 7.3565L2.31603 8.954C1.75448 9.13584 1.28637 9.53018 1.0118 10.0527C0.73723 10.5752 0.678002 11.1844 0.846779 11.75L2.07228 15.7797C2.15236 16.0045 2.26809 16.215 2.41503 16.403L0.513029 17.0368C0.324062 17.0996 0.167805 17.235 0.0786313 17.413C-0.0105423 17.5911 -0.0253271 17.7973 0.0375292 17.9862C0.100385 18.1752 0.235734 18.3315 0.4138 18.4206C0.591866 18.5098 0.798062 18.5246 0.987029 18.4618L12.075 14.7673C11.9662 15.2093 11.959 15.6702 12.0539 16.1155C12.1488 16.5607 12.3434 16.9786 12.623 17.3379C12.9027 17.6971 13.2601 17.9883 13.6684 18.1896C14.0767 18.3909 14.5253 18.497 14.9805 18.5C18.924 18.398 18.9413 12.6267 15 12.5ZM3.49653 15.311L2.28228 11.3158C2.22641 11.1268 2.24669 10.9235 2.33877 10.7493C2.43085 10.575 2.58743 10.4438 2.77503 10.3835L7.80003 8.786C7.98802 8.72625 8.19198 8.74296 8.36774 8.83253C8.54349 8.92209 8.67689 9.07728 8.73903 9.2645L9.90003 13.094L9.90828 13.1173C9.96714 13.2901 9.96091 13.4785 9.89077 13.6471C9.82062 13.8157 9.69138 13.9529 9.52728 14.033L4.36128 15.758C4.18753 15.7985 4.00502 15.7758 3.84653 15.6939C3.68804 15.6119 3.56395 15.4762 3.49653 15.311ZM14.9805 17C14.5827 17 14.2012 16.842 13.9199 16.5607C13.6386 16.2794 13.4805 15.8978 13.4805 15.5C13.4805 15.1022 13.6386 14.7206 13.9199 14.4393C14.2012 14.158 14.5827 14 14.9805 14C15.3784 14 15.7599 14.158 16.0412 14.4393C16.3225 14.7206 16.4805 15.1022 16.4805 15.5C16.4805 15.8978 16.3225 16.2794 16.0412 16.5607C15.7599 16.842 15.3784 17 14.9805 17ZM7.53753 11C7.56737 11.0939 7.57844 11.1927 7.57009 11.2908C7.56174 11.389 7.53415 11.4845 7.48888 11.572C7.44361 11.6594 7.38155 11.7371 7.30626 11.8006C7.23096 11.8641 7.14389 11.9122 7.05003 11.942L5.20203 12.5308C5.01237 12.591 4.80653 12.5735 4.6298 12.482C4.45307 12.3905 4.31993 12.2325 4.25965 12.0429C4.19938 11.8532 4.21693 11.6474 4.30842 11.4706C4.39991 11.2939 4.55787 11.1608 4.74753 11.1005L6.59628 10.5125C6.78574 10.4525 6.9913 10.4701 7.16778 10.5615C7.34427 10.6529 7.47725 10.8106 7.53753 11Z"
                                            fill="#333333" />
                                    </svg>
                                </div>
                                <div class="product-item-detail-delivery-info">
                                    <div class="delivery-info-item">
                                        <span>Доставка в регионы</span>
                                        <div class="dot"></div><span>Расчёт отдельно</span>
                                    </div>
                                    <div class="delivery-info-text">
                                        <p>Осуществляется транспортными компаниями и рассчитывается отдельно после
                                            оформления заказа</p>
                                    </div>
                                </div>
                            </div>
                            <div class="product-item-detail-delivery" style="align-items: flex-start;">
                                <div class="product-item-detail-delivery-icon">
                                    <img src="/upload/ya_delivery.svg" style="width:18px;    margin-top: 5px;">
                                </div>
                                <div class="product-item-detail-delivery-info">
                                    <div class="delivery-info-item">
                                        <span>Яндекс доставка Москва до двери или в ПВЗ</span>
                                    </div>
                                    <div class="delivery-info-text">
                                        <p>
                                            После подтверждения менеджера Ваш заказ будет отправлен в кратчайшие сроки:<br>
                                            Москва и МО - 2-3 дня<br>
                                            Регионы - 3-10 дней (зависит от расстояния)<br>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?
                        echo '<noindex>';
                        //QUICK_ORDER//
                        if ($arParams["QUICK_ORDER"] && (!$object || ($object && $objectContacts)) && (!$haveOffers || ($haveOffers && ($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST")))) {
                            $quickOrderQuantityId = $itemIds["QUANTITY_ID"];
                            if (!$haveOffers && !empty($arResult["PROPERTIES"]["M2_COUNT"]["VALUE"])) {
                                if ($isMeasurePc)
                                    $quickOrderQuantityId = $itemIds["PC_QUANTITY_ID"];
                                elseif ($isMeasureSqM)
                                    $quickOrderQuantityId = $itemIds["SQ_M_QUANTITY_ID"];
                            } ?>
                            <? $APPLICATION->IncludeComponent(
                                "altop:quick.order.enext",
                                "",
                                array(
                                    "MODE" => "PRODUCT",
                                    "PRODUCT_ID" => $actualItem["ID"],
                                    "CONTAINER_ID" => $itemIds["QUICK_ORDER_LINK"],
                                    "CONTAINER_CLASS" => "product-item-detail-quick-order" . ($minOrderSum > 0 && $price["RATIO_PRICE"] < $minOrderSum ? " product-item-detail-quick-order-hidden" : ""),
                                    "DEFAULT_DISPLAY" => !$partnersUrl && $actualItem["CAN_BUY"] && $price["PRICE"] > 0,
                                    "QUANTITY_ID" => $quickOrderQuantityId,
                                    "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
                                    "PARTIAL_PRODUCT_PROPERTIES" => $arParams["PARTIAL_PRODUCT_PROPERTIES"],
                                    "CART_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
                                    "BASKET_PROPS_ID" => $itemIds["BASKET_PROP_DIV"],
                                    "OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
                                    "BASKET_SKU_PROPS" => $arResult["OFFERS_PROP_CODES"],
                                    "OBJECT_ID" => $object ? $object["ID"] : ""
                                ),
                                $component,
                                array("HIDE_ICONS" => "Y")
                            ); ?>
                            <? unset($quickOrderQuantityId);
                        } ?>
                        </noindex>
                    </div>
                    <?//OBJECT//
                    if ($arParams["OFFERS_VIEW"] != "OBJECTS" && $object) { ?>
                        <div class="product-item-detail-object-container">
                            <a target="_blank" class="product-item-detail-object" href="<?= $object['DETAIL_PAGE_URL'] ?>">
                                <span class="product-item-detail-object-image">
                                    <? if (is_array($object["PREVIEW_PICTURE"])) { ?>
                                        <img src="<?= $object['PREVIEW_PICTURE']['SRC'] ?>"
                                            width="<?= $object['PREVIEW_PICTURE']['WIDTH'] ?>"
                                            height="<?= $object['PREVIEW_PICTURE']['HEIGHT'] ?>" alt="<?= $object['NAME'] ?>" />
                                    <? } else { ?>
                                        <img src="<?= SITE_TEMPLATE_PATH ?>/images/no_photo.png" width="222" height="222"
                                            alt="<?= $object['NAME'] ?>" title="<?= $object['NAME'] ?>" />
                                    <? } ?>
                                </span>
                                <span class="product-item-detail-object-text"><?= $object["NAME"] ?></span>
                            </a>
                            <div class="product-item-detail-object-contacts">
                                <button type="button" class="product-item-detail-object-btn"><i
                                        class="icon-phone-call"></i></button>
                            </div>
                        </div>
                    <? } ?>
                    <? if (!empty($arResult["PROPERTIES"]["BRAND"]["FULL_VALUE"]["CERTIFICATE"]) && strpos($arResult["PROPERTIES"]["BRAND"]["FULL_VALUE"]["CERTIFICATE"], "/upload/") === 0) { ?>
                        <!--<a style="margin-left: 10px" class="thumbnail" data-fancybox="1" href="<?//$arResult['PROPERTIES']['BRAND']['FULL_VALUE']['CERTIFICATE'] ?>">
<img style="padding-top: 20px" src="<?//$arResult['PROPERTIES']['BRAND']['FULL_VALUE']['CERTIFICATE'] ?>" alt="<?//$arResult['PROPERTIES']['BRAND']['FULL_VALUE']['NAME'] ?>" title="<?//$arResult['PROPERTIES']['BRAND']['FULL_VALUE']['NAME'] ?>" />
            </a>-->
                        <? if (strpos($APPLICATION->GetCurPage(), $arResult['CODE'])) { ?>
                            <!--<a class='fanc' href="#" data-toggle="modal" data-target=".dialog1">-->
                            <a class='fanc' href="#dialog1">
                                <img style="padding-top: 20px;padding-left: 10px;"
                                    src="<?= $arResult['PROPERTIES']['BRAND']['FULL_VALUE']['CERTIFICATE'] ?>"
                                    class="img-responsive img-rounded center-block" alt="certificate" title="certificate" />
                            </a>
                            <hr />
                            <!--<div class="modal fade dialog1" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
<h4 class="modal-title">Cертификат <?= $arResult['PROPERTIES']['BRAND']['FULL_VALUE']['NAME'] ?> для Santehpodbor.ru</h4>
</div>
<div class="modal-body active">
<img src="<?= $arResult['PROPERTIES']['BRAND']['FULL_VALUE']['CERTIFICATE'] ?>" class="img-responsive img-rounded center-block"  alt="<?= $arResult['PROPERTIES']['BRAND']['FULL_VALUE']['NAME'] ?>" title="<?= $arResult['PROPERTIES']['BRAND']['FULL_VALUE']['NAME'] ?>" />
</div>
</div>
</div>
</div>-->
                            <div style="display:none;">
                                <div id="dialog1" class="dialog1">
                                    <h4 class="modal-title">Cертификат
                                        <?= $arResult['PROPERTIES']['BRAND']['FULL_VALUE']['NAME'] ?> для Santehpodbor.ru</h4>
                                    <img src="<?= $arResult['PROPERTIES']['BRAND']['FULL_VALUE']['CERTIFICATE'] ?>"
                                        class="img-responsive img-rounded center-block"
                                        alt="<?= $arResult['PROPERTIES']['BRAND']['FULL_VALUE']['NAME'] ?>"
                                        title="<?= $arResult['PROPERTIES']['BRAND']['FULL_VALUE']['NAME'] ?>" />
                                </div>
                            </div>
                            <script>
                                $("a.fanc").fancybox({
                                    maxWidth: 600,
                                    height: 'auto',
                                    autoSize: true,
                                    fitToView: true,
                                });
                            </script>
                        <? } else { ?>
                            <img style="padding-top: 20px;padding-left: 10px;"
                                src="<?= $arResult['PROPERTIES']['BRAND']['FULL_VALUE']['CERTIFICATE'] ?>"
                                class="img-responsive img-rounded center-block" alt="" />
                        <? } ?>

                    <? } ?>
                    <div class="product-item-detail-ghost-bottom"></div>
                </div>
                <?//ARTICLES//
                if (!empty($arResult["PROPERTIES"]["ARTICLES"]["VALUE"])) { ?>
                    <div class="col-xs-12 product-item-detail-tab-content" data-entity="tab-container"
                        data-value="articles">
                        <div class="h2"><?= $arResult["PROPERTIES"]["ARTICLES"]["NAME"] ?></div>
                        <div class="product-item-detail-articles">
                            <? $GLOBALS["arArticlesFilter"] = array("ID" => $arResult["PROPERTIES"]["ARTICLES"]["VALUE"]); ?>
                            <? $APPLICATION->IncludeComponent(
                                "bitrix:news.list",
                                "articles",
                                array(
                                    "IBLOCK_TYPE" => "content",
                                    "IBLOCK_ID" => $arResult["PROPERTIES"]["ARTICLES"]["LINK_IBLOCK_ID"],
                                    "NEWS_COUNT" => "8",
                                    "SORT_BY1" => "SORT",
                                    "SORT_ORDER1" => "ASC",
                                    "SORT_BY2" => "ACTIVE_FROM",
                                    "SORT_ORDER2" => "DESC",
                                    "FILTER_NAME" => "arArticlesFilter",
                                    "FIELD_CODE" => array(),
                                    "PROPERTY_CODE" => array(),
                                    "CHECK_DATES" => "Y",
                                    "DETAIL_URL" => "",
                                    "AJAX_MODE" => "",
                                    "AJAX_OPTION_SHADOW" => "",
                                    "AJAX_OPTION_JUMP" => "",
                                    "AJAX_OPTION_STYLE" => "",
                                    "AJAX_OPTION_HISTORY" => "",
                                    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                                    "CACHE_TIME" => $arParams["CACHE_TIME"],
                                    "CACHE_FILTER" => $arParams["CACHE_FILTER"],
                                    "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                                    "PREVIEW_TRUNCATE_LEN" => "",
                                    "ACTIVE_DATE_FORMAT" => "",
                                    "DISPLAY_PANEL" => "",
                                    "SET_TITLE" => "N",
                                    "SET_BROWSER_TITLE" => "N",
                                    "SET_META_KEYWORDS" => "N",
                                    "SET_META_DESCRIPTION" => "N",
                                    "SET_STATUS_404" => "N",
                                    "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                                    "ADD_SECTIONS_CHAIN" => "N",
                                    "HIDE_LINK_WHEN_NO_DETAIL" => "",
                                    "PARENT_SECTION" => "",
                                    "PARENT_SECTION_CODE" => "",
                                    "DISPLAY_NAME" => "",
                                    "DISPLAY_DATE" => "",
                                    "DISPLAY_TOP_PAGER" => "N",
                                    "DISPLAY_BOTTOM_PAGER" => "Y",
                                    "PAGER_SHOW_ALWAYS" => "",
                                    "PAGER_TEMPLATE" => "arrows",
                                    "PAGER_DESC_NUMBERING" => "",
                                    "PAGER_DESC_NUMBERING_CACHE_TIME" => "",
                                    "PAGER_SHOW_ALL" => "",
                                    "AJAX_OPTION_ADDITIONAL" => "",
                                    "DISPLAY_PAGINATION" => "N"
                                ),
                                $component,
                                array("HIDE_ICONS" => "Y")
                            ); ?>
                        </div>
                    </div>
                <? }
                //SECTIONS_MORE_PRODUCTS//
                if ($moreProductsIds) { ?>
                    <div class="col-xs-12 product-item-detail-tab-content" data-entity="tab-container"
                        data-value="more-products">
                        <div class="h2"><?= $arResult["PROPERTIES"]["MORE_PRODUCTS"]["NAME"] ?></div>
                        <?//SECTIONS// ?>
                        <div class="product-item-detail-more-products-sections-links"
                            data-entity="moreProductsSectionsLinks">
                            <div class="product-item-detail-more-products-section-link active"
                                data-entity="moreProductsSectionsLink" data-section-id="0">
                                <?= Loc::getMessage("CT_BCE_CATALOG_SECTIONS_ALL") ?><span><?= count($moreProductsIds) ?></span>
                            </div>
                            <? if (!empty($arResult["PROPERTIES"]["MORE_PRODUCTS"]["SECTIONS"])) {
                                foreach ($arResult["PROPERTIES"]["MORE_PRODUCTS"]["SECTIONS"] as $arSection) { ?>
                                    <div class="product-item-detail-more-products-section-link"
                                        data-entity="moreProductsSectionsLink" data-section-id="<?= $arSection['ID'] ?>">
                                        <?= $arSection["NAME"] ?><span><?= $arSection["COUNT"] ?></span></div>
                                <? }
                                unset($arSection);
                            } ?>
                        </div>
                        <?//MORE_PRODUCTS// ?>
                        <div class="product-item-detail-more-products">
                            <? $GLOBALS["arMoreProductsFilter"] = array("ID" => $moreProductsIds); ?>
                            <? $APPLICATION->IncludeComponent(
                                "bitrix:catalog.section",
                                ".default_2",
                                array(
                                    "COMPONENT_TEMPLATE" => ".default",
                                    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                                    "SECTION_ID" => "",
                                    "SECTION_CODE" => "",
                                    "SECTION_USER_FIELDS" => array(),
                                    "FILTER_NAME" => "arMoreProductsFilter",
                                    "INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
                                    "SHOW_ALL_WO_SECTION" => "Y",
                                    "CUSTOM_FILTER" => "",
                                    "HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
                                    "HIDE_NOT_AVAILABLE_OFFERS" => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
                                    "ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
                                    "ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
                                    "ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
                                    "ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
                                    "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
                                    "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
                                    "OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
                                    "OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
                                    "PAGE_ELEMENT_COUNT" => "12",
                                    "LINE_ELEMENT_COUNT" => "4",
                                    "PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
                                    "OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
                                    "OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
                                    "OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],
                                    "PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false}]",
                                    "PRODUCT_DISPLAY_MODE" => $arParams["PRODUCT_DISPLAY_MODE"],
                                    "OFFER_TREE_PROPS" => $arParams["OFFER_TREE_PROPS"],
                                    "PRODUCT_SUBSCRIPTION" => $arParams["PRODUCT_SUBSCRIPTION"],
                                    "SHOW_DISCOUNT_PERCENT" => $arParams["SHOW_DISCOUNT_PERCENT"],
                                    "SHOW_OLD_PRICE" => $arParams["SHOW_OLD_PRICE"],
                                    "SHOW_MAX_QUANTITY" => $arParams["SHOW_MAX_QUANTITY"],
                                    "MESS_SHOW_MAX_QUANTITY" => $arParams["MESS_SHOW_MAX_QUANTITY"],
                                    "RELATIVE_QUANTITY_FACTOR" => $arParams["RELATIVE_QUANTITY_FACTOR"],
                                    "MESS_RELATIVE_QUANTITY_MANY" => $arParams["MESS_RELATIVE_QUANTITY_MANY"],
                                    "MESS_RELATIVE_QUANTITY_FEW" => $arParams["MESS_RELATIVE_QUANTITY_FEW"],
                                    "MESS_BTN_BUY" => $arParams["MESS_BTN_BUY"],
                                    "MESS_BTN_ADD_TO_BASKET" => $arParams["MESS_BTN_ADD_TO_BASKET"],
                                    "MESS_BTN_SUBSCRIBE" => $arParams["MESS_BTN_SUBSCRIBE"],
                                    "MESS_BTN_DETAIL" => $arParams["MESS_BTN_DETAIL"],
                                    "MESS_NOT_AVAILABLE" => $arParams["MESS_NOT_AVAILABLE"],
                                    "RCM_TYPE" => "personal",
                                    "RCM_PROD_ID" => "",
                                    "SHOW_FROM_SECTION" => "N",
                                    "SECTION_URL" => "",
                                    "DETAIL_URL" => "",
                                    "SECTION_ID_VARIABLE" => "SECTION_ID",
                                    "SEF_MODE" => "N",
                                    "AJAX_MODE" => "N",
                                    "AJAX_OPTION_JUMP" => "N",
                                    "AJAX_OPTION_STYLE" => "Y",
                                    "AJAX_OPTION_HISTORY" => "N",
                                    "AJAX_OPTION_ADDITIONAL" => "",
                                    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                                    "CACHE_TIME" => $arParams["CACHE_TIME"],
                                    "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                                    "SET_TITLE" => "N",
                                    "SET_BROWSER_TITLE" => "N",
                                    "BROWSER_TITLE" => "-",
                                    "SET_META_KEYWORDS" => "N",
                                    "META_KEYWORDS" => "-",
                                    "SET_META_DESCRIPTION" => "N",
                                    "META_DESCRIPTION" => "-",
                                    "SET_LAST_MODIFIED" => "N",
                                    "USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"] ? "Y" : "N",
                                    "ADD_SECTIONS_CHAIN" => "N",
                                    "CACHE_FILTER" => $arParams["CACHE_FILTER"],
                                    "USE_REVIEW" => $arParams["USE_REVIEW"],
                                    "REVIEWS_IBLOCK_TYPE" => $arParams["REVIEWS_IBLOCK_TYPE"],
                                    "REVIEWS_IBLOCK_ID" => $arParams["REVIEWS_IBLOCK_ID"],
                                    "REVIEWS_NEWS_COUNT" => $arParams["REVIEWS_NEWS_COUNT"],
                                    "REVIEWS_SORT_BY1" => $arParams["REVIEWS_SORT_BY1"],
                                    "REVIEWS_SORT_ORDER1" => $arParams["REVIEWS_SORT_ORDER1"],
                                    "REVIEWS_SORT_BY2" => $arParams["REVIEWS_SORT_BY2"],
                                    "REVIEWS_SORT_ORDER2" => $arParams["REVIEWS_SORT_ORDER2"],
                                    "REVIEWS_ACTIVE_DATE_FORMAT" => $arParams["REVIEWS_ACTIVE_DATE_FORMAT"],
                                    "REVIEWS_PROPERTY_CODE" => $arParams["REVIEWS_PROPERTY_CODE"],
                                    "MESS_REVIEWS_TAB" => $arParams["MESS_REVIEWS_TAB"],
                                    "ACTION_VARIABLE" => "action",
                                    "PRODUCT_ID_VARIABLE" => "id",
                                    "PRICE_CODE" => $arParams["PRICE_CODE"],
                                    "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"] ? "Y" : "N",
                                    "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"] ? "Y" : "N",
                                    "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"] ? "Y" : "N",
                                    "CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
                                    "CURRENCY_ID" => $arParams["CURRENCY_ID"],
                                    "BASKET_URL" => $arParams["BASKET_URL"],
                                    "USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"] ? "Y" : "N",
                                    "PRODUCT_QUANTITY_VARIABLE" => "quantity",
                                    "ADD_PROPERTIES_TO_BASKET" => $arParams["ADD_PROPERTIES_TO_BASKET"],
                                    "PRODUCT_PROPS_VARIABLE" => "prop",
                                    "PARTIAL_PRODUCT_PROPERTIES" => $arParams["PARTIAL_PRODUCT_PROPERTIES"],
                                    "PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
                                    "OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
                                    "ADD_TO_BASKET_ACTION" => $showBuyBtn ? "BUY" : "ADD",
                                    "DISPLAY_COMPARE" => $arParams["DISPLAY_COMPARE"] ? "Y" : "N",
                                    "COMPARE_PATH" => $arParams["COMPARE_PATH"],
                                    "MESS_BTN_COMPARE" => $arParams["MESS_BTN_COMPARE"],
                                    "COMPARE_NAME" => $arParams["COMPARE_NAME"],
                                    "USE_ENHANCED_ECOMMERCE" => "N",
                                    "PAGER_TEMPLATE" => "arrows",
                                    "DISPLAY_TOP_PAGER" => "N",
                                    "DISPLAY_BOTTOM_PAGER" => "N",
                                    "PAGER_TITLE" => "",
                                    "PAGER_SHOW_ALWAYS" => "N",
                                    "PAGER_DESC_NUMBERING" => "N",
                                    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                                    "PAGER_SHOW_ALL" => "N",
                                    "PAGER_BASE_LINK_ENABLE" => "N",
                                    "LAZY_LOAD" => "Y",
                                    "LOAD_ON_SCROLL" => "N",
                                    "SET_STATUS_404" => "N",
                                    "SHOW_404" => "N",
                                    "MESSAGE_404" => "",
                                    "COMPATIBLE_MODE" => "N",
                                    "DISABLE_INIT_JS_IN_COMPONENT" => "N",
                                    "DETAIL_ADD_PICT_PROP" => $arParams["ADD_PICT_PROP"],
                                    "DETAIL_OFFER_ADD_PICT_PROP" => $arParams["OFFER_ADD_PICT_PROP"],
                                    "DETAIL_USE_RATIO_IN_RANGES" => $arParams["USE_RATIO_IN_RANGES"],
                                    "DETAIL_PROPERTY_CODE" => $arParams["PROPERTY_CODE"],
                                    "DETAIL_OFFERS_FIELD_CODE" => $arParams["OFFERS_FIELD_CODE"],
                                    "DETAIL_OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
                                    "DETAIL_MAIN_BLOCK_PROPERTY_CODE" => $arParams["MAIN_BLOCK_PROPERTY_CODE"],
                                    "DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE" => $arParams["MAIN_BLOCK_OFFERS_PROPERTY_CODE"],
                                    "DETAIL_IMAGE_RESOLUTION" => $arParams["IMAGE_RESOLUTION"],
                                    "DETAIL_ADD_DETAIL_TO_SLIDER" => $arParams["ADD_DETAIL_TO_SLIDER"],
                                    "DETAIL_DETAIL_PICTURE_MODE" => $arParams["DETAIL_PICTURE_MODE"],
                                    "DETAIL_SHOW_SLIDER" => $arParams["SHOW_SLIDER"],
                                    "DETAIL_SLIDER_INTERVAL" => $arParams["SLIDER_INTERVAL"],
                                    "DETAIL_SLIDER_PROGRESS" => $arParams["SLIDER_PROGRESS"],
                                    "USE_GIFTS_DETAIL" => $arParams["USE_GIFTS_DETAIL"],
                                    "GIFTS_DETAIL_PAGE_ELEMENT_COUNT" => $arParams["GIFTS_DETAIL_PAGE_ELEMENT_COUNT"],
                                    "GIFTS_DETAIL_HIDE_BLOCK_TITLE" => $arParams["GIFTS_DETAIL_HIDE_BLOCK_TITLE"],
                                    "GIFTS_DETAIL_BLOCK_TITLE" => $arParams["GIFTS_DETAIL_BLOCK_TITLE"],
                                    "GIFTS_DETAIL_TEXT_LABEL_GIFT" => $arParams["GIFTS_DETAIL_TEXT_LABEL_GIFT"],
                                    "GIFTS_MESS_BTN_BUY" => $arParams["~GIFTS_MESS_BTN_BUY"],
                                    "USE_STORE" => $arParams["USE_STORE"],
                                    "STORE_PATH" => $arParams["STORE_PATH"],
                                    "STORES" => $arParams["STORES"],
                                    "USE_MIN_AMOUNT" => $arParams["USE_MIN_AMOUNT"],
                                    "USER_FIELDS" => $arParams["USER_FIELDS"],
                                    "FIELDS" => $arParams["FIELDS"],
                                    "MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
                                    "SHOW_EMPTY_STORE" => $arParams["SHOW_EMPTY_STORE"],
                                    "SHOW_GENERAL_STORE_INFORMATION" => $arParams["SHOW_GENERAL_STORE_INFORMATION"],
                                    "MAIN_TITLE" => $arParams["~MAIN_TITLE"],
                                    "SET_ITEMS_COUNT" => $arParams["SET_ITEMS_COUNT"],
                                    "REINIT_ADD_BUY_URL_TEMPLATE" => $arParams["REINIT_ADD_BUY_URL_TEMPLATE"],
                                    "OBJECTS_USE_REVIEW" => $arParams["OBJECTS_USE_REVIEW"],
                                    "OBJECTS_REVIEWS_IBLOCK_ID" => $arParams["OBJECTS_REVIEWS_IBLOCK_ID"],
                                    "CONTACTS_IBLOCK_ID" => $arParams["CONTACTS_IBLOCK_ID"],
                                    "CONTACTS_USE_REVIEW" => $arParams["CONTACTS_USE_REVIEW"],
                                    "CONTACTS_REVIEWS_IBLOCK_ID" => $arParams["CONTACTS_REVIEWS_IBLOCK_ID"],
                                    "QUICK_VIEW" => isset($arParams["POPUP_MODE"]) && $arParams["POPUP_MODE"] == "Y" ? "OFF" : $arSettings["QUICK_VIEW"]
                                ),
                                $component,
                                array("HIDE_ICONS" => "Y")
                            ); ?>
                        </div>
                    </div>
                <? }
                //REVIEWS//
                if (isset($arResult["REVIEWS_COUNT"])) { ?>
                    <div class="col-xs-12 product-item-detail-tab-content" data-entity="tab-container" data-value="reviews">
                        <!--noindex-->
                        <div class="h2"><?= $arParams["MESS_REVIEWS_TAB"] ?></div>
                        <div class="product-item-detail-reviews">
                            <? $GLOBALS["arReviewsFilter"] = array("PROPERTY_PRODUCT_ID" => $arResult["ID"]); ?>
                            <? $APPLICATION->IncludeComponent(
                                "bitrix:news.list",
                                "reviews",
                                array(
                                    "IBLOCK_TYPE" => $arParams["REVIEWS_IBLOCK_TYPE"],
                                    "IBLOCK_ID" => $arParams["REVIEWS_IBLOCK_ID"],
                                    "NEWS_COUNT" => $arParams["REVIEWS_NEWS_COUNT"],
                                    "SORT_BY1" => $arParams["REVIEWS_SORT_BY1"],
                                    "SORT_ORDER1" => $arParams["REVIEWS_SORT_ORDER1"],
                                    "SORT_BY2" => $arParams["REVIEWS_SORT_BY2"],
                                    "SORT_ORDER2" => $arParams["REVIEWS_SORT_ORDER2"],
                                    "FILTER_NAME" => "arReviewsFilter",
                                    "FIELD_CODE" => array(),
                                    "PROPERTY_CODE" => $arParams["REVIEWS_PROPERTY_CODE"],
                                    "CHECK_DATES" => "Y",
                                    "DETAIL_URL" => "",
                                    "AJAX_MODE" => "",
                                    "AJAX_OPTION_SHADOW" => "",
                                    "AJAX_OPTION_JUMP" => "",
                                    "AJAX_OPTION_STYLE" => "",
                                    "AJAX_OPTION_HISTORY" => "",
                                    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                                    "CACHE_TIME" => $arParams["CACHE_TIME"],
                                    "CACHE_FILTER" => $arParams["CACHE_FILTER"],
                                    "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                                    "PREVIEW_TRUNCATE_LEN" => "",
                                    "ACTIVE_DATE_FORMAT" => $arParams["REVIEWS_ACTIVE_DATE_FORMAT"],
                                    "DISPLAY_PANEL" => "",
                                    "SET_TITLE" => "N",
                                    "SET_BROWSER_TITLE" => "N",
                                    "SET_META_KEYWORDS" => "N",
                                    "SET_META_DESCRIPTION" => "N",
                                    "SET_STATUS_404" => "N",
                                    "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                                    "ADD_SECTIONS_CHAIN" => "",
                                    "HIDE_LINK_WHEN_NO_DETAIL" => "",
                                    "PARENT_SECTION" => "",
                                    "PARENT_SECTION_CODE" => "",
                                    "DISPLAY_NAME" => "",
                                    "DISPLAY_DATE" => "",
                                    "DISPLAY_TOP_PAGER" => "N",
                                    "DISPLAY_BOTTOM_PAGER" => "Y",
                                    "PAGER_SHOW_ALWAYS" => "",
                                    "PAGER_TEMPLATE" => "arrows",
                                    "PAGER_DESC_NUMBERING" => "",
                                    "PAGER_DESC_NUMBERING_CACHE_TIME" => "",
                                    "PAGER_SHOW_ALL" => "",
                                    "AJAX_OPTION_ADDITIONAL" => ""
                                ),
                                $component,
                                array("HIDE_ICONS" => "Y")
                            ); ?>
                        </div>
                        <!--/noindex-->
                    </div>
                <? } ?>
            </div>
        </div>
        <?//PREDICTION//
        if ($arResult["CATALOG"] && (!$object || ($object && $objectContacts)) && (!$haveOffers || ($haveOffers && ($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST"))) && $actualItem["CAN_BUY"] && Bitrix\Main\ModuleManager::isModuleInstalled("sale")) { ?>
            <? $APPLICATION->IncludeComponent(
                "bitrix:sale.prediction.product.detail",
                ".default",
                array(
                    "BUTTON_ID" => !$partnersUrl ? ($showBuyBtn ? $itemIds["BUY_LINK"] : $itemIds["ADD_BASKET_LINK"]) : $itemIds["PARTNERS_LINK"],
                    "CUSTOM_SITE_ID" => isset($arParams["CUSTOM_SITE_ID"]) ? $arParams["CUSTOM_SITE_ID"] : null,
                    "POTENTIAL_PRODUCT_TO_BUY" => array(
                        "ID" => isset($arResult["ID"]) ? $arResult["ID"] : null,
                        "MODULE" => isset($arResult["MODULE"]) ? $arResult["MODULE"] : "catalog",
                        "PRODUCT_PROVIDER_CLASS" => isset($arResult["PRODUCT_PROVIDER_CLASS"]) ? $arResult["PRODUCT_PROVIDER_CLASS"] : "CCatalogProductProvider",
                        "QUANTITY" => isset($arResult["QUANTITY"]) ? $arResult["QUANTITY"] : null,
                        "IBLOCK_ID" => isset($arResult["IBLOCK_ID"]) ? $arResult["IBLOCK_ID"] : null,
                        "PRIMARY_OFFER_ID" => isset($arResult["OFFERS"][0]["ID"]) ? $arResult["OFFERS"][0]["ID"] : null,
                        "SECTION" => array(
                            "ID" => isset($arResult["SECTION"]["ID"]) ? $arResult["SECTION"]["ID"] : null,
                            "IBLOCK_ID" => isset($arResult["SECTION"]["IBLOCK_ID"]) ? $arResult["SECTION"]["IBLOCK_ID"] : null,
                            "LEFT_MARGIN" => isset($arResult["SECTION"]["LEFT_MARGIN"]) ? $arResult["SECTION"]["LEFT_MARGIN"] : null,
                            "RIGHT_MARGIN" => isset($arResult["SECTION"]["RIGHT_MARGIN"]) ? $arResult["SECTION"]["RIGHT_MARGIN"] : null,
                        )
                    )
                ),
                $component,
                array("HIDE_ICONS" => "Y")
            ); ?>
        <? }
        //META// ?>
        <meta itemprop="name" content="<?= $name ?>" />
        <? if (!empty($arResult["PROPERTIES"]["BRAND"]["FULL_VALUE"])) { ?>
            <meta itemprop="brand" content="<?= $arResult['PROPERTIES']['BRAND']['FULL_VALUE']['NAME'] ?>" />
        <? } ?>
        <meta itemprop="category" content="<?= $arResult['CATEGORY_PATH'] ?>" />
        <? if ($haveOffers) {
            foreach ($arResult["JS_OFFERS"] as $offer) {
                $currentOffersList = array();
                if (!empty($offer["TREE"]) && is_array($offer["TREE"])) {
                    foreach ($offer["TREE"] as $propName => $skuId) {
                        $propId = (int) substr($propName, 5);
                        foreach ($skuProps as $prop) {
                            if ($prop["ID"] == $propId) {
                                foreach ($prop["VALUES"] as $propId => $propValue) {
                                    if ($propId == $skuId) {
                                        $currentOffersList[] = $propValue["NAME"];
                                        break;
                                    }
                                }
                                unset($propId, $propValue);
                            }
                        }
                        unset($prop);
                    }
                    unset($propName, $skuId);
                }
                $offerPrice = $offer["ITEM_PRICES"][$offer["ITEM_PRICE_SELECTED"]]; ?>
                <span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                    <meta itemprop="sku" content="<?= htmlspecialcharsbx(implode('/', $currentOffersList)) ?>" />
                    <meta itemprop="price" content="<?= $offerPrice['RATIO_PRICE'] ?>" />
                    <meta itemprop="priceCurrency" content="<?= $offerPrice['CURRENCY'] ?>" />
                    <link itemprop="availability"
                        href="http://schema.org/<?= ($offer['CAN_BUY'] ? 'InStock' : 'OutOfStock') ?>" />
                </span>
            <? }
            unset($offerPrice, $currentOffersList, $offer);
        } else { ?>
            <span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                <meta itemprop="price" content="<?= $price['RATIO_PRICE'] ?>" />
                <meta itemprop="priceCurrency" content="<?= $price['CURRENCY'] ?>" />
                <link itemprop="availability"
                    href="http://schema.org/<?= ($actualItem['CAN_BUY'] ? 'InStock' : 'OutOfStock') ?>" />
            </span>
        <? } ?>
    </div>

    <? if (isset($arParams["REINIT_ADD_BUY_URL_TEMPLATE"]) && $arParams["REINIT_ADD_BUY_URL_TEMPLATE"] == "Y") {
        $addUrlTemplate = $arResult["DETAIL_PAGE_URL"] . "?" . $arParams["ACTION_VARIABLE"] . "=ADD2BASKET&" . $arParams["PRODUCT_ID_VARIABLE"] . "=#ID#";
        $buyUrlTemplate = $arResult["DETAIL_PAGE_URL"] . "?" . $arParams["ACTION_VARIABLE"] . "=BUY&" . $arParams["PRODUCT_ID_VARIABLE"] . "=#ID#";
    } else {
        $addUrlTemplate = $arResult["~ADD_URL_TEMPLATE"];
        $buyUrlTemplate = $arResult["~BUY_URL_TEMPLATE"];
    }

    if ($haveOffers) {
        $useRatio = $arParams["USE_RATIO_IN_RANGES"] === "Y";

        foreach ($arResult["JS_OFFERS"] as $ind => &$jsOffer) {
            $fullOffer = $arResult["OFFERS"][$ind];
            $measureName = $fullOffer["ITEM_MEASURE"]["TITLE"];

            $strAllProps = "";
            $strMainProps = "";
            $strPriceRangesRatio = "";
            $strPriceRanges = "";

            if (($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST") && $arResult["SHOW_OFFERS_PROPS"]) {
                if (!empty($jsOffer["DISPLAY_PROPERTIES"])) {
                    if ($arSettings["TAB_PROPERTIES"] == "Y" && !empty($arResult["PROPS_GROUPS"])) {
                        $strAllProps .= "
<div class='product-item-detail-properties-group' data-entity='sku-props'>
    <div class='product-item-detail-properties-group-name'>" . Loc::getMessage("CT_BCE_CATALOG_PROPS_GROUPS_OFFERS_NAME") . "</div>
    <div class='product-item-detail-properties-group-val'></div>
</div>
";
                    }
                    foreach ($jsOffer["DISPLAY_PROPERTIES"] as $property) {
                        $current = "
<div class='product-item-detail-properties' data-entity='sku-props'>
    <div class='product-item-detail-properties-name'>" . $property["NAME"] . "</div>
    <div class='product-item-detail-properties-val'>" . (is_array($property["VALUE"]) ? implode(" / ", $property["VALUE"]) : $property["VALUE"]) . "</div>
</div>
";
                        if ($arSettings["TAB_PROPERTIES"] == "Y") {
                            if (!empty($arResult["PROPS_GROUPS"])) {
                                $strAllProps .= "
<div class='product-item-detail-properties-group-property' data-entity='sku-props'>
    <div class='product-item-detail-properties-group-property-name'>" . $property["NAME"] . "</div>
    <div class='product-item-detail-properties-group-property-val'>" . (is_array($property["VALUE"]) ? implode(" / ", $property["VALUE"]) : $property["VALUE"]) . "</div>
</div>
";
                            } else {
                                $strAllProps .= $current;
                            }
                        }
                        if (isset($arParams["MAIN_BLOCK_OFFERS_PROPERTY_CODE"][$property["CODE"]])) {
                            $strMainProps .= $current;
                        }
                    }
                    unset($current, $property);
                }
            }

            if ($arParams["USE_PRICE_COUNT"] && count($jsOffer["ITEM_QUANTITY_RANGES"]) > 1) {
                foreach ($jsOffer["ITEM_QUANTITY_RANGES"] as $range) {
                    if ($range["HASH"] !== "ZERO-INF") {
                        $itemPrice = false;
                        foreach ($jsOffer["ITEM_PRICES"] as $itemPrice) {
                            if ($itemPrice["QUANTITY_HASH"] === $range["HASH"]) {
                                break;
                            }
                        }
                        if ($itemPrice) {
                            $strPriceRanges .= "<div class='product-item-detail-properties'><div class='product-item-detail-properties-name'>";
                            if (is_infinite($range["SORT_TO"])) {
                                $strPriceRanges .= Loc::getMessage("CT_BCE_CATALOG_RANGE_FROM", array("#FROM#" => $range["SORT_FROM"] . " " . $measureName));
                            } else {
                                $strPriceRanges .= $range["SORT_FROM"] . ($range["SORT_TO"] != $range["SORT_FROM"] ? " - " . $range["SORT_TO"] : "") . " " . $measureName;
                            }
                            $strPriceRanges .= "</div><div class='product-item-detail-properties-val'>" . ($useRatio ? $itemPrice["PRINT_RATIO_PRICE"] : $itemPrice["PRINT_PRICE"]) . "</div></div>";
                        }
                        unset($itemPrice);
                    }
                }
                unset($range);
            }

            $jsOffer["ARTICLE"] = !empty($arResult["OFFERS"][$ind]["PROPERTIES"]["ARTNUMBER"]["VALUE"])
                ? $arResult["OFFERS"][$ind]["PROPERTIES"]["ARTNUMBER"]["VALUE"]
                : "-";

            $offerObject = !empty($arResult["OFFERS"][$ind]["PROPERTIES"]["OBJECT"]["FULL_VALUE"]) ? $arResult["OFFERS"][$ind]["PROPERTIES"]["OBJECT"]["FULL_VALUE"] : false;
            $offerObjectContacts = $offerObject["PHONE_SMS"] || $offerObject["EMAIL_EMAIL"] ? true : false;
            if ($offerObject) {
                $jsOffer["OBJECT"] = array(
                    "ID" => $offerObject["ID"],
                    "NAME" => $offerObject["NAME"],
                    "ADDRESS" => $offerObject["ADDRESS"],
                    "TIMEZONE" => $offerObject["TIMEZONE"],
                    "WORKING_HOURS" => $offerObject["WORKING_HOURS"],
                    "PHONE" => $offerObject["PHONE"],
                    "WHATSAPP" => $offerObject["WHATSAPP"],
                    "VIBER" => $offerObject["VIBER"],
                    "TELEGRAM" => $offerObject["TELEGRAM"],
                    "INSTAGRAM" => $offerObject["INSTAGRAM"],
                    "EMAIL" => $offerObject["EMAIL"],
                    "SKYPE" => $offerObject["SKYPE"],
                    "CALLBACK_FORM" => $offerObjectContacts,
                    "SITE_ID" => $offerObject["SITE_ID"]
                );
                if ($arSettings["OFFERS_ON_MAP"] == "Y") {
                    $jsOffer["OBJECT"]["DETAIL_PAGE_URL"] = $offerObject["DETAIL_PAGE_URL"];
                    $jsOffer["OBJECT"]["MAP"] = $offerObject["MAP"];
                    $jsOffer["OBJECT"]["RATING_VALUE"] = $offerObject["RATING_VALUE"];
                    $jsOffer["OBJECT"]["REVIEWS_COUNT"] = $offerObject["REVIEWS_COUNT"];
                }
            }
            unset($offerObjectContacts, $offerObject);

            $jsOffer["PARTNERS_URL"] = !empty($arResult["OFFERS"][$ind]["PROPERTIES"]["PARTNERS_URL"]["VALUE"]) ? true : (!empty($arResult["PROPERTIES"]["PARTNERS_URL"]["VALUE"]) ? true : false);

            $jsOffer["DISPLAY_PROPERTIES"] = $strAllProps;
            $jsOffer["DISPLAY_PROPERTIES_MAIN_BLOCK"] = $strMainProps;
            $jsOffer["PRICE_RANGES_HTML"] = $strPriceRanges;

            if ($arSettings["OFFERS_ON_MAP"] == "Y") {
                $jsOffer["CATALOG_QUANTITY"] = $arResult["OFFERS"][$ind]["CATALOG_QUANTITY"];
                $jsOffer["CATALOG_QUANTITY_TRACE"] = $arResult["OFFERS"][$ind]["CATALOG_QUANTITY_TRACE"];
                $jsOffer["CATALOG_CAN_BUY_ZERO"] = $arResult["OFFERS"][$ind]["CATALOG_CAN_BUY_ZERO"];
            }
        }
        unset($strAllProps, $strMainProps, $strPriceRangesRatio, $strPriceRanges, $ind, $jsOffer, $useRatio);

        $jsParams = array(
            "CONFIG" => array(
                "USE_CATALOG" => $arResult["CATALOG"],
                "SHOW_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],
                "SHOW_PRICE" => true,
                "SHOW_DISCOUNT_PERCENT" => $arParams["SHOW_DISCOUNT_PERCENT"] === "Y",
                "SHOW_OLD_PRICE" => $arParams["SHOW_OLD_PRICE"] === "Y",
                "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
                "DISPLAY_COMPARE" => $arParams["DISPLAY_COMPARE"],
                "SHOW_SKU_PROPS" => $arResult["SHOW_OFFERS_PROPS"],
                "OFFER_GROUP" => $arResult["OFFER_GROUP"],
                "MAIN_PICTURE_MODE" => $arParams["DETAIL_PICTURE_MODE"],
                "ADD_TO_BASKET_ACTION" => $arParams["ADD_TO_BASKET_ACTION"],
                "SHOW_MAX_QUANTITY" => $arParams["SHOW_MAX_QUANTITY"],
                "RELATIVE_QUANTITY_FACTOR" => $arParams["RELATIVE_QUANTITY_FACTOR"],
                "USE_SUBSCRIBE" => $showSubscribe,
                "MIN_ORDER_SUM" => $minOrderSum,
                "SHOW_SLIDER" => $arParams["SHOW_SLIDER"],
                "SLIDER_INTERVAL" => $arParams["SLIDER_INTERVAL"],
                "ALT" => $alt,
                "TITLE" => $title,
                "MAGNIFIER_ZOOM_PERCENT" => 200,
                "USE_ENHANCED_ECOMMERCE" => $arParams["USE_ENHANCED_ECOMMERCE"],
                "DATA_LAYER_NAME" => $arParams["DATA_LAYER_NAME"],
                "BRAND_PROPERTY" => !empty($arResult["DISPLAY_PROPERTIES"][$arParams["BRAND_PROPERTY"]])
                    ? $arResult["DISPLAY_PROPERTIES"][$arParams["BRAND_PROPERTY"]]["DISPLAY_VALUE"]
                    : null,
                "SITE_ID" => SITE_ID
            ),
            "PRODUCT_TYPE" => $arResult["CATALOG_TYPE"],
            "OFFERS_VIEW" => $arParams["OFFERS_VIEW"],
            "VISUAL" => $itemIds,
            "DEFAULT_PICTURE" => array(
                "PREVIEW_PICTURE" => $arResult["DEFAULT_PICTURE"],
                "DETAIL_PICTURE" => $arResult["DEFAULT_PICTURE"]
            ),
            "PRODUCT" => array(
                "ID" => $arResult["ID"],
                "IBLOCK_ID" => $arResult["IBLOCK_ID"],
                "ACTIVE" => $arResult["ACTIVE"],
                "NAME" => $arResult["~NAME"],
                "CATEGORY" => $arResult["CATEGORY_PATH"]
            ),
            "BASKET" => array(
                "QUANTITY" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                "BASKET_URL" => $arParams["BASKET_URL"],
                "SKU_PROPS" => $arResult["OFFERS_PROP_CODES"],
                "ADD_URL_TEMPLATE" => $addUrlTemplate,
                "BUY_URL_TEMPLATE" => $buyUrlTemplate
            ),
            "OFFERS" => $arResult["JS_OFFERS"],
            "OFFER_SELECTED" => $arResult["OFFERS_SELECTED"],
            "TREE_PROPS" => $skuProps
        );
    } else {
        $jsParams = array(
            "CONFIG" => array(
                "USE_CATALOG" => $arResult["CATALOG"],
                "SHOW_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],
                "SHOW_PRICE" => !empty($arResult["ITEM_PRICES"]),
                "SHOW_DISCOUNT_PERCENT" => $arParams["SHOW_DISCOUNT_PERCENT"] === "Y",
                "SHOW_OLD_PRICE" => $arParams["SHOW_OLD_PRICE"] === "Y",
                "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
                "DISPLAY_COMPARE" => $arParams["DISPLAY_COMPARE"],
                "MAIN_PICTURE_MODE" => $arParams["DETAIL_PICTURE_MODE"],
                "ADD_TO_BASKET_ACTION" => $arParams["ADD_TO_BASKET_ACTION"],
                "SHOW_MAX_QUANTITY" => $arParams["SHOW_MAX_QUANTITY"],
                "RELATIVE_QUANTITY_FACTOR" => $arParams["RELATIVE_QUANTITY_FACTOR"],
                "USE_SUBSCRIBE" => $showSubscribe,
                "MIN_ORDER_SUM" => $minOrderSum,
                "SHOW_SLIDER" => $arParams["SHOW_SLIDER"],
                "SLIDER_INTERVAL" => $arParams["SLIDER_INTERVAL"],
                "ALT" => $alt,
                "TITLE" => $title,
                "MAGNIFIER_ZOOM_PERCENT" => 200,
                "USE_ENHANCED_ECOMMERCE" => $arParams["USE_ENHANCED_ECOMMERCE"],
                "DATA_LAYER_NAME" => $arParams["DATA_LAYER_NAME"],
                "BRAND_PROPERTY" => !empty($arResult["DISPLAY_PROPERTIES"][$arParams["BRAND_PROPERTY"]])
                    ? $arResult["DISPLAY_PROPERTIES"][$arParams["BRAND_PROPERTY"]]["DISPLAY_VALUE"]
                    : null,
                "SITE_ID" => SITE_ID
            ),
            "VISUAL" => $itemIds,
            "PRODUCT_TYPE" => $arResult["CATALOG_TYPE"],
            "PRODUCT" => array(
                "ID" => $arResult["ID"],
                "IBLOCK_ID" => $arResult["IBLOCK_ID"],
                "ACTIVE" => $arResult["ACTIVE"],
                "PICT" => is_array($arResult["DETAIL_PICTURE"]) ? $arResult["DETAIL_PICTURE"] : $arResult["DEFAULT_PICTURE"],
                "NAME" => $arResult["~NAME"],
                "SUBSCRIPTION" => true,
                "ITEM_PRICE_MODE" => $arResult["ITEM_PRICE_MODE"],
                "ITEM_PRICES" => $arResult["ITEM_PRICES"],
                "ITEM_PRICE_SELECTED" => $arResult["ITEM_PRICE_SELECTED"],
                "ITEM_QUANTITY_RANGES" => $arResult["ITEM_QUANTITY_RANGES"],
                "ITEM_QUANTITY_RANGE_SELECTED" => $arResult["ITEM_QUANTITY_RANGE_SELECTED"],
                "ITEM_MEASURE_RATIOS" => $arResult["ITEM_MEASURE_RATIOS"],
                "ITEM_MEASURE_RATIO_SELECTED" => $arResult["ITEM_MEASURE_RATIO_SELECTED"],
                "ITEM_MEASURE" => $arResult["ITEM_MEASURE"],
                "SLIDER_COUNT" => $arResult["MORE_PHOTO_COUNT"],
                "SLIDER" => $arResult["MORE_PHOTO"],
                "CAN_BUY" => $arResult["CAN_BUY"],
                "CHECK_QUANTITY" => $arResult["CHECK_QUANTITY"],
                "QUANTITY_FLOAT" => is_float($measureRatio),
                "MAX_QUANTITY" => $arResult["CATALOG_QUANTITY"],
                "STEP_QUANTITY" => $measureRatio,
                "CATEGORY" => $arResult["CATEGORY_PATH"]
            ),
            "BASKET" => array(
                "QUANTITY" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                "PROPS" => $arParams["PRODUCT_PROPS_VARIABLE"],
                "BASKET_URL" => $arParams["BASKET_URL"],
                "ADD_URL_TEMPLATE" => $addUrlTemplate,
                "BUY_URL_TEMPLATE" => $buyUrlTemplate
            )
        );

        if (!empty($arResult["PROPERTIES"]["M2_COUNT"]["VALUE"])) {
            if ($isMeasurePc) {
                $jsParams["PRODUCT"]["PC_MAX_QUANTITY"] = $arResult["CATALOG_QUANTITY"];
                $jsParams["PRODUCT"]["PC_STEP_QUANTITY"] = $measureRatio;

                $jsParams["PRODUCT"]["SQ_M_MAX_QUANTITY"] = round($arResult["CATALOG_QUANTITY"] / str_replace(",", ".", $arResult["PROPERTIES"]["M2_COUNT"]["VALUE"]), 2);
                $jsParams["PRODUCT"]["SQ_M_STEP_QUANTITY"] = round($measureRatio / str_replace(",", ".", $arResult["PROPERTIES"]["M2_COUNT"]["VALUE"]), 2);
            } elseif ($isMeasureSqM) {
                $jsParams["PRODUCT"]["PC_MAX_QUANTITY"] = floor($arResult["CATALOG_QUANTITY"] / $measureRatio);
                $jsParams["PRODUCT"]["PC_STEP_QUANTITY"] = 1;

                $jsParams["PRODUCT"]["SQ_M_MAX_QUANTITY"] = $arResult["CATALOG_QUANTITY"];
                $jsParams["PRODUCT"]["SQ_M_STEP_QUANTITY"] = $measureRatio;
            }
        }
    }

    if ($arParams["DISPLAY_COMPARE"]) {
        $jsParams["COMPARE"] = array(
            "COMPARE_NAME" => $arParams["COMPARE_NAME"],
            "COMPARE_PATH" => $arParams["COMPARE_PATH"],
            "COMPARE_URL_TEMPLATE" => $arResult["~COMPARE_URL_TEMPLATE"],
            "COMPARE_DELETE_URL_TEMPLATE" => $arResult["~COMPARE_DELETE_URL_TEMPLATE"]
        );
    }

    if ($object) {
        $jsParams["OBJECT"] = array(
            "ID" => $object["ID"],
            "NAME" => $object["NAME"],
            "ADDRESS" => $object["ADDRESS"],
            "TIMEZONE" => $object["TIMEZONE"],
            "WORKING_HOURS" => $object["WORKING_HOURS"],
            "PHONE" => $object["PHONE"],
            "WHATSAPP" => $object["WHATSAPP"],
            "VIBER" => $object["VIBER"],
            "TELEGRAM" => $object["TELEGRAM"],
            "INSTAGRAM" => $object["INSTAGRAM"],
            "EMAIL" => $object["EMAIL"],
            "SKYPE" => $object["SKYPE"],
            "CALLBACK_FORM" => $objectContacts,
            "SITE_ID" => $object["SITE_ID"]
        );
    }

    $signer = new Bitrix\Main\Security\Sign\Signer;
    $signedParams = $signer->sign(base64_encode(serialize($arResult["ORIGINAL_PARAMETERS"])), "catalog.element");

    if ($moreProductsIds) {
        $signedMoreProductsIds = $signer->sign(base64_encode(serialize($moreProductsIds)), "catalog.element");
        $jsParams["MORE_PRODUCTS"] = array(
            "PRODUCTS_IDS" => $signedMoreProductsIds
        );
    } ?>

    <script type="text/javascript" data-skip-moving="true">
(function() {
    var attempts = 0;
    var maxAttempts = 200;
    var cartPopupWaitAttempts = 40;

    var initCatalogElement = function() {
        var isCatalogReady = !!(window.BX && window.JCCatalogElement);
        var isCartPopupReady = !!window.__ENEXT_CART_POPUP_PATCH_READY__ || attempts >= cartPopupWaitAttempts;

        if(!isCatalogReady || !isCartPopupReady) {
            attempts++;
            if(attempts < maxAttempts) {
                window.setTimeout(initCatalogElement, 50);
            } else if(window.console && window.console.warn) {
                window.console.warn('JCCatalogElement is not loaded or cart popup patch is not ready');
            }
            return;
        }

        if(window.jQuery) {
            window.jQuery('.advantages-wrapper, .reviews-wrapper, .feedback-wrapper').wrap('<noindex></noindex>');
        }
        BX.message({
            CATALOG_ELEMENT_SQ_M_MESSAGE: '<?= GetMessageJS("CT_BCE_CATALOG_MEASURE_SQ_M") ?>',
            CATALOG_ELEMENT_ECONOMY_INFO_MESSAGE: '<?= GetMessageJS("CT_BCE_CATALOG_ECONOMY_INFO2") ?>',
            CATALOG_ELEMENT_BASKET_URL: '<?= $arParams["BASKET_URL"] ?>',
            CATALOG_ELEMENT_ADD_BASKET_MESSAGE: '<?= ($showBuyBtn ? $arParams["MESS_BTN_BUY"] : $arParams["MESS_BTN_ADD_TO_BASKET"]) ?>',
            CATALOG_ELEMENT_ADD_BASKET_OK_MESSAGE: '<?= GetMessageJS("CT_BCE_CATALOG_ADD_OK") ?>',
            CATALOG_ELEMENT_DELAY_MESSAGE: '<?= $arParams["MESS_BTN_DELAY"] ?>',
            CATALOG_ELEMENT_DELAY_OK_MESSAGE: '<?= GetMessageJS("CT_BCE_CATALOG_DELAY_OK") ?>',
            CATALOG_ELEMENT_RELATIVE_QUANTITY_MANY: '<?= CUtil::JSEscape($arParams["MESS_RELATIVE_QUANTITY_MANY"]) ?>',
            CATALOG_ELEMENT_RELATIVE_QUANTITY_FEW: '<?= CUtil::JSEscape($arParams["MESS_RELATIVE_QUANTITY_FEW"]) ?>',
            CATALOG_ELEMENT_COMPARE_MESSAGE: '<?= $arParams["MESS_BTN_COMPARE"] ?>',
            CATALOG_ELEMENT_COMPARE_OK_MESSAGE: '<?= GetMessageJS("CT_BCE_CATALOG_COMPARE_OK") ?>',
            CATALOG_ELEMENT_GEO_DELIVERY_UNDEFINED: '<?= GetMessageJS("CT_BCE_CATALOG_GEO_DELIVERY_UNDEFINED") ?>',
            CATALOG_ELEMENT_GEO_DELIVERY_FROM: '<?= GetMessageJS("CT_BCE_CATALOG_GEO_DELIVERY_FROM") ?>',
            CATALOG_ELEMENT_GEO_DELIVERY_LOCATION: '<?= GetMessageJS("CT_BCE_CATALOG_GEO_DELIVERY_LOCATION") ?>',
            CATALOG_ELEMENT_GEO_DELIVERY_SLIDE_PANEL_TITLE: '<?= GetMessageJS("CT_BCE_CATALOG_GEO_DELIVERY_SLIDE_PANEL_TITLE") ?>',
            CATALOG_ELEMENT_SKU_ITEMS_SLIDE_PANEL_TITLE: '<?= GetMessageJS("CT_BCE_CATALOG_SKU_ITEMS") ?>',
            CATALOG_ELEMENT_OBJECT_TODAY: '<?= GetMessageJS("CT_BCE_CATALOG_OBJECT_TODAY") ?>',
            CATALOG_ELEMENT_OBJECT_24_HOURS: '<?= GetMessageJS("CT_BCE_CATALOG_OBJECT_24_HOURS") ?>',
            CATALOG_ELEMENT_OBJECT_OFF: '<?= GetMessageJS("CT_BCE_CATALOG_OBJECT_OFF") ?>',
            CATALOG_ELEMENT_OBJECT_BREAK: '<?= GetMessageJS("CT_BCE_CATALOG_OBJECT_BREAK") ?>',
            CATALOG_ELEMENT_OBJECT_LOADING: '<?= GetMessageJS("CT_BCE_CATALOG_OBJECT_LOADING"); ?>',
            CATALOG_ELEMENT_SECTIONS_ALL: '<?= GetMessageJS("CT_BCE_CATALOG_SECTIONS_ALL") ?>',
            CATALOG_ELEMENT_SECTIONS_SHOW_ALL: '<?= GetMessageJS("CT_BCE_CATALOG_SECTIONS_SHOW_ALL") ?>',
            CATALOG_ELEMENT_SECTIONS_HIDE: '<?= GetMessageJS("CT_BCE_CATALOG_SECTIONS_HIDE") ?>',
            CATALOG_ELEMENT_TEMPLATE_PATH: '<?= $templateFolder ?>',
            CATALOG_ELEMENT_PARAMETERS: '<?= CUtil::JSEscape($signedParams) ?>'
        });
        window['<?=CUtil::JSEscape($obName)?>'] = new JCCatalogElement(<?= CUtil::PhpToJSObject($jsParams, false, true) ?>);
    
    };

    initCatalogElement();
})();
</script>
    <? unset($actualItem, $itemIds, $jsParams);