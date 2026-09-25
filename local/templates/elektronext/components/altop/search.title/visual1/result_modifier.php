<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$boolCatalog = CModule::IncludeModule("catalog");

foreach($arResult["CATEGORIES"] as $arCategory) {
	foreach($arCategory["ITEMS"] as $arItem) {
		if(isset($arItem["ITEM_ID"]) && $arItem["MODULE_ID"] == "iblock") {
			if(substr($arItem["ITEM_ID"], 0, 1) !== "S") {
				if($boolCatalog && (is_array(CCatalogSKU::GetInfoByProductIBlock($arItem["PARAM2"])) || is_array(CCatalogSKU::GetInfoByOfferIBlock($arItem["PARAM2"]))))
					$arResult["SEARCH"]["PRODUCTS"][$arItem["PARAM2"]]["ITEMS"][$arItem["ITEM_ID"]] = $arItem;
				else
					$arResult["SEARCH"]["ELEMENTS"][$arItem["PARAM2"]]["ITEMS"][$arItem["ITEM_ID"]] = $arItem;
			} else {
				$sectionId = substr($arItem["ITEM_ID"], 1);
				$arResult["SEARCH"]["SECTIONS"][$arItem["PARAM2"]]["ITEMS"][$sectionId] = $arItem;
				unset($sectionId);
			}
		}		
	}
	unset($arItem);
}
unset($arCategory);

$arResult["ELEMENTS"] = array();

//SECTIONS//
if(!empty($arResult["SEARCH"]["SECTIONS"]) && CModule::IncludeModule("iblock")) {
	foreach($arResult["SEARCH"]["SECTIONS"] as $iblockId => $arSearchSection) {		
		$rsSections = CIBlockSection::GetList(array(), array("GLOBAL_ACTIVE" => "Y", "ID" => array_keys($arSearchSection["ITEMS"]), "IBLOCK_ID" => $iblockId), false, array("ID", "IBLOCK_ID", "PICTURE", "UF_ICON"));
		while($arSection = $rsSections->GetNext()) {
			$arResult["ELEMENTS"][$arSection["ID"]] = $arResult["SEARCH"]["SECTIONS"][$arSection["IBLOCK_ID"]]["ITEMS"][$arSection["ID"]];
			if(!empty($arSection["UF_ICON"]))
				$arResult["ELEMENTS"][$arSection["ID"]]["UF_ICON"] = $arSection["UF_ICON"];
			elseif($arSection["PICTURE"] > 0)
				$arResult["ELEMENTS"][$arSection["ID"]]["PREVIEW_PICTURE"] = CFile::ResizeImageGet($arSection["PICTURE"], array("width" => 50, "height" => 50), BX_RESIZE_IMAGE_PROPORTIONAL, true);			
		}
		unset($item, $arSection, $rsSections);
	}
	unset($iblockId, $arSearchSection);
}
unset($arResult["SEARCH"]["SECTIONS"]);

//ELEMENTS//
if(!empty($arResult["SEARCH"]["ELEMENTS"]) && CModule::IncludeModule("iblock")) {
	foreach($arResult["SEARCH"]["ELEMENTS"] as $iblockId => $arSearchElement) {	
		$rsElements = CIBlockElement::GetList(array(), array("ID" => array_keys($arSearchElement["ITEMS"]), "ACTIVE" => "Y", "IBLOCK_ID" => $iblockId), false, false, array("ID", "CODE", "IBLOCK_ID", "PREVIEW_PICTURE", "DETAIL_PICTURE"));
		while($obElement = $rsElements->GetNextElement()) {
			$arElement = $obElement->GetFields();
			$arElement["PROPERTIES"] = $obElement->GetProperties();

			$arResult["ELEMENTS"][$arElement["ID"]] = $arResult["SEARCH"]["ELEMENTS"][$arElement["IBLOCK_ID"]]["ITEMS"][$arElement["ID"]];
			
			if($arElement["PREVIEW_PICTURE"] > 0)
				$arResult["ELEMENTS"][$arElement["ID"]]["PREVIEW_PICTURE"] = CFile::ResizeImageGet($arElement["PREVIEW_PICTURE"], array("width" => 50, "height" => 50), BX_RESIZE_IMAGE_PROPORTIONAL, true);
			elseif($arElement["DETAIL_PICTURE"] > 0)
				$arResult["ELEMENTS"][$arElement["ID"]]["PREVIEW_PICTURE"] = CFile::ResizeImageGet($arElement["DETAIL_PICTURE"], array("width" => 50, "height" => 50), BX_RESIZE_IMAGE_PROPORTIONAL, true);

			if(!empty($arElement["PROPERTIES"]["BRAND"]["VALUE"]))
				$arResult["COLLECTIONS"][$arElement["ID"]] = array(					
					"CODE" => $arElement["CODE"],
					"BRAND" => $arElement["PROPERTIES"]["BRAND"]["VALUE"]
				);
		}
		unset($arElement, $rsElements);
	}
	unset($iblockId, $arSearchElement);
}
unset($arResult["SEARCH"]["ELEMENTS"]);

//COLLECTIONS//
if(!empty($arResult["COLLECTIONS"])) {
	foreach($arResult["COLLECTIONS"] as $arElement) {
		$brandIds[] = $arElement["BRAND"];
	}
	unset($arElement);
	
	if(!empty($brandIds)) {
		$rsElements = CIBlockElement::GetList(array(), array("ID" => array_unique($brandIds)), false, false, array("ID", "IBLOCK_ID", "DETAIL_PAGE_URL"));
		while($arElement = $rsElements->GetNext()) {
			$arBrands[$arElement["ID"]] = $arElement["DETAIL_PAGE_URL"];
		}
		unset($arElement, $rsElements);
	}
	unset($brandIds);

	if(!empty($arBrands)) {
		foreach($arResult["COLLECTIONS"] as $id => $arElement) {
			if($arBrands[$arElement["BRAND"]])
				$arResult["ELEMENTS"][$id]["URL"] = $arBrands[$arElement["BRAND"]].$arElement["CODE"]."/";
		}
		unset($id, $arElement);
	}
	unset($arBrands);
}
unset($arResult["COLLECTIONS"]);

//PRODUCTS//
if(!empty($arResult["SEARCH"]["PRODUCTS"]) && CModule::IncludeModule("iblock")) {
	$arParams["PRICE_VAT_INCLUDE"] = $arParams["PRICE_VAT_INCLUDE"] !== "N";

	$arConvertParams = array();
	if("Y" == $arParams["CONVERT_CURRENCY"]) {
		if(!CModule::IncludeModule("currency")) {
			$arParams["CONVERT_CURRENCY"] = "N";
			$arParams["CURRENCY_ID"] = "";
		} else {
			$arCurrencyInfo = CCurrency::GetByID($arParams["CURRENCY_ID"]);
			if(!(is_array($arCurrencyInfo) && !empty($arCurrencyInfo))) {
				$arParams["CONVERT_CURRENCY"] = "N";
				$arParams["CURRENCY_ID"] = "";
			} else {
				$arParams["CURRENCY_ID"] = $arCurrencyInfo["CURRENCY"];
				$arConvertParams["CURRENCY_ID"] = $arCurrencyInfo["CURRENCY"];
			}
		}
	}

	if(is_array($arParams["PRICE_CODE"]))
		$arResult["PRICES"] = CIBlockPriceTools::GetCatalogPrices(0, $arParams["PRICE_CODE"]);
	else
		$arResult["PRICES"] = array();
	
	$arSelect = array("ID", "IBLOCK_ID", "PREVIEW_PICTURE", "DETAIL_PICTURE", "CATALOG_TYPE");
	$arFilter = array("ACTIVE" => "Y");
	foreach($arResult["PRICES"] as $value) {
		$arSelect[] = $value["SELECT"];
		$arFilter["CATALOG_SHOP_QUANTITY_".$value["ID"]] = 1;
	}

	foreach($arResult["SEARCH"]["PRODUCTS"] as $iblockId => $arSearchProduct) {
		$arFilter["ID"] = array_keys($arSearchProduct["ITEMS"]);
		$arFilter["IBLOCK_ID"] = $iblockId;

		if($boolCatalog && is_array(CCatalogSKU::GetInfoByProductIBlock($iblockId)))
			$arFilter["SECTION_GLOBAL_ACTIVE"] = "Y";

		$rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
		while($obElement = $rsElements->GetNextElement()) {
			$arElement = $obElement->GetFields();
			$arProps = $obElement->GetProperties();
			
			$arResult["ELEMENTS"][$arElement["ID"]] = $arResult["SEARCH"]["PRODUCTS"][$arElement["IBLOCK_ID"]]["ITEMS"][$arElement["ID"]];

			if($arElement["PREVIEW_PICTURE"] > 0)
				$arResult["ELEMENTS"][$arElement["ID"]]["PREVIEW_PICTURE"] = CFile::ResizeImageGet($arElement["PREVIEW_PICTURE"], array("width" => 50, "height" => 50), BX_RESIZE_IMAGE_PROPORTIONAL, true);
			elseif($arElement["DETAIL_PICTURE"] > 0)
				$arResult["ELEMENTS"][$arElement["ID"]]["PREVIEW_PICTURE"] = CFile::ResizeImageGet($arElement["DETAIL_PICTURE"], array("width" => 50, "height" => 50), BX_RESIZE_IMAGE_PROPORTIONAL, true);

			$arResult["ELEMENTS"][$arElement["ID"]]["PROPERTIES"] = $arProps;
			
			$itemsList[$arElement["ID"]] = $arElement;

			if($arElement["CATALOG_TYPE"] == Bitrix\Catalog\ProductTable::TYPE_OFFER)
				$offerList[$arElement["ID"]] = $arElement["ID"];
		}
		unset($arProps, $arElement, $obElement, $rsElements);
	}
	unset($iblockId, $arSearchProduct);
	
	if(!empty($offerList)) {
		$parents = $boolCatalog ? CCatalogSku::getProductList($offerList) : false;
		if(!empty($parents) && is_array($parents)) {
			$offersMap = array();
			foreach($parents as $offerId => $parentData) {
				$offersMap[$parentData["ID"]][$offerId] = $offerId;
			}
			unset($offerId, $parentData);
			
			$rsElements = CIBlockElement::GetList(array(), array("ID" => array_keys($offersMap), "ACTIVE" => "Y"), false, false, array("ID", "IBLOCK_ID", "PREVIEW_PICTURE", "DETAIL_PICTURE"));
			while($obElement = $rsElements->GetNextElement()) {
				$arElement = $obElement->GetFields();
				$arProps = $obElement->GetProperties();
				
				foreach($offersMap[$arElement["ID"]] as $offerId) {
					unset($offerList[$offerId]);
					
					if($arResult["ELEMENTS"][$offerId]["PREVIEW_PICTURE"] === null) {
						if($arElement["PREVIEW_PICTURE"] > 0)
							$arResult["ELEMENTS"][$offerId]["PREVIEW_PICTURE"] = CFile::ResizeImageGet($arElement["PREVIEW_PICTURE"], array("width" => 50, "height" => 50), BX_RESIZE_IMAGE_PROPORTIONAL, true);
						elseif($arElement["DETAIL_PICTURE"] > 0)
							$arResult["ELEMENTS"][$offerId]["PREVIEW_PICTURE"] = CFile::ResizeImageGet($arElement["DETAIL_PICTURE"], array("width" => 50, "height" => 50), BX_RESIZE_IMAGE_PROPORTIONAL, true);
					}
					
					foreach($arResult["ELEMENTS"][$offerId]["PROPERTIES"] as $arProp) {
						if(($arProp["CODE"] == "ARTNUMBER" || $arProp["CODE"] == "OLD_PRICE") && empty($arProp["VALUE"])) {
							$arResult["ELEMENTS"][$offerId]["PROPERTIES"][$arProp["CODE"]]["VALUE"] = $arProps[$arProp["CODE"]]["VALUE"];
						} else {
							if($boolCatalog && is_array(CCatalogSKU::GetInfoByLinkProperty($arProp["ID"])))
								continue;
							
							if(($arProp["PROPERTY_TYPE"] == "L" || $arProp["PROPERTY_TYPE"] == "E" || ($arProp["PROPERTY_TYPE"] == "S" && $arProp["USER_TYPE"] == "directory" && CIBlockPriceTools::checkPropDirectory($arProp))) && !empty($arProp["VALUE"])) {
								$arResult["ELEMENTS"][$offerId]["DISPLAY_PROPERTIES"][$arProp["CODE"]] = CIBlockFormatProperties::GetDisplayValue($arResult["ELEMENTS"][$offerId], $arProp, "catalog_out");
							}
						}
					}
					unset($arProp);
				}
				unset($offerId);
			}
			unset($arProps, $arElement, $obElement, $rsElements, $offersMap);
		}
		unset($parents);

		if(!empty($offerList)) {
			foreach($offerList as $clearId)
				unset($arResult["ELEMENTS"][$clearId]);
			unset($clearId);
		}
	}
	unset($offerList);

	$offersExist = CCatalogSKU::getExistOffers(array_keys($itemsList));
	foreach($itemsList as $item) {
		if(!$offersExist[$item["ID"]]) {
			$priceList = CIBlockPriceTools::GetItemPrices($item["IBLOCK_ID"], $arResult["PRICES"], $item, $arParams["PRICE_VAT_INCLUDE"], $arConvertParams);

			if(empty($priceList))
				continue;

			$minimalPrice = CIBlockPriceTools::getMinPriceFromList($priceList);
			if(!empty($minimalPrice) && $minimalPrice["DISCOUNT_VALUE"] > 0) {
				if(!empty($arResult["ELEMENTS"][$item["ID"]]["PROPERTIES"]["OLD_PRICE"]["VALUE"]) && $minimalPrice["DISCOUNT_DIFF_PERCENT"] == 0) {
					$oldPrice = str_replace(",", ".", $arResult["ELEMENTS"][$item["ID"]]["PROPERTIES"]["OLD_PRICE"]["VALUE"]);
					$minimalPrice["VALUE"] = Bitrix\Catalog\Product\Price::roundPrice($minimalPrice["PRICE_ID"], $oldPrice, $minimalPrice["CURRENCY"]);
					$minimalPrice["PRINT_VALUE"] = CCurrencyLang::CurrencyFormat($minimalPrice["VALUE"], $minimalPrice["CURRENCY"], true);
					unset($oldPrice);
				}
				$arResult["ELEMENTS"][$item["ID"]]["MIN_PRICE"] = $minimalPrice;
				$arResult["ELEMENTS"][$item["ID"]]["MIN_PRICE"]["OFFERS"] = false;
			}
			unset($minimalPrice, $priceList);
		} else {
			$arOffers = CIBlockPriceTools::GetOffersArray(
				$item["IBLOCK_ID"],
				$item["ID"],
				array("SORT" => "ASC"),
				array(),
				array(),
				0,
				$arResult["PRICES"],
				$arParams["CATALOG_PRICE_VAT_INCLUDE"],
				$arConvertParams
			);
			foreach($arOffers as $arOffer) {
				$minimalPrice = CIBlockPriceTools::getMinPriceFromList($arOffer["PRICES"]);
				if(!empty($minimalPrice) && $minimalPrice["DISCOUNT_VALUE"] > 0) {
					if((!empty($arOffer["PROPERTIES"]["OLD_PRICE"]["VALUE"]) || !empty($arResult["ELEMENTS"][$item["ID"]]["PROPERTIES"]["OLD_PRICE"]["VALUE"])) && $minimalPrice["DISCOUNT_DIFF_PERCENT"] == 0) {
						$oldPrice = !empty($arOffer["PROPERTIES"]["OLD_PRICE"]["VALUE"]) ? str_replace(",", ".", $arOffer["PROPERTIES"]["OLD_PRICE"]["VALUE"]) : str_replace(",", ".", $arResult["ELEMENTS"][$item["ID"]]["PROPERTIES"]["OLD_PRICE"]["VALUE"]);
						$minimalPrice["VALUE"] = Bitrix\Catalog\Product\Price::roundPrice($minimalPrice["PRICE_ID"], $oldPrice, $minimalPrice["CURRENCY"]);
						$minimalPrice["PRINT_VALUE"] = CCurrencyLang::CurrencyFormat($minimalPrice["VALUE"], $minimalPrice["CURRENCY"], true);
						unset($oldPrice);
					}
					$priceList[] = $minimalPrice;
				}
				unset($minimalPrice);
			}
			unset($arOffer);

			if(!empty($priceList)) {
				Bitrix\Main\Type\Collection::sortByColumn($priceList, array("DISCOUNT_VALUE" => SORT_NUMERIC));
				$arResult["ELEMENTS"][$item["ID"]]["MIN_PRICE"] = $priceList[0];
				$arResult["ELEMENTS"][$item["ID"]]["MIN_PRICE"]["OFFERS"] = true;
			}
			unset($priceList);
		}
	}
	unset($item, $offersExist);
}
unset($arResult["SEARCH"]["PRODUCTS"]);