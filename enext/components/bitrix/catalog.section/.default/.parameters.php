<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Web\Json;

if(!Loader::includeModule("iblock"))
	return;

$boolCatalog = Loader::includeModule("catalog");
CBitrixComponent::includeComponentClass($componentName);

$defaultValue = array("-" => GetMessage("CP_BCS_TPL_PROP_EMPTY"));
$arSKU = false;
$boolSKU = false;
if($boolCatalog && (isset($arCurrentValues["IBLOCK_ID"]) && 0 < intval($arCurrentValues["IBLOCK_ID"]))) {
	$arSKU = CCatalogSku::GetInfoByProductIBlock($arCurrentValues["IBLOCK_ID"]);
	$boolSKU = !empty($arSKU) && is_array($arSKU);	
}

$arTemplateParameters["BACKGROUND_IMAGE"] = array(
	"HIDDEN" => "Y"
);

$arAllPropList = array();
$arFilePropList = $defaultValue;

if(isset($arCurrentValues["IBLOCK_ID"]) && intval($arCurrentValues["IBLOCK_ID"]) > 0) {
	$rsProps = CIBlockProperty::GetList(
		array("SORT" => "ASC", "ID" => "ASC"),
		array("IBLOCK_ID" => $arCurrentValues["IBLOCK_ID"], "ACTIVE" => "Y")
	);
	while($arProp = $rsProps->Fetch()) {
		$strPropName = "[".$arProp["ID"]."]".("" != $arProp["CODE"] ? "[".$arProp["CODE"]."]" : "")." ".$arProp["NAME"];

		if($arProp["CODE"] == "") {
			$arProp["CODE"] = $arProp["ID"];
		}

		$arAllPropList[$arProp["CODE"]] = $strPropName;

		if($arProp["PROPERTY_TYPE"] == "F") {
			$arFilePropList[$arProp["CODE"]] = $strPropName;
		}
	}
	
	$lineElementCount = (int)$arCurrentValues["LINE_ELEMENT_COUNT"] ?: 4;
	$pageElementCount = (int)$arCurrentValues["PAGE_ELEMENT_COUNT"] ?: 12;

	$arTemplateParameters["PRODUCT_ROW_VARIANTS"] = array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("CP_BCS_TPL_PRODUCT_ROW_VARIANTS"),
		"TYPE" => "CUSTOM",
		"BIG_DATA" => "Y",
		"COUNT_PARAM_NAME" => "PAGE_ELEMENT_COUNT",		
		"JS_FILE" => CatalogSectionComponent::getSettingsScript($templateFolder, "dragdrop_add"),
		"JS_EVENT" => "initDraggableAddControl",
		"JS_MESSAGES" => Json::encode(array(
			"variant" => GetMessage("CP_BCS_TPL_SETTINGS_VARIANT"),
			"delete" => GetMessage("CP_BCS_TPL_SETTINGS_DELETE"),
			"quantity" => GetMessage("CP_BCS_TPL_SETTINGS_QUANTITY"),
			"quantityBigData" => GetMessage("CP_BCS_TPL_SETTINGS_QUANTITY_BIG_DATA")
		)),
		"JS_DATA" => Json::encode(CatalogSectionComponent::getTemplateVariantsMap()),
		"DEFAULT" => Json::encode(CatalogSectionComponent::predictRowVariants($lineElementCount, $pageElementCount))
	);
	
	if($boolSKU) {
		$arTemplateParameters["PRODUCT_DISPLAY_MODE"] = array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("CP_BCS_TPL_PRODUCT_DISPLAY_MODE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"ADDITIONAL_VALUES" => "N",
			"REFRESH" => "Y",
			"DEFAULT" => "N",
			"VALUES" => array(
				"N" => GetMessage("CP_BCS_TPL_DML_SIMPLE"),
				"Y" => GetMessage("CP_BCS_TPL_DML_EXT")
			)
		);
	}
	
	if($boolSKU && isset($arCurrentValues["PRODUCT_DISPLAY_MODE"]) && "Y" == $arCurrentValues["PRODUCT_DISPLAY_MODE"]) {		
		$arAllOfferPropList = array();
		$arFileOfferPropList = $arTreeOfferPropList = $defaultValue;
		$rsProps = CIBlockProperty::GetList(
			array("SORT" => "ASC", "ID" => "ASC"),
			array("IBLOCK_ID" => $arSKU["IBLOCK_ID"], "ACTIVE" => "Y")
		);
		while($arProp = $rsProps->Fetch()) {
			if($arProp["ID"] == $arSKU["SKU_PROPERTY_ID"])
				continue;
			$arProp["USER_TYPE"] = (string)$arProp["USER_TYPE"];
			$strPropName = "[".$arProp["ID"]."]".("" != $arProp["CODE"] ? "[".$arProp["CODE"]."]" : "")." ".$arProp["NAME"];
			if("" == $arProp["CODE"])
				$arProp["CODE"] = $arProp["ID"];			
			$arAllOfferPropList[$arProp["CODE"]] = $strPropName;
			if("F" == $arProp["PROPERTY_TYPE"])
				$arFileOfferPropList[$arProp["CODE"]] = $strPropName;
			if("N" != $arProp["MULTIPLE"])
				continue;
			if(
				"L" == $arProp["PROPERTY_TYPE"]
				|| "E" == $arProp["PROPERTY_TYPE"]
				|| ("S" == $arProp["PROPERTY_TYPE"] && "directory" == $arProp["USER_TYPE"] && CIBlockPriceTools::checkPropDirectory($arProp))
			)
				$arTreeOfferPropList[$arProp["CODE"]] = $strPropName;
		}		
		$arTemplateParameters["OFFER_TREE_PROPS"] = array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("CP_BCS_TPL_OFFER_TREE_PROPS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "N",
			"REFRESH" => "N",
			"DEFAULT" => "-",
			"VALUES" => $arTreeOfferPropList
		);
	}
}

if($boolCatalog) {	
	$arTemplateParameters["PRODUCT_SUBSCRIPTION"] = array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("CP_BCS_TPL_PRODUCT_SUBSCRIPTION"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y"
	);
	$arTemplateParameters["SHOW_DISCOUNT_PERCENT"] = array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("CP_BCS_TPL_SHOW_DISCOUNT_PERCENT"),
		"TYPE" => "CHECKBOX",
		"REFRESH" => "Y",
		"DEFAULT" => "N"
	);
	$arTemplateParameters["SHOW_OLD_PRICE"] = array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("CP_BCS_TPL_SHOW_OLD_PRICE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N"
	);
	$arTemplateParameters["SHOW_MAX_QUANTITY"] = array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("CP_BCS_TPL_SHOW_MAX_QUANTITY"),
		"TYPE" => "LIST",
		"REFRESH" => "Y",
		"MULTIPLE" => "N",
		"VALUES" => array(
			"N" => GetMessage("CP_BCS_TPL_SHOW_MAX_QUANTITY_N"),
			"Y" => GetMessage("CP_BCS_TPL_SHOW_MAX_QUANTITY_Y"),
			"M" => GetMessage("CP_BCS_TPL_SHOW_MAX_QUANTITY_M")
		),
		"DEFAULT" => array("N"),
	);

	if(isset($arCurrentValues["SHOW_MAX_QUANTITY"])) {
		if($arCurrentValues["SHOW_MAX_QUANTITY"] !== "N") {
			$arTemplateParameters["MESS_SHOW_MAX_QUANTITY"] = array(
				"PARENT" => "VISUAL",
				"NAME" => GetMessage("CP_BCS_TPL_MESS_SHOW_MAX_QUANTITY"),
				"TYPE" => "STRING",
				"DEFAULT" => GetMessage("CP_BCS_TPL_MESS_SHOW_MAX_QUANTITY_DEFAULT")
			);
		}

		if($arCurrentValues["SHOW_MAX_QUANTITY"] === "M") {
			$arTemplateParameters["RELATIVE_QUANTITY_FACTOR"] = array(
				"PARENT" => "VISUAL",
				"NAME" => GetMessage("CP_BCS_TPL_RELATIVE_QUANTITY_FACTOR"),
				"TYPE" => "STRING",
				"DEFAULT" => "5"
			);
			$arTemplateParameters["MESS_RELATIVE_QUANTITY_MANY"] = array(
				"PARENT" => "VISUAL",
				"NAME" => GetMessage("CP_BCS_TPL_MESS_RELATIVE_QUANTITY_MANY"),
				"TYPE" => "STRING",
				"DEFAULT" => GetMessage("CP_BCS_TPL_MESS_RELATIVE_QUANTITY_MANY_DEFAULT")
			);
			$arTemplateParameters["MESS_RELATIVE_QUANTITY_FEW"] = array(
				"PARENT" => "VISUAL",
				"NAME" => GetMessage("CP_BCS_TPL_MESS_RELATIVE_QUANTITY_FEW"),
				"TYPE" => "STRING",
				"DEFAULT" => GetMessage("CP_BCS_TPL_MESS_RELATIVE_QUANTITY_FEW_DEFAULT")
			);
		}
	}
	
	$arTemplateParameters["ADD_TO_BASKET_ACTION"] = array(
		"PARENT" => "BASKET",
		"NAME" => GetMessage("CP_BCS_TPL_ADD_TO_BASKET_ACTION"),
		"TYPE" => "LIST",
		"VALUES" => array(
			"ADD" => GetMessage("ADD_TO_BASKET_ACTION_ADD"),
			"BUY" => GetMessage("ADD_TO_BASKET_ACTION_BUY")
		),
		"DEFAULT" => "ADD",
		"REFRESH" => "N"
	);	
}

$arTemplateParameters["MESS_BTN_BUY"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("CP_BCS_TPL_MESS_BTN_BUY"),
	"TYPE" => "STRING",
	"DEFAULT" => GetMessage("CP_BCS_TPL_MESS_BTN_BUY_DEFAULT")
);

$arTemplateParameters["LAZY_LOAD"] = array(
	"PARENT" => "PAGER_SETTINGS",
	"NAME" => GetMessage("CP_BCS_TPL_LAZY_LOAD"),
	"TYPE" => "CHECKBOX",
	"REFRESH" => "Y",
	"DEFAULT" => "N"
);

if(isset($arCurrentValues["LAZY_LOAD"]) && $arCurrentValues["LAZY_LOAD"] === "Y") {
	$arTemplateParameters["MESS_BTN_LAZY_LOAD"] = array(
		"PARENT" => "PAGER_SETTINGS",
		"NAME" => GetMessage("CP_BCS_TPL_MESS_BTN_LAZY_LOAD"),
		"TYPE" => "TEXT",
		"DEFAULT" => GetMessage("CP_BCS_TPL_MESS_BTN_LAZY_LOAD_DEFAULT")
	);
}

$arTemplateParameters["LOAD_ON_SCROLL"] = array(
	"PARENT" => "PAGER_SETTINGS",
	"NAME" => GetMessage("CP_BCS_TPL_LOAD_ON_SCROLL"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N"
);

$arTemplateParameters["MESS_BTN_ADD_TO_BASKET"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("CP_BCS_TPL_MESS_BTN_ADD_TO_BASKET"),
	"TYPE" => "STRING",
	"DEFAULT" => GetMessage("CP_BCS_TPL_MESS_BTN_ADD_TO_BASKET_DEFAULT")
);
$arTemplateParameters["MESS_BTN_SUBSCRIBE"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("CP_BCS_TPL_MESS_BTN_SUBSCRIBE"),
	"TYPE" => "STRING",
	"DEFAULT" => GetMessage("CP_BCS_TPL_MESS_BTN_SUBSCRIBE_DEFAULT")
);

if(isset($arCurrentValues["DISPLAY_COMPARE"]) && $arCurrentValues["DISPLAY_COMPARE"] === "Y") {
	$arTemplateParameters["MESS_BTN_COMPARE"] = array(
		"PARENT" => "COMPARE",
		"NAME" => GetMessage("CP_BCS_TPL_MESS_BTN_COMPARE"),
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage("CP_BCS_TPL_MESS_BTN_COMPARE_DEFAULT")
	);
	$arTemplateParameters["COMPARE_NAME"] = array(
		"PARENT" => "COMPARE",
		"NAME" => GetMessage("CP_BCS_TPL_COMPARE_NAME"),
		"TYPE" => "STRING",
		"DEFAULT" => "CATALOG_COMPARE_LIST"
	);
}

$arTemplateParameters["MESS_BTN_DETAIL"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("CP_BCS_TPL_MESS_BTN_DETAIL"),
	"TYPE" => "STRING",
	"DEFAULT" => GetMessage("CP_BCS_TPL_MESS_BTN_DETAIL_DEFAULT")
);
$arTemplateParameters["MESS_NOT_AVAILABLE"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("CP_BCS_TPL_MESS_NOT_AVAILABLE"),
	"TYPE" => "STRING",
	"DEFAULT" => GetMessage("CP_BCS_TPL_MESS_NOT_AVAILABLE_DEFAULT")
);
$arTemplateParameters["RCM_TYPE"] = array(
	"PARENT" => "BIG_DATA_SETTINGS",
	"NAME" => GetMessage("CP_BCS_TPL_TYPE_TITLE"),
	"TYPE" => "LIST",
	"MULTIPLE" => "N",
	"VALUES" => array(
		// personal
		"personal" => GetMessage("CP_BCS_TPL_PERSONAL"),
		// general
		"bestsell" => GetMessage("CP_BCS_TPL_BESTSELLERS"),
		// item2item
		"similar_sell" => GetMessage("CP_BCS_TPL_SOLD_WITH"),
		"similar_view" => GetMessage("CP_BCS_TPL_VIEWED_WITH"),
		"similar" => GetMessage("CP_BCS_TPL_SIMILAR"),
		// randomly distributed
		"any_similar" => GetMessage("CP_BCS_TPL_SIMILAR_ANY"),
		"any_personal" => GetMessage("CP_BCS_TPL_PERSONAL_WBEST"),
		"any" => GetMessage("CP_BCS_TPL_RAND")
	),
	"DEFAULT" => "personal"
);
$arTemplateParameters["RCM_PROD_ID"] = array(
	"PARENT" => "BIG_DATA_SETTINGS",
	"NAME" => GetMessage("CP_BCS_TPL_PRODUCT_ID_PARAM"),
	"TYPE" => "STRING",
	"DEFAULT" => "={$_REQUEST["PRODUCT_ID"]}"
);
$arTemplateParameters["SHOW_FROM_SECTION"] = array(
	"PARENT" => "BIG_DATA_SETTINGS",
	"NAME" => GetMessage("CP_BCS_TPL_SHOW_FROM_SECTION"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N"
);
$arTemplateParameters["USE_ENHANCED_ECOMMERCE"] = array(
	"PARENT" => "ANALYTICS_SETTINGS",
	"NAME" => GetMessage("CP_BCS_TPL_USE_ENHANCED_ECOMMERCE"),
	"TYPE" => "CHECKBOX",
	"REFRESH" => "Y",
	"DEFAULT" => "N"
);

if(isset($arCurrentValues["USE_ENHANCED_ECOMMERCE"]) && $arCurrentValues["USE_ENHANCED_ECOMMERCE"] === "Y") {
	$arTemplateParameters["DATA_LAYER_NAME"] = array(
		"PARENT" => "ANALYTICS_SETTINGS",
		"NAME" => GetMessage("CP_BCS_TPL_DATA_LAYER_NAME"),
		"TYPE" => "STRING",
		"DEFAULT" => "dataLayer"
	);
	$arTemplateParameters["BRAND_PROPERTY"] = array(
		"PARENT" => "ANALYTICS_SETTINGS",
		"NAME" => GetMessage("CP_BCS_TPL_BRAND_PROPERTY"),
		"TYPE" => "LIST",
		"MULTIPLE" => "N",
		"DEFAULT" => "",
		"VALUES" => $defaultValue + $arAllPropList
	);
}

$arReviewsIBlock = array();
$rsIBlock = CIBlock::GetList(array("sort" => "asc"), array("TYPE" => $arCurrentValues["REVIEWS_IBLOCK_TYPE"], "ACTIVE" => "Y"));
while($arr = $rsIBlock->Fetch()) {
	$arReviewsIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}
unset($arr, $rsIBlock);

$arReviewsProperty_LNS = array();
$rsProps = CIBlockProperty::GetList(
	array("SORT" => "ASC", "ID" => "ASC"),
	array("IBLOCK_ID" => $arCurrentValues["REVIEWS_IBLOCK_ID"], "ACTIVE" => "Y")
);
while($arProp = $rsProps->Fetch()) {
	if(in_array($arProp["PROPERTY_TYPE"], array("L", "N", "S", "E"))) {
		$arReviewsProperty_LNS[$arProp["CODE"]] = "[".$arProp["CODE"]."] ".$arProp["NAME"];
	}
}
unset($arProp, $rsProps);

$arTemplateParameters["USE_REVIEW"] = array(	
	"NAME" => GetMessage("CP_BCS_TPL_USE_REVIEW"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "Y",
	"REFRESH" => "Y",
);

if(isset($arCurrentValues["USE_REVIEW"]) && $arCurrentValues["USE_REVIEW"] === "Y") {
	$arTemplateParameters["REVIEWS_IBLOCK_TYPE"] = array(				
		"NAME" => GetMessage("CP_BCS_TPL_REVIEWS_IBLOCK_TYPE"),
		"TYPE" => "LIST",		
		"REFRESH" => "Y",
		"VALUES" => CIBlockParameters::GetIBlockTypes(),
	);
	$arTemplateParameters["REVIEWS_IBLOCK_ID"] = array(				
		"NAME" => GetMessage("CP_BCS_TPL_REVIEWS_IBLOCK_ID"),
		"TYPE" => "LIST",
		"REFRESH" => "Y",		
		"VALUES" => $arReviewsIBlock,
		"ADDITIONAL_VALUES" => "Y",
	);
	$arTemplateParameters["REVIEWS_NEWS_COUNT"] = array(
		"NAME" => GetMessage("CP_BCS_TPL_REVIEWS_NEWS_COUNT"),
		"TYPE" => "STRING",
		"DEFAULT" => "5",
	);	
	$arTemplateParameters["REVIEWS_SORT_BY1"] = array(
		"NAME" => GetMessage("CP_BCS_TPL_REVIEWS_SORT_BY1"),
		"TYPE" => "LIST",
		"DEFAULT" => "sort",
		"VALUES" => CIBlockParameters::GetElementSortFields(
			array("SHOWS", "SORT", "TIMESTAMP_X", "NAME", "ID", "ACTIVE_FROM", "ACTIVE_TO"),
			array("KEY_LOWERCASE" => "Y")
		),
		"ADDITIONAL_VALUES" => "Y",
	);	
	$arTemplateParameters["REVIEWS_SORT_ORDER1"] = array(
		"NAME" => GetMessage("CP_BCS_TPL_REVIEWS_SORT_ORDER1"),
		"TYPE" => "LIST",
		"DEFAULT" => "asc",
		"VALUES" => array(
			"asc" => GetMessage("CP_BCS_TPL_REVIEWS_SORT_ORDER_ASC"),
			"desc" => GetMessage("CP_BCS_TPL_REVIEWS_SORT_ORDER_DESC"),
		),
		"ADDITIONAL_VALUES" => "Y",
	);	
	$arTemplateParameters["REVIEWS_SORT_BY2"] = array(
		"NAME" => GetMessage("CP_BCS_TPL_REVIEWS_SORT_BY2"),
		"TYPE" => "LIST",
		"DEFAULT" => "active_from",
		"VALUES" => CIBlockParameters::GetElementSortFields(
			array("SHOWS", "SORT", "TIMESTAMP_X", "NAME", "ID", "ACTIVE_FROM", "ACTIVE_TO"),
			array("KEY_LOWERCASE" => "Y")
		),
		"ADDITIONAL_VALUES" => "Y",
	);	
	$arTemplateParameters["REVIEWS_SORT_ORDER2"] = array(
		"NAME" => GetMessage("CP_BCS_TPL_REVIEWS_SORT_ORDER2"),
		"TYPE" => "LIST",
		"DEFAULT" => "desc",
		"VALUES" => array(
			"asc" => GetMessage("CP_BCS_TPL_REVIEWS_SORT_ORDER_ASC"),
			"desc" => GetMessage("CP_BCS_TPL_REVIEWS_SORT_ORDER_DESC"),
		),
		"ADDITIONAL_VALUES" => "Y",
	);
	$arTemplateParameters["REVIEWS_ACTIVE_DATE_FORMAT"] = CIBlockParameters::GetDateFormat(GetMessage("CP_BCS_TPL_REVIEWS_ACTIVE_DATE_FORMAT"), "ADDITIONAL_SETTINGS");
	$arTemplateParameters["REVIEWS_PROPERTY_CODE"] = array(
		"NAME" => GetMessage("CP_BCS_TPL_REVIEWS_PROPERTY_CODE"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arReviewsProperty_LNS,
		"ADDITIONAL_VALUES" => "Y",
	);
	$arTemplateParameters["MESS_REVIEWS_TAB"] = array(
		"NAME" => GetMessage("CP_BCS_TPL_MESS_REVIEWS_TAB"),
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage("CP_BCS_TPL_MESS_REVIEWS_TAB_DEFAULT")
	);
}

$arTemplateParameters["DETAIL_ADD_PICT_PROP"] = array(
	"NAME" => GetMessage("CP_BCS_TPL_DETAIL_ADD_PICT_PROP"),
	"TYPE" => "LIST",
	"MULTIPLE" => "N",
	"ADDITIONAL_VALUES" => "N",
	"REFRESH" => "N",
	"DEFAULT" => "-",
	"VALUES" => $arFilePropList
);

if($boolSKU) {
	$arTemplateParameters["DETAIL_OFFER_ADD_PICT_PROP"] = array(
		"NAME" => GetMessage("CP_BCS_TPL_DETAIL_OFFER_ADD_PICT_PROP"),
		"TYPE" => "LIST",
		"MULTIPLE" => "N",
		"ADDITIONAL_VALUES" => "N",
		"REFRESH" => "N",
		"DEFAULT" => "-",
		"VALUES" => $arFileOfferPropList
	);
}

$arTemplateParameters["DETAIL_USE_RATIO_IN_RANGES"] = array(
	"NAME" => GetMessage("CP_BCS_TPL_DETAIL_USE_RATIO_IN_RANGES"),
	"TYPE" => "CHECKBOX",
	"HIDDEN" => isset($arCurrentValues["USE_PRICE_COUNT"]) && $arCurrentValues["USE_PRICE_COUNT"] === "Y" ? "N" : "Y",
	"DEFAULT" => "Y"
);

$arTemplateParameters["DETAIL_PROPERTY_CODE"] = array(
	"NAME" => GetMessage("CP_BCS_TPL_DETAIL_PROPERTY_CODE"),
	"TYPE" => "LIST",
	"MULTIPLE" => "Y",
	"ADDITIONAL_VALUES" => "Y",
	"VALUES" => $arAllPropList,
);

if(!empty($arCurrentValues["DETAIL_PROPERTY_CODE"])) {
	$selected = array();
	foreach($arCurrentValues["DETAIL_PROPERTY_CODE"] as $code) {
		if(isset($arAllPropList[$code])) {
			$selected[$code] = $arAllPropList[$code];
		}
	}

	$arTemplateParameters["DETAIL_MAIN_BLOCK_PROPERTY_CODE"] = array(
		"NAME" => GetMessage("CP_BCS_TPL_DETAIL_MAIN_BLOCK_PROPERTY_CODE"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"SIZE" => (count($selected) > 5 ? 8 : 3),
		"VALUES" => $selected
	);
}

if($boolSKU) {
	$arTemplateParameters["DETAIL_OFFERS_FIELD_CODE"] = CIBlockParameters::GetFieldCode(GetMessage("CP_BCS_TPL_DETAIL_OFFERS_FIELD_CODE"), "ADDITIONAL_SETTINGS");

	$arTemplateParameters["DETAIL_OFFERS_PROPERTY_CODE"] = array(
		"NAME" => GetMessage("CP_BCS_TPL_DETAIL_OFFERS_PROPERTY_CODE"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"ADDITIONAL_VALUES" => "Y",
		"VALUES" => $arAllOfferPropList,
	);
	
	if(!empty($arCurrentValues["DETAIL_OFFERS_PROPERTY_CODE"])) {
		$selected = array();
		foreach($arCurrentValues["DETAIL_OFFERS_PROPERTY_CODE"] as $code) {
			if(isset($arAllOfferPropList[$code])) {
				$selected[$code] = $arAllOfferPropList[$code];
			}
		}

		$arTemplateParameters["DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE"] = array(
			"NAME" => GetMessage("CP_BCS_TPL_DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"SIZE" => (count($selected) > 5 ? 8 : 3),
			"VALUES" => $selected
		);
	}
}

$arTemplateParameters["DETAIL_IMAGE_RESOLUTION"] = array(
	"NAME" => GetMessage("CP_BCS_TPL_DETAIL_IMAGE_RESOLUTION"),
	"TYPE" => "LIST",
	"VALUES" => array(
		"16by9" => GetMessage("CP_BCS_TPL_DETAIL_IMAGE_RESOLUTION_16_BY_9"),
		"1by1" => GetMessage("CP_BCS_TPL_DETAIL_IMAGE_RESOLUTION_1_BY_1")
	),
	"DEFAULT" => "16by9"
);

$arTemplateParameters["DETAIL_ADD_DETAIL_TO_SLIDER"] = array(
	"NAME" => GetMessage("CP_BCS_TPL_DETAIL_ADD_DETAIL_TO_SLIDER"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N"
);

$arTemplateParameters["DETAIL_DETAIL_PICTURE_MODE"] = array(
	"NAME" => GetMessage("CP_BCS_TPL_DETAIL_DETAIL_PICTURE_MODE"),
	"TYPE" => "LIST",
	"MULTIPLE" => "Y",
	"DEFAULT" => array("POPUP", "MAGNIFIER"),
	"VALUES" => array(
		"POPUP" => GetMessage("CP_BCS_TPL_DETAIL_DETAIL_PICTURE_MODE_POPUP"),
		"MAGNIFIER" => GetMessage("CP_BCS_TPL_DETAIL_DETAIL_PICTURE_MODE_MAGNIFIER"),
	)
);

$arTemplateParameters["DETAIL_SHOW_SLIDER"] = array(
	"NAME" => GetMessage("CP_BCS_TPL_DETAIL_SHOW_SLIDER"),
	"TYPE" => "CHECKBOX",
	"MULTIPLE" => "N",
	"REFRESH" => "Y",
	"DEFAULT" => "N"
);

if(isset($arCurrentValues["DETAIL_SHOW_SLIDER"]) && $arCurrentValues["DETAIL_SHOW_SLIDER"] === "Y") {
	$arTemplateParameters["DETAIL_SLIDER_INTERVAL"] = array(
		"NAME" => GetMessage("CP_BCS_TPL_DETAIL_SLIDER_INTERVAL"),
		"TYPE" => "TEXT",
		"MULTIPLE" => "N",
		"REFRESH" => "N",
		"DEFAULT" => "5000"
	);

	$arTemplateParameters["DETAIL_SLIDER_PROGRESS"] = array(
		"NAME" => GetMessage("CP_BCS_TPL_DETAIL_SLIDER_PROGRESS"),
		"TYPE" => "CHECKBOX",
		"MULTIPLE" => "N",
		"REFRESH" => "N",
		"DEFAULT" => "N"
	);
}

$arTemplateParameters["USE_GIFTS_DETAIL"] = array(
	"NAME" => GetMessage("CP_BCS_TPL_USE_GIFTS_DETAIL"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "Y",
	"REFRESH" => "Y",
);

if($arCurrentValues["USE_GIFTS_DETAIL"] === "Y") {
	$arTemplateParameters["GIFTS_DETAIL_PAGE_ELEMENT_COUNT"] = array(
		"NAME" => GetMessage("CP_BCS_TPL_GIFTS_DETAIL_PAGE_ELEMENT_COUNT"),
		"TYPE" => "STRING",
		"DEFAULT" => "5",
	);
	$arTemplateParameters["GIFTS_DETAIL_HIDE_BLOCK_TITLE"] = array(
		"NAME" => GetMessage("CP_BCS_TPL_GIFTS_DETAIL_HIDE_BLOCK_TITLE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "",
	);
	$arTemplateParameters["GIFTS_DETAIL_BLOCK_TITLE"] = array(
		"NAME" => GetMessage("CP_BCS_TPL_GIFTS_DETAIL_BLOCK_TITLE"),
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage("CP_BCS_TPL_GIFTS_DETAIL_BLOCK_TITLE_DEFAULT"),
	);
	$arTemplateParameters["GIFTS_DETAIL_TEXT_LABEL_GIFT"] = array(
		"NAME" => GetMessage("CP_BCS_TPL_GIFTS_DETAIL_TEXT_LABEL_GIFT"),
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage("CP_BCS_TPL_GIFTS_DETAIL_TEXT_LABEL_GIFT_DEFAULT"),
	);
	$arTemplateParameters["GIFTS_MESS_BTN_BUY"] = array(
		"NAME" => GetMessage("CP_BCS_TPL_GIFTS_MESS_BTN_BUY"),
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage("CP_BCS_TPL_GIFTS_MESS_BTN_BUY_DEFAULT")
	);
}

$arTemplateParameters["USE_STORE"] = array(
	"NAME" => GetMessage("CP_BCS_TPL_USE_STORE"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N",
	"REFRESH" => "Y",
);

if($boolCatalog && $arCurrentValues["USE_STORE"] === "Y") {
	$arStore = array();
	$storeIterator = CCatalogStore::GetList(
		array(),
		array("ISSUING_CENTER" => "Y"),
		false,
		false,
		array("ID", "TITLE")
	);
	while($store = $storeIterator->GetNext())
		$arStore[$store["ID"]] = "[".$store["ID"]."] ".$store["TITLE"];

	$userFields = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("CAT_STORE", 0, LANGUAGE_ID);
	$propertyUF = array();

	foreach($userFields as $fieldName => $userField)
		$propertyUF[$fieldName] = $userField["LIST_COLUMN_LABEL"] ? $userField["LIST_COLUMN_LABEL"] : $fieldName;

	$arTemplateParameters["STORES"] = array(
		"NAME" => GetMessage("CP_BCS_TPL_STORES"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arStore,
		"ADDITIONAL_VALUES" => "Y"
	);
	$arTemplateParameters["USE_MIN_AMOUNT"] = array(
		"NAME" => GetMessage("CP_BCS_TPL_USE_MIN_AMOUNT"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"REFRESH" => "Y",
	);
	$arTemplateParameters["USER_FIELDS"] = array(
			"NAME" => GetMessage("CP_BCS_TPL_USER_FIELDS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $propertyUF,
		);
	$arTemplateParameters["FIELDS"] = array(
		"NAME" => GetMessage("CP_BCS_TPL_FIELDS"),
		"TYPE"  => "LIST",
		"MULTIPLE" => "Y",
		"ADDITIONAL_VALUES" => "Y",
		"VALUES" => array(
			"TITLE"  => GetMessage("CP_BCS_TPL_TITLE"),
			"ADDRESS"  => GetMessage("CP_BCS_TPL_ADDRESS"),
			"DESCRIPTION"  => GetMessage("CP_BCS_TPL_DESCRIPTION"),
			"PHONE"  => GetMessage("CP_BCS_TPL_PHONE"),
			"SCHEDULE"  => GetMessage("CP_BCS_TPL_SCHEDULE"),
			"EMAIL"  => GetMessage("CP_BCS_TPL_EMAIL"),
			"IMAGE_ID"  => GetMessage("CP_BCS_TPL_IMAGE_ID"),
			"COORDINATES"  => GetMessage("CP_BCS_TPL_COORDINATES"),
		)
	);
	if($arCurrentValues["USE_MIN_AMOUNT"] != "N") {
		$arTemplateParameters["MIN_AMOUNT"] = array(
			"NAME" => GetMessage("CP_BCS_TPL_MIN_AMOUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => 5,
		);
	}
	$arTemplateParameters["SHOW_EMPTY_STORE"] = array(
		"NAME" => GetMessage("CP_BCS_TPL_SHOW_EMPTY_STORE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	);
	$arTemplateParameters["SHOW_GENERAL_STORE_INFORMATION"] = array(
		"NAME" => GetMessage("CP_BCS_TPL_SHOW_GENERAL_STORE_INFORMATION"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N"
	);
	$arTemplateParameters["STORE_PATH"] = array(
		"NAME" => GetMessage("CP_BCS_TPL_STORE_PATH"),
		"TYPE" => "STRING",
		"DEFAULT" => "/store/#store_id#",
	);
	$arTemplateParameters["MAIN_TITLE"] = array(
		"NAME" => GetMessage("CP_BCS_TPL_MAIN_TITLE"),
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage("CP_BCS_TPL_MAIN_TITLE_DEFAULT"),
	);
}

$arTemplateParameters["SET_ITEMS_COUNT"] = array(
	"NAME" => GetMessage("CP_BCS_TPL_SET_ITEMS_COUNT"),
	"TYPE" => "STRING",
	"DEFAULT" => "3"
);

$arObjectsReviewsIBlock = array();
$rsIBlock = CIBlock::GetList(array("sort" => "asc"), array("TYPE" => $arCurrentValues["OBJECTS_REVIEWS_IBLOCK_TYPE"], "ACTIVE" => "Y"));
while($arr = $rsIBlock->Fetch()) {
	$arObjectsReviewsIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}
unset($arr, $rsIBlock);

$arTemplateParameters["OBJECTS_USE_REVIEW"] = array(
	"NAME" => GetMessage("CP_BCS_TPL_OBJECTS_USE_REVIEW"),
	"TYPE" => "CHECKBOX",
	"REFRESH" => "Y",
	"DEFAULT" => "Y"
);

if(isset($arCurrentValues["OBJECTS_USE_REVIEW"]) && $arCurrentValues["OBJECTS_USE_REVIEW"] === "Y") {
	$arTemplateParameters["OBJECTS_REVIEWS_IBLOCK_TYPE"] = array(
		"NAME" => GetMessage("CP_BCS_TPL_OBJECTS_REVIEWS_IBLOCK_TYPE"),
		"TYPE" => "LIST",		
		"REFRESH" => "Y",
		"VALUES" => CIBlockParameters::GetIBlockTypes(),
	);
	
	$arTemplateParameters["OBJECTS_REVIEWS_IBLOCK_ID"] = array(
		"NAME" => GetMessage("CP_BCS_TPL_OBJECTS_REVIEWS_IBLOCK_ID"),
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
	"NAME" => GetMessage("CP_BCS_TPL_CONTACTS_IBLOCK_TYPE"),
	"TYPE" => "LIST",		
	"REFRESH" => "Y",
	"VALUES" => CIBlockParameters::GetIBlockTypes(),
);

$arTemplateParameters["CONTACTS_IBLOCK_ID"] = array(
	"NAME" => GetMessage("CP_BCS_TPL_CONTACTS_IBLOCK_ID"),
	"TYPE" => "LIST",
	"REFRESH" => "Y",		
	"VALUES" => $arContactsIBlock,
	"ADDITIONAL_VALUES" => "Y",
);

$arTemplateParameters["CONTACTS_USE_REVIEW"] = array(
	"NAME" => GetMessage("CP_BCS_TPL_CONTACTS_USE_REVIEW"),
	"TYPE" => "CHECKBOX",
	"REFRESH" => "Y",
	"DEFAULT" => "Y"
);

if(isset($arCurrentValues["CONTACTS_USE_REVIEW"]) && $arCurrentValues["CONTACTS_USE_REVIEW"] === "Y") {
	$arTemplateParameters["CONTACTS_REVIEWS_IBLOCK_TYPE"] = array(
		"NAME" => GetMessage("CP_BCS_TPL_CONTACTS_REVIEWS_IBLOCK_TYPE"),
		"TYPE" => "LIST",		
		"REFRESH" => "Y",
		"VALUES" => CIBlockParameters::GetIBlockTypes(),
	);
	
	$arTemplateParameters["CONTACTS_REVIEWS_IBLOCK_ID"] = array(
		"NAME" => GetMessage("CP_BCS_TPL_CONTACTS_REVIEWS_IBLOCK_ID"),
		"TYPE" => "LIST",
		"REFRESH" => "Y",		
		"VALUES" => $arContactsReviewsIBlock,
		"ADDITIONAL_VALUES" => "Y",
	);

	$arTemplateParameters["CONTACTS_REVIEWS_PAGE_LINK"] = array(
		"NAME" => GetMessage("CP_BCS_TPL_CONTACTS_REVIEWS_PAGE_LINK"),
		"TYPE" => "STRING",
		"DEFAULT" => '={SITE_DIR."about/reviews/"}'
	);
}