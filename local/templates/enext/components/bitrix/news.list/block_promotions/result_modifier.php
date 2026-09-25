<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if(count($arResult["ITEMS"]) < 1)
	return;

//DISPLAY_ACTIVE_TO//
foreach($arResult["ITEMS"] as &$arItem) {	
	if(!isset($arItem["DISPLAY_ACTIVE_TO"]) && !empty($arItem["ACTIVE_TO"]))
		$arItem["DISPLAY_ACTIVE_TO"] = CIBlockFormatProperties::DateFormat($arParams["ACTIVE_DATE_FORMAT"], MakeTimeStamp($arItem["ACTIVE_TO"], CSite::GetDateFormat()));
}
unset($arItem);

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

//CACHE_ITEMS//
foreach($arResult["ITEMS"] as $key => $arItem) {	
	$arResult["CACHE_ITEMS"][$key] = array(
		"ID" => $arItem["ID"],
		"EDIT_LINK" => $arItem["EDIT_LINK"],
		"DELETE_LINK" => $arItem["DELETE_LINK"],
		"NAME" => $arItem["NAME"],
		"PREVIEW_PICTURE" => $arItem["PREVIEW_PICTURE"],
		"DETAIL_PAGE_URL" => $arItem["DETAIL_PAGE_URL"],		
		"ACTIVE_TO" => $arItem["ACTIVE_TO"],
		"DISPLAY_ACTIVE_TO" => $arItem["DISPLAY_ACTIVE_TO"],
		"MARKER" => $arItem["MARKER"],
		"SHOW_TIMER" => $arItem["DISPLAY_PROPERTIES"]["SHOW_TIMER"]["VALUE"]
	);
}
unset($key, $arItem);

//CACHE_KEYS//
$this->__component->SetResultCacheKeys(
	array(
		"CACHE_ITEMS"
	)
);