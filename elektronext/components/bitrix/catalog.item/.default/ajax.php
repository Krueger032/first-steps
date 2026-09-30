<?define("STOP_STATISTICS", true);
define("NOT_CHECK_PERMISSIONS", true);

$siteId = isset($_REQUEST["siteId"]) && is_string($_REQUEST["siteId"]) ? $_REQUEST["siteId"] : "";
$siteId = substr(preg_replace("/[^a-z0-9_]/i", "", $siteId), 0, 2);
if(!empty($siteId) && is_string($siteId)) {
	define("SITE_ID", $siteId);
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
if($request->isAjaxRequest()) {
	$action = $request->get("action");
	if($action == "ADD_TO_DELAY" || $action == "DELETE_FROM_DELAY") {
		$productID = intval($request->get("productId"));
		$qnt = doubleval($request->get("quantity")) ?: 1;
		
		if($productID > 0 && Bitrix\Main\Loader::includeModule("sale")) {
			$dbBasketItems = CSaleBasket::GetList(
				array(),
				array(
					"PRODUCT_ID" => $productID,
					"LID" => SITE_ID,
					"DELAY" => $action == "ADD_TO_DELAY" ? "N" : "Y",
					"CAN_BUY" => "Y",
					"FUSER_ID" => Bitrix\Sale\Fuser::getId(true),
					"ORDER_ID" => "NULL"
				),
				false,
				false,
				array("ID", "DELAY", "CAN_BUY")
			);
			switch($action) {
				case "ADD_TO_DELAY":				
					if($arItem = $dbBasketItems->Fetch()) {
						if(CSaleBasket::Update($arItem["ID"], array("DELAY" => "Y")))
							echo Bitrix\Main\Web\Json::encode(array("STATUS" => "ADDED"));
					} else {
						if(Bitrix\Main\Loader::includeModule("catalog") && Add2BasketByProductID($productID, $qnt, array("LID" => SITE_ID, "DELAY" => "Y"), array()))
							echo Bitrix\Main\Web\Json::encode(array("STATUS" => "ADDED"));
					}
					break;
				case "DELETE_FROM_DELAY":				
					if($arItem = $dbBasketItems->Fetch()) {
						if(CSaleBasket::Delete($arItem["ID"]))
							echo Bitrix\Main\Web\Json::encode(array("STATUS" => "DELETED"));
					}
					break;
			}
			die();
		}
	} elseif($action == "ADD_TO_CART") {
		$productId = intval($request->get("productId"));
		$qnt = doubleval($request->get("quantity")) ?: 1;

		if($productId > 0 && Bitrix\Main\Loader::includeModule("sale")) {
			$dbBasketItems = CSaleBasket::GetList(
				array(),
				array(
					"PRODUCT_ID" => $productId,
					"LID" => SITE_ID,					
					"CAN_BUY" => "Y",
					"FUSER_ID" => Bitrix\Sale\Fuser::getId(true),
					"ORDER_ID" => "NULL"
				),
				false,
				false,
				array("ID", "QUANTITY", "DELAY")
			);
			if($arItem = $dbBasketItems->Fetch()) {
				if($arItem["DELAY"] == "Y") {
					if(CSaleBasket::Update($arItem["ID"], array("DELAY" => "N", "QUANTITY" => $qnt)))
						echo Bitrix\Main\Web\Json::encode(array("STATUS" => "OK"));
				} else {
					if(CSaleBasket::Update($arItem["ID"], array("QUANTITY" => $arItem["QUANTITY"] + $qnt)))
						echo Bitrix\Main\Web\Json::encode(array("STATUS" => "OK"));
				}
			} else {
				echo Bitrix\Main\Web\Json::encode(array("STATUS" => "ADD_TO_CART"));
			}
		}
	} elseif($action == "checkComparedDelayedBuyedAddedQuantity") {
		$productId = intval($request->get("productId"));
		$offers = $request->get("offers");
		$offerNum = intval($request->get("offerNum"));
		
		$result = array();

		$checkCompared = $request->get("checkCompared");
		if($checkCompared) {
			$compareName = $request->get("compareName");
			$iblockId = intval($request->get("iblockId"));

			$compared = false;
			$comparedIds = array();
			
			if(!empty($compareName) && !empty($_SESSION[$compareName][$iblockId])) {
				if(!empty($offers)) {
					foreach($offers as $key => $arOffer) {
						if(array_key_exists($arOffer["ID"], $_SESSION[$compareName][$iblockId]["ITEMS"])) {
							if($key == $offerNum) {
								$compared = true;
							}
							$comparedIds[] = $arOffer["ID"];
						}
					}
					unset($key, $arOffer);
				} elseif(array_key_exists($productId, $_SESSION[$compareName][$iblockId]["ITEMS"])) {
					$compared = true;
				}
			}

			$result["compared"] = $compared;
			$result["comparedIds"] = $comparedIds;
		}
		
		$checkDelayed = $request->get("checkDelayed");
		$checkBuyedAdded = $request->get("checkBuyedAdded");
		$checkQuantity = $request->get("checkQuantity");
		if($checkDelayed || $checkBuyedAdded) {
			if($checkDelayed) {
				$delayed = false;			
				$delayedIds = array();
			}

			if($checkBuyedAdded) {
				$buyedAdded = false;
				$buyedAddedIds = array();
			}

			if($checkQuantity) {
				$quantity = false;
				$quantityIds = array();
			}

			if(Bitrix\Main\Loader::includeModule("sale")) {
				$fuserId = Bitrix\Sale\Fuser::getId(true);
				$dbItems = CSaleBasket::GetList(
					array("NAME" => "ASC", "ID" => "ASC"),
					array(			
						"LID" => SITE_ID,
						"CAN_BUY" => "Y",
						"FUSER_ID" => $fuserId,
						"ORDER_ID" => "NULL"
					),
					false,
					false,
					array("ID", "PRODUCT_ID", "QUANTITY", "DELAY", "TYPE", "SET_PARENT_ID")
				);
				while($arItem = $dbItems->GetNext()) {
					if(CSaleBasketHelper::isSetItem($arItem))
						continue;			
					
					if(!empty($offers)) {
						foreach($offers as $key => $arOffer) {
							if($arOffer["ID"] == $arItem["PRODUCT_ID"]) {
								if($key == $offerNum) {
									if($checkDelayed && $arItem["DELAY"] == "Y")
										$delayed = true;
									elseif($checkBuyedAdded && $arItem["DELAY"] == "N")
										$buyedAdded = true;

									if($checkQuantity && $arItem["DELAY"] == "N")
										$quantity = $arItem["QUANTITY"];
								}					
								if($checkDelayed && $arItem["DELAY"] == "Y")
									$delayedIds[] = $arOffer["ID"];
								elseif($checkBuyedAdded && $arItem["DELAY"] == "N")
									$buyedAddedIds[] = $arOffer["ID"];

								if($checkQuantity && $arItem["DELAY"] == "N")
									$quantityIds[$arOffer["ID"]] = $arItem["QUANTITY"];
							}
						}
						unset($key, $arOffer);
					} elseif($productId == $arItem["PRODUCT_ID"]) {
						if($checkDelayed && $arItem["DELAY"] == "Y")
							$delayed = true;
						elseif($checkBuyedAdded && $arItem["DELAY"] == "N")
							$buyedAdded = true;

						if($checkQuantity && $arItem["DELAY"] == "N")
							$quantity = $arItem["QUANTITY"];
					}
				}
				unset($arItem, $dbItems, $fuserId);
			}

			if($checkDelayed) {
				$result["delayed"] = $delayed;
				$result["delayedIds"] = $delayedIds;
			}

			if($checkBuyedAdded) {
				$result["buyedAdded"] = $buyedAdded;			
				$result["buyedAddedIds"] = $buyedAddedIds;
			}

			if($checkQuantity) {
				$result["quantity"] = $quantity;
				$result["quantityIds"] = $quantityIds;
			}
		}
		
		echo Bitrix\Main\Web\Json::encode($result);
	} elseif($action == "updateCartProductQuantity") {
		$productId = intval($request->get("productId"));
		$qnt = doubleval($request->get("quantity")) ?: 1;

		if($productId > 0 && Bitrix\Main\Loader::includeModule("sale")) {
			$dbBasketItems = CSaleBasket::GetList(
				array(),
				array(
					"PRODUCT_ID" => $productId,
					"LID" => SITE_ID,
					"DELAY" => "N",
					"CAN_BUY" => "Y",
					"FUSER_ID" => Bitrix\Sale\Fuser::getId(true),
					"ORDER_ID" => "NULL"
				),
				false,
				false,
				array("ID", "QUANTITY")
			);
			if($arItem = $dbBasketItems->Fetch()) {
				if($arItem["QUANTITY"] != $qnt) {
					if(CSaleBasket::Update($arItem["ID"], array("QUANTITY" => $qnt)))
						echo Bitrix\Main\Web\Json::encode(array("STATUS" => "UPDATED"));
				}
			}
		}
	} elseif($action == "quickView" || $action == "quickViewFull") {
		$signer = new Bitrix\Main\Security\Sign\Signer;
		$parameters = unserialize(base64_decode($signer->unsign($request->get("parameters"), "catalog.item")));

		foreach($parameters as $key => $arParams) {
			if($parameters[$key] === true)
				$parameters[$key] = "Y";
			elseif($parameters[$key] === false)
				$parameters[$key] = "N";
		}
		unset($key, $arParams);
		
		$productId = intval($request->get("productId"));
		if($productId > 0) {
			$rcmId = $request->get("rcmId");
			$APPLICATION->IncludeComponent("bitrix:catalog.element", $action == "quickView" ? "article" : "",
				array(
					"IBLOCK_TYPE" => $parameters["IBLOCK_TYPE"],
					"IBLOCK_ID" => $parameters["IBLOCK_ID"],
					"PROPERTY_CODE" => $parameters["DETAIL_PROPERTY_CODE"],					
					"SET_META_KEYWORDS" => "N",
					"META_KEYWORDS" => "-",
					"SET_META_DESCRIPTION" => "N",
					"META_DESCRIPTION" => "-",				
					"SET_BROWSER_TITLE" => "N",
					"BROWSER_TITLE" => "-",				
					"SET_CANONICAL_URL" => "N",
					"BASKET_URL" => $parameters["BASKET_URL"],
					"ACTION_VARIABLE" => "action",
					"PRODUCT_ID_VARIABLE" => "id",
					"SECTION_ID_VARIABLE" => "SECTION_ID",
					"CHECK_SECTION_ID_VARIABLE" => "N",
					"PRODUCT_QUANTITY_VARIABLE" => "quantity",
					"PRODUCT_PROPS_VARIABLE" => "prop",
					"CACHE_TYPE" => $parameters["CACHE_TYPE"],
					"CACHE_TIME" => $parameters["CACHE_TIME"],
					"CACHE_GROUPS" => $parameters["CACHE_GROUPS"],
					"SET_TITLE" => "N",
					"SET_LAST_MODIFIED" => "N",
					"MESSAGE_404" => "",
					"SET_STATUS_404" => "N",
					"SHOW_404" => "N",
					"FILE_404" => "",
					"PRICE_CODE" => $parameters["PRICE_CODE"],
					"USE_PRICE_COUNT" => $parameters["USE_PRICE_COUNT"],
					"USE_RATIO_IN_RANGES" => $parameters["DETAIL_USE_RATIO_IN_RANGES"],
					"SHOW_PRICE_COUNT" => $parameters["SHOW_PRICE_COUNT"],
					"PRICE_VAT_INCLUDE" => $parameters["PRICE_VAT_INCLUDE"],
					"PRICE_VAT_SHOW_VALUE" => "N",
					"USE_PRODUCT_QUANTITY" => $parameters["USE_PRODUCT_QUANTITY"],
					"PRODUCT_PROPERTIES" => $parameters["PRODUCT_PROPERTIES"],
					"ADD_PROPERTIES_TO_BASKET" => $parameters["ADD_PROPERTIES_TO_BASKET"],
					"PARTIAL_PRODUCT_PROPERTIES" => $parameters["PARTIAL_PRODUCT_PROPERTIES"],
					"LINK_IBLOCK_TYPE" => "",
					"LINK_IBLOCK_ID" => "",
					"LINK_PROPERTY_SID" => "",
					"LINK_ELEMENTS_URL" => "",
				
					"OFFERS_CART_PROPERTIES" => $parameters["OFFERS_CART_PROPERTIES"],
					"OFFERS_FIELD_CODE" => $parameters["DETAIL_OFFERS_FIELD_CODE"],				
					"OFFERS_PROPERTY_CODE" => $parameters["DETAIL_OFFERS_PROPERTY_CODE"],
					"OFFERS_SORT_FIELD" => $parameters["OFFERS_SORT_FIELD"],
					"OFFERS_SORT_ORDER" => $parameters["OFFERS_SORT_ORDER"],
					"OFFERS_SORT_FIELD2" => $parameters["OFFERS_SORT_FIELD2"],
					"OFFERS_SORT_ORDER2" => $parameters["OFFERS_SORT_ORDER2"],

					"ELEMENT_ID" => $productId,
					"ELEMENT_CODE" => "",
					"SECTION_ID" => "",
					"SECTION_CODE" => "",
					"SECTION_URL" => "",
					"DETAIL_URL" => "",
					"CONVERT_CURRENCY" => $parameters["CONVERT_CURRENCY"],
					"CURRENCY_ID" => $parameters["CURRENCY_ID"],
					"HIDE_NOT_AVAILABLE" => $parameters["HIDE_NOT_AVAILABLE"],
					"HIDE_NOT_AVAILABLE_OFFERS" => $parameters["HIDE_NOT_AVAILABLE_OFFERS"],
					"PRODUCT_DISPLAY_MODE" => $parameters["PRODUCT_DISPLAY_MODE"],
					
					"USE_ELEMENT_COUNTER" => "Y",
					"SHOW_DEACTIVATED" => "N",
					
					"USE_MAIN_ELEMENT_SECTION" => $parameters["USE_MAIN_ELEMENT_SECTION"],
					"STRICT_SECTION_CHECK" => "N",
					"ADD_PICT_PROP" => $parameters["DETAIL_ADD_PICT_PROP"],				
					"OFFER_ADD_PICT_PROP" => $parameters["DETAIL_OFFER_ADD_PICT_PROP"],
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
					"MESS_BTN_COMPARE" => $parameters["MESS_BTN_COMPARE"],
					"MAIN_BLOCK_PROPERTY_CODE" => $parameters["DETAIL_MAIN_BLOCK_PROPERTY_CODE"],
					"MAIN_BLOCK_OFFERS_PROPERTY_CODE" => $parameters["DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE"],				
					"IMAGE_RESOLUTION" => $parameters["DETAIL_IMAGE_RESOLUTION"],				
					"ADD_DETAIL_TO_SLIDER" => $parameters["DETAIL_ADD_DETAIL_TO_SLIDER"],				
					"ADD_SECTIONS_CHAIN" => "N",
					"ADD_ELEMENT_CHAIN" => "N",				
					"DETAIL_PICTURE_MODE" => $parameters["DETAIL_DETAIL_PICTURE_MODE"],
					"ADD_TO_BASKET_ACTION" => array($parameters["ADD_TO_BASKET_ACTION"]),
					"ADD_TO_BASKET_ACTION_PRIMARY" => "",
					"DISPLAY_COMPARE" => $parameters["DISPLAY_COMPARE"],
					"COMPARE_PATH" => $parameters["COMPARE_PATH"],
					"COMPARE_NAME" => $parameters["COMPARE_NAME"],
					"BACKGROUND_IMAGE" => "-",
					"COMPATIBLE_MODE" => "N",
					"DISABLE_INIT_JS_IN_COMPONENT" => "N",
					"SET_VIEWED_IN_COMPONENT" => "N",
					"SHOW_SLIDER" => $parameters["DETAIL_SHOW_SLIDER"],
					"SLIDER_INTERVAL" => $parameters["DETAIL_SLIDER_INTERVAL"],
					"SLIDER_PROGRESS" => $parameters["DETAIL_SLIDER_PROGRESS"],
					"USE_ENHANCED_ECOMMERCE" => "N",
					"DATA_LAYER_NAME" => "",
					"BRAND_PROPERTY" => "",

					"USE_GIFTS_DETAIL" => $parameters["USE_GIFTS_DETAIL"],
					"GIFTS_DETAIL_PAGE_ELEMENT_COUNT" => $parameters["GIFTS_DETAIL_PAGE_ELEMENT_COUNT"],
					"GIFTS_DETAIL_HIDE_BLOCK_TITLE" => $parameters["GIFTS_DETAIL_HIDE_BLOCK_TITLE"],
					"GIFTS_DETAIL_BLOCK_TITLE" => $parameters["GIFTS_DETAIL_BLOCK_TITLE"],
					"GIFTS_DETAIL_TEXT_LABEL_GIFT" => $parameters["GIFTS_DETAIL_TEXT_LABEL_GIFT"],
					"GIFTS_MESS_BTN_BUY" => $parameters["~GIFTS_MESS_BTN_BUY"],

					"USE_STORE" => $parameters["USE_STORE"],
					"STORE_PATH" => $parameters["STORE_PATH"],
					"STORES" => $parameters["STORES"],
					"USE_MIN_AMOUNT" => $parameters["USE_MIN_AMOUNT"],
					"USER_FIELDS" => $parameters["USER_FIELDS"],
					"FIELDS" => $parameters["FIELDS"],
					"MIN_AMOUNT" => $parameters["MIN_AMOUNT"],
					"SHOW_EMPTY_STORE" => $parameters["SHOW_EMPTY_STORE"],
					"SHOW_GENERAL_STORE_INFORMATION" => $parameters["SHOW_GENERAL_STORE_INFORMATION"],
					"MAIN_TITLE" => $parameters["~MAIN_TITLE"],
						
					"LIST_PROPERTY_CODE" => $parameters["PROPERTY_CODE"],
					"LIST_OFFERS_FIELD_CODE" => $parameters["OFFERS_FIELD_CODE"],
					"LIST_OFFERS_PROPERTY_CODE" => $parameters["OFFERS_PROPERTY_CODE"],
					"LIST_OFFERS_LIMIT" => $parameters["OFFERS_LIMIT"],
						
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
						
					"SET_ITEMS_COUNT" => $parameters["SET_ITEMS_COUNT"],

					"OBJECTS_USE_REVIEW" => $parameters["OBJECTS_USE_REVIEW"],
					"OBJECTS_REVIEWS_IBLOCK_ID" => $parameters["OBJECTS_REVIEWS_IBLOCK_ID"],
					"CONTACTS_IBLOCK_ID" => $parameters["CONTACTS_IBLOCK_ID"],
					"CONTACTS_USE_REVIEW" => $parameters["CONTACTS_USE_REVIEW"],
					"CONTACTS_REVIEWS_IBLOCK_ID" => $parameters["CONTACTS_REVIEWS_IBLOCK_ID"],
					"CONTACTS_REVIEWS_PAGE_LINK" => $parameters["CONTACTS_REVIEWS_PAGE_LINK"],

					"REINIT_ADD_BUY_URL_TEMPLATE" => "Y",
					"POPUP_MODE" => "Y",
					"RCM_ID" => !empty($rcmId) ? $rcmId : ""
				),
				false
			);

			$content = ob_get_contents();
			ob_end_clean();

			$result = array(
				"content" => $content
			);

			if(Bitrix\Main\Loader::includeModule("iblock")) {
				$needProductInfo = $request->get("needProductInfo");
				if($needProductInfo == "Y") {
					$rsElement = CIBlockElement::GetList(array(), array("ID" => $productId), false, false, array("ID", "IBLOCK_ID", "NAME", "DETAIL_PAGE_URL"));
					if($arElement = $rsElement->GetNext()) {
						$ipropValues = new Bitrix\Iblock\InheritedProperty\ElementValues($arElement["IBLOCK_ID"], $arElement["ID"]);
						$iproperty = $ipropValues->getValues();
						$result["productName"] = !empty($iproperty["ELEMENT_PAGE_TITLE"]) ? $iproperty["ELEMENT_PAGE_TITLE"] : $arElement["NAME"];
						$result["productUrl"] = $arElement["DETAIL_PAGE_URL"];
					}
					unset($iproperty, $ipropValues, $arElement, $rsElement);
				}

				$prevNext = $request->get("prevNext");
				if($prevNext == "Y") {
					$arOrder = array(
						$parameters["ELEMENT_SORT_FIELD"] => $parameters["ELEMENT_SORT_ORDER"],
						$parameters["ELEMENT_SORT_FIELD2"] => $parameters["ELEMENT_SORT_ORDER2"]
					);
					
					$arFilter = array(
						"ACTIVE" => "Y",
						"ACTIVE_DATE" => "Y",
						"IBLOCK_ID" => $parameters["IBLOCK_ID"],
						"SECTION_ID" => $parameters["SECTION_ID"],
						"SECTION_CODE" => $parameters["SECTION_CODE"],
						"INCLUDE_SUBSECTIONS" => $parameters["INCLUDE_SUBSECTIONS"],
						"SECTION_GLOBAL_ACTIVE" => "Y",
						"AVAILABLE" => "Y"
					);

					$arrFilter = !empty($parameters["GLOBAL_FILTER"]) ? $parameters["GLOBAL_FILTER"] : array();
					
					$arNavStartParams = array(
						"nElementID" => $productId,
						"nPageSize" => "1"
					);

					$next = false;
					$rsElements = CIBlockElement::GetList($arOrder, array_merge($arFilter , $arrFilter), false, $arNavStartParams);
					while($arElement = $rsElements->GetNext()) {
						if($arElement["ID"] == $productId) {
							$next = true;
						} elseif($next) {
							$result["nextProductId"] = $arElement["ID"];
						} else {
							$result["prevProductId"] = $arElement["ID"];
						}
					}
					unset($arElement, $rsElements, $next);
				}
				
				Bitrix\Iblock\Component\Base::sendJsonAnswer($result);
			}
		}
	}
}