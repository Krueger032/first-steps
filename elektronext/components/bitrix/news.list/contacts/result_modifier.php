<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

if(count($arResult["ITEMS"]) < 1)
	return;

$arDays = array("MON", "TUE", "WED", "THU", "FRI", "SAT", "SUN");

foreach($arResult["ITEMS"] as &$arItem) {	
	foreach($arItem["DISPLAY_PROPERTIES"] as $arProp) {
		//ADDRESS//
		if($arProp["CODE"] == "ADDRESS" && !empty($arProp["VALUE"])) {
			$arItem[$arProp["CODE"]] = $arProp["VALUE"];
		//MAP//
		} elseif($arProp["CODE"] == "MAP" && !empty($arProp["VALUE"])) {
			$arItem[$arProp["CODE"]] = $arProp["VALUE"];
		//TIMEZONE//
		} elseif($arProp["CODE"] == "TIMEZONE" && !empty($arProp["VALUE"])) {
			$timezoneIds[] = $arProp["VALUE"];
		//WORKING_HOURS//
		} elseif(in_array($arProp["CODE"], $arDays) && !empty($arProp["VALUE"])) {
			$workingHoursIds[] = $arProp["VALUE"];		
		//PHONE_WHATSAPP_VIBER_TELEGRAM_INSTAGRAM_EMAIL_SKYPE//
		} elseif(($arProp["CODE"] == "PHONE" || $arProp["CODE"] == "WHATSAPP" || $arProp["CODE"] == "VIBER" || $arProp["CODE"] == "TELEGRAM" || $arProp["CODE"] == "INSTAGRAM" || $arProp["CODE"] == "EMAIL" || $arProp["CODE"] == "SKYPE") && !empty($arProp["VALUE"])) {
			$arItem[$arProp["CODE"]] = array(
				"VALUE" => $arProp["VALUE"],
				"DESCRIPTION" => $arProp["DESCRIPTION"]
			);
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

//RATING_REVIEWS_COUNT//
if($arParams["USE_REVIEW"] != "N") {
	$arResult["REVIEWS_PAGE_LINK"] = !empty($arParams["REVIEWS_PAGE_LINK"]) ? $arParams["REVIEWS_PAGE_LINK"] : SITE_DIR."about/reviews/";
	if(intval($arParams["REVIEWS_IBLOCK_ID"]) > 0) {
		$ratingSum = $reviewsCount = 0;
		$rsElements = CIBlockElement::GetList(array(), array("ACTIVE" => "Y", "IBLOCK_ID" => $arParams["REVIEWS_IBLOCK_ID"]), false, false, array("ID", "IBLOCK_ID"));
		while($obElement = $rsElements->GetNextElement()) {
			$arElement = $obElement->GetFields();
			$arProps = $obElement->GetProperties();
					
			$ratingSum += $arProps["RATING"]["VALUE_XML_ID"];
					
			$reviewsCount++;
		}
		unset($arProps, $arElement, $obElement, $rsElements);

		$reviewsDeclension = new Bitrix\Main\Grid\Declension(Loc::getMessage("CONTACTS_ITEM_REVIEW"), Loc::getMessage("CONTACTS_ITEM_REVIEWS_1"), Loc::getMessage("CONTACTS_ITEM_REVIEWS_2"));

		$arResult["RATING_VALUE"] = $reviewsCount > 0 ? sprintf("%.1f", round($ratingSum / $reviewsCount, 1)) : 0;
		$arResult["REVIEWS_COUNT"] = $reviewsCount;
		$arResult["REVIEWS_DECLENSION"] = $reviewsDeclension->get($reviewsCount);		

		unset($reviewsDeclension, $reviewsCount, $ratingSum);
	}
}

//OBJECTS//
if(intval($arParams["OBJECTS_IBLOCK_ID"] > 0)) {
	$rsElements = CIBlockElement::GetList(array(), array("ACTIVE" => "Y", "IBLOCK_ID" => $arParams["OBJECTS_IBLOCK_ID"], "!PROPERTY_SHOW_IN_CONTACTS" => false), false, false, array("ID", "IBLOCK_ID", "NAME", "PREVIEW_PICTURE", "DETAIL_PAGE_URL"));	
	while($obElement = $rsElements->GetNextElement()) {
		$arElement = $obElement->GetFields();

		//OBJECT_PREVIEW_PICTURE//
		if($arElement["PREVIEW_PICTURE"] > 0)
			$arElement["PREVIEW_PICTURE"] = CFile::GetFileArray($arElement["PREVIEW_PICTURE"]);

		//OBJECT_PROPERTIES//
		$arElement["PROPERTIES"] = $obElement->GetProperties();

		if(!empty($arElement["PROPERTIES"]["MAP"]["VALUE"])) {
			$arTmp = explode(",", $arElement["PROPERTIES"]["MAP"]["VALUE"]);
			$arResult["OBJECTS"][$arElement["ID"]] = array(
				"ID" => $arElement["ID"],
				"NAME" => $arElement["NAME"],
				"PREVIEW_PICTURE_SRC" => is_array($arElement["PREVIEW_PICTURE"]) ? $arElement["PREVIEW_PICTURE"]["SRC"] : false,
				"DETAIL_PAGE_URL" => $arElement["DETAIL_PAGE_URL"],
				"ADDRESS" => $arElement["PROPERTIES"]["ADDRESS"]["VALUE"],
				"LON" => $arTmp[1],
				"LAT" => $arTmp[0],
				"AFFILIATES" => $arElement["PROPERTIES"]["AFFILIATES"]["VALUE"],
				"HIDE_IN_OBJECTS_LIST" => !$arElement["PROPERTIES"]["HIDE_IN_OBJECTS_LIST"]["VALUE"] ? false : true
			);
			unset($arTmp);
		}
	 }
	unset($arElement, $obElement, $rsElements);

	if(!empty($arResult["OBJECTS"])) {
		foreach($arResult["OBJECTS"] as $arObject) {
			if(!empty($arObject["AFFILIATES"])) {
				if(!is_array($arObject["AFFILIATES"])) {
					if(!!$arResult["OBJECTS"][$arObject["AFFILIATES"]]["HIDE_IN_OBJECTS_LIST"])
						$arResult["OBJECTS"][$arObject["AFFILIATES"]]["PARENT_ID"][] = $arObject["ID"];
				} else {
					foreach($arObject["AFFILIATES"] as $arAffiliateId) {
						if(!!$arResult["OBJECTS"][$arAffiliateId]["HIDE_IN_OBJECTS_LIST"])
							$arResult["OBJECTS"][$arAffiliateId]["PARENT_ID"][] = $arObject["ID"];
					}
					unset($arAffiliateId);
				}
			}
		}
		unset($arObject);
	}
}