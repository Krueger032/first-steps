<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$arDays = array("MON", "TUE", "WED", "THU", "FRI", "SAT", "SUN");

foreach($arResult["DISPLAY_PROPERTIES"] as $arProp) {
	//ADDRESS//
	if($arProp["CODE"] == "ADDRESS" && !empty($arProp["VALUE"])) {
		$arResult[$arProp["CODE"]] = $arProp["VALUE"];
	//MAP//
	} elseif($arProp["CODE"] == "MAP" && !empty($arProp["VALUE"])) {
		$arResult[$arProp["CODE"]] = $arProp["VALUE"];		
	//TIMEZONE//
	} elseif($arProp["CODE"] == "TIMEZONE" && !empty($arProp["VALUE"])) {
		$rsElement = CIBlockElement::GetList(array(), array("ID" => $arProp["VALUE"], "IBLOCK_ID" => $arProp["LINK_IBLOCK_ID"]), false, false, array("ID", "IBLOCK_ID"));	
		while($obElement = $rsElement->GetNextElement()) {
			$arElement = $obElement->GetFields();
			$arElement["PROPERTIES"] = $obElement->GetProperties();

			$arResult[$arProp["CODE"]] = $arElement["PROPERTIES"]["OFFSET"]["VALUE"];
		}
		unset($arElement, $obElement, $rsElement);
	//WORKING_HOURS//
	} elseif(in_array($arProp["CODE"], $arDays) && !empty($arProp["VALUE"])) {
		$workingHoursIds[] = $arProp["VALUE"];
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
					"HEIGHT" => $arFileTmp["height"],
					"DESCRIPTION" => $arProp["FILE_VALUE"]["DESCRIPTION"]
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
						"HEIGHT" => $arFileTmp["height"],
						"DESCRIPTION" => $val["DESCRIPTION"]
					);
					unset($arFileTmp);
				} else {
					$arResult["MORE_PHOTO"][] = $val;
				}
			}
			unset($val);
		}
		unset($ibFieldsDetailPic, $ibFields);
	//TOUR_3D//	
	} elseif($arProp["CODE"] == "TOUR_3D" && !empty($arProp["VALUE"])) {
		$arResult[$arProp["CODE"]] = array(
			"NAME" => $arProp["NAME"],
			"VALUE" => $arProp["DISPLAY_VALUE"]
		);
	//AFFILIATES//
	} elseif($arProp["CODE"] == "AFFILIATES" && !empty($arProp["VALUE"])) {
		$arResult[$arProp["CODE"]]["NAME"] = $arProp["NAME"];
		$rsElements = CIBlockElement::GetList(array(), array("ID" => $arProp["VALUE"], "IBLOCK_ID" => $arProp["LINK_IBLOCK_ID"]), false, false, array("ID", "IBLOCK_ID", "NAME", "PREVIEW_PICTURE", "DETAIL_PAGE_URL"));
		while($obElement = $rsElements->GetNextElement()) {
			$arElement = $obElement->GetFields();

			//AFFILIATE_PREVIEW_PICTURE//
			if($arElement["PREVIEW_PICTURE"] > 0)
				$arElement["PREVIEW_PICTURE"] = CFile::GetFileArray($arElement["PREVIEW_PICTURE"]);

			//AFFILIATE_PROPERTIES//
			$arElement["PROPERTIES"] = $obElement->GetProperties();

			$arResult[$arProp["CODE"]]["VALUE"][$arElement["ID"]] = array(
				"ID" => $arElement["ID"],
				"NAME" => $arElement["NAME"],
				"PREVIEW_PICTURE_SRC" => is_array($arElement["PREVIEW_PICTURE"]) ? $arElement["PREVIEW_PICTURE"]["SRC"] : false,
				"DETAIL_PAGE_URL" => $arElement["DETAIL_PAGE_URL"],
				"ADDRESS" => $arElement["PROPERTIES"]["ADDRESS"]["VALUE"],
				"MAP" => $arElement["PROPERTIES"]["MAP"]["VALUE"],
				"HIDE_IN_OBJECTS_LIST" => !$arElement["PROPERTIES"]["HIDE_IN_OBJECTS_LIST"]["VALUE"] ? false : true
			);
		}
		unset($arElement, $obElement, $rsElements);
	//PHONE_WHATSAPP_VIBER_TELEGRAM_INSTAGRAM_EMAIL_SKYPE_LINKS//
	} elseif(($arProp["CODE"] == "PHONE" || $arProp["CODE"] == "WHATSAPP" || $arProp["CODE"] == "VIBER" || $arProp["CODE"] == "TELEGRAM" || $arProp["CODE"] == "INSTAGRAM" || $arProp["CODE"] == "MAX" || $arProp["CODE"] == "EMAIL" || $arProp["CODE"] == "SKYPE" || $arProp["CODE"] == "LINKS") && !empty($arProp["VALUE"])) {
		$arResult[$arProp["CODE"]] = array(
			"VALUE" => $arProp["VALUE"],
			"DESCRIPTION" => $arProp["DESCRIPTION"]
		);
	//PHONE_SMS_EMAIL_EMAIL//
	} elseif(($arProp["CODE"] == "PHONE_SMS" || $arProp["CODE"] == "EMAIL_EMAIL") && !empty($arProp["VALUE"])) {
		$arResult[$arProp["CODE"]] = true;
	//DELIVERY_PAYMENT_METHODS//
	} elseif(($arProp["CODE"] == "DELIVERY_METHODS" || $arProp["CODE"] == "PAYMENT_METHODS") && !empty($arProp["VALUE"])) {
		$arResult[$arProp["CODE"]] = $arProp["VALUE"];
	}
}
unset($arProp);

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
		foreach($arResult["DISPLAY_PROPERTIES"] as $arProp) {
			if(in_array($arProp["CODE"], $arDays) && !empty($arProp["VALUE"])) {
				if(array_key_exists($arProp["VALUE"], $arWorkingHours)) {
					$arResult["WORKING_HOURS"][$arProp["CODE"]] = $arWorkingHours[$arProp["VALUE"]];
					$arResult["WORKING_HOURS"][$arProp["CODE"]]["NAME"] = $arProp["NAME"];
				}
			}
		}
		unset($arProp);
	}
	unset($arWorkingHours);
}
unset($workingHoursIds);

//PROMOTIONS_IDS//
if($arParams["SHOW_PROMOTIONS"] != "N" && intval($arParams["PROMOTIONS_IBLOCK_ID"]) > 0) {
	$rsElements = CIBlockElement::GetList(array(), array("ACTIVE" => "Y", "ACTIVE_DATE" => "Y", "IBLOCK_ID" => $arParams["PROMOTIONS_IBLOCK_ID"], "PROPERTY_OBJECT" => $arResult["ID"]), false, false, array("ID", "IBLOCK_ID"));	
	while($arElement = $rsElements->GetNext()) {		
		$arResult["PROMOTIONS_IDS"][$arElement["ID"]] = $arElement["ID"];
	}
	unset($arElement, $rsElements);

	if(!empty($arResult["PROMOTIONS_IDS"])) {
		$rsGroups = CIBlockElement::GetElementGroups($arResult["PROMOTIONS_IDS"], true, array("ID", "GLOBAL_ACTIVE", "IBLOCK_ELEMENT_ID"));
		while($arGroup = $rsGroups->GetNext()) {
			if($arGroup["GLOBAL_ACTIVE"] != "Y")
				unset($arResult["PROMOTIONS_IDS"][$arGroup["IBLOCK_ELEMENT_ID"]]);
		}
		unset($arGroup, $rsGroups);
	}
}

//SECTIONS//
//PRODUCTS_IDS//
if(intval($arParams["CATALOG_IBLOCK_ID"]) > 0) {
	if(Bitrix\Main\Loader::includeModule("catalog")) {
		$mxResult = CCatalogSKU::GetInfoByProductIBlock($arParams["CATALOG_IBLOCK_ID"]);
		$offersIblockId = is_array($mxResult) ? $mxResult["IBLOCK_ID"] : false;
		if(intval($offersIblockId) > 0) {
			$rsElements = CIBlockElement::GetList(array(), array("ACTIVE" => "Y", "IBLOCK_ID" => $offersIblockId, "PROPERTY_OBJECT" => $arResult["ID"]), false, false, array("ID", "IBLOCK_ID"));	
			while($arElement = $rsElements->GetNext()) {
				$offersIds[] = $arElement["ID"];
			}
			unset($arElement, $rsElements);

			if(!empty($offersIds)) {
				$productList = CCatalogSku::getProductList($offersIds);
				if(!empty($productList)) {
					foreach($productList as $offerId => $offerInfo) {
						$productsIds[] = $offerInfo["ID"];
					}
					unset($offerInfo, $offerId);
				}
				unset($productList);
			}
			unset($offersIds);
		}
		unset($offersIblockId, $mxResult);
	}

	$arFilter = array(
		"ACTIVE" => "Y",
		"IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"],
		"SECTION_GLOBAL_ACTIVE" => "Y"
	);
	
	if(!empty($productsIds)) {
		$arFilter[] = array("LOGIC" => "OR", array("PROPERTY_OBJECT" => $arResult["ID"]), array("ID" => array_unique($productsIds)));
	} else {
		$arFilter["PROPERTY_OBJECT"] = $arResult["ID"];
	}
	unset($productsIds);
	
	$rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID"));	
	while($arElement = $rsElements->GetNext()) {	
		if(!empty($arElement["IBLOCK_SECTION_ID"]))
			$arResult["SECTIONS_IDS"][] = $arElement["IBLOCK_SECTION_ID"];
		$arResult["PRODUCTS_IDS"][] = $arElement["ID"];
	}
	unset($arElement, $rsElements, $arFilter);
	
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
		unset($arSection, $rsSections, $arCount);
	}
}

//RATING_REVIEWS_COUNT//
if($arParams["USE_REVIEW"] != "N" && intval($arParams["REVIEWS_IBLOCK_ID"]) > 0) {
	$ratingSum = $reviewsCount = 0;
	$rsElements = CIBlockElement::GetList(array(), array("ACTIVE" => "Y", "IBLOCK_ID" => $arParams["REVIEWS_IBLOCK_ID"], "PROPERTY_OBJECT_ID" => $arResult["ID"]), false, false, array("ID", "IBLOCK_ID"));
	while($obElement = $rsElements->GetNextElement()) {
		$arElement = $obElement->GetFields();
		$arProps = $obElement->GetProperties();
		
		$ratingSum += $arProps["RATING"]["VALUE_XML_ID"];
		
		$reviewsCount++;
	}
	unset($arProps, $arElement, $obElement, $rsElements);

	$reviewsDeclension = new Bitrix\Main\Grid\Declension(Loc::getMessage("OBJECTS_ITEM_DETAIL_REVIEW"), Loc::getMessage("OBJECTS_ITEM_DETAIL_REVIEWS_1"), Loc::getMessage("OBJECTS_ITEM_DETAIL_REVIEWS_2"));
	
	$arResult["RATING_VALUE"] = $reviewsCount > 0 ? sprintf("%.1f", round($ratingSum / $reviewsCount, 1)) : 0;
	$arResult["REVIEWS_COUNT"] = $reviewsCount;
	$arResult["REVIEWS_DECLENSION"] = $reviewsDeclension->get($reviewsCount);

	unset($reviewsDeclension, $reviewsCount, $ratingSum);
}