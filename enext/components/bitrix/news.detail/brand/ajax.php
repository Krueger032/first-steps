<?define("STOP_STATISTICS", true);
define("NOT_CHECK_PERMISSIONS", true);

$siteId = isset($_REQUEST["siteId"]) && is_string($_REQUEST["siteId"]) ? $_REQUEST["siteId"] : "";
$siteId = substr(preg_replace("/[^a-z0-9_]/i", "", $siteId), 0, 2);
if(!empty($siteId) && is_string($siteId)) {
	define("SITE_ID", $siteId);
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
if($request->isAjaxRequest() && $request->get("action") == "changeSectionLink") {
	$signer = new Bitrix\Main\Security\Sign\Signer;
	$parameters = unserialize(base64_decode($signer->unsign($request->get("parameters"), "news.detail")));
	
	$productsIds = unserialize(base64_decode($signer->unsign($request->get("productsIds"), "news.detail")));
	if(!empty($productsIds))
		$GLOBALS["arBrandsProdFilter"]["ID"] = $productsIds;
	
	$sectionID = intval($request->get("sectionId"));
	if($sectionID > 0)
		$GLOBALS["arBrandsProdFilter"]["SECTION_ID"] = $sectionID;
	
	$APPLICATION->IncludeComponent("bitrix:catalog.section", ".default", 
		array(
			"COMPONENT_TEMPLATE" => ".default",
			"IBLOCK_TYPE" => $parameters["IBLOCK_TYPE"],
			"IBLOCK_ID" => $parameters["IBLOCK_ID"],
			"SECTION_ID" => "",
			"SECTION_CODE" => "",
			"SECTION_USER_FIELDS" => array(),
			"FILTER_NAME" => "arBrandsProdFilter",
			"INCLUDE_SUBSECTIONS" => $parameters["INCLUDE_SUBSECTIONS"],
			"SHOW_ALL_WO_SECTION" => "Y",
			"CUSTOM_FILTER" => "",
			"HIDE_NOT_AVAILABLE" => $parameters["HIDE_NOT_AVAILABLE"],
			"HIDE_NOT_AVAILABLE_OFFERS" => $parameters["HIDE_NOT_AVAILABLE_OFFERS"],
			"ELEMENT_SORT_FIELD" => $parameters["ELEMENT_SORT_FIELD"],
			"ELEMENT_SORT_ORDER" => $parameters["ELEMENT_SORT_ORDER"],
			"ELEMENT_SORT_FIELD2" => $parameters["ELEMENT_SORT_FIELD2"],
			"ELEMENT_SORT_ORDER2" => $parameters["ELEMENT_SORT_ORDER2"],
			"OFFERS_SORT_FIELD" => $parameters["OFFERS_SORT_FIELD"],
			"OFFERS_SORT_ORDER" => $parameters["OFFERS_SORT_ORDER"],
			"OFFERS_SORT_FIELD2" => $parameters["OFFERS_SORT_FIELD2"],
			"OFFERS_SORT_ORDER2" => $parameters["OFFERS_SORT_ORDER2"],
			"PAGE_ELEMENT_COUNT" => "12",			
			"LINE_ELEMENT_COUNT" => "4",
			"PROPERTY_CODE" => $parameters["PROPERTY_CODE"],
			"OFFERS_FIELD_CODE" => $parameters["OFFERS_FIELD_CODE"],
			"OFFERS_PROPERTY_CODE" => $parameters["OFFERS_PROPERTY_CODE"],
			"OFFERS_LIMIT" => $parameters["OFFERS_LIMIT"],			
			"PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false}]",
			"PRODUCT_DISPLAY_MODE" => $parameters["PRODUCT_DISPLAY_MODE"],			
			"OFFER_TREE_PROPS" => $parameters["OFFER_TREE_PROPS"],
			"PRODUCT_SUBSCRIPTION" => $parameters["PRODUCT_SUBSCRIPTION"],
			"SHOW_DISCOUNT_PERCENT" => $parameters["SHOW_DISCOUNT_PERCENT"],
			"SHOW_OLD_PRICE" => $parameters["SHOW_OLD_PRICE"],
			"SHOW_MAX_QUANTITY" => $parameters["SHOW_MAX_QUANTITY"],
			"MESS_SHOW_MAX_QUANTITY" => $parameters["MESS_SHOW_MAX_QUANTITY"],
			"RELATIVE_QUANTITY_FACTOR" => $parameters["RELATIVE_QUANTITY_FACTOR"],
			"MESS_RELATIVE_QUANTITY_MANY" => $parameters["MESS_RELATIVE_QUANTITY_MANY"],
			"MESS_RELATIVE_QUANTITY_FEW" => $parameters["MESS_RELATIVE_QUANTITY_FEW"],
			"MESS_BTN_BUY" => $parameters["MESS_BTN_BUY"],
			"MESS_BTN_ADD_TO_BASKET" => $parameters["MESS_BTN_ADD_TO_BASKET"],
			"MESS_BTN_SUBSCRIBE" => $parameters["MESS_BTN_SUBSCRIBE"],
			"MESS_BTN_DETAIL" => $parameters["MESS_BTN_DETAIL"],
			"MESS_NOT_AVAILABLE" => $parameters["MESS_NOT_AVAILABLE"],
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
			"CACHE_TYPE" => $parameters["CACHE_TYPE"],
			"CACHE_TIME" => $parameters["CACHE_TIME"],
			"CACHE_GROUPS" => $parameters["CACHE_GROUPS"],
			"SET_TITLE" => "N",
			"SET_BROWSER_TITLE" => "N",
			"BROWSER_TITLE" => "-",
			"SET_META_KEYWORDS" => "N",
			"META_KEYWORDS" => "-",
			"SET_META_DESCRIPTION" => "N",
			"META_DESCRIPTION" => "-",
			"SET_LAST_MODIFIED" => "N",
			"USE_MAIN_ELEMENT_SECTION" => $parameters["USE_MAIN_ELEMENT_SECTION"],
			"ADD_SECTIONS_CHAIN" => "N",
			"CACHE_FILTER" => $parameters["CACHE_FILTER"],
			"USE_REVIEW" => $parameters["USE_REVIEW"],
			"REVIEWS_IBLOCK_TYPE" => $parameters["REVIEWS_IBLOCK_TYPE"],
			"REVIEWS_IBLOCK_ID" => $parameters["REVIEWS_IBLOCK_ID"],
			"REVIEWS_NEWS_COUNT" => $parameters["REVIEWS_NEWS_COUNT"],
			"REVIEWS_SORT_BY1" => $parameters["REVIEWS_SORT_BY1"],
			"REVIEWS_SORT_ORDER1" => $parameters["REVIEWS_SORT_ORDER1"],
			"REVIEWS_SORT_BY2" => $parameters["REVIEWS_SORT_BY2"],
			"REVIEWS_SORT_ORDER2" => $parameters["REVIEWS_SORT_ORDER2"],
			"REVIEWS_ACTIVE_DATE_FORMAT" => $parameters["REVIEWS_ACTIVE_DATE_FORMAT"],
			"REVIEWS_PROPERTY_CODE" => $parameters["REVIEWS_PROPERTY_CODE"],
			"MESS_REVIEWS_TAB" => $parameters["MESS_REVIEWS_TAB"],
			"ACTION_VARIABLE" => "action",
			"PRODUCT_ID_VARIABLE" => "id",								
			"PRICE_CODE" => $parameters["PRICE_CODE"],
			"USE_PRICE_COUNT" => $parameters["USE_PRICE_COUNT"],
			"SHOW_PRICE_COUNT" => $parameters["SHOW_PRICE_COUNT"] ? "Y" : "N",
			"PRICE_VAT_INCLUDE" => $parameters["PRICE_VAT_INCLUDE"],
			"CONVERT_CURRENCY" => $parameters["CONVERT_CURRENCY"],
			"CURRENCY_ID" => $parameters["CURRENCY_ID"],
			"BASKET_URL" => $parameters["BASKET_URL"],
			"USE_PRODUCT_QUANTITY" => $parameters["USE_PRODUCT_QUANTITY"],
			"PRODUCT_QUANTITY_VARIABLE" => "quantity",
			"ADD_PROPERTIES_TO_BASKET" => $parameters["ADD_PROPERTIES_TO_BASKET"],
			"PRODUCT_PROPS_VARIABLE" => "prop",
			"PARTIAL_PRODUCT_PROPERTIES" => $parameters["PARTIAL_PRODUCT_PROPERTIES"],
			"PRODUCT_PROPERTIES" => $parameters["PRODUCT_PROPERTIES"],
			"OFFERS_CART_PROPERTIES" => $parameters["OFFERS_CART_PROPERTIES"],
			"ADD_TO_BASKET_ACTION" => $parameters["ADD_TO_BASKET_ACTION"],
			"DISPLAY_COMPARE" => $parameters["DISPLAY_COMPARE"],
			"COMPARE_PATH" => $parameters["COMPARE_PATH"],
			"MESS_BTN_COMPARE" => $parameters["MESS_BTN_COMPARE"],
			"COMPARE_NAME" => $parameters["COMPARE_NAME"],
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
			"DETAIL_ADD_PICT_PROP" => $parameters["DETAIL_ADD_PICT_PROP"],
			"DETAIL_OFFER_ADD_PICT_PROP" => $parameters["DETAIL_OFFER_ADD_PICT_PROP"],
			"DETAIL_USE_RATIO_IN_RANGES" => $parameters["DETAIL_USE_RATIO_IN_RANGES"],
			"DETAIL_PROPERTY_CODE" => $parameters["DETAIL_PROPERTY_CODE"],				
			"DETAIL_OFFERS_FIELD_CODE" => $parameters["DETAIL_OFFERS_FIELD_CODE"],
			"DETAIL_OFFERS_PROPERTY_CODE" => $parameters["DETAIL_OFFERS_PROPERTY_CODE"],
			"DETAIL_MAIN_BLOCK_PROPERTY_CODE" => $parameters["DETAIL_MAIN_BLOCK_PROPERTY_CODE"],
			"DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE" => $parameters["DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE"],	
			"DETAIL_IMAGE_RESOLUTION" => $parameters["DETAIL_IMAGE_RESOLUTION"],				
			"DETAIL_ADD_DETAIL_TO_SLIDER" => $parameters["DETAIL_ADD_DETAIL_TO_SLIDER"],
			"DETAIL_DETAIL_PICTURE_MODE" => $parameters["DETAIL_DETAIL_PICTURE_MODE"],
			"DETAIL_SHOW_SLIDER" => $parameters["DETAIL_SHOW_SLIDER"],
			"DETAIL_SLIDER_INTERVAL" => $parameters["DETAIL_SLIDER_INTERVAL"],
			"DETAIL_SLIDER_PROGRESS" => $parameters["DETAIL_SLIDER_PROGRESS"],
			"USE_GIFTS_DETAIL" => $parameters["USE_GIFTS_DETAIL"],
			"GIFTS_DETAIL_PAGE_ELEMENT_COUNT" => $parameters["GIFTS_DETAIL_PAGE_ELEMENT_COUNT"],
			"GIFTS_DETAIL_HIDE_BLOCK_TITLE" => $parameters["GIFTS_DETAIL_HIDE_BLOCK_TITLE"],
			"GIFTS_DETAIL_BLOCK_TITLE" => $parameters["GIFTS_DETAIL_BLOCK_TITLE"],
			"GIFTS_DETAIL_TEXT_LABEL_GIFT" => $parameters["GIFTS_DETAIL_TEXT_LABEL_GIFT"],
			"GIFTS_MESS_BTN_BUY" => $parameters["GIFTS_MESS_BTN_BUY"],
			"USE_STORE" => $parameters["USE_STORE"],
			"STORE_PATH" => $parameters["STORE_PATH"],
			"STORES" => $parameters["STORES"],
			"USE_MIN_AMOUNT" => $parameters["USE_MIN_AMOUNT"],
			"USER_FIELDS" => $parameters["USER_FIELDS"],
			"FIELDS" => $parameters["FIELDS"],
			"MIN_AMOUNT" => $parameters["MIN_AMOUNT"],
			"SHOW_EMPTY_STORE" => $parameters["SHOW_EMPTY_STORE"],
			"SHOW_GENERAL_STORE_INFORMATION" => $parameters["SHOW_GENERAL_STORE_INFORMATION"],
			"MAIN_TITLE" => $parameters["MAIN_TITLE"],
			"SET_ITEMS_COUNT" => $parameters["SET_ITEMS_COUNT"],
			"REINIT_ADD_BUY_URL_TEMPLATE" => $parameters["REINIT_ADD_BUY_URL_TEMPLATE"],
			"OBJECTS_USE_REVIEW" => $parameters["OBJECTS_USE_REVIEW"],
			"OBJECTS_REVIEWS_IBLOCK_ID" => $parameters["OBJECTS_REVIEWS_IBLOCK_ID"],
			"CONTACTS_IBLOCK_ID" => $parameters["CONTACTS_IBLOCK_ID"],
			"CONTACTS_USE_REVIEW" => $parameters["CONTACTS_USE_REVIEW"],
			"CONTACTS_REVIEWS_IBLOCK_ID" => $parameters["CONTACTS_REVIEWS_IBLOCK_ID"],
			"CONTACTS_REVIEWS_PAGE_LINK" => $parameters["CONTACTS_REVIEWS_PAGE_LINK"]
		),
		false
	);
		
	$content = ob_get_contents();
	ob_end_clean();
	
	$arSettings = CEnext::GetFrontParametrsValues(SITE_ID);

	$webpSupport = strpos($_SERVER["HTTP_ACCEPT"], "image/webp") !== false || strpos($_SERVER["HTTP_USER_AGENT"], " Chrome/") !== false ? true : false;
	
	$GLOBALS["IMG_LAZYLOAD"] = $arSettings["IMG_LAZYLOAD"] == "Y";
	$GLOBALS["IMG_WEBP"] = $arSettings["IMG_WEBP"] == "Y" && function_exists("imagewebp") && $webpSupport;
	
	if($GLOBALS["IMG_LAZYLOAD"] || $GLOBALS["IMG_WEBP"]) {
		$content = preg_replace_callback("/<img[^>]+src=\"([^\"]+)\"/is", function($matches) {
			if($GLOBALS["IMG_LAZYLOAD"])
				$matches[0] = str_replace(" src=", " data-lazyload-src=", $matches[0]);

			if($GLOBALS["IMG_WEBP"]) {
				if(substr($matches[1], 0, 4) != "http" && substr($matches[1], 0, 2) != "//" && substr($matches[1], 0, 11) != "data:image/") {
					$pathinfo = pathinfo($matches[1]);
					if(in_array($pathinfo["extension"], array("jpg", "jpeg", "png"))) {
						$newFile = $_SERVER["DOCUMENT_ROOT"].$pathinfo["dirname"]."/".$pathinfo["filename"].".webp";
						if(file_exists($newFile)) {
							$newSrc = $pathinfo["dirname"]."/".$pathinfo["filename"].".webp?".filemtime($newFile);
							$matches[0] = str_replace($matches[1], $newSrc, $matches[0]);
						}
						unset($newSrc, $newFile);
					}
					unset($pathinfo);
				}
			}
			
			return $matches[0];					
		}, $content);
	}
	
	if(Bitrix\Main\Loader::includeModule("iblock")) {
		Bitrix\Iblock\Component\Base::sendJsonAnswer(array(
			"content" => $content,
			"imgLazyLoad" => $GLOBALS["IMG_LAZYLOAD"],
			"imgWebp" => $GLOBALS["IMG_WEBP"]
		));
	}
}