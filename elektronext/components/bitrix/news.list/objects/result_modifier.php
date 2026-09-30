<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

if(count($arResult["ITEMS"]) < 1)
	return;

$arDays = array("MON", "TUE", "WED", "THU", "FRI", "SAT", "SUN");

foreach($arResult["ITEMS"] as &$arItem) {
	//ITEM_IDS//
	$itemIds[] = $arItem["ID"];
	foreach($arItem["DISPLAY_PROPERTIES"] as $arProp) {
		//HIDE_IN_OBJECTS_LIST//
		if($arProp["CODE"] == "HIDE_IN_OBJECTS_LIST" && !empty($arProp["VALUE"])) {
			$arItem[$arProp["CODE"]] = true;
		//ADDRESS//
		} elseif($arProp["CODE"] == "ADDRESS" && !empty($arProp["VALUE"])) {
			$arItem[$arProp["CODE"]] = $arProp["VALUE"];
		//TIMEZONE//
		} elseif($arProp["CODE"] == "TIMEZONE" && !empty($arProp["VALUE"])) {
			$timezoneIds[] = $arProp["VALUE"];
		//WORKING_HOURS//
		} elseif(in_array($arProp["CODE"], $arDays) && !empty($arProp["VALUE"])) {
			$workingHoursIds[] = $arProp["VALUE"];
		//TOUR_3D//	
		} elseif($arProp["CODE"] == "TOUR_3D" && !empty($arProp["VALUE"])) {
			$arItem[$arProp["CODE"]] = array(
				"NAME" => $arProp["NAME"],
				"VALUE" => $arProp["DISPLAY_VALUE"]
			);
		//AFFILIATES//
		} elseif($arProp["CODE"] == "AFFILIATES" && !empty($arProp["VALUE"])) {
			$arItem[$arProp["CODE"]] = array(
				"NAME" => $arProp["NAME"],
				"VALUE" => $arProp["VALUE"]
			);
		//PHONE_WHATSAPP_VIBER_TELEGRAM_INSTAGRAM_EMAIL_SKYPE//
		} elseif(($arProp["CODE"] == "PHONE" || $arProp["CODE"] == "WHATSAPP" || $arProp["CODE"] == "VIBER" || $arProp["CODE"] == "TELEGRAM" || $arProp["CODE"] == "INSTAGRAM" || $arProp["CODE"] == "EMAIL" || $arProp["CODE"] == "SKYPE") && !empty($arProp["VALUE"])) {
			$arItem[$arProp["CODE"]] = array(
				"VALUE" => $arProp["VALUE"],
				"DESCRIPTION" => $arProp["DESCRIPTION"]
			);
		//PHONE_SMS_EMAIL_EMAIL//
		} elseif(($arProp["CODE"] == "PHONE_SMS" || $arProp["CODE"] == "EMAIL_EMAIL") && !empty($arProp["VALUE"])) {
			$arItem[$arProp["CODE"]] = true;
		}
	}
	unset($arProp);
}
unset($arItem);

//WORKING_HOURS//
if(!empty($workingHoursIds)) {	
	$rsElements = CIBlockElement::GetList(array(), array("ID" => array_unique($workingHoursIds)), false, false, array("ID", "IBLOCK_ID"));	
	while($obElement = $rsElements->GetNextElement()) {
		$arElement = $obElement->GetFields();
		$arElement["PROPERTIES"] = $obElement->GetProperties();

		$arWorkingHours[$arElement["ID"]] = array(
			"WORK_START" => strtotime($arElement["PROPERTIES"]["WORK_START"]["VALUE"]) ? $arElement["PROPERTIES"]["WORK_START"]["VALUE"] : "",
			"WORK_END" => strtotime($arElement["PROPERTIES"]["WORK_END"]["VALUE"]) ? $arElement["PROPERTIES"]["WORK_END"]["VALUE"] : "",
			"BREAK_START" => strtotime($arElement["PROPERTIES"]["BREAK_START"]["VALUE"]) ? $arElement["PROPERTIES"]["BREAK_START"]["VALUE"] : "",
			"BREAK_END" => strtotime($arElement["PROPERTIES"]["BREAK_END"]["VALUE"]) ? $arElement["PROPERTIES"]["BREAK_END"]["VALUE"] : ""
		);
	}
	unset($arElement, $obElement, $rsElements);
	
	if(!empty($arWorkingHours)) {
		foreach($arResult["ITEMS"] as &$arItem) {
			foreach($arItem["DISPLAY_PROPERTIES"] as $arProp) {
				if(in_array($arProp["CODE"], $arDays) && !empty($arProp["VALUE"])) {
					if(array_key_exists($arProp["VALUE"], $arWorkingHours)) {
						$arItem["WORKING_HOURS"][$arProp["CODE"]] = $arWorkingHours[$arProp["VALUE"]];
						$arItem["WORKING_HOURS"][$arProp["CODE"]]["NAME"] = $arProp["NAME"];
					}
				}
			}
			unset($arProp);
		}
		unset($arItem);
	}
	unset($arWorkingHours);
}
unset($workingHoursIds);

//TIMEZONE//
if(!empty($timezoneIds)) {
	$rsElements = CIBlockElement::GetList(array(), array("ID" => array_unique($timezoneIds)), false, false, array("ID", "IBLOCK_ID"));	
	while($obElement = $rsElements->GetNextElement()) {
		$arElement = $obElement->GetFields();
		$arElement["PROPERTIES"] = $obElement->GetProperties();

		$arTimeZones[$arElement["ID"]] = $arElement["PROPERTIES"]["OFFSET"]["VALUE"];
	}
	unset($arElement, $obElement, $rsElements);

	if(!empty($arTimeZones)) {
		foreach($arResult["ITEMS"] as &$arItem) {
			foreach($arItem["DISPLAY_PROPERTIES"] as $arProp) {
				if($arProp["CODE"] == "TIMEZONE" && !empty($arProp["VALUE"])) {
					if(array_key_exists($arProp["VALUE"], $arTimeZones))
						$arItem[$arProp["CODE"]] = $arTimeZones[$arProp["VALUE"]];
				}
			}
			unset($arProp);
		}
		unset($arItem);
	}
	unset($arTimeZones);
}
unset($timezoneIds);

if(!empty($itemIds)) {
	//PROMOTIONS_IDS//
	if($arParams["SHOW_PROMOTIONS"] != "N" && intval($arParams["PROMOTIONS_IBLOCK_ID"]) > 0) {
		$rsElements = CIBlockElement::GetList(array(), array("ACTIVE" => "Y", "ACTIVE_DATE" => "Y", "IBLOCK_ID" => $arParams["PROMOTIONS_IBLOCK_ID"], "PROPERTY_OBJECT" => $itemIds), false, false, array("ID", "IBLOCK_ID", "PROPERTY_OBJECT"));	
		while($arElement = $rsElements->GetNext()) {
			$arPromotions[$arElement["PROPERTY_OBJECT_VALUE"]][] = $arElement["ID"];
		}
		unset($arElement, $rsElements);

		if(!empty($arPromotions)) {
			$arPromoIds = array();		
			foreach($arPromotions as $ids) {
				$arPromoIds = array_merge($arPromoIds, $ids);
			}
			unset($ids);
				
			if(!empty($arPromoIds)) {
				$rsGroups = CIBlockElement::GetElementGroups(array_unique($arPromoIds), true, array("ID", "GLOBAL_ACTIVE", "IBLOCK_ELEMENT_ID"));
				while($arGroup = $rsGroups->GetNext()) {
					if($arGroup["GLOBAL_ACTIVE"] != "Y") {
						foreach($arPromotions as $key => $ids) {
							foreach($ids as $key2 => $id) {
								if($id == $arGroup["IBLOCK_ELEMENT_ID"])
									unset($arPromotions[$key][$key2]);
							}
							unset($key2, $id);
						}
						unset($key, $ids);
					}
				}
				unset($arGroup, $rsGroups);
			}
			unset($arPromoIds);
		
			if(!empty($arPromotions)) {
				foreach($arResult["ITEMS"] as &$arItem) {
					if(array_key_exists($arItem["ID"], $arPromotions))
						$arItem["PROMOTIONS_IDS"] = $arPromotions[$arItem["ID"]];
				}
				unset($arItem);
			}
		}
		unset($arPromotions);
	}
	
	//PRODUCTS_IDS//
	if(intval($arParams["CATALOG_IBLOCK_ID"]) > 0) {
		if(Bitrix\Main\Loader::includeModule("catalog")) {
			$mxResult = CCatalogSKU::GetInfoByProductIBlock($arParams["CATALOG_IBLOCK_ID"]);
			$offersIblockId = is_array($mxResult) ? $mxResult["IBLOCK_ID"] : false;
			if(intval($offersIblockId) > 0) {
				$rsElements = CIBlockElement::GetList(array(), array("ACTIVE" => "Y", "IBLOCK_ID" => $offersIblockId, "PROPERTY_OBJECT" => $itemIds), false, false, array("ID", "IBLOCK_ID", "PROPERTY_OBJECT"));	
				while($arElement = $rsElements->GetNext()) {
					$arOffers[$arElement["ID"]] = $arElement["PROPERTY_OBJECT_VALUE"];
				}
				unset($arElement, $rsElements);
				
				if(!empty($arOffers)) {
					$productList = CCatalogSku::getProductList(array_keys($arOffers));
					if(!empty($productList)) {
						foreach($productList as $offerId => $offerInfo) {
							if(array_key_exists($offerId, $arOffers))
								$arProducts[$arOffers[$offerId]][] = $offerInfo["ID"];
						}
						unset($offerId, $offerInfo);
					}
					unset($productList);
				}
				unset($arOffers);
			}
			unset($offersIblockId, $mxResult);
		}
		
		$rsElements = CIBlockElement::GetList(array(), array("ACTIVE" => "Y", "IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"], "SECTION_GLOBAL_ACTIVE" => "Y", "PROPERTY_OBJECT" => $itemIds), false, false, array("ID", "IBLOCK_ID", "PROPERTY_OBJECT"));
		while($arElement = $rsElements->GetNext()) {
			$arProducts[$arElement["PROPERTY_OBJECT_VALUE"]][] = $arElement["ID"];
		}
		unset($arElement, $rsElements);
		
		if(!empty($arProducts)) {
			foreach($arResult["ITEMS"] as &$arItem) {
				if(array_key_exists($arItem["ID"], $arProducts))
					$arItem["PRODUCTS_IDS"] = $arProducts[$arItem["ID"]];
			}
			unset($arItem);
		}
		unset($arProducts);
	}

	//RATING_REVIEWS_COUNT//
	if($arParams["USE_REVIEW"] != "N" && intval($arParams["REVIEWS_IBLOCK_ID"]) > 0) {
		foreach($arResult["ITEMS"] as $arItem) {
			$ratingSum[$arItem["ID"]] = 0;
			$reviewsCount[$arItem["ID"]] = 0;
		}
		unset($arItem);
		
		$rsElements = CIBlockElement::GetList(array(), array("ACTIVE" => "Y", "IBLOCK_ID" => $arParams["REVIEWS_IBLOCK_ID"], "PROPERTY_OBJECT_ID" => $itemIds), false, false, array("ID", "IBLOCK_ID"));
		while($obElement = $rsElements->GetNextElement()) {
			$arElement = $obElement->GetFields();
			$arProps = $obElement->GetProperties();

			$ratingSum[$arProps["OBJECT_ID"]["VALUE"]] += $arProps["RATING"]["VALUE_XML_ID"];
			
			$reviewsCount[$arProps["OBJECT_ID"]["VALUE"]]++;
		}
		unset($arProps, $arElement, $obElement, $rsElements);

		$reviewsDeclension = new Bitrix\Main\Grid\Declension(Loc::getMessage("OBJECT_ITEM_REVIEW"), Loc::getMessage("OBJECT_ITEM_REVIEWS_1"), Loc::getMessage("OBJECT_ITEM_REVIEWS_2"));

		foreach($arResult["ITEMS"] as &$arItem) {
			$arItem["RATING_VALUE"] = $reviewsCount[$arItem["ID"]] > 0 ? sprintf("%.1f", round($ratingSum[$arItem["ID"]] / $reviewsCount[$arItem["ID"]], 1)) : 0;
			$arItem["REVIEWS_COUNT"] = $reviewsCount[$arItem["ID"]];
			$arItem["REVIEWS_DECLENSION"] = $reviewsDeclension->get($reviewsCount[$arItem["ID"]]);
		}
		unset($arItem, $reviewsDeclension, $reviewsCount, $ratingSum);
	}
}
unset($itemIds);