<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader,
	Bitrix\Iblock,
	Bitrix\Catalog,
	Bitrix\Currency;

if(!Loader::includeModule("iblock"))
	return;

$catalogIncluded = Loader::includeModule("catalog");

$catalogIblockExists = (!empty($arCurrentValues["CATALOG_IBLOCK_ID"]) && (int)$arCurrentValues["CATALOG_IBLOCK_ID"] > 0);

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$defaultValue = array("-" => GetMessage("CP_BN_DEFAULT_VALUE"));

$arSort = CIBlockParameters::GetElementSortFields(
	array("SHOWS", "SORT", "TIMESTAMP_X", "NAME", "ID", "ACTIVE_FROM", "ACTIVE_TO"),
	array("KEY_LOWERCASE" => "Y")
);

$arAscDesc = array(
	"asc" => GetMessage("CP_BN_SORT_ASC"),
	"desc" => GetMessage("CP_BN_SORT_DESC"),
);

$offersIblock = array();
if($catalogIncluded) {
	$iterator = Catalog\CatalogIblockTable::getList(array(
		"select" => array("IBLOCK_ID"),
		"filter" => array("!=PRODUCT_IBLOCK_ID" => 0)
	));
	while($row = $iterator->fetch())
		$offersIblock[$row["IBLOCK_ID"]] = true;
	unset($row, $iterator);
}

$arCatalogIBlock = array();
$iblockFilter = !empty($arCurrentValues['IBLOCK_TYPE']) ? array('TYPE' => $arCurrentValues['CATALOG_IBLOCK_TYPE'], 'ACTIVE' => 'Y') : array('ACTIVE' => 'Y');
$rsIBlock = CIBlock::GetList(array("SORT" => "ASC"), $iblockFilter);
while($arr = $rsIBlock->Fetch()) {
	$id = (int)$arr["ID"];
	if(isset($offersIblock[$id]))
		continue;
	$arCatalogIBlock[$id] = "[".$id."] ".$arr["NAME"];
}
unset($id, $arr, $rsIBlock, $iblockFilter);
unset($offersIblock);

$arAllPropList = array();
$arNumberPropList = array();
$arTreePropList = array();
$arFilePropList = $defaultValue;
if($catalogIblockExists) {
	$propertyIterator = Iblock\PropertyTable::getList(array(
		"select" => array("ID", "IBLOCK_ID", "NAME", "CODE", "PROPERTY_TYPE", "MULTIPLE", "LINK_IBLOCK_ID", "USER_TYPE", "SORT"),
		"filter" => array("=IBLOCK_ID" => $arCurrentValues["CATALOG_IBLOCK_ID"], "=ACTIVE" => "Y"),
		"order" => array("SORT" => "ASC", "NAME" => "ASC")
	));
	while($property = $propertyIterator->fetch()) {
		$propertyCode = (string)$property["CODE"];
		if($propertyCode == "")
			$propertyCode = $property["ID"];
		
		$propertyName = "[".$propertyCode."] ".$property["NAME"];

		$arAllPropList[$propertyCode] = $propertyName;

		if($property["PROPERTY_TYPE"] != Iblock\PropertyTable::TYPE_FILE) {
			if($property["MULTIPLE"] === "Y") {
				$arTreePropList[$propertyCode] = $propertyName;
			} elseif($property["PROPERTY_TYPE"] == Iblock\PropertyTable::TYPE_LIST) {
				$arTreePropList[$propertyCode] = $propertyName;
			} elseif($property["PROPERTY_TYPE"] == Iblock\PropertyTable::TYPE_ELEMENT && (int)$property["LINK_IBLOCK_ID"] > 0) {
				$arTreePropList[$propertyCode] = $propertyName;
			}
		} else {
			$arFilePropList[$propertyCode] = $propertyName;
		}
		
		if($property["PROPERTY_TYPE"] == Iblock\PropertyTable::TYPE_NUMBER) {
			$arNumberPropList[$propertyCode] = $propertyName;
		}
	}
	unset($propertyCode, $propertyName, $property, $propertyIterator);
}

$offers = false;
$arAllOfferPropList = array();
$arWithoutFileOfferPropList = array();
$arTreeOfferPropList = $arFileOfferPropList = $defaultValue;
if($catalogIncluded && $catalogIblockExists) {	
	$offers = CCatalogSku::GetInfoByProductIBlock($arCurrentValues["CATALOG_IBLOCK_ID"]);
	if(!empty($offers)) {
		$propertyIterator = Iblock\PropertyTable::getList(array(
			"select" => array("ID", "IBLOCK_ID", "NAME", "CODE", "PROPERTY_TYPE", "MULTIPLE", "LINK_IBLOCK_ID", "USER_TYPE", "USER_TYPE_SETTINGS", "SORT"),
			"filter" => array("=IBLOCK_ID" => $offers["IBLOCK_ID"], "=ACTIVE" => "Y", "!=ID" => $offers["SKU_PROPERTY_ID"]),
			"order" => array("SORT" => "ASC", "NAME" => "ASC")
		));
		while($property = $propertyIterator->fetch()) {
			$propertyCode = (string)$property["CODE"];
			if($propertyCode == "")
				$propertyCode = $property["ID"];
			
			$propertyName = "[".$propertyCode."] ".$property["NAME"];
			
			$arAllOfferPropList[$propertyCode] = $propertyName;

			if($property["PROPERTY_TYPE"] != Iblock\PropertyTable::TYPE_FILE) {
				$arWithoutFileOfferPropList[$propertyCode] = $propertyName;
			} else {
				$arFileOfferPropList[$propertyCode] = $propertyName;
			}

			$property["USER_TYPE_SETTINGS"] = unserialize($property["USER_TYPE_SETTINGS"]);

			if($property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_LIST || $property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_ELEMENT || ($property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_STRING && $property["USER_TYPE"] == "directory" && CIBlockPriceTools::checkPropDirectory($property))) {
				$arTreeOfferPropList[$propertyCode] = $propertyName;
			}
		}
		unset($propertyCode, $propertyName, $property, $propertyIterator);
	}
}

$arPrice = array();
if($catalogIncluded) {
	$arOfferSort = array_merge($arSort, CCatalogIBlockParameters::GetCatalogSortFields());
	if(isset($arSort["CATALOG_AVAILABLE"]))
		unset($arSort["CATALOG_AVAILABLE"]);
	$arPrice = CCatalogIBlockParameters::getPriceTypesList();
} else {
	$arOfferSort = $arSort;
	$arPrice = $arNumberPropList;
}

$arTemplateParameters["USE_SEARCH"] = array(	
	"HIDDEN" => "Y"
);

$arTemplateParameters["USE_RSS"] = array(	
	"HIDDEN" => "Y"
);
if(isset($arCurrentValues["USE_RSS"]) && $arCurrentValues["USE_RSS"] == "Y") {
	$arTemplateParameters["NUM_NEWS"] = array(
		"HIDDEN" => "Y"
	);
	$arTemplateParameters["NUM_DAYS"] = array(
		"HIDDEN" => "Y"
	);
	$arTemplateParameters["YANDEX"] = array(
		"HIDDEN" => "Y"
	);
}

$arTemplateParameters["USE_RATING"] = array(
	"HIDDEN" => "Y"
);
if(isset($arCurrentValues["USE_RATING"]) && $arCurrentValues["USE_RATING"] == "Y") {
	$arTemplateParameters["MAX_VOTE"] = array(
		"HIDDEN" => "Y"
	);
	$arTemplateParameters["VOTE_NAMES"] = array(
		"HIDDEN" => "Y"
	);
	$arTemplateParameters["DISPLAY_AS_RATING"] = array(
		"HIDDEN" => "Y"
	);
}

$arTemplateParameters["USE_CATEGORIES"] = array(
	"HIDDEN" => "Y"
);
if(isset($arCurrentValues["USE_CATEGORIES"]) && $arCurrentValues["USE_CATEGORIES"] == "Y") {
	$arTemplateParameters["CATEGORY_IBLOCK"] = array(
		"HIDDEN" => "Y"
	);
	$arTemplateParameters["CATEGORY_CODE"] = array(
		"HIDDEN" => "Y"
	);
	$arTemplateParameters["CATEGORY_ITEMS_COUNT"] = array(
		"HIDDEN" => "Y"
	);
	if(is_array($arCurrentValues["CATEGORY_IBLOCK"])) {
		foreach($arCurrentValues["CATEGORY_IBLOCK"] as $iblock_id) {
			if(intval($iblock_id) > 0) {
				$arTemplateParameters["CATEGORY_THEME_".intval($iblock_id)] = array(
					"HIDDEN" => "Y"
				);
			}
		}
	}
}

$arTemplateParameters["USE_REVIEW"] = array(
	"HIDDEN" => "Y"
);
if(isset($arCurrentValues["USE_REVIEW"]) && $arCurrentValues["USE_REVIEW"] == "Y") {
	$arTemplateParameters["MESSAGES_PER_PAGE"] = array(
		"HIDDEN" => "Y"
	);
	$arTemplateParameters["USE_CAPTCHA"] = array(
		"HIDDEN" => "Y"
	);
	$arTemplateParameters["REVIEW_AJAX_POST"] = array(
		"HIDDEN" => "Y"
	);
	$arTemplateParameters["PATH_TO_SMILE"] = array(
		"HIDDEN" => "Y"
	);
	$arTemplateParameters["FORUM_ID"] = array(
		"HIDDEN" => "Y"
	);
	$arTemplateParameters["URL_TEMPLATES_READ"] = array(
		"HIDDEN" => "Y"
	);
	$arTemplateParameters["SHOW_LINK_TO_FORUM"] = array(
		"HIDDEN" => "Y"
	);
}

$arTemplateParameters["SHOW_LAST_NEWS"] = array(
	"NAME" => GetMessage("SHOW_LAST_NEWS"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "Y"
);

$arTemplateParameters["CATALOG_IBLOCK_TYPE"] = array(
	"NAME" => GetMessage("CP_BN_CATALOG_IBLOCK_TYPE"),
	"TYPE" => "LIST",		
	"REFRESH" => "Y",
	"VALUES" => $arIBlockType,
);	

$arTemplateParameters["CATALOG_IBLOCK_ID"] = array(
	"NAME" => GetMessage("CP_BN_CATALOG_IBLOCK_ID"),
	"TYPE" => "LIST",
	"REFRESH" => "Y",		
	"VALUES" => $arCatalogIBlock,
	"ADDITIONAL_VALUES" => "Y",
);

if($catalogIncluded) {
	$arTemplateParameters["CATALOG_HIDE_NOT_AVAILABLE"] = array(		
		"NAME" => GetMessage("CP_BN_CATALOG_HIDE_NOT_AVAILABLE"),
		"TYPE" => "LIST",
		"DEFAULT" => "N",
		"VALUES" => array(
			"Y" => GetMessage("CP_BN_CATALOG_HIDE_NOT_AVAILABLE_HIDE"),
			"L" => GetMessage("CP_BN_CATALOG_HIDE_NOT_AVAILABLE_LAST"),
			"N" => GetMessage("CP_BN_CATALOG_HIDE_NOT_AVAILABLE_SHOW")
		),
		"ADDITIONAL_VALUES" => "N"
	);
	
	$arTemplateParameters["CATALOG_HIDE_NOT_AVAILABLE_OFFERS"] = array(		
		"NAME" => GetMessage("CP_BN_CATALOG_HIDE_NOT_AVAILABLE_OFFERS"),
		"TYPE" => "LIST",
		"DEFAULT" => "N",
		"VALUES" => array(
			"Y" => GetMessage("CP_BN_CATALOG_HIDE_NOT_AVAILABLE_OFFERS_HIDE"),
			"L" => GetMessage("CP_BN_CATALOG_HIDE_NOT_AVAILABLE_OFFERS_SUBSCRIBE"),
			"N" => GetMessage("CP_BN_CATALOG_HIDE_NOT_AVAILABLE_OFFERS_SHOW")
		)
	);
}

$arTemplateParameters["CATALOG_DETAIL_ADD_PICT_PROP"] = array(
	"NAME" => GetMessage("CP_BN_CATALOG_DETAIL_ADD_PICT_PROP"),
	"TYPE" => "LIST",
	"MULTIPLE" => "N",
	"ADDITIONAL_VALUES" => "N",
	"REFRESH" => "N",
	"DEFAULT" => "-",
	"VALUES" => $arFilePropList
);

if(!empty($offers)) {
	$arTemplateParameters["CATALOG_PRODUCT_DISPLAY_MODE"] = array(		
		"NAME" => GetMessage("CP_BN_CATALOG_PRODUCT_DISPLAY_MODE"),
		"TYPE" => "LIST",
		"MULTIPLE" => "N",
		"ADDITIONAL_VALUES" => "N",
		"REFRESH" => "Y",
		"DEFAULT" => "N",
		"VALUES" => array(
			"N" => GetMessage("CP_BN_CATALOG_PRODUCT_DISPLAY_MODE_SIMPLE"),
			"Y" => GetMessage("CP_BN_CATALOG_PRODUCT_DISPLAY_MODE_EXT")
		)
	);

	$arTemplateParameters["CATALOG_DETAIL_OFFER_ADD_PICT_PROP"] = array(
		"NAME" => GetMessage("CP_BN_CATALOG_DETAIL_OFFER_ADD_PICT_PROP"),
		"TYPE" => "LIST",
		"MULTIPLE" => "N",
		"ADDITIONAL_VALUES" => "N",
		"REFRESH" => "N",
		"DEFAULT" => "-",
		"VALUES" => $arFileOfferPropList
	);
	
	if(isset($arCurrentValues["CATALOG_PRODUCT_DISPLAY_MODE"]) && "Y" == $arCurrentValues["CATALOG_PRODUCT_DISPLAY_MODE"]) {
		$arTemplateParameters["CATALOG_OFFER_TREE_PROPS"] = array(			
			"NAME" => GetMessage("CP_BN_CATALOG_OFFER_TREE_PROPS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "N",
			"REFRESH" => "N",
			"DEFAULT" => "-",
			"VALUES" => $arTreeOfferPropList
		);
	}
}

if($catalogIncluded) {
	$arTemplateParameters["CATALOG_PRODUCT_SUBSCRIPTION"] = array(		
		"NAME" => GetMessage("CP_BN_CATALOG_PRODUCT_SUBSCRIPTION"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y"
	);

	$arTemplateParameters["CATALOG_SHOW_DISCOUNT_PERCENT"] = array(		
		"NAME" => GetMessage("CP_BN_CATALOG_SHOW_DISCOUNT_PERCENT"),
		"TYPE" => "CHECKBOX",
		"REFRESH" => "Y",
		"DEFAULT" => "Y"
	);

	$arTemplateParameters["CATALOG_SHOW_OLD_PRICE"] = array(		
		"NAME" => GetMessage("CP_BN_CATALOG_SHOW_OLD_PRICE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y"
	);

	$arTemplateParameters["CATALOG_SHOW_MAX_QUANTITY"] = array(		
		"NAME" => GetMessage("CP_BN_CATALOG_SHOW_MAX_QUANTITY"),
		"TYPE" => "LIST",
		"REFRESH" => "Y",
		"MULTIPLE" => "N",
		"VALUES" => array(
			"N" => GetMessage("CP_BN_CATALOG_SHOW_MAX_QUANTITY_N"),
			"Y" => GetMessage("CP_BN_CATALOG_SHOW_MAX_QUANTITY_Y"),
			"M" => GetMessage("CP_BN_CATALOG_SHOW_MAX_QUANTITY_M")
		),
		"DEFAULT" => array("M"),
	);

	if(isset($arCurrentValues["CATALOG_SHOW_MAX_QUANTITY"])) {
		if($arCurrentValues["CATALOG_SHOW_MAX_QUANTITY"] !== "N") {
			$arTemplateParameters["CATALOG_MESS_SHOW_MAX_QUANTITY"] = array(				
				"NAME" => GetMessage("CP_BN_CATALOG_MESS_SHOW_MAX_QUANTITY"),
				"TYPE" => "STRING",
				"DEFAULT" => GetMessage("CP_BN_CATALOG_MESS_SHOW_MAX_QUANTITY_DEFAULT")
			);
		}

		if($arCurrentValues["CATALOG_SHOW_MAX_QUANTITY"] === "M") {
			$arTemplateParameters["CATALOG_RELATIVE_QUANTITY_FACTOR"] = array(				
				"NAME" => GetMessage("CP_BN_CATALOG_RELATIVE_QUANTITY_FACTOR"),
				"TYPE" => "STRING",
				"DEFAULT" => "5"
			);
			$arTemplateParameters["CATALOG_MESS_RELATIVE_QUANTITY_MANY"] = array(				
				"NAME" => GetMessage("CP_BN_CATALOG_MESS_RELATIVE_QUANTITY_MANY"),
				"TYPE" => "STRING",
				"DEFAULT" => GetMessage("CP_BN_CATALOG_MESS_RELATIVE_QUANTITY_MANY_DEFAULT")
			);
			$arTemplateParameters["CATALOG_MESS_RELATIVE_QUANTITY_FEW"] = array(				
				"NAME" => GetMessage("CP_BN_CATALOG_MESS_RELATIVE_QUANTITY_FEW"),
				"TYPE" => "STRING",
				"DEFAULT" => GetMessage("CP_BN_CATALOG_MESS_RELATIVE_QUANTITY_FEW_DEFAULT")
			);
		}
	}
}

$arTemplateParameters["CATALOG_MESS_BTN_BUY"] = array(	
	"NAME" => GetMessage("CP_BN_CATALOG_MESS_BTN_BUY"),
	"TYPE" => "STRING",
	"DEFAULT" => GetMessage("CP_BN_CATALOG_MESS_BTN_BUY_DEFAULT")
);

$arTemplateParameters["CATALOG_MESS_BTN_ADD_TO_BASKET"] = array(	
	"NAME" => GetMessage("CP_BN_CATALOG_MESS_BTN_ADD_TO_BASKET"),
	"TYPE" => "STRING",
	"DEFAULT" => GetMessage("CP_BN_CATALOG_MESS_BTN_ADD_TO_BASKET_DEFAULT")
);

$arTemplateParameters["CATALOG_MESS_BTN_SUBSCRIBE"] = array(	
	"NAME" => GetMessage("CP_BN_CATALOG_MESS_BTN_SUBSCRIBE"),
	"TYPE" => "STRING",
	"DEFAULT" => GetMessage("CP_BN_CATALOG_MESS_BTN_SUBSCRIBE_DEFAULT")
);

$arTemplateParameters["CATALOG_MESS_BTN_DETAIL"] = array(	
	"NAME" => GetMessage("CP_BN_CATALOG_MESS_BTN_DETAIL"),
	"TYPE" => "STRING",
	"DEFAULT" => GetMessage("CP_BN_CATALOG_MESS_BTN_DETAIL_DEFAULT")
);

$arTemplateParameters["CATALOG_MESS_NOT_AVAILABLE"] = array(	
	"NAME" => GetMessage("CP_BN_CATALOG_MESS_NOT_AVAILABLE"),
	"TYPE" => "STRING",
	"DEFAULT" => GetMessage("CP_BN_CATALOG_MESS_NOT_AVAILABLE_DEFAULT")
);

$arTemplateParameters["CATALOG_USE_MAIN_ELEMENT_SECTION"] = array(
	"NAME" => GetMessage("CP_BN_CATALOG_USE_MAIN_ELEMENT_SECTION"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N",
);

$arTemplateParameters["CATALOG_PRICE_CODE"] = array(		
	"NAME" => GetMessage("CP_BN_CATALOG_PRICE_CODE"),
	"TYPE" => "LIST",
	"MULTIPLE" => "Y",
	"VALUES" => $arPrice,
);

$arTemplateParameters["CATALOG_USE_PRICE_COUNT"] = array(
	"NAME" => GetMessage("CP_BN_CATALOG_USE_PRICE_COUNT"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "Y",
);

$arTemplateParameters["CATALOG_SHOW_PRICE_COUNT"] = array(
	"NAME" => GetMessage("CP_BN_CATALOG_SHOW_PRICE_COUNT"),
	"TYPE" => "STRING",
	"DEFAULT" => "1",
);

$arTemplateParameters["CATALOG_PRICE_VAT_INCLUDE"] = array(		
	"NAME" => GetMessage("CP_BN_CATALOG_PRICE_VAT_INCLUDE"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "Y",
);

if($catalogIncluded) {
	$arTemplateParameters["CATALOG_CONVERT_CURRENCY"] = array(				
		"NAME" => GetMessage("CP_BN_CATALOG_CONVERT_CURRENCY"),
		"TYPE" => "CHECKBOX",				
		"REFRESH" => "Y",
		"DEFAULT" => "N",
	);

	if(isset($arCurrentValues["CATALOG_CONVERT_CURRENCY"]) && $arCurrentValues["CATALOG_CONVERT_CURRENCY"] === "Y") {
		$arTemplateParameters["CATALOG_CURRENCY_ID"] = array(			
			"NAME" => GetMessage("CP_BN_CATALOG_CURRENCY_ID"),
			"TYPE" => "LIST",
			"VALUES" => Currency\CurrencyManager::getCurrencyList(),
			"DEFAULT" => Currency\CurrencyManager::getBaseCurrency(),
			"ADDITIONAL_VALUES" => "Y",
		);
	}
}

$arTemplateParameters["CATALOG_DETAIL_USE_RATIO_IN_RANGES"] = array(
	"NAME" => GetMessage("CP_BN_CATALOG_DETAIL_USE_RATIO_IN_RANGES"),
	"TYPE" => "CHECKBOX",
	"HIDDEN" => isset($arCurrentValues["CATALOG_USE_PRICE_COUNT"]) && $arCurrentValues["CATALOG_USE_PRICE_COUNT"] === "Y" ? "N" : "Y",
	"DEFAULT" => "Y"
);

$arTemplateParameters["CATALOG_BASKET_URL"] = array(	
	"NAME" => GetMessage("CP_BN_CATALOG_BASKET_URL"),
	"TYPE" => "STRING",
	"DEFAULT" => "/personal/cart/",
);

$arTemplateParameters["CATALOG_USE_PRODUCT_QUANTITY"] = array(
	"NAME" => GetMessage("CP_BN_CATALOG_USE_PRODUCT_QUANTITY"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "Y",
	"REFRESH" => "Y",
);

$arTemplateParameters["CATALOG_ADD_PROPERTIES_TO_BASKET"] = array(	
	"NAME" => GetMessage("CP_BN_CATALOG_ADD_PROPERTIES_TO_BASKET"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "Y",
	"REFRESH" => "Y"
);

$arTemplateParameters["CATALOG_PARTIAL_PRODUCT_PROPERTIES"] = array(	
	"NAME" => GetMessage("CP_BN_CATALOG_PARTIAL_PRODUCT_PROPERTIES"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "Y",
	"HIDDEN" => (isset($arCurrentValues["CATALOG_ADD_PROPERTIES_TO_BASKET"]) && $arCurrentValues["CATALOG_ADD_PROPERTIES_TO_BASKET"] === "N" ? "Y" : "N")
);

$arTemplateParameters["CATALOG_PRODUCT_PROPERTIES"] = array(
	"NAME" => GetMessage("CP_BN_CATALOG_PRODUCT_PROPERTIES"),
	"TYPE" => "LIST",
	"MULTIPLE" => "Y",
	"VALUES" => $arTreePropList,
	"HIDDEN" => (isset($arCurrentValues["CATALOG_ADD_PROPERTIES_TO_BASKET"]) && $arCurrentValues["CATALOG_ADD_PROPERTIES_TO_BASKET"] === "N" ? "Y" : "N")
);

if(!empty($offers)) {
	$arTemplateParameters["CATALOG_OFFERS_CART_PROPERTIES"] = array(		
		"NAME" => GetMessage("CP_BN_CATALOG_OFFERS_CART_PROPERTIES"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arWithoutFileOfferPropList,
		"HIDDEN" => (isset($arCurrentValues["CATALOG_ADD_PROPERTIES_TO_BASKET"]) && $arCurrentValues["CATALOG_ADD_PROPERTIES_TO_BASKET"] === "N" ? "Y" : "N")
	);
}

if($catalogIncluded) {
	$arTemplateParameters["CATALOG_ADD_TO_BASKET_ACTION"] = array(		
		"NAME" => GetMessage("CP_BN_CATALOG_ADD_TO_BASKET_ACTION"),
		"TYPE" => "LIST",
		"VALUES" => array(
			"ADD" => GetMessage("CP_BN_CATALOG_ADD_TO_BASKET_ACTION_ADD"),
			"BUY" => GetMessage("CP_BN_CATALOG_ADD_TO_BASKET_ACTION_BUY")
		),
		"DEFAULT" => "ADD",
		"REFRESH" => "N"
	);
}

if(!empty($offers)) {
	$arTemplateParameters["CATALOG_OFFERS_PROPERTY_CODE"] = array(
		"NAME" => GetMessage("CP_BN_CATALOG_OFFERS_PROPERTY_CODE"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arAllOfferPropList,
		"ADDITIONAL_VALUES" => "Y",
	);
}

$arTemplateParameters["CATALOG_DETAIL_PROPERTY_CODE"] = array(
	"NAME" => GetMessage("CP_BN_CATALOG_DETAIL_PROPERTY_CODE"),
	"TYPE" => "LIST",
	"MULTIPLE" => "Y",
	"ADDITIONAL_VALUES" => "Y",
	"VALUES" => $arAllPropList,
);

if(!empty($arCurrentValues["CATALOG_DETAIL_PROPERTY_CODE"])) {
	$selected = array();
	foreach($arCurrentValues["CATALOG_DETAIL_PROPERTY_CODE"] as $code) {
		if(isset($arAllPropList[$code])) {
			$selected[$code] = $arAllPropList[$code];
		}
	}

	$arTemplateParameters["CATALOG_DETAIL_MAIN_BLOCK_PROPERTY_CODE"] = array(
		"NAME" => GetMessage("CP_BN_CATALOG_DETAIL_MAIN_BLOCK_PROPERTY_CODE"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"SIZE" => (count($selected) > 5 ? 8 : 3),
		"VALUES" => $selected
	);
}

if(!empty($offers)) {
	$arTemplateParameters["CATALOG_DETAIL_OFFERS_FIELD_CODE"] = CIBlockParameters::GetFieldCode(GetMessage("CP_BN_CATALOG_DETAIL_OFFERS_FIELD_CODE"), "ADDITIONAL_SETTINGS");

	$arTemplateParameters["CATALOG_DETAIL_OFFERS_PROPERTY_CODE"] = array(
		"NAME" => GetMessage("CP_BN_CATALOG_DETAIL_OFFERS_PROPERTY_CODE"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"ADDITIONAL_VALUES" => "Y",
		"VALUES" => $arAllOfferPropList,
	);
	
	if(!empty($arCurrentValues["CATALOG_DETAIL_OFFERS_PROPERTY_CODE"])) {
		$selected = array();
		foreach($arCurrentValues["CATALOG_DETAIL_OFFERS_PROPERTY_CODE"] as $code) {
			if(isset($arAllOfferPropList[$code])) {
				$selected[$code] = $arAllOfferPropList[$code];
			}
		}

		$arTemplateParameters["CATALOG_DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE"] = array(
			"NAME" => GetMessage("CP_BN_CATALOG_DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"SIZE" => (count($selected) > 5 ? 8 : 3),
			"VALUES" => $selected
		);
	}
}

$arTemplateParameters["CATALOG_DETAIL_IMAGE_RESOLUTION"] = array(
	"NAME" => GetMessage("CP_BN_CATALOG_DETAIL_IMAGE_RESOLUTION"),
	"TYPE" => "LIST",
	"VALUES" => array(
		"16by9" => GetMessage("CP_BN_CATALOG_DETAIL_IMAGE_RESOLUTION_16_BY_9"),
		"1by1" => GetMessage("CP_BN_CATALOG_DETAIL_IMAGE_RESOLUTION_1_BY_1")
	),
	"DEFAULT" => "16by9"
);

$arTemplateParameters["CATALOG_DETAIL_SHOW_SLIDER"] = array(
	"NAME" => GetMessage("CP_BN_CATALOG_DETAIL_SHOW_SLIDER"),
	"TYPE" => "CHECKBOX",
	"MULTIPLE" => "N",
	"REFRESH" => "Y",
	"DEFAULT" => "N"
);

if(isset($arCurrentValues["CATALOG_DETAIL_SHOW_SLIDER"]) && $arCurrentValues["CATALOG_DETAIL_SHOW_SLIDER"] === "Y") {
	$arTemplateParameters["CATALOG_DETAIL_SLIDER_INTERVAL"] = array(
		"NAME" => GetMessage("CP_BN_CATALOG_DETAIL_SLIDER_INTERVAL"),
		"TYPE" => "TEXT",
		"MULTIPLE" => "N",
		"REFRESH" => "N",
		"DEFAULT" => "5000"
	);

	$arTemplateParameters["CATALOG_DETAIL_SLIDER_PROGRESS"] = array(
		"NAME" => GetMessage("CP_BN_CATALOG_DETAIL_SLIDER_PROGRESS"),
		"TYPE" => "CHECKBOX",
		"MULTIPLE" => "N",
		"REFRESH" => "N",
		"DEFAULT" => "N"
	);
}

$arTemplateParameters["CATALOG_DETAIL_DETAIL_PICTURE_MODE"] = array(
	"NAME" => GetMessage("CP_BN_CATALOG_DETAIL_DETAIL_PICTURE_MODE"),
	"TYPE" => "LIST",
	"MULTIPLE" => "Y",
	"DEFAULT" => array("POPUP", "MAGNIFIER"),
	"VALUES" => array(
		"POPUP" => GetMessage("CP_BN_CATALOG_DETAIL_DETAIL_PICTURE_MODE_POPUP"),
		"MAGNIFIER" => GetMessage("CP_BN_CATALOG_DETAIL_DETAIL_PICTURE_MODE_MAGNIFIER"),
	)
);

$arTemplateParameters["CATALOG_DETAIL_ADD_DETAIL_TO_SLIDER"] = array(
	"NAME" => GetMessage("CP_BN_CATALOG_DETAIL_ADD_DETAIL_TO_SLIDER"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N"
);

if(!empty($offers)) {
	$arTemplateParameters["CATALOG_OFFERS_SORT_FIELD"] = array(
		"NAME" => GetMessage("CP_BN_CATALOG_OFFERS_SORT_FIELD"),
		"TYPE" => "LIST",
		"VALUES" => $arOfferSort,
		"ADDITIONAL_VALUES" => "Y",
		"DEFAULT" => "sort",
	);

	$arTemplateParameters["CATALOG_OFFERS_SORT_ORDER"] = array(
		"NAME" => GetMessage("CP_BN_CATALOG_OFFERS_SORT_ORDER"),
		"TYPE" => "LIST",
		"VALUES" => $arAscDesc,
		"DEFAULT" => "asc",
		"ADDITIONAL_VALUES" => "Y",
	);

	$arTemplateParameters["CATALOG_OFFERS_SORT_FIELD2"] = array(
		"NAME" => GetMessage("CP_BN_CATALOG_OFFERS_SORT_FIELD2"),
		"TYPE" => "LIST",
		"VALUES" => $arOfferSort,
		"ADDITIONAL_VALUES" => "Y",
		"DEFAULT" => "id",
	);

	$arTemplateParameters["CATALOG_OFFERS_SORT_ORDER2"] = array(
		"NAME" => GetMessage("CP_BN_CATALOG_OFFERS_SORT_ORDER2"),
		"TYPE" => "LIST",
		"VALUES" => $arAscDesc,
		"DEFAULT" => "desc",
		"ADDITIONAL_VALUES" => "Y",
	);
}

$arCatalogReviewsIBlock = array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), array("TYPE" => $arCurrentValues["CATALOG_REVIEWS_IBLOCK_TYPE"], "ACTIVE" => "Y"));
while($arr = $rsIBlock->Fetch()) {
	$arCatalogReviewsIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}
unset($arr, $rsIBlock);

$arTemplateParameters["CATALOG_USE_REVIEW"] = array(
	"NAME" => GetMessage("CP_BN_CATALOG_USE_REVIEW"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "Y",
	"REFRESH" => "Y",
);

if(isset($arCurrentValues["CATALOG_USE_REVIEW"]) && $arCurrentValues["CATALOG_USE_REVIEW"] == "Y") {	
	$arTemplateParameters["CATALOG_REVIEWS_IBLOCK_TYPE"] = array(
		"NAME" => GetMessage("CP_BN_CATALOG_REVIEWS_IBLOCK_TYPE"),
		"TYPE" => "LIST",		
		"REFRESH" => "Y",
		"VALUES" => $arIBlockType,
	);

	$arTemplateParameters["CATALOG_REVIEWS_IBLOCK_ID"] = array(
		"NAME" => GetMessage("CP_BN_CATALOG_REVIEWS_IBLOCK_ID"),
		"TYPE" => "LIST",
		"REFRESH" => "Y",		
		"VALUES" => $arCatalogReviewsIBlock,
		"ADDITIONAL_VALUES" => "Y",
	);
}

$arTemplateParameters["CATALOG_DISPLAY_COMPARE"] = array(
	"NAME" => GetMessage("CP_BN_CATALOG_DISPLAY_COMPARE"),
	"TYPE" => "CHECKBOX",
	"REFRESH" => "Y",
	"DEFAULT" => "Y"
);

if(isset($arCurrentValues["CATALOG_DISPLAY_COMPARE"]) && $arCurrentValues["CATALOG_DISPLAY_COMPARE"] == "Y") {
	$arTemplateParameters["CATALOG_COMPARE_PATH"] = array(		
		"NAME" => GetMessage("CP_BN_CATALOG_COMPARE_PATH"),
		"TYPE" => "STRING",
		"DEFAULT" => ""
	);

	$arTemplateParameters["CATALOG_MESS_BTN_COMPARE"] = array(		
		"NAME" => GetMessage("CP_BN_CATALOG_MESS_BTN_COMPARE"),
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage("CP_BN_CATALOG_MESS_BTN_COMPARE_DEFAULT")
	);

	$arTemplateParameters["CATALOG_COMPARE_NAME"] = array(		
		"NAME" => GetMessage("CP_BN_CATALOG_COMPARE_NAME"),
		"TYPE" => "STRING",
		"DEFAULT" => "CATALOG_COMPARE_LIST"
	);
}

$arObjectsReviewsIBlock = array();
$rsIBlock = CIBlock::GetList(array("sort" => "asc"), array("TYPE" => $arCurrentValues["OBJECTS_REVIEWS_IBLOCK_TYPE"], "ACTIVE" => "Y"));
while($arr = $rsIBlock->Fetch()) {
	$arObjectsReviewsIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}
unset($arr, $rsIBlock);

$arTemplateParameters["OBJECTS_USE_REVIEW"] = array(
	"NAME" => GetMessage("CP_BN_OBJECTS_USE_REVIEW"),
	"TYPE" => "CHECKBOX",
	"REFRESH" => "Y",
	"DEFAULT" => "Y"
);

if(isset($arCurrentValues["OBJECTS_USE_REVIEW"]) && $arCurrentValues["OBJECTS_USE_REVIEW"] == "Y") {
	$arTemplateParameters["OBJECTS_REVIEWS_IBLOCK_TYPE"] = array(
		"NAME" => GetMessage("CP_BN_OBJECTS_REVIEWS_IBLOCK_TYPE"),
		"TYPE" => "LIST",		
		"REFRESH" => "Y",
		"VALUES" => $arIBlockType,
	);
	
	$arTemplateParameters["OBJECTS_REVIEWS_IBLOCK_ID"] = array(
		"NAME" => GetMessage("CP_BN_OBJECTS_REVIEWS_IBLOCK_ID"),
		"TYPE" => "LIST",
		"REFRESH" => "Y",		
		"VALUES" => $arObjectsReviewsIBlock,
		"ADDITIONAL_VALUES" => "Y",
	);
}

$arContactsIBlock = array();
$rsIBlock = CIBlock::GetList(array("sort" => "asc"), array("TYPE" => $arCurrentValues["CONTACTS_IBLOCK_TYPE"], "ACTIVE" => "Y"));
while($arr = $rsIBlock->Fetch()) {
	$arContactsIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}
unset($arr, $rsIBlock);

$arContactsReviewsIBlock = array();
$rsIBlock = CIBlock::GetList(array("sort" => "asc"), array("TYPE" => $arCurrentValues["CONTACTS_REVIEWS_IBLOCK_TYPE"], "ACTIVE" => "Y"));
while($arr = $rsIBlock->Fetch()) {
	$arContactsReviewsIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}
unset($arr, $rsIBlock);

$arTemplateParameters["CONTACTS_IBLOCK_TYPE"] = array(
	"NAME" => GetMessage("CP_BN_CONTACTS_IBLOCK_TYPE"),
	"TYPE" => "LIST",		
	"REFRESH" => "Y",
	"VALUES" => $arIBlockType,
);

$arTemplateParameters["CONTACTS_IBLOCK_ID"] = array(
	"NAME" => GetMessage("CP_BN_CONTACTS_IBLOCK_ID"),
	"TYPE" => "LIST",
	"REFRESH" => "Y",		
	"VALUES" => $arContactsIBlock,
	"ADDITIONAL_VALUES" => "Y",
);

$arTemplateParameters["CONTACTS_USE_REVIEW"] = array(
	"NAME" => GetMessage("CP_BN_CONTACTS_USE_REVIEW"),
	"TYPE" => "CHECKBOX",
	"REFRESH" => "Y",
	"DEFAULT" => "Y"
);

if(isset($arCurrentValues["CONTACTS_USE_REVIEW"]) && $arCurrentValues["CONTACTS_USE_REVIEW"] == "Y") {
	$arTemplateParameters["CONTACTS_REVIEWS_IBLOCK_TYPE"] = array(
		"NAME" => GetMessage("CP_BN_CONTACTS_REVIEWS_IBLOCK_TYPE"),
		"TYPE" => "LIST",		
		"REFRESH" => "Y",
		"VALUES" => $arIBlockType,
	);
	
	$arTemplateParameters["CONTACTS_REVIEWS_IBLOCK_ID"] = array(
		"NAME" => GetMessage("CP_BN_CONTACTS_REVIEWS_IBLOCK_ID"),
		"TYPE" => "LIST",
		"REFRESH" => "Y",		
		"VALUES" => $arContactsReviewsIBlock,
		"ADDITIONAL_VALUES" => "Y",
	);

	$arTemplateParameters["CONTACTS_REVIEWS_PAGE_LINK"] = array(
		"NAME" => GetMessage("CP_BN_CONTACTS_REVIEWS_PAGE_LINK"),
		"TYPE" => "STRING",
		"DEFAULT" => '={SITE_DIR."about/reviews/"}'
	);
}