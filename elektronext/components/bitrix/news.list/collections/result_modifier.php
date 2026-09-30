<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if(count($arResult["ITEMS"]) < 1)
	return;

//BRAND_MARKERS_COLOR_IDS//
foreach($arResult["ITEMS"] as $arItem) {
	foreach($arItem["DISPLAY_PROPERTIES"] as $arProp) {
		if($arProp["CODE"] == "BRAND" && !empty($arProp["VALUE"])) {
			$brandIds[] = $arProp["VALUE"];
		} elseif($arProp["CODE"] == "MARKER" && !empty($arProp["VALUE"])) {
			if(!is_array($arProp["VALUE"])) {
				$markersIds[] = $arProp["VALUE"];
			} else {
				foreach($arProp["VALUE"] as $val) {
					$markersIds[] = $val;
				}
				unset($val);
			}
		} elseif($arProp["CODE"] == "COLORS" && !empty($arProp["VALUE"])) {
			if(!is_array($arProp["VALUE"])) {
				$colorIds[$arProp["USER_TYPE_SETTINGS"]["TABLE_NAME"]][] = $arProp["VALUE"];
			} else {
				foreach($arProp["VALUE"] as $val) {
					$colorIds[$arProp["USER_TYPE_SETTINGS"]["TABLE_NAME"]][] = $val;
				}
				unset($val);
			}
		}
	}
	unset($arProp);
}
unset($arItem);

//DETAIL_PAGE_URL//
//BRAND//
if(!empty($brandIds)) {	
	$rsElements = CIBlockElement::GetList(array(), array("ID" => array_unique($brandIds)), false, false, array("ID", "IBLOCK_ID", "NAME", "DETAIL_PAGE_URL"));	
	while($obElement = $rsElements->GetNextElement()) {
		$arElement = $obElement->GetFields();
		$arElement["PROPERTIES"] = $obElement->GetProperties();
		
		$arBrandUrls[$arElement["ID"]] = $arElement["~DETAIL_PAGE_URL"];

		$arBrands[$arElement["ID"]]["NAME"] = $arElement["NAME"];

		foreach($arElement["PROPERTIES"] as $arBrandProp) {
			if($arBrandProp["CODE"] == "COUNTRY" && !empty($arBrandProp["VALUE"])) {
				$rsCountry = CIBlockElement::GetList(array(), array("ID" => $arBrandProp["VALUE"], "IBLOCK_ID" => $arBrandProp["LINK_IBLOCK_ID"]), false, false, array("ID", "IBLOCK_ID", "NAME"));
				if($arCountry = $rsCountry->GetNext()) {
					$arBrands[$arElement["ID"]]["COUNTRY"] = $arCountry["NAME"];
				}
				unset($arCountry, $rsCountry);
			}
		}
		unset($arBrandProp);
	}
	unset($arElement, $obElement, $rsElements);

	if(!empty($arBrandUrls)) {
		foreach($arResult["ITEMS"] as &$arItem) {
			foreach($arItem["DISPLAY_PROPERTIES"] as $arProp) {
				if($arProp["CODE"] == "BRAND" && !empty($arProp["VALUE"])) {
					if(array_key_exists($arProp["VALUE"], $arBrandUrls))
						$arItem["DETAIL_PAGE_URL"] = $arBrandUrls[$arProp["VALUE"]].$arItem["CODE"]."/";
				}
			}
			unset($arProp);
		}
		unset($arItem);
	}
	unset($arBrandUrls);

	if(!empty($arBrands)) {
		foreach($arResult["ITEMS"] as &$arItem) {
			foreach($arItem["DISPLAY_PROPERTIES"] as $arProp) {
				if($arProp["CODE"] == "BRAND" && !empty($arProp["VALUE"])) {
					if(array_key_exists($arProp["VALUE"], $arBrands))
						$arItem[$arProp["CODE"]] = $arBrands[$arProp["VALUE"]];
				}
			}
			unset($arProp);
		}
		unset($arItem);
	}
	unset($arBrands);
}
unset($brandIds);

//MARKERS//
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
							$arItem[$arProp["CODE"]][] = $arMarkers[$arProp["VALUE"]];
					} else {
						foreach($arProp["VALUE"] as $val) {
							if(array_key_exists($val, $arMarkers))
								$arItem[$arProp["CODE"]][] = $arMarkers[$val];
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

//COLORS//
if(!empty($colorIds) && CModule::IncludeModule("highloadblock")) {
	foreach($colorIds as $tableName => $ids) {
		$hlblock = Bitrix\Highloadblock\HighloadBlockTable::getList(array("filter" => array("TABLE_NAME" => $tableName)))->fetch();
		if(empty($hlblock))
			continue;

		$entity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
		
		$entityFields = $entity->getFields();
		if(!array_key_exists("UF_CODE", $entityFields))
			continue;

		$entityDataClass = $entity->getDataClass();
		
		$directorySelect = array("ID", "UF_NAME", "UF_FILE", "UF_CODE", "UF_XML_ID");
		$directoryOrder = array();
		$directoryFilter = array("UF_XML_ID" => array_unique($ids));
		
		$entityGetList = array(
			"select" => $directorySelect,
			"order" => $directoryOrder,
			"filter" => $directoryFilter
		);
		$propEnums = $entityDataClass::getList($entityGetList);
		while($oneEnum = $propEnums->fetch()) {
			$values[$tableName][$oneEnum["UF_XML_ID"]]["NAME"] = $oneEnum["UF_NAME"];
			if($oneEnum["UF_FILE"] > 0) {
				$arTmp = CFile::GetFileArray($oneEnum["UF_FILE"]);
				if(is_array($arTmp))
					$values[$tableName][$oneEnum["UF_XML_ID"]]["FILE"] = $arTmp["SRC"];
				unset($arTmp);
			}
			$values[$tableName][$oneEnum["UF_XML_ID"]]["CODE"] = $oneEnum["UF_CODE"];
		}
		unset($oneEnum, $propEnums);
	}
	unset($tableName, $ids);
	
	if(!empty($values)) {
		foreach($arResult["ITEMS"] as &$arItem) {
			foreach($arItem["DISPLAY_PROPERTIES"] as $arProp) {
				if($arProp["CODE"] == "COLORS" && !empty($arProp["VALUE"])) {
					if(!is_array($arProp["VALUE"])) {
						if(array_key_exists($arProp["VALUE"], $values[$arProp["USER_TYPE_SETTINGS"]["TABLE_NAME"]]))
							$arItem[$arProp["CODE"]][] = $values[$arProp["USER_TYPE_SETTINGS"]["TABLE_NAME"]][$arProp["VALUE"]];
					} else {
						foreach($arProp["VALUE"] as $val) {
							if(array_key_exists($val, $values[$arProp["USER_TYPE_SETTINGS"]["TABLE_NAME"]]))
								$arItem[$arProp["CODE"]][] = $values[$arProp["USER_TYPE_SETTINGS"]["TABLE_NAME"]][$val];
						}
						unset($val);
					}
				}
			}
			unset($arProp);
		}
		unset($arItem);
	}
	unset($values);
}
unset($colorIds);

//MIN_PRICE//
if($arParams["SHOW_MIN_PRICE"] != "N") {
	foreach($arResult["ITEMS"] as $arItem) {
		$ids[] = $arItem["ID"];
	}
	unset($arItem);

	if(!empty($ids) && CModule::IncludeModule("catalog")) {
		$arConvertParams = array();
		if("Y" == $arParams["CATALOG_CONVERT_CURRENCY"]) {
			if(!CModule::IncludeModule("currency")) {
				$arParams["CATALOG_CONVERT_CURRENCY"] = "N";
				$arParams["CATALOG_CURRENCY_ID"] = "";
			} else {
				$arCurrencyInfo = CCurrency::GetByID($arParams["CATALOG_CURRENCY_ID"]);
				if(!(is_array($arCurrencyInfo) && !empty($arCurrencyInfo))) {
					$arParams["CATALOG_CONVERT_CURRENCY"] = "N";
					$arParams["CATALOG_CURRENCY_ID"] = "";
				} else {
					$arParams["CATALOG_CURRENCY_ID"] = $arCurrencyInfo["CURRENCY"];
					$arConvertParams["CURRENCY_ID"] = $arCurrencyInfo["CURRENCY"];
				}
			}
		}

		if(is_array($arParams["CATALOG_PRICE_CODE"]))
			$arr["PRICES"] = CIBlockPriceTools::GetCatalogPrices(0, $arParams["CATALOG_PRICE_CODE"]);
		else
			$arr["PRICES"] = array();

		$arSelect = array("ID", "IBLOCK_ID", "PROPERTY_COLLECTION");
		
		$arFilter = array(
			"ACTIVE" => "Y",
			"IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"],
			"SECTION_GLOBAL_ACTIVE" => "Y",
			"PROPERTY_COLLECTION" => $ids
		);

		foreach($arr["PRICES"] as $value) {
			$arSelect[] = $value["SELECT"];
			$arFilter["CATALOG_SHOP_QUANTITY_".$value["ID"]] = 1;
		}
		unset($value);
		
		$rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
		while($arElement = $rsElements->GetNext()) {
			$arProducts[$arElement["ID"]] = $arElement;
		}
		unset($arElement, $rsElements);

		if(!empty($arProducts)) {
			$offersExist = CCatalogSKU::getExistOffers(array_keys($arProducts));
			foreach($arProducts as $arElement) {
				if(!$offersExist[$arElement["ID"]]) {			
					$arElement["PRICES"] = CIBlockPriceTools::GetItemPrices($arElement["IBLOCK_ID"], $arr["PRICES"], $arElement, $arParams["CATALOG_PRICE_VAT_INCLUDE"], $arConvertParams);
					foreach($arElement["PRICES"] as $code => $arPrice) {
						if($arPrice["MIN_PRICE"] == "Y" && $arPrice["DISCOUNT_VALUE"] > 0) {
							$arCollections[$arElement["PROPERTY_COLLECTION_VALUE"]][] = array(
								"DISCOUNT_VALUE" => $arPrice["DISCOUNT_VALUE"],
								"PRINT_DISCOUNT_VALUE" => $arPrice["PRINT_DISCOUNT_VALUE"]
							);
						}
					}
					unset($code, $arPrice);
				} else {
					$arOffers = CIBlockPriceTools::GetOffersArray(
						$arElement["IBLOCK_ID"],
						$arElement["ID"],
						array("SORT" => "ASC"),
						array(),
						array(),
						0,
						$arr["PRICES"],
						$arParams["CATALOG_PRICE_VAT_INCLUDE"],
						$arConvertParams
					);
					foreach($arOffers as $arOffer) {
						foreach($arOffer["PRICES"] as $code => $arPrice) {
							if($arPrice["MIN_PRICE"] == "Y" && $arPrice["DISCOUNT_VALUE"] > 0) {
								$arCollections[$arElement["PROPERTY_COLLECTION_VALUE"]][] = array(
									"DISCOUNT_VALUE" => $arPrice["DISCOUNT_VALUE"],
									"PRINT_DISCOUNT_VALUE" => $arPrice["PRINT_DISCOUNT_VALUE"]
								);
							}
						}
						unset($code, $arPrice);
					}
					unset($arOffer);
				}
			}
			unset($arElement);
		}
		unset($arProducts);

		if(!empty($arCollections)) {
			foreach($arCollections as &$arCollection) {
				Bitrix\Main\Type\Collection::sortByColumn($arCollection, array("DISCOUNT_VALUE" => SORT_NUMERIC));
			}
			unset($arCollection);
		
			foreach($arResult["ITEMS"] as &$arItem) {
				if(array_key_exists($arItem["ID"], $arCollections))
					$arItem["MIN_PRICE"] = $arCollections[$arItem["ID"]][0]["PRINT_DISCOUNT_VALUE"];
			}
			unset($arItem);
		}
		unset($arCollections);
	}
	unset($ids);
}