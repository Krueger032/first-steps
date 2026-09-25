<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arSettings = CEnext::GetFrontParametrsValues(SITE_ID);

//QUICK_VIEW//
$arParams["QUICK_VIEW"] = $arSettings["QUICK_VIEW"];

//PRODUCTS_LIST_VIEW_MOBILE//
$arParams["PRODUCTS_LIST_VIEW_MOBILE"] = $arSettings["PRODUCTS_LIST_VIEW_MOBILE"];

//OFFERS_VIEW//
$arParams["OFFERS_VIEW"] = $arSettings["OFFERS_VIEW"];

//UNDER_ORDER//
$arParams["UNDER_ORDER"] = true;
if($arSettings["UNDER_ORDER"] != "Y")
	$arParams["UNDER_ORDER"] = false;

//DISABLE_BASKET//
$arParams["DISABLE_BASKET"] = false;
if($arSettings["DISABLE_BASKET"] == "Y")
	$arParams["DISABLE_BASKET"] = true;

//SHOW_SUBSCRIBE//
if($arParams["PRODUCT_SUBSCRIPTION"] == "Y") {
	$saleNotifyOption = Bitrix\Main\Config\Option::get("sale", "subscribe_prod");
	if(strlen($saleNotifyOption) > 0)
		$saleNotifyOption = unserialize($saleNotifyOption);
	$saleNotifyOption = is_array($saleNotifyOption) ? $saleNotifyOption : array();
	foreach($saleNotifyOption as $siteId => $data) {
		if($siteId == SITE_ID && $data["use"] != "Y")
			$arParams["PRODUCT_SUBSCRIPTION"] = "N";
	}
}

//BRANDS_OBJECTS//
foreach($arResult["ITEMS"] as $item) {
	foreach($item["PROPERTIES"] as $prop) {
		if($prop["CODE"] == "BRAND" && !empty($prop["VALUE"]))
			$brandIds[] = $prop["VALUE"];
		elseif($prop["CODE"] == "OBJECT" && !empty($prop["VALUE"]))
			$objectsIds[] = $prop["VALUE"];
	}
	unset($prop);
}
unset($item);

//BRANDS//
if(!empty($brandIds)) {
	$rsElements = CIBlockElement::GetList(array(), array("ID" => array_unique($brandIds)), false, false, array("ID", "IBLOCK_ID", "NAME", "PREVIEW_PICTURE"));
	while($arElement = $rsElements->GetNext()) {
		$arBrands[$arElement["ID"]] = array(
			"NAME" => $arElement["NAME"],
			"PREVIEW_PICTURE" => $arElement["PREVIEW_PICTURE"] > 0 ? CFile::GetFileArray($arElement["PREVIEW_PICTURE"]) : array()
		);
	}
	unset($arElement, $rsElements);
	
	if(!empty($arBrands)) {
		foreach($arResult["ITEMS"] as &$item) {		
			foreach($item["PROPERTIES"] as &$prop) {
				if($prop["CODE"] == "BRAND" && !empty($prop["VALUE"])) {
					if(array_key_exists($prop["VALUE"], $arBrands))
						$prop["FULL_VALUE"] = $arBrands[$prop["VALUE"]];
				}
			}
			unset($prop);
		}
		unset($item);
	}
	unset($arBrands);
}
unset($brandIds);

//OBJECTS//
if(!empty($objectsIds)) {
	$rsElements = CIBlockElement::GetList(array(), array("ID" => array_unique($objectsIds)), false, false, array("ID", "IBLOCK_ID", "NAME", "PROPERTY_PHONE_SMS", "PROPERTY_EMAIL_EMAIL"));
	while($arElement = $rsElements->GetNext()) {
		$arObjects[$arElement["ID"]] = array(
			"ID" => $arElement["ID"],
			"NAME" => $arElement["NAME"],
			"PHONE_SMS" => !empty($arElement["PROPERTY_PHONE_SMS_VALUE"]),
			"EMAIL_EMAIL" => !empty($arElement["PROPERTY_EMAIL_EMAIL_VALUE"])
		);
	}
	unset($arElement, $rsElements);
	
	if(!empty($arObjects)) {
		foreach($arResult["ITEMS"] as &$item) {		
			foreach($item["PROPERTIES"] as &$prop) {
				if($prop["CODE"] == "OBJECT" && !empty($prop["VALUE"])) {
					if(array_key_exists($prop["VALUE"], $arObjects))
						$prop["FULL_VALUE"] = $arObjects[$prop["VALUE"]];
				}
			}
			unset($prop);
		}
		unset($item);
	}
	unset($arObjects);
}
unset($objectsIds);

//PRODUCT_PROPERTIES//
if($arParams["ADD_PROPERTIES_TO_BASKET"] == "Y" && !empty($arParams["PRODUCT_PROPERTIES"])) {
	foreach($arResult["ITEMS"] as &$item) {
		if(!isset($item["PRODUCT_PROPERTIES"]) || empty($item["PRODUCT_PROPERTIES"]))
			$item["PRODUCT_PROPERTIES"] = CIBlockPriceTools::GetProductProperties($item["IBLOCK_ID"], $item["ID"], $arParams["PRODUCT_PROPERTIES"], $item["PROPERTIES"]);
	}
	unset($item);
}

//TARGET//
//OFFERS_OBJECTS//
//OFFERS_PARTNERS_URL//
foreach($arResult["ITEMS"] as $key => &$item) {
	//TARGET//
	$item["TARGET"] = "_self";	
	if(!empty($item["OFFERS"])) {
		//OFFERS_OBJECTS//
		$item["OFFERS_OBJECTS"] = false;
		foreach($item["OFFERS"] as $arOffer) {
			if(!empty($arOffer["PROPERTIES"]["OBJECT"]["VALUE"])) {
				$item["OFFERS_OBJECTS"] = true;
				break;
			}
		}
		unset($arOffer);		
		//OFFERS_PARTNERS_URL//
		foreach($item["OFFERS"] as $keyOffer => $arOffer) {
			$arResult["ITEMS"][$key]["JS_OFFERS"][$keyOffer]["PARTNERS_URL"] = false;
		}
		unset($keyOffer, $arOffer);
	}
}
unset($key, $item);

//UF_CODE//
$isSkuProps = false;
foreach($arResult["ITEMS"] as $item) {
	if(!empty($item["OFFERS"]) && !empty($item["OFFERS_PROP"])) {
		$isSkuProps = true;
		break;
	}
}
unset($item);

if(($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST") && $arParams["PRODUCT_DISPLAY_MODE"] === "Y" && !!$isSkuProps) {
	foreach($arResult["SKU_PROPS"] as &$skuProps) {		
		foreach($skuProps as &$skuProperty) {
			if($skuProperty["SHOW_MODE"] == "PICT") {
				$entity = $skuProperty["USER_TYPE_SETTINGS"]["ENTITY"];
				if(!($entity instanceof Bitrix\Main\Entity\Base))
					continue;

				$entityFields = $entity->getFields();
				if(!array_key_exists("UF_CODE", $entityFields))
					continue;

				$entityDataClass = $entity->getDataClass();
				
				$directorySelect = array("ID", "UF_CODE");
				$directoryOrder = array();
				
				$entityGetList = array(
					"select" => $directorySelect,
					"order" => $directoryOrder
				);
				$propEnums = $entityDataClass::getList($entityGetList);
				while($oneEnum = $propEnums->fetch()) {
					$values[$oneEnum["ID"]] = $oneEnum["UF_CODE"];
				}

				foreach($skuProperty["VALUES"] as &$val) {				
					if(isset($values[$val["ID"]]))
						$val["CODE"] = $values[$val["ID"]];
				}
				unset($val, $values);
			}
		}
		unset($skuProperty);	
	}
	unset($skuProps);
}
unset($isSkuProps);