<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$selectedBrandId = !empty($GLOBALS["arCurrentBrandCollection"]["BRAND_ID"]) ? (int)$GLOBALS["arCurrentBrandCollection"]["BRAND_ID"] : 0;

foreach($arResult["DISPLAY_PROPERTIES"] as &$arProp) {
	//BRAND//
	if($arProp["CODE"] == "BRAND" && !empty($arProp["VALUE"])) {
		$brandValues = is_array($arProp["VALUE"]) ? $arProp["VALUE"] : array($arProp["VALUE"]);
		$brandValues = array_map("intval", $brandValues);

		if($selectedBrandId <= 0 || !in_array($selectedBrandId, $brandValues))
			$selectedBrandId = !empty($brandValues) ? (int)reset($brandValues) : 0;

		$rsElement = CIBlockElement::GetList(array(), array("ID" => $selectedBrandId > 0 ? $selectedBrandId : $brandValues, "IBLOCK_ID" => $arProp["LINK_IBLOCK_ID"]), false, false, array("ID", "IBLOCK_ID", "NAME"));
		while($obElement = $rsElement->GetNextElement()) {
			$arElement = $obElement->GetFields();
			$arElement["PROPERTIES"] = $obElement->GetProperties();
			
			$arResult["BRAND_ID"] = $arElement["ID"];
			$arResult["BRAND"]["NAME"] = $arElement["NAME"];
			
			foreach($arElement["PROPERTIES"] as $arBrandProp) {
				if($arBrandProp["CODE"] == "COUNTRY" && !empty($arBrandProp["VALUE"])) {
					$rsCountry = CIBlockElement::GetList(array(), array("ID" => $arBrandProp["VALUE"], "IBLOCK_ID" => $arBrandProp["LINK_IBLOCK_ID"]), false, false, array("ID", "IBLOCK_ID", "NAME"));
					if($arCountry = $rsCountry->GetNext()) {
						$arResult["BRAND"]["COUNTRY"] = $arCountry["NAME"];
					}
					unset($arCountry, $rsCountry);
				}
			}
			unset($arBrandProp);
		}
		unset($arElement, $obElement, $rsElement);
	//MARKERS//
	} elseif($arProp["CODE"] == "MARKER" && !empty($arProp["VALUE"])) {
		$rsElement = CIBlockElement::GetList(array(), array("ID" => $arProp["VALUE"], "IBLOCK_ID" => $arProp["LINK_IBLOCK_ID"], "ACTIVE" => "Y"), false, false, array("ID", "IBLOCK_ID", "NAME", "SORT"));	
		while($obElement = $rsElement->GetNextElement()) {
			$arElement = $obElement->GetFields();
			$arElement["PROPERTIES"] = $obElement->GetProperties();

			$arResult["MARKER"][] = array(
				"NAME" => $arElement["NAME"],
				"SORT" => $arElement["SORT"],
				"BACKGROUND_1" => $arElement["PROPERTIES"]["BACKGROUND_1"]["VALUE"],
				"BACKGROUND_2" => $arElement["PROPERTIES"]["BACKGROUND_2"]["VALUE"],
				"ICON" => $arElement["PROPERTIES"]["ICON"]["VALUE"],
				"FONT_SIZE" => $arElement["PROPERTIES"]["FONT_SIZE"]["VALUE_XML_ID"]
			);
		}
		unset($arElement, $obElement, $rsElement);

		if(!empty($arResult["MARKER"]))
			Bitrix\Main\Type\Collection::sortByColumn($arResult["MARKER"], array("SORT" => SORT_NUMERIC, "NAME" => SORT_ASC));
	//COLORS//
	} elseif($arProp["CODE"] == "COLORS" && !empty($arProp["VALUE"])) {
		if(CModule::IncludeModule("highloadblock")) {
			$hlblock = Bitrix\Highloadblock\HighloadBlockTable::getList(array("filter" => array("TABLE_NAME" => $arProp["USER_TYPE_SETTINGS"]["TABLE_NAME"])))->fetch();
			if(empty($hlblock))
				continue;

			$entity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);

			$entityFields = $entity->getFields();
			if(!array_key_exists("UF_CODE", $entityFields))
				continue;

			$entityDataClass = $entity->getDataClass();

			$directorySelect = array("ID", "UF_NAME", "UF_FILE", "UF_CODE", "UF_XML_ID");
			$directoryOrder = array();
			$directoryFilter = array("UF_XML_ID" => $arProp["VALUE"]);

			$entityGetList = array(
				"select" => $directorySelect,
				"order" => $directoryOrder,
				"filter" => $directoryFilter
			);
			$propEnums = $entityDataClass::getList($entityGetList);
			while($oneEnum = $propEnums->fetch()) {
				$arResult["COLORS"][$oneEnum["UF_XML_ID"]] = array(
					"NAME" => $oneEnum["UF_NAME"],
					"CODE" => $oneEnum["UF_CODE"]
				);

				if($oneEnum["UF_FILE"] > 0) {
					$arTmp = CFile::GetFileArray($oneEnum["UF_FILE"]);
					if(is_array($arTmp))
						$arResult["COLORS"][$oneEnum["UF_XML_ID"]]["FILE"] = $arTmp["SRC"];
					unset($arTmp);
				}
			}
		}
	//MORE_PHOTO//
	} elseif($arProp["CODE"] == "MORE_PHOTO" && !empty($arProp["FILE_VALUE"])) {		
		$ibFields = CIBlock::GetArrayByID($arResult["IBLOCK_ID"], "FIELDS");
		$ibFieldsDetailPic = $ibFields["DETAIL_PICTURE"]["DEFAULT_VALUE"];
		if(isset($arProp["FILE_VALUE"]["ID"])) {
			if($ibFieldsDetailPic["SCALE"] == "Y") {
				$arFileTmp = CFile::ResizeImageGet(
					$arProp["FILE_VALUE"],
					array(
						"width" => !empty($ibFieldsDetailPic["WIDTH"]) ? $ibFieldsDetailPic["WIDTH"] : 10000,
						"height" => !empty($ibFieldsDetailPic["HEIGHT"]) ? $ibFieldsDetailPic["HEIGHT"] : 10000
					),
					BX_RESIZE_IMAGE_PROPORTIONAL,
					true
				);
				$arResult["MORE_PHOTO"][] = array(
					"SRC" => $arFileTmp["src"],
					"WIDTH" => $arFileTmp["width"],
					"HEIGHT" => $arFileTmp["height"]
				);
				unset($arFileTmp);
			} else {
				$arResult["MORE_PHOTO"][] = $arProp["FILE_VALUE"];
			}
		} else {
			foreach($arProp["FILE_VALUE"] as $val) {		
				if($ibFieldsDetailPic["SCALE"] == "Y") {
					$arFileTmp = CFile::ResizeImageGet(
						$val,
						array(
							"width" => !empty($ibFieldsDetailPic["WIDTH"]) ? $ibFieldsDetailPic["WIDTH"] : 10000,
							"height" => !empty($ibFieldsDetailPic["HEIGHT"]) ? $ibFieldsDetailPic["HEIGHT"] : 10000
						),
						BX_RESIZE_IMAGE_PROPORTIONAL,
						true
					);
					$arResult["MORE_PHOTO"][] = array(
						"SRC" => $arFileTmp["src"],
						"WIDTH" => $arFileTmp["width"],
						"HEIGHT" => $arFileTmp["height"]
					);
					unset($arFileTmp);
				} else {
					$arResult["MORE_PHOTO"][] = $val;
				}
			}
			unset($val);
		}
		unset($ibFieldsDetailPic, $ibFields);
	}
}
unset($arProp);

if($selectedBrandId <= 0 && !empty($arResult["ID"]) && !empty($arParams["IBLOCK_ID"]) && Bitrix\Main\Loader::includeModule("iblock")) {
	$brandValues = array();
	$rsBrandProp = CIBlockElement::GetProperty($arParams["IBLOCK_ID"], $arResult["ID"], array(), array("CODE" => "BRAND"));
	while($arBrandProp = $rsBrandProp->Fetch()) {
		if(!empty($arBrandProp["VALUE"]))
			$brandValues[] = (int)$arBrandProp["VALUE"];
	}
	unset($arBrandProp, $rsBrandProp);

	if(!empty($brandValues))
		$selectedBrandId = (int)reset($brandValues);
	unset($brandValues);
}

if($selectedBrandId > 0 && empty($arResult["BRAND_ID"]))
	$arResult["BRAND_ID"] = $selectedBrandId;

$productBrandId = !empty($arResult["BRAND_ID"]) ? (int)$arResult["BRAND_ID"] : $selectedBrandId;

//MIN_PRICE//
if($arParams["SHOW_MIN_PRICE"] != "N" && CModule::IncludeModule("catalog")) {
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

	$arSelect = array("ID", "IBLOCK_ID");
		
	$arFilter = array(
		"ACTIVE" => "Y",
		"IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"],
		"SECTION_GLOBAL_ACTIVE" => "Y",
		"PROPERTY_COLLECTION" => $arResult["ID"]
	);

	if($productBrandId > 0)
		$arFilter["PROPERTY_BRAND"] = $productBrandId;

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
						$arCollections[] = array(
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
							$arCollections[] = array(
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
		Bitrix\Main\Type\Collection::sortByColumn($arCollections, array("DISCOUNT_VALUE" => SORT_NUMERIC));
		
		$arResult["MIN_PRICE"] = $arCollections[0]["PRINT_DISCOUNT_VALUE"];
	}
	unset($arCollections);
}

//SECTIONS//
//PRODUCTS_IDS//
$arResult["SECTIONS_IDS"] = array();
$arResult["PRODUCTS_IDS"] = array();
$arResult["SECTIONS"] = array();

$arProductsFilter = array("ACTIVE" => "Y", "IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"], "SECTION_GLOBAL_ACTIVE" => "Y", "PROPERTY_COLLECTION" => $arResult["ID"]);
if($productBrandId > 0)
	$arProductsFilter["PROPERTY_BRAND"] = $productBrandId;

$rsElements = CIBlockElement::GetList(array(), $arProductsFilter, false, false, array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID"));	
while($arElement = $rsElements->GetNext()) {	
	if(!empty($arElement["IBLOCK_SECTION_ID"]))
		$arResult["SECTIONS_IDS"][] = $arElement["IBLOCK_SECTION_ID"];
	$arResult["PRODUCTS_IDS"][] = $arElement["ID"];
}
unset($arElement, $rsElements);
unset($arProductsFilter);

if(!empty($arResult["SECTIONS_IDS"])) {
	$arCount = array_count_values($arResult["SECTIONS_IDS"]);
	$rsSections = CIBlockSection::GetList(array("NAME" => "ASC"), array("ID" => array_unique($arResult["SECTIONS_IDS"])), false, array("ID", "IBLOCK_ID", "NAME"));	
	while($arSection = $rsSections->GetNext()) {
		$arResult["SECTIONS"][] = array(
			"ID" => $arSection["ID"],
			"NAME" => $arSection["NAME"],
			"COUNT" => $arCount[$arSection["ID"]]
		);
	}
}
