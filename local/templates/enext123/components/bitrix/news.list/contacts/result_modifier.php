<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

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

//OBJECTS//
if($arParams["OBJECTS_IBLOCK_ID"] > 0) {
	$rsElements = CIBlockElement::GetList(array(), array("ACTIVE" => "Y", "IBLOCK_ID" => $arParams["OBJECTS_IBLOCK_ID"], "!PROPERTY_SHOW_IN_CONTACTS" => false), false, false, array("ID", "IBLOCK_ID", "NAME", "PREVIEW_PICTURE", "DETAIL_PAGE_URL"));	
	while($obElement = $rsElements->GetNextElement()) {
		$arElement = $obElement->GetFields();
		$arElement["PROPERTIES"] = $obElement->GetProperties();

		$arResult["OBJECTS"]["VALUE"][] = array(
			"NAME" => $arElement["NAME"],
			"PREVIEW_PICTURE" => $arElement["PREVIEW_PICTURE"] > 0 ? CFile::GetFileArray($arElement["PREVIEW_PICTURE"]) : false,
			"DETAIL_PAGE_URL" => $arElement["DETAIL_PAGE_URL"],
			"ADDRESS" => !empty($arElement["PROPERTIES"]["ADDRESS"]["VALUE"]) ? $arElement["PROPERTIES"]["ADDRESS"]["VALUE"] : false,
			"MAP" => !empty($arElement["PROPERTIES"]["MAP"]["VALUE"]) ? $arElement["PROPERTIES"]["MAP"]["VALUE"] : false
		);
	}
	unset($arElement, $obElement, $rsElements);
}