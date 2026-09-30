<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if(count($arResult["ITEMS"]) < 1)
	return;

//MARKERS//
foreach($arResult["ITEMS"] as $arItem) {
	foreach($arItem["DISPLAY_PROPERTIES"] as $arProp) {
		if($arProp["CODE"] == "MARKER" && !empty($arProp["VALUE"])) {
			if(!is_array($arProp["VALUE"])) {
				$markersIds[] = $arProp["VALUE"];
			} else {
				foreach($arProp["VALUE"] as $val) {
					$markersIds[] = $val;
				}
				unset($val);
			}
		}
	}
	unset($arProp);
}
unset($arItem);

if(!empty($markersIds)) {	
	$rsElements = CIBlockElement::GetList(array(), array("ID" => array_unique($markersIds), "ACTIVE" => "Y"), false, false, array("ID", "IBLOCK_ID", "NAME", "SORT"));	
	while($obElement = $rsElements->GetNextElement()) {
		$arElement = $obElement->GetFields();
		$arElement["PROPERTIES"] = $obElement->GetProperties();
		
		$arMarkers[$arElement["ID"]] = array(
			"NAME" => $arElement["NAME"],
			"SORT" => $arElement["SORT"],
			"BACKGROUND_1" => $arElement["PROPERTIES"]["BACKGROUND_1"]["VALUE"],
			"BACKGROUND_2" => $arElement["PROPERTIES"]["BACKGROUND_2"]["VALUE"],
			"ICON" => $arElement["PROPERTIES"]["ICON"]["VALUE"],
			"FONT_SIZE" => $arElement["PROPERTIES"]["FONT_SIZE"]["VALUE_XML_ID"]
		);
	}
	unset($arElement, $obElement, $rsElements);

	if(!empty($arMarkers)) {
		foreach($arResult["ITEMS"] as &$arItem) {
			foreach($arItem["DISPLAY_PROPERTIES"] as $arProp) {
				if($arProp["CODE"] == "MARKER" && !empty($arProp["VALUE"])) {
					if(!is_array($arProp["VALUE"])) {
						if(array_key_exists($arProp["VALUE"], $arMarkers))
							$arItem["MARKER"][] = $arMarkers[$arProp["VALUE"]];
					} else {
						foreach($arProp["VALUE"] as $val) {
							if(array_key_exists($val, $arMarkers))
								$arItem["MARKER"][] = $arMarkers[$val];
						}
						unset($val);
					}
				}
			}
			unset($arProp);

			if(!empty($arItem["MARKER"]))
				Bitrix\Main\Type\Collection::sortByColumn($arItem["MARKER"], array("SORT" => SORT_NUMERIC, "NAME" => SORT_ASC));
		}
		unset($arItem);
	}
	unset($arMarkers);
}
unset($markersIds);

//ITEMS_COUNT//
$arResult["ITEMS_COUNT"] = 0;

//COUNTRIES//
$rsElements = CIBlockElement::GetList(array(), array("ACTIVE" => "Y", "IBLOCK_ID" => $arParams["IBLOCK_ID"]), false, false, array("ID", "IBLOCK_ID", "PROPERTY_COUNTRY"));	
while($arElement = $rsElements->GetNext()) {	
	$arResult["ITEMS_COUNT"]++;
	if(!empty($arElement["PROPERTY_COUNTRY_VALUE"]))
		$arResult["COUNTRIES_IDS"][] = $arElement["PROPERTY_COUNTRY_VALUE"];
}
unset($arElement, $rsElements);

if(!empty($arResult["COUNTRIES_IDS"])) {
	$arCount = array_count_values($arResult["COUNTRIES_IDS"]);
	$rsElements = CIBlockElement::GetList(array("SORT" => "ASC", "NAME" => "ASC"), array("ID" => array_unique($arResult["COUNTRIES_IDS"])), false, false, array("ID", "IBLOCK_ID", "NAME"));	
	while($arElement = $rsElements->GetNext()) {
		$arResult["COUNTRIES"][] = array(
			"ID" => $arElement["ID"],
			"NAME" => $arElement["NAME"],
			"COUNT" => $arCount[$arElement["ID"]]
		);
	}
	unset($arElement, $rsElements, $arCount);
}
unset($arResult["COUNTRIES_IDS"]);