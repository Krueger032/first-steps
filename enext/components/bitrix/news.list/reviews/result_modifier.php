<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if(!empty($arResult["ITEMS"])) {
	foreach($arResult["ITEMS"] as &$arItem) {
		//FILES//
		if(!empty($arItem["DISPLAY_PROPERTIES"]["FILES"]["FILE_VALUE"])) {
			if(isset($arItem["DISPLAY_PROPERTIES"]["FILES"]["FILE_VALUE"]["ID"])) {
				$arFileTmp = CFile::ResizeImageGet($arItem["DISPLAY_PROPERTIES"]["FILES"]["FILE_VALUE"], array("width" => 120, "height" => 120), BX_RESIZE_IMAGE_PROPORTIONAL, true);
				$arItem["DISPLAY_PROPERTIES"]["FILES"]["PREVIEW"] = array(
					"SRC" => $arFileTmp["src"],
					"WIDTH" => $arFileTmp["width"],
					"HEIGHT" => $arFileTmp["height"],
				);
				unset($arFileTmp);
			} else {
				foreach($arItem["DISPLAY_PROPERTIES"]["FILES"]["FILE_VALUE"] as $val) {
					$arFileTmp = CFile::ResizeImageGet($val, array("width" => 120, "height" => 120), BX_RESIZE_IMAGE_PROPORTIONAL, true);
					$arItem["DISPLAY_PROPERTIES"]["FILES"]["PREVIEW"][] = array(
						"SRC" => $arFileTmp["src"],
						"WIDTH" => $arFileTmp["width"],
						"HEIGHT" => $arFileTmp["height"],
					);
					unset($arFileTmp);
				}
				unset($val);
			}
		}

		//USER_NAME//
		if(!empty($arItem["DISPLAY_PROPERTIES"]["USER_ID"]["VALUE"])) {
			$rsUser = CUser::GetByID($arItem["DISPLAY_PROPERTIES"]["USER_ID"]["VALUE"]);
			if($arUser = $rsUser->Fetch())
				$arItem["USER_NAME"] = $arUser["NAME"];
		}
		
		//DATE_PUBLISHED//
		if(!empty($arItem["ACTIVE_FROM"]))
			$arItem["DATE_PUBLISHED"] = CIBlockFormatProperties::DateFormat("Y-m-d", MakeTimeStamp($arItem["ACTIVE_FROM"], CSite::GetDateFormat()));
	}
	unset($arItem);
}

//ELEMENT_NAME//
$arResult["ELEMENT_NAME"] = "";
$productId = intval($GLOBALS[$arParams["FILTER_NAME"]]["PROPERTY_PRODUCT_ID"]);
$objectId = intval($GLOBALS[$arParams["FILTER_NAME"]]["PROPERTY_OBJECT_ID"]);
if($productId > 0 || $objectId > 0) {
	$rsElement = CIBlockElement::GetList(array(), array("ID" => $productId > 0 ? $productId : $objectId), false, false, array("ID", "IBLOCK_ID", "NAME"));
	if($arElement = $rsElement->GetNext()) {
		$arResult["ELEMENT_NAME"] = $arElement["NAME"];
	}
	unset($arElement, $rsElement);
}
unset($productId, $objectId);

//RATING_LIST//
$rsPropsEnum = CIBlockPropertyEnum::GetList(array("SORT" => "ASC"), array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "CODE" => "RATING"));
while($arPropEnum = $rsPropsEnum->GetNext()) {
	$arResult["RATING_LIST"][$arPropEnum["ID"]] = array(
		"XML_ID" => $arPropEnum["XML_ID"],
		"VALUE" => $arPropEnum["VALUE"],
		"ELEMENTS" => array()
	);
}
unset($arPropEnum, $rsPropsEnum);

//RATING//
$arFilter = array("ACTIVE" => "Y", "IBLOCK_ID" => $arParams["IBLOCK_ID"]);
if(!empty($GLOBALS[$arParams["FILTER_NAME"]]))
	$arFilter += $GLOBALS[$arParams["FILTER_NAME"]];

$ratingSum = $ratingRecommend = $reviewsCount = 0;	
$rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, array("ID", "IBLOCK_ID"));
while($obElement = $rsElements->GetNextElement()) {
	$arElement = $obElement->GetFields();
	$arProps = $obElement->GetProperties();
	
	$ratingSum += $arProps["RATING"]["VALUE_XML_ID"];		

	if($arProps["RECOMMEND"]["VALUE_XML_ID"] == "Y")
		$ratingRecommend++;

	if($arResult["RATING_LIST"][$arProps["RATING"]["VALUE_ENUM_ID"]]["XML_ID"] == $arProps["RATING"]["VALUE_XML_ID"])
		$arResult["RATING_LIST"][$arProps["RATING"]["VALUE_ENUM_ID"]]["ELEMENTS"][] = $arElement["ID"];

	$reviewsCount++;
}
unset($arProps, $arElement, $obElement, $rsElements);

$arResult["RATING"] = array(
	"GENERAL_VALUE" => $reviewsCount > 0 ? sprintf("%.1f", round($ratingSum / $reviewsCount, 1)) : 0,
	"USERS_RECOMMEND_PERCENT" => $reviewsCount > 0 ? round(($ratingRecommend / $reviewsCount) * 100) : 0
);

$arResult["REVIEWS_COUNT"] = $reviewsCount;