<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if(count($arResult["STORES"]) < 1)
	return;

foreach($arResult["STORES"] as &$arItem) {
	$storesIds[] = $arItem["ID"];	
	if(!empty($arItem["IMAGE_ID"]))
		$arItem["PREVIEW_PICTURE"] = CFile::ResizeImageGet($arItem["IMAGE_ID"], array("width" => 76, "height" => 40), BX_RESIZE_IMAGE_PROPORTIONAL, true);
}
unset($arItem);

if(!empty($storesIds)) {
	$arStores = array();
	$rsStores = CCatalogStore::GetList(array("TITLE" => "ASC", "ID" => "ASC"), array("ID" => $storesIds), false, false, array("ID", "UF_OBJECT"));
	while($arStore = $rsStores->GetNext()) {
		if($arStore["UF_OBJECT"] > 0)
			$arStores[$arStore["ID"]]["OBJECT"]["VALUE"] = $arStore["UF_OBJECT"];
	}
	unset($arStore, $rsStores);

	if(!empty($arStores)) {
		foreach($arStores as $arStore) {
			$objectsIds[] = $arStore["OBJECT"]["VALUE"];
		}
		unset($arStore);

		$arObjects = array();
		if(!empty($objectsIds)) {
			$arDays = array("MON", "TUE", "WED", "THU", "FRI", "SAT", "SUN");
			$rsElements = CIBlockElement::GetList(array(), array("ID" => array_unique($objectsIds)), false, false, array("ID", "IBLOCK_ID", "NAME", "PREVIEW_PICTURE", "DETAIL_PAGE_URL"));
			while($obElement = $rsElements->GetNextElement()) {
				$arElement = $obElement->GetFields();
				$arElement["PROPERTIES"] = $obElement->GetProperties();

				$arObjects[$arElement["ID"]] = array(
					"ID" => $arElement["ID"],
					"NAME" => $arElement["NAME"],
					"PREVIEW_PICTURE" => $arElement["PREVIEW_PICTURE"] > 0 ? CFile::GetFileArray($arElement["PREVIEW_PICTURE"]) : false,
					"DETAIL_PAGE_URL" => $arElement["DETAIL_PAGE_URL"]
				);

				foreach($arElement["PROPERTIES"] as $arElProp) {
					//OBJECT_ADDRESS//
					if($arElProp["CODE"] == "ADDRESS" && !empty($arElProp["VALUE"])) {
						$arObjects[$arElement["ID"]][$arElProp["CODE"]] = $arElProp["VALUE"];
					//OBJECT_TIMEZONE//
					} elseif($arElProp["CODE"] == "TIMEZONE" && !empty($arElProp["VALUE"])) {
						$rsTZElement = CIBlockElement::GetList(array(), array("ID" => $arElProp["VALUE"], "IBLOCK_ID" => $arElProp["LINK_IBLOCK_ID"]), false, false, array("ID", "IBLOCK_ID"));	
						while($obTZElement = $rsTZElement->GetNextElement()) {
							$arTZElement = $obTZElement->GetFields();
							$arTZElement["PROPERTIES"] = $obTZElement->GetProperties();

							$arObjects[$arElement["ID"]][$arElProp["CODE"]] = $arTZElement["PROPERTIES"]["OFFSET"]["VALUE"];
						}
						unset($arTZElement, $obTZElement, $rsTZElement);
					//OBJECT_WORKING_HOURS//
					} elseif(in_array($arElProp["CODE"], $arDays) && !empty($arElProp["VALUE"])) {
						$workingHoursIds[] = $arElProp["VALUE"];
					}
				}
				unset($arElProp);

				//OBJECT_WORKING_HOURS//
				if(!empty($workingHoursIds)) {	
					$rsWHElements = CIBlockElement::GetList(array(), array("ID" => array_unique($workingHoursIds)), false, false, array("ID", "IBLOCK_ID"));	
					while($obWHElement = $rsWHElements->GetNextElement()) {
						$arWHElement = $obWHElement->GetFields();
						$arWHElement["PROPERTIES"] = $obWHElement->GetProperties();

						$arWorkingHours[$arWHElement["ID"]] = array(
							"WORK_START" => strtotime($arWHElement["PROPERTIES"]["WORK_START"]["VALUE"]) ? $arWHElement["PROPERTIES"]["WORK_START"]["VALUE"] : "",
							"WORK_END" => strtotime($arWHElement["PROPERTIES"]["WORK_END"]["VALUE"]) ? $arWHElement["PROPERTIES"]["WORK_END"]["VALUE"] : "",
							"BREAK_START" => strtotime($arWHElement["PROPERTIES"]["BREAK_START"]["VALUE"]) ? $arWHElement["PROPERTIES"]["BREAK_START"]["VALUE"] : "",
							"BREAK_END" => strtotime($arWHElement["PROPERTIES"]["BREAK_END"]["VALUE"]) ? $arWHElement["PROPERTIES"]["BREAK_END"]["VALUE"] : ""
						);
					}
					unset($arWHElement, $obWHElement, $rsWHElements);
					
					if(!empty($arWorkingHours)) {
						foreach($arElement["PROPERTIES"] as $arElProp) {
							if(in_array($arElProp["CODE"], $arDays) && !empty($arElProp["VALUE"])) {
								if(array_key_exists($arElProp["VALUE"], $arWorkingHours)) {
									$arObjects[$arElement["ID"]]["WORKING_HOURS"][$arElProp["CODE"]] = $arWorkingHours[$arElProp["VALUE"]];
									$arObjects[$arElement["ID"]]["WORKING_HOURS"][$arElProp["CODE"]]["NAME"] = $arElProp["NAME"];
								}
							}
						}
						unset($arElProp);
					}
					unset($arWorkingHours);
				}
				unset($workingHoursIds);
			}
			unset($arElement, $obElement, $rsElements, $arDays);
		}
		unset($objectsIds);

		foreach($arStores as &$arStore) {
			if(array_key_exists($arStore["OBJECT"]["VALUE"], $arObjects))
				$arStore["OBJECT"]["FULL_VALUE"] = $arObjects[$arStore["OBJECT"]["VALUE"]];
		}
		unset($arStore);
		unset($arObjects);

		foreach($arResult["STORES"] as &$arItem) {
			if(array_key_exists($arItem["ID"], $arStores))
				$arItem["OBJECT"] = $arStores[$arItem["ID"]]["OBJECT"]["FULL_VALUE"];
		}
		unset($arItem);
	}
	unset($arStores);
}
unset($storesIds);