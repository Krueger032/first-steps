<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Loader,
	Bitrix\Main\ModuleManager,
	Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

global $arSettings;

$APPLICATION->SetPageProperty("wideScreenMode", "-ws");?>

<div class="row">
	<div class="col-xs-12">
		<?if(isset($arParams["USE_COMMON_SETTINGS_BASKET_POPUP"]) && $arParams["USE_COMMON_SETTINGS_BASKET_POPUP"] == "Y") {
			$basketAction = (isset($arParams["COMMON_ADD_TO_BASKET_ACTION"]) ? array($arParams["COMMON_ADD_TO_BASKET_ACTION"]) : array());
		} else {
			$basketAction = (isset($arParams["DETAIL_ADD_TO_BASKET_ACTION"]) ? $arParams["DETAIL_ADD_TO_BASKET_ACTION"] : array());
		}?>
		<?$elementId = $APPLICATION->IncludeComponent("bitrix:catalog.element", "",
			array(
				"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
				"META_KEYWORDS" => $arParams["DETAIL_META_KEYWORDS"],
				"META_DESCRIPTION" => $arParams["DETAIL_META_DESCRIPTION"],
				"BROWSER_TITLE" => $arParams["DETAIL_BROWSER_TITLE"],
				"SET_CANONICAL_URL" => $arParams["DETAIL_SET_CANONICAL_URL"],
				"BASKET_URL" => $arParams["BASKET_URL"],
				"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
				"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
				"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
				"CHECK_SECTION_ID_VARIABLE" => (isset($arParams["DETAIL_CHECK_SECTION_ID_VARIABLE"]) ? $arParams["DETAIL_CHECK_SECTION_ID_VARIABLE"] : ""),
				"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
				"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
				"SET_TITLE" => $arParams["SET_TITLE"],
				"SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
				"MESSAGE_404" => $arParams["~MESSAGE_404"],
				"SET_STATUS_404" => $arParams["SET_STATUS_404"],
				"SHOW_404" => $arParams["SHOW_404"],
				"FILE_404" => $arParams["FILE_404"],
				"PRICE_CODE" => $arParams["PRICE_CODE"],
				"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
				"USE_RATIO_IN_RANGES" => $arParams["USE_RATIO_IN_RANGES"],
				"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
				"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
				"PRICE_VAT_SHOW_VALUE" => $arParams["PRICE_VAT_SHOW_VALUE"],
				"USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],
				"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
				"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ""),
				"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ""),
				"LINK_IBLOCK_TYPE" => $arParams["LINK_IBLOCK_TYPE"],
				"LINK_IBLOCK_ID" => $arParams["LINK_IBLOCK_ID"],
				"LINK_PROPERTY_SID" => $arParams["LINK_PROPERTY_SID"],
				"LINK_ELEMENTS_URL" => $arParams["LINK_ELEMENTS_URL"],

				"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
				"OFFERS_FIELD_CODE" => $arParams["DETAIL_OFFERS_FIELD_CODE"],				
				"OFFERS_PROPERTY_CODE" => $arParams["DETAIL_OFFERS_PROPERTY_CODE"],
				"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
				"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
				"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
				"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],

				"ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
				"ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
				"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
				"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
				"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
				"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
				"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
				"CURRENCY_ID" => $arParams["CURRENCY_ID"],
				"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
				"HIDE_NOT_AVAILABLE_OFFERS" => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
				"PRODUCT_DISPLAY_MODE" => $arParams["PRODUCT_DISPLAY_MODE"],
				"USE_ELEMENT_COUNTER" => $arParams["USE_ELEMENT_COUNTER"],
				"SHOW_DEACTIVATED" => $arParams["SHOW_DEACTIVATED"],
				"USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
				"STRICT_SECTION_CHECK" => (isset($arParams["DETAIL_STRICT_SECTION_CHECK"]) ? $arParams["DETAIL_STRICT_SECTION_CHECK"] : ""),
				"ADD_PICT_PROP" => $arParams["ADD_PICT_PROP"],				
				"OFFER_ADD_PICT_PROP" => $arParams["OFFER_ADD_PICT_PROP"],
				"OFFER_TREE_PROPS" => $arParams["OFFER_TREE_PROPS"],
				"PRODUCT_SUBSCRIPTION" => $arParams["PRODUCT_SUBSCRIPTION"],
				"SHOW_DISCOUNT_PERCENT" => $arParams["SHOW_DISCOUNT_PERCENT"],				
				"SHOW_OLD_PRICE" => $arParams["SHOW_OLD_PRICE"],
				"SHOW_MAX_QUANTITY" => $arParams["SHOW_MAX_QUANTITY"],
				"MESS_SHOW_MAX_QUANTITY" => (isset($arParams["~MESS_SHOW_MAX_QUANTITY"]) ? $arParams["~MESS_SHOW_MAX_QUANTITY"] : ""),
				"RELATIVE_QUANTITY_FACTOR" => (isset($arParams["RELATIVE_QUANTITY_FACTOR"]) ? $arParams["RELATIVE_QUANTITY_FACTOR"] : ""),
				"MESS_RELATIVE_QUANTITY_MANY" => (isset($arParams["~MESS_RELATIVE_QUANTITY_MANY"]) ? $arParams["~MESS_RELATIVE_QUANTITY_MANY"] : ""),
				"MESS_RELATIVE_QUANTITY_FEW" => (isset($arParams["~MESS_RELATIVE_QUANTITY_FEW"]) ? $arParams["~MESS_RELATIVE_QUANTITY_FEW"] : ""),
				"MESS_BTN_BUY" => (isset($arParams["~MESS_BTN_BUY"]) ? $arParams["~MESS_BTN_BUY"] : ""),
				"MESS_BTN_ADD_TO_BASKET" => (isset($arParams["~MESS_BTN_ADD_TO_BASKET"]) ? $arParams["~MESS_BTN_ADD_TO_BASKET"] : ""),
				"MESS_BTN_SUBSCRIBE" => (isset($arParams["~MESS_BTN_SUBSCRIBE"]) ? $arParams["~MESS_BTN_SUBSCRIBE"] : ""),
				"MESS_BTN_DETAIL" => (isset($arParams["~MESS_BTN_DETAIL"]) ? $arParams["~MESS_BTN_DETAIL"] : ""),
				"MESS_NOT_AVAILABLE" => (isset($arParams["~MESS_NOT_AVAILABLE"]) ? $arParams["~MESS_NOT_AVAILABLE"] : ""),				
				"MESS_BTN_COMPARE" => (isset($arParams["~MESS_BTN_COMPARE"]) ? $arParams["~MESS_BTN_COMPARE"] : ""),
				"MESS_PRICE_RANGES_TITLE" => (isset($arParams["~MESS_PRICE_RANGES_TITLE"]) ? $arParams["~MESS_PRICE_RANGES_TITLE"] : ""),
				"MESS_DESCRIPTION_TAB" => (isset($arParams["~MESS_DESCRIPTION_TAB"]) ? $arParams["~MESS_DESCRIPTION_TAB"] : ""),
				"MESS_PROPERTIES_TAB" => (isset($arParams["~MESS_PROPERTIES_TAB"]) ? $arParams["~MESS_PROPERTIES_TAB"] : ""),				
				"MAIN_BLOCK_PROPERTY_CODE" => (isset($arParams["DETAIL_MAIN_BLOCK_PROPERTY_CODE"]) ? $arParams["DETAIL_MAIN_BLOCK_PROPERTY_CODE"] : ""),
				"MAIN_BLOCK_OFFERS_PROPERTY_CODE" => (isset($arParams["DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE"]) ? $arParams["DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE"] : ""),				
				"IMAGE_RESOLUTION" => (isset($arParams["DETAIL_IMAGE_RESOLUTION"]) ? $arParams["DETAIL_IMAGE_RESOLUTION"] : ""),				
				"ADD_DETAIL_TO_SLIDER" => (isset($arParams["DETAIL_ADD_DETAIL_TO_SLIDER"]) ? $arParams["DETAIL_ADD_DETAIL_TO_SLIDER"] : ""),				
				"ADD_SECTIONS_CHAIN" => "N",
				"ADD_ELEMENT_CHAIN" => "N",				
				"DETAIL_PICTURE_MODE" => (isset($arParams["DETAIL_DETAIL_PICTURE_MODE"]) ? $arParams["DETAIL_DETAIL_PICTURE_MODE"] : array()),
				"ADD_TO_BASKET_ACTION" => $basketAction,
				"ADD_TO_BASKET_ACTION_PRIMARY" => (isset($arParams["DETAIL_ADD_TO_BASKET_ACTION_PRIMARY"]) ? $arParams["DETAIL_ADD_TO_BASKET_ACTION_PRIMARY"] : null),
				"DISPLAY_COMPARE" => (isset($arParams["USE_COMPARE"]) ? $arParams["USE_COMPARE"] : ""),
				"COMPARE_PATH" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["compare"],
				"COMPARE_NAME" => $arParams["COMPARE_NAME"],
				"COMPATIBLE_MODE" => (isset($arParams["COMPATIBLE_MODE"]) ? $arParams["COMPATIBLE_MODE"] : ""),
				"DISABLE_INIT_JS_IN_COMPONENT" => (isset($arParams["DISABLE_INIT_JS_IN_COMPONENT"]) ? $arParams["DISABLE_INIT_JS_IN_COMPONENT"] : ""),
				"SET_VIEWED_IN_COMPONENT" => (isset($arParams["DETAIL_SET_VIEWED_IN_COMPONENT"]) ? $arParams["DETAIL_SET_VIEWED_IN_COMPONENT"] : ""),
				"SHOW_SLIDER" => (isset($arParams["DETAIL_SHOW_SLIDER"]) ? $arParams["DETAIL_SHOW_SLIDER"] : ""),
				"SLIDER_INTERVAL" => (isset($arParams["DETAIL_SLIDER_INTERVAL"]) ? $arParams["DETAIL_SLIDER_INTERVAL"] : ""),
				"SLIDER_PROGRESS" => (isset($arParams["DETAIL_SLIDER_PROGRESS"]) ? $arParams["DETAIL_SLIDER_PROGRESS"] : ""),
				"USE_ENHANCED_ECOMMERCE" => (isset($arParams["USE_ENHANCED_ECOMMERCE"]) ? $arParams["USE_ENHANCED_ECOMMERCE"] : ""),
				"DATA_LAYER_NAME" => (isset($arParams["DATA_LAYER_NAME"]) ? $arParams["DATA_LAYER_NAME"] : ""),
				"BRAND_PROPERTY" => (isset($arParams["BRAND_PROPERTY"]) ? $arParams["BRAND_PROPERTY"] : ""),

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
				
				"LIST_PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
				"LIST_OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
				"LIST_OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
				"LIST_OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],
				"ADD_SECTIONS_CHAIN_EPILOG" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : ""),
				"ADD_ELEMENT_CHAIN_EPILOG" => (isset($arParams["ADD_ELEMENT_CHAIN"]) ? $arParams["ADD_ELEMENT_CHAIN"] : ""),

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

				"OBJECTS_USE_REVIEW" => $arParams["OBJECTS_USE_REVIEW"],
				"OBJECTS_REVIEWS_IBLOCK_ID" => $arParams["OBJECTS_REVIEWS_IBLOCK_ID"],
				"CONTACTS_IBLOCK_ID" => $arParams["CONTACTS_IBLOCK_ID"],
				"CONTACTS_USE_REVIEW" => $arParams["CONTACTS_USE_REVIEW"],
				"CONTACTS_REVIEWS_IBLOCK_ID" => $arParams["CONTACTS_REVIEWS_IBLOCK_ID"],
				"CONTACTS_REVIEWS_PAGE_LINK" => $arParams["CONTACTS_REVIEWS_PAGE_LINK"]
			),
			$component
		);?>		
		<?$GLOBALS["CATALOG_CURRENT_ELEMENT_ID"] = $elementId;
		unset($basketAction);?>
	</div>
</div>

<?if($elementId) {
	//PRO_WS//
	if(false){
		if($arSettings["BIGDATA"]["VALUE"] == "Y") {?>
			<div class="row product-item-detail-bigdata" data-entity="parent-container" style="display: none;">
				<div class="col-xs-12">
					<div class="h2" data-entity="header" data-showed="false" style="display: none; opacity: 0;">
						<?=(!empty($arSettings["BIGDATA_TITLE"]["VALUE"]) ? $arSettings["BIGDATA_TITLE"]["VALUE"] : Loc::getMessage("CATALOG_BIGDATA"))?>
					</div>
					<?if(isset($arParams["USE_COMMON_SETTINGS_BASKET_POPUP"]) && $arParams["USE_COMMON_SETTINGS_BASKET_POPUP"] == "Y") {
						$basketAction = isset($arParams["COMMON_ADD_TO_BASKET_ACTION"]) ? $arParams["COMMON_ADD_TO_BASKET_ACTION"] : "";
					} else {
						$basketAction = isset($arParams["SECTION_ADD_TO_BASKET_ACTION"]) ? $arParams["SECTION_ADD_TO_BASKET_ACTION"] : "";
					}?>
					<?$APPLICATION->IncludeComponent("bitrix:catalog.section", "",
						array(
							"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
							"IBLOCK_ID" => $arParams["IBLOCK_ID"],
							"SECTION_ID" => "",
							"SECTION_CODE" => "",
							"ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
							"ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
							"ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
							"ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
							"PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],						
							"INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
							"SHOW_ALL_WO_SECTION" => "Y",
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
							"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
							"PRICE_CODE" => $arParams["PRICE_CODE"],
							"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
							"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
							"PAGE_ELEMENT_COUNT" => 0,
							"FILTER_IDS" => array($elementId),

							"SET_TITLE" => "N",
							"SET_BROWSER_TITLE" => "N",
							"SET_META_KEYWORDS" => "N",
							"SET_META_DESCRIPTION" => "N",
							"SET_LAST_MODIFIED" => "N",
							"ADD_SECTIONS_CHAIN" => "N",

							"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
							"USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],
							"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ""),
							"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ""),
							"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],

							"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
							"OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
							"OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
							"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
							"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
							"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
							"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
							"OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],

							"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
							"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
							"USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
							"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
							"CURRENCY_ID" => $arParams["CURRENCY_ID"],
							"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
							"HIDE_NOT_AVAILABLE_OFFERS" => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
							
							"PRODUCT_DISPLAY_MODE" => $arParams["PRODUCT_DISPLAY_MODE"],
							"PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'2','BIG_DATA':true},{'VARIANT':'2','BIG_DATA':true}]",
								
							"DISPLAY_TOP_PAGER" => "N",
							"DISPLAY_BOTTOM_PAGER" => "N",
								
							"RCM_TYPE" => strtolower($arSettings["BIGDATA_RCM_TYPE"]["VALUE"]),
							"RCM_PROD_ID" => $elementId,
							"SHOW_FROM_SECTION" => "N",
							
							"OFFER_TREE_PROPS" => $arParams["OFFER_TREE_PROPS"],
							"SHOW_DISCOUNT_PERCENT" => $arParams["SHOW_DISCOUNT_PERCENT"],
							"SHOW_OLD_PRICE" => $arParams["SHOW_OLD_PRICE"],
							"SHOW_MAX_QUANTITY" => $arParams["SHOW_MAX_QUANTITY"],
							"MESS_SHOW_MAX_QUANTITY" => (isset($arParams["~MESS_SHOW_MAX_QUANTITY"]) ? $arParams["~MESS_SHOW_MAX_QUANTITY"] : ""),
							"RELATIVE_QUANTITY_FACTOR" => (isset($arParams["RELATIVE_QUANTITY_FACTOR"]) ? $arParams["RELATIVE_QUANTITY_FACTOR"] : ""),
							"MESS_RELATIVE_QUANTITY_MANY" => (isset($arParams["~MESS_RELATIVE_QUANTITY_MANY"]) ? $arParams["~MESS_RELATIVE_QUANTITY_MANY"] : ""),
							"MESS_RELATIVE_QUANTITY_FEW" => (isset($arParams["~MESS_RELATIVE_QUANTITY_FEW"]) ? $arParams["~MESS_RELATIVE_QUANTITY_FEW"] : ""),
							"MESS_BTN_BUY" => (isset($arParams["~MESS_BTN_BUY"]) ? $arParams["~MESS_BTN_BUY"] : ""),
							"MESS_BTN_ADD_TO_BASKET" => (isset($arParams["~MESS_BTN_ADD_TO_BASKET"]) ? $arParams["~MESS_BTN_ADD_TO_BASKET"] : ""),
							"MESS_BTN_SUBSCRIBE" => (isset($arParams["~MESS_BTN_SUBSCRIBE"]) ? $arParams["~MESS_BTN_SUBSCRIBE"] : ""),
							"MESS_BTN_DETAIL" => (isset($arParams["~MESS_BTN_DETAIL"]) ? $arParams["~MESS_BTN_DETAIL"] : ""),
							"MESS_NOT_AVAILABLE" => (isset($arParams["~MESS_NOT_AVAILABLE"]) ? $arParams["~MESS_NOT_AVAILABLE"] : ""),		"MESS_BTN_COMPARE" => (isset($arParams["~MESS_BTN_COMPARE"]) ? $arParams["~MESS_BTN_COMPARE"] : ""),			
							
							"USE_ENHANCED_ECOMMERCE" => (isset($arParams["USE_ENHANCED_ECOMMERCE"]) ? $arParams["USE_ENHANCED_ECOMMERCE"] : ""),
							"DATA_LAYER_NAME" => (isset($arParams["DATA_LAYER_NAME"]) ? $arParams["DATA_LAYER_NAME"] : ""),
							"BRAND_PROPERTY" => (isset($arParams["BRAND_PROPERTY"]) ? $arParams["BRAND_PROPERTY"] : ""),
							
							"ADD_TO_BASKET_ACTION" => $basketAction,
							"COMPARE_PATH" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["compare"],
							"COMPARE_NAME" => $arParams["COMPARE_NAME"],
							"DISABLE_INIT_JS_IN_COMPONENT" => (isset($arParams["DISABLE_INIT_JS_IN_COMPONENT"]) ? $arParams["DISABLE_INIT_JS_IN_COMPONENT"] : ""),

							"DETAIL_ADD_PICT_PROP" => $arParams["ADD_PICT_PROP"],
							"DETAIL_OFFER_ADD_PICT_PROP" => $arParams["OFFER_ADD_PICT_PROP"],
							"DETAIL_PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],				
							"DETAIL_OFFERS_FIELD_CODE" => $arParams["DETAIL_OFFERS_FIELD_CODE"],
							"DETAIL_OFFERS_PROPERTY_CODE" => $arParams["DETAIL_OFFERS_PROPERTY_CODE"],
							"DETAIL_MAIN_BLOCK_PROPERTY_CODE" => $arParams["DETAIL_MAIN_BLOCK_PROPERTY_CODE"],
							"DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE" => $arParams["DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE"],	
							"DETAIL_IMAGE_RESOLUTION" => $arParams["DETAIL_IMAGE_RESOLUTION"],				
							"DETAIL_ADD_DETAIL_TO_SLIDER" => $arParams["DETAIL_ADD_DETAIL_TO_SLIDER"],
							"DETAIL_DETAIL_PICTURE_MODE" => $arParams["DETAIL_DETAIL_PICTURE_MODE"],
							"DETAIL_SHOW_SLIDER" => $arParams["DETAIL_SHOW_SLIDER"],
							"DETAIL_SLIDER_INTERVAL" => $arParams["DETAIL_SLIDER_INTERVAL"],
							"DETAIL_SLIDER_PROGRESS" => $arParams["DETAIL_SLIDER_PROGRESS"],

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

							"OBJECTS_USE_REVIEW" => $arParams["OBJECTS_USE_REVIEW"],
							"OBJECTS_REVIEWS_IBLOCK_ID" => $arParams["OBJECTS_REVIEWS_IBLOCK_ID"],
							"CONTACTS_IBLOCK_ID" => $arParams["CONTACTS_IBLOCK_ID"],
							"CONTACTS_USE_REVIEW" => $arParams["CONTACTS_USE_REVIEW"],
							"CONTACTS_REVIEWS_IBLOCK_ID" => $arParams["CONTACTS_REVIEWS_IBLOCK_ID"],
							"CONTACTS_REVIEWS_PAGE_LINK" => $arParams["CONTACTS_REVIEWS_PAGE_LINK"]
						),
						$component,
						array("HIDE_ICONS" => "Y")
					);?>
					<?unset($basketAction);?>
				</div>
			</div>
		<?}
	}
	if($arSettings["RELATED_PRODUCTS"]["VALUE"] == "Y") {?>
		<div class="row product-item-detail-related" data-entity="parent-container" style="display: none;">
			<div class="col-xs-12">
				<div class="h2" data-entity="header" data-showed="false" style="display: none; opacity: 0;">
					<?=Loc::getMessage("CATALOG_RELATED")?>
				</div>
				<? if (isset($arParams["USE_COMMON_SETTINGS_BASKET_POPUP"]) && $arParams["USE_COMMON_SETTINGS_BASKET_POPUP"] == "Y") {
					$basketAction = isset($arParams["COMMON_ADD_TO_BASKET_ACTION"])
                        ? $arParams["COMMON_ADD_TO_BASKET_ACTION"]
                        : "";
				} else {
					$basketAction = isset($arParams["SECTION_ADD_TO_BASKET_ACTION"])
                        ? $arParams["SECTION_ADD_TO_BASKET_ACTION"]
                        : "";
				}
                if (!$arResult["VARIABLES"]["SECTION_ID"] && !$arResult["VARIABLES"]["SECTION_CODE"]) {
                    $rsElement = CIBlockElement::GetList(
                        [],
                        ["ID" => $elementId, "IBLOCK_ID" => $arParams["IBLOCK_ID"]],
                        false,
                        false,
                        ["ID", "IBLOCK_SECTION_ID"]
                    );
                    if ($arElement = $rsElement->GetNext()) {
                        $arResult["VARIABLES"]["SECTION_ID"] = $arElement["IBLOCK_SECTION_ID"];
                    }
                } ?>
				<?$APPLICATION->IncludeComponent("bitrix:catalog.section", "",
					array(
						"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
						"IBLOCK_ID" => $arParams["IBLOCK_ID"],
						"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
						"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
						"ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
						"ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
						"ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
						"ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
						"PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],						
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
						"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
						"PRICE_CODE" => $arParams["PRICE_CODE"],
						"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
						"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
						"PAGE_ELEMENT_COUNT" => 6,
						"FILTER_IDS" => array($elementId),

						"SET_TITLE" => "N",
						"SET_BROWSER_TITLE" => "N",
						"SET_META_KEYWORDS" => "N",
						"SET_META_DESCRIPTION" => "N",
						"SET_LAST_MODIFIED" => "N",
						"ADD_SECTIONS_CHAIN" => "N",

						"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
						"USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],
						"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ""),
						"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ""),
						"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],

						"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
						"OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
						"OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
						"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
						"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
						"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
						"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
						"OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],

						"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
						"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
						"USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
						"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
						"CURRENCY_ID" => $arParams["CURRENCY_ID"],
						"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
						"HIDE_NOT_AVAILABLE_OFFERS" => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
						
						"PRODUCT_DISPLAY_MODE" => $arParams["PRODUCT_DISPLAY_MODE"],
						"PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false}]",
							
						"DISPLAY_TOP_PAGER" => "N",
						"DISPLAY_BOTTOM_PAGER" => "N",
							
						"RCM_TYPE" => isset($arParams["BIG_DATA_RCM_TYPE"]) ? $arParams["BIG_DATA_RCM_TYPE"] : "",
						"RCM_PROD_ID" => $elementId,
						"SHOW_FROM_SECTION" => "Y",
						
						"OFFER_TREE_PROPS" => $arParams["OFFER_TREE_PROPS"],
						"SHOW_DISCOUNT_PERCENT" => $arParams["SHOW_DISCOUNT_PERCENT"],
						"SHOW_OLD_PRICE" => $arParams["SHOW_OLD_PRICE"],
						"SHOW_MAX_QUANTITY" => $arParams["SHOW_MAX_QUANTITY"],
						"MESS_SHOW_MAX_QUANTITY" => (isset($arParams["~MESS_SHOW_MAX_QUANTITY"]) ? $arParams["~MESS_SHOW_MAX_QUANTITY"] : ""),
						"RELATIVE_QUANTITY_FACTOR" => (isset($arParams["RELATIVE_QUANTITY_FACTOR"]) ? $arParams["RELATIVE_QUANTITY_FACTOR"] : ""),
						"MESS_RELATIVE_QUANTITY_MANY" => (isset($arParams["~MESS_RELATIVE_QUANTITY_MANY"]) ? $arParams["~MESS_RELATIVE_QUANTITY_MANY"] : ""),
						"MESS_RELATIVE_QUANTITY_FEW" => (isset($arParams["~MESS_RELATIVE_QUANTITY_FEW"]) ? $arParams["~MESS_RELATIVE_QUANTITY_FEW"] : ""),
						"MESS_BTN_BUY" => (isset($arParams["~MESS_BTN_BUY"]) ? $arParams["~MESS_BTN_BUY"] : ""),
						"MESS_BTN_ADD_TO_BASKET" => (isset($arParams["~MESS_BTN_ADD_TO_BASKET"]) ? $arParams["~MESS_BTN_ADD_TO_BASKET"] : ""),
						"MESS_BTN_SUBSCRIBE" => (isset($arParams["~MESS_BTN_SUBSCRIBE"]) ? $arParams["~MESS_BTN_SUBSCRIBE"] : ""),
						"MESS_BTN_DETAIL" => (isset($arParams["~MESS_BTN_DETAIL"]) ? $arParams["~MESS_BTN_DETAIL"] : ""),
						"MESS_NOT_AVAILABLE" => (isset($arParams["~MESS_NOT_AVAILABLE"]) ? $arParams["~MESS_NOT_AVAILABLE"] : ""),		"MESS_BTN_COMPARE" => (isset($arParams["~MESS_BTN_COMPARE"]) ? $arParams["~MESS_BTN_COMPARE"] : ""),			
						
						"USE_ENHANCED_ECOMMERCE" => (isset($arParams["USE_ENHANCED_ECOMMERCE"]) ? $arParams["USE_ENHANCED_ECOMMERCE"] : ""),
						"DATA_LAYER_NAME" => (isset($arParams["DATA_LAYER_NAME"]) ? $arParams["DATA_LAYER_NAME"] : ""),
						"BRAND_PROPERTY" => (isset($arParams["BRAND_PROPERTY"]) ? $arParams["BRAND_PROPERTY"] : ""),
						
						"ADD_TO_BASKET_ACTION" => $basketAction,
						"COMPARE_PATH" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["compare"],
						"COMPARE_NAME" => $arParams["COMPARE_NAME"],
						"DISABLE_INIT_JS_IN_COMPONENT" => (isset($arParams["DISABLE_INIT_JS_IN_COMPONENT"]) ? $arParams["DISABLE_INIT_JS_IN_COMPONENT"] : ""),

						"DETAIL_ADD_PICT_PROP" => $arParams["ADD_PICT_PROP"],
						"DETAIL_OFFER_ADD_PICT_PROP" => $arParams["OFFER_ADD_PICT_PROP"],
						"DETAIL_PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],				
						"DETAIL_OFFERS_FIELD_CODE" => $arParams["DETAIL_OFFERS_FIELD_CODE"],
						"DETAIL_OFFERS_PROPERTY_CODE" => $arParams["DETAIL_OFFERS_PROPERTY_CODE"],
						"DETAIL_MAIN_BLOCK_PROPERTY_CODE" => $arParams["DETAIL_MAIN_BLOCK_PROPERTY_CODE"],
						"DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE" => $arParams["DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE"],	
						"DETAIL_IMAGE_RESOLUTION" => $arParams["DETAIL_IMAGE_RESOLUTION"],				
						"DETAIL_ADD_DETAIL_TO_SLIDER" => $arParams["DETAIL_ADD_DETAIL_TO_SLIDER"],
						"DETAIL_DETAIL_PICTURE_MODE" => $arParams["DETAIL_DETAIL_PICTURE_MODE"],
						"DETAIL_SHOW_SLIDER" => $arParams["DETAIL_SHOW_SLIDER"],
						"DETAIL_SLIDER_INTERVAL" => $arParams["DETAIL_SLIDER_INTERVAL"],
						"DETAIL_SLIDER_PROGRESS" => $arParams["DETAIL_SLIDER_PROGRESS"],

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

						"OBJECTS_USE_REVIEW" => $arParams["OBJECTS_USE_REVIEW"],
						"OBJECTS_REVIEWS_IBLOCK_ID" => $arParams["OBJECTS_REVIEWS_IBLOCK_ID"],
						"CONTACTS_IBLOCK_ID" => $arParams["CONTACTS_IBLOCK_ID"],
						"CONTACTS_USE_REVIEW" => $arParams["CONTACTS_USE_REVIEW"],
						"CONTACTS_REVIEWS_IBLOCK_ID" => $arParams["CONTACTS_REVIEWS_IBLOCK_ID"],
						"CONTACTS_REVIEWS_PAGE_LINK" => $arParams["CONTACTS_REVIEWS_PAGE_LINK"]
					),
					$component,
					array("HIDE_ICONS" => "Y")
				);?>
				<?unset($basketAction);?>
			</div>
		</div>
	<?}
}