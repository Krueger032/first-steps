<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Web\Json;

if(!Loader::includeModule("iblock"))
	return;

$boolCatalog = Loader::includeModule("catalog");
CBitrixComponent::includeComponentClass("bitrix:catalog.section");
CBitrixComponent::includeComponentClass("bitrix:catalog.element");

$arSKU = false;
$boolSKU = false;
if($boolCatalog && (isset($arCurrentValues["IBLOCK_ID"]) && (int)$arCurrentValues["IBLOCK_ID"]) > 0) {
	$arSKU = CCatalogSKU::GetInfoByProductIBlock($arCurrentValues["IBLOCK_ID"]);
	$boolSKU = !empty($arSKU) && is_array($arSKU);
}

$defaultValue = array("-" => GetMessage("CP_BC_TPL_PROP_EMPTY"));

$documentRoot = Loader::getDocumentRoot();

$arTemplateParameters["USER_CONSENT"] = array(
	"HIDDEN" => "Y"
);
$arTemplateParameters["USER_CONSENT_ID"] = array(
	"HIDDEN" => "Y"
);
$arTemplateParameters["USER_CONSENT_IS_CHECKED"] = array(
	"HIDDEN" => "Y"
);
$arTemplateParameters["USER_CONSENT_IS_LOADED"] = array(
	"HIDDEN" => "Y"
);

$arTemplateParameters["SECTION_TOP_DEPTH"] = array(
	"HIDDEN" => "Y"
);
$arTemplateParameters["SECTIONS_HIDE_SECTION_NAME"] = array(
	"PARENT" => "SECTIONS_SETTINGS",
	"NAME" => GetMessage("CPT_BC_SECTIONS_HIDE_SECTION_NAME"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N"
);

$arTemplateParameters["INSTANT_RELOAD"] = array(
	"PARENT" => "FILTER_SETTINGS",
	"NAME" => GetMessage("CPT_BC_INSTANT_RELOAD"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N",
);

$arTemplateParameters["SEARCH_PAGE_RESULT_COUNT"] = array(
	"PARENT" => "SEARCH_SETTINGS",
	"NAME" => GetMessage("CP_BC_TPL_SEARCH_PAGE_RESULT_COUNT"),
	"TYPE" => "STRING",
	"DEFAULT" => "50",
);
$arTemplateParameters["SEARCH_RESTART"] = array(
	"PARENT" => "SEARCH_SETTINGS",
	"NAME" => GetMessage("CP_BC_TPL_SEARCH_RESTART"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N",
);
$arTemplateParameters["SEARCH_NO_WORD_LOGIC"] = array(
	"PARENT" => "SEARCH_SETTINGS",
	"NAME" => GetMessage("CP_BC_TPL_SEARCH_NO_WORD_LOGIC"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "Y",
);
$arTemplateParameters["SEARCH_USE_LANGUAGE_GUESS"] = array(
	"PARENT" => "SEARCH_SETTINGS",
	"NAME" => GetMessage("CP_BC_TPL_SEARCH_USE_LANGUAGE_GUESS"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "Y",
);
$arTemplateParameters["SEARCH_CHECK_DATES"] = array(
	"PARENT" => "SEARCH_SETTINGS",
	"NAME" => GetMessage("CP_BC_TPL_SEARCH_CHECK_DATES"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "Y",
);

$arTemplateParameters["SECTION_BACKGROUND_IMAGE"] = array(
	"HIDDEN" => "Y"
);

$arTemplateParameters["DETAIL_BACKGROUND_IMAGE"] = array(
	"HIDDEN" => "Y"
);

$arAllPropList = array();
$arFilePropList = $defaultValue;

if(isset($arCurrentValues["IBLOCK_ID"]) && (int)$arCurrentValues["IBLOCK_ID"] > 0) {
	$rsProps = CIBlockProperty::GetList(
		array("SORT" => "ASC", "ID" => "ASC"),
		array("IBLOCK_ID" => $arCurrentValues["IBLOCK_ID"], "ACTIVE" => "Y")
	);
	while($arProp = $rsProps->Fetch()) {
		$strPropName = "[".$arProp["ID"]."]".("" != $arProp["CODE"] ? "[".$arProp["CODE"]."]" : "")." ".$arProp["NAME"];
		if("" == $arProp["CODE"]) {
			$arProp["CODE"] = $arProp["ID"];
		}

		$arAllPropList[$arProp["CODE"]] = $strPropName;

		if("F" == $arProp["PROPERTY_TYPE"]) {
			$arFilePropList[$arProp["CODE"]] = $strPropName;
		}
	}
	
	$lineElementCount = (int)$arCurrentValues["LINE_ELEMENT_COUNT"] ?: 4;
	$pageElementCount = (int)$arCurrentValues["PAGE_ELEMENT_COUNT"] ?: 12;

	$arTemplateParameters["LIST_PRODUCT_ROW_VARIANTS"] = array(
		"PARENT" => "LIST_SETTINGS",
		"NAME" => GetMessage("CP_BC_TPL_PRODUCT_ROW_VARIANTS"),
		"TYPE" => "CUSTOM",
		"BIG_DATA" => "Y",
		"COUNT_PARAM_NAME" => "PAGE_ELEMENT_COUNT",
		"JS_FILE" => CatalogSectionComponent::getSettingsScript($templateFolder, "dragdrop_add"),
		"JS_EVENT" => "initDraggableAddControl",
		"JS_MESSAGES" => Json::encode(array(
			"variant" => GetMessage("CP_BC_TPL_SETTINGS_VARIANT"),
			"delete" => GetMessage("CP_BC_TPL_SETTINGS_DELETE"),
			"quantity" => GetMessage("CP_BC_TPL_SETTINGS_QUANTITY"),
			"quantityBigData" => GetMessage("CP_BC_TPL_SETTINGS_QUANTITY_BIG_DATA")
		)),
		"JS_DATA" => Json::encode(CatalogSectionComponent::getTemplateVariantsMap()),
		"DEFAULT" => Json::encode(CatalogSectionComponent::predictRowVariants($lineElementCount, $pageElementCount))
	);
	
	$arTemplateParameters["ADD_PICT_PROP"] = array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("CP_BC_TPL_ADD_PICT_PROP"),
		"TYPE" => "LIST",
		"MULTIPLE" => "N",
		"ADDITIONAL_VALUES" => "N",
		"REFRESH" => "N",
		"DEFAULT" => "-",
		"VALUES" => $arFilePropList
	);
	
	if($boolSKU) {
		$arTemplateParameters["PRODUCT_DISPLAY_MODE"] = array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("CP_BC_TPL_PRODUCT_DISPLAY_MODE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"ADDITIONAL_VALUES" => "N",
			"REFRESH" => "Y",
			"DEFAULT" => "N",
			"VALUES" => array(
				"N" => GetMessage("CP_BC_TPL_DML_SIMPLE"),
				"Y" => GetMessage("CP_BC_TPL_DML_EXT")
			)
		);

		$arAllOfferPropList = array();
		$arFileOfferPropList = array(
			"-" => GetMessage("CP_BC_TPL_PROP_EMPTY")
		);
		$arTreeOfferPropList = array(
			"-" => GetMessage("CP_BC_TPL_PROP_EMPTY")
		);
		
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
		$arTemplateParameters["OFFER_ADD_PICT_PROP"] = array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("CP_BC_TPL_OFFER_ADD_PICT_PROP"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"ADDITIONAL_VALUES" => "N",
			"REFRESH" => "N",
			"DEFAULT" => "-",
			"VALUES" => $arFileOfferPropList
		);
		$arTemplateParameters["OFFER_TREE_PROPS"] = array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("CP_BC_TPL_OFFER_TREE_PROPS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "N",
			"REFRESH" => "N",
			"DEFAULT" => "-",
			"VALUES" => $arTreeOfferPropList
		);
	}
}

$arCurrentValues["DETAIL_PROPERTY_CODE"] = isset($arCurrentValues["DETAIL_PROPERTY_CODE"]) ? $arCurrentValues["DETAIL_PROPERTY_CODE"] : array();
if(!empty($arCurrentValues["DETAIL_PROPERTY_CODE"])) {
	$selected = array();

	foreach($arCurrentValues["DETAIL_PROPERTY_CODE"] as $code) {
		if(isset($arAllPropList[$code])) {
			$selected[$code] = $arAllPropList[$code];
		}
	}

	$arTemplateParameters["DETAIL_MAIN_BLOCK_PROPERTY_CODE"] = array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("CP_BC_TPL_MAIN_BLOCK_PROPERTY_CODE"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"SIZE" => (count($selected) > 5 ? 8 : 3),
		"VALUES" => $selected
	);
}

$arCurrentValues["DETAIL_OFFERS_PROPERTY_CODE"] = isset($arCurrentValues["DETAIL_OFFERS_PROPERTY_CODE"]) ? $arCurrentValues["DETAIL_OFFERS_PROPERTY_CODE"] : array();
if(!empty($arCurrentValues["DETAIL_OFFERS_PROPERTY_CODE"])) {
	$selected = array();

	foreach($arCurrentValues["DETAIL_OFFERS_PROPERTY_CODE"] as $code) {
		if(isset($arAllOfferPropList[$code])) {
			$selected[$code] = $arAllOfferPropList[$code];
		}
	}

	$arTemplateParameters["DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE"] = array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("CP_BC_TPL_MAIN_BLOCK_OFFERS_PROPERTY_CODE"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"SIZE" => (count($selected) > 5 ? 8 : 3),
		"VALUES" => $selected
	);
}

$arTemplateParameters["DETAIL_IMAGE_RESOLUTION"] = array(
	"PARENT" => "DETAIL_SETTINGS",
	"NAME" => GetMessage("CP_BC_TPL_DETAIL_IMAGE_RESOLUTION"),
	"TYPE" => "LIST",
	"VALUES" => array(
		"16by9" => GetMessage("CP_BC_TPL_DETAIL_IMAGE_RESOLUTION_16_BY_9"),
		"1by1" => GetMessage("CP_BC_TPL_DETAIL_IMAGE_RESOLUTION_1_BY_1")
	),
	"DEFAULT" => "16by9"
);
$arTemplateParameters["DETAIL_SHOW_SLIDER"] = array(
	"PARENT" => "DETAIL_SETTINGS",
	"NAME" => GetMessage("CP_BC_TPL_DETAIL_SHOW_SLIDER"),
	"TYPE" => "CHECKBOX",
	"MULTIPLE" => "N",
	"REFRESH" => "Y",
	"DEFAULT" => "N"
);
if(isset($arCurrentValues["DETAIL_SHOW_SLIDER"]) && $arCurrentValues["DETAIL_SHOW_SLIDER"] === "Y") {
	$arTemplateParameters["DETAIL_SLIDER_INTERVAL"] = array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("CP_BC_TPL_DETAIL_SLIDER_INTERVAL"),
		"TYPE" => "TEXT",
		"MULTIPLE" => "N",
		"REFRESH" => "N",
		"DEFAULT" => "5000"
	);
	$arTemplateParameters["DETAIL_SLIDER_PROGRESS"] = array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("CP_BC_TPL_DETAIL_SLIDER_PROGRESS"),
		"TYPE" => "CHECKBOX",
		"MULTIPLE" => "N",
		"REFRESH" => "N",
		"DEFAULT" => "N"
	);
}
$arTemplateParameters["DETAIL_DETAIL_PICTURE_MODE"] = array(
	"PARENT" => "DETAIL_SETTINGS",
	"NAME" => GetMessage("CP_BC_TPL_DETAIL_DETAIL_PICTURE_MODE"),
	"TYPE" => "LIST",
	"MULTIPLE" => "Y",
	"DEFAULT" => array("POPUP", "MAGNIFIER"),
	"VALUES" => array(
		"POPUP" => GetMessage("DETAIL_DETAIL_PICTURE_MODE_POPUP"),
		"MAGNIFIER" => GetMessage("DETAIL_DETAIL_PICTURE_MODE_MAGNIFIER"),
	)
);
$arTemplateParameters["DETAIL_ADD_DETAIL_TO_SLIDER"] = array(
	"PARENT" => "DETAIL_SETTINGS",
	"NAME" => GetMessage("CP_BC_TPL_DETAIL_ADD_DETAIL_TO_SLIDER"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N"
);

if($boolCatalog) {
	$arTemplateParameters["USE_COMMON_SETTINGS_BASKET_POPUP"] = array(
		"PARENT" => "BASKET",
		"NAME" => GetMessage("CP_BC_TPL_USE_COMMON_SETTINGS_BASKET_POPUP"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"REFRESH" => "Y"
	);
	$useCommonSettingsBasketPopup = (
		isset($arCurrentValues["USE_COMMON_SETTINGS_BASKET_POPUP"])
		&& $arCurrentValues["USE_COMMON_SETTINGS_BASKET_POPUP"] == "Y"
	);
	$addToBasketActions = array(
		"BUY" => GetMessage("ADD_TO_BASKET_ACTION_BUY"),
		"ADD" => GetMessage("ADD_TO_BASKET_ACTION_ADD")
	);
	$arTemplateParameters["COMMON_ADD_TO_BASKET_ACTION"] = array(
		"PARENT" => "BASKET",
		"NAME" => GetMessage("CP_BC_TPL_COMMON_ADD_TO_BASKET_ACTION"),
		"TYPE" => "LIST",
		"VALUES" => $addToBasketActions,
		"DEFAULT" => "ADD",
		"REFRESH" => "N",
		"HIDDEN" => ($useCommonSettingsBasketPopup ? "N" : "Y")
	);	
	$arTemplateParameters["MESS_PRICE_RANGES_TITLE"] = array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("CP_BC_TPL_MESS_PRICE_RANGES_TITLE"),
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage("CP_BC_TPL_MESS_PRICE_RANGES_TITLE_DEFAULT")
	);
	$arTemplateParameters["MESS_DESCRIPTION_TAB"] = array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("CP_BC_TPL_MESS_DESCRIPTION_TAB"),
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage("CP_BC_TPL_MESS_DESCRIPTION_TAB_DEFAULT")
	);
	$arTemplateParameters["MESS_PROPERTIES_TAB"] = array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("CP_BC_TPL_MESS_PROPERTIES_TAB"),
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage("CP_BC_TPL_MESS_PROPERTIES_TAB_DEFAULT")
	);
	$arTemplateParameters["SECTION_ADD_TO_BASKET_ACTION"] = array(
		"PARENT" => "BASKET",
		"NAME" => GetMessage("CP_BC_TPL_SECTION_ADD_TO_BASKET_ACTION"),
		"TYPE" => "LIST",
		"VALUES" => $addToBasketActions,
		"DEFAULT" => "ADD",
		"REFRESH" => "N",
		"HIDDEN" => (!$useCommonSettingsBasketPopup ? "N" : "Y")
	);
	$arTemplateParameters["DETAIL_ADD_TO_BASKET_ACTION"] = array(
		"PARENT" => "BASKET",
		"NAME" => GetMessage("CP_BC_TPL_DETAIL_ADD_TO_BASKET_ACTION"),
		"TYPE" => "LIST",
		"VALUES" => $addToBasketActions,
		"DEFAULT" => "BUY",
		"REFRESH" => "Y",
		"MULTIPLE" => "Y",
		"HIDDEN" => (!$useCommonSettingsBasketPopup ? "N" : "Y")
	);

	if(!$useCommonSettingsBasketPopup && !empty($arCurrentValues["DETAIL_ADD_TO_BASKET_ACTION"])) {
		$selected = array();

		if(!is_array($arCurrentValues["DETAIL_ADD_TO_BASKET_ACTION"])) {
			$arCurrentValues["DETAIL_ADD_TO_BASKET_ACTION"] = array($arCurrentValues["DETAIL_ADD_TO_BASKET_ACTION"]);
		}

		foreach($arCurrentValues["DETAIL_ADD_TO_BASKET_ACTION"] as $action) {
			if(isset($addToBasketActions[$action])) {
				$selected[$action] = $addToBasketActions[$action];
			}
		}

		$arTemplateParameters["DETAIL_ADD_TO_BASKET_ACTION_PRIMARY"] = array(
			"PARENT" => "BASKET",
			"NAME" => GetMessage("CP_BC_TPL_DETAIL_ADD_TO_BASKET_ACTION_PRIMARY"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $selected,
			"DEFAULT" => "BUY",
			"REFRESH" => "N"
		);
		unset($selected);
	}
	
	$arTemplateParameters["PRODUCT_SUBSCRIPTION"] = array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("CP_BC_TPL_PRODUCT_SUBSCRIPTION"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	);
	$arTemplateParameters["SHOW_DISCOUNT_PERCENT"] = array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("CP_BC_TPL_SHOW_DISCOUNT_PERCENT"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"REFRESH" => "Y",
	);	
	$arTemplateParameters["SHOW_OLD_PRICE"] = array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("CP_BC_TPL_SHOW_OLD_PRICE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
	);
	$arTemplateParameters["SHOW_MAX_QUANTITY"] = array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("CP_BC_TPL_SHOW_MAX_QUANTITY"),
		"TYPE" => "LIST",
		"REFRESH" => "Y",
		"MULTIPLE" => "N",
		"VALUES" => array(
			"N" => GetMessage("CP_BC_TPL_SHOW_MAX_QUANTITY_N"),
			"Y" => GetMessage("CP_BC_TPL_SHOW_MAX_QUANTITY_Y"),
			"M" => GetMessage("CP_BC_TPL_SHOW_MAX_QUANTITY_M")
		),
		"DEFAULT" => array("N")
	);
	if(isset($arCurrentValues["SHOW_MAX_QUANTITY"])) {
		if($arCurrentValues["SHOW_MAX_QUANTITY"] !== "N") {
			$arTemplateParameters["MESS_SHOW_MAX_QUANTITY"] = array(
				"PARENT" => "VISUAL",
				"NAME" => GetMessage("CP_BC_TPL_MESS_SHOW_MAX_QUANTITY"),
				"TYPE" => "STRING",
				"DEFAULT" => GetMessage("CP_BC_TPL_MESS_SHOW_MAX_QUANTITY_DEFAULT")
			);
		}
		if($arCurrentValues["SHOW_MAX_QUANTITY"] === "M") {
			$arTemplateParameters["RELATIVE_QUANTITY_FACTOR"] = array(
				"PARENT" => "VISUAL",
				"NAME" => GetMessage("CP_BC_TPL_RELATIVE_QUANTITY_FACTOR"),
				"TYPE" => "STRING",
				"DEFAULT" => "5"
			);
			$arTemplateParameters["MESS_RELATIVE_QUANTITY_MANY"] = array(
				"PARENT" => "VISUAL",
				"NAME" => GetMessage("CP_BC_TPL_MESS_RELATIVE_QUANTITY_MANY"),
				"TYPE" => "STRING",
				"DEFAULT" => GetMessage("CP_BC_TPL_MESS_RELATIVE_QUANTITY_MANY_DEFAULT")
			);
			$arTemplateParameters["MESS_RELATIVE_QUANTITY_FEW"] = array(
				"PARENT" => "VISUAL",
				"NAME" => GetMessage("CP_BC_TPL_MESS_RELATIVE_QUANTITY_FEW"),
				"TYPE" => "STRING",
				"DEFAULT" => GetMessage("CP_BC_TPL_MESS_RELATIVE_QUANTITY_FEW_DEFAULT")
			);
		}
	}
}

$arTemplateParameters["LAZY_LOAD"] = array(
	"PARENT" => "PAGER_SETTINGS",
	"NAME" => GetMessage("CP_BC_TPL_LAZY_LOAD"),
	"TYPE" => "CHECKBOX",
	"REFRESH" => "Y",
	"DEFAULT" => "N"
);
if(isset($arCurrentValues["LAZY_LOAD"]) && $arCurrentValues["LAZY_LOAD"] === "Y") {
	$arTemplateParameters["MESS_BTN_LAZY_LOAD"] = array(
		"PARENT" => "PAGER_SETTINGS",
		"NAME" => GetMessage("CP_BC_TPL_MESS_BTN_LAZY_LOAD"),
		"TYPE" => "TEXT",
		"DEFAULT" => GetMessage("CP_BC_TPL_MESS_BTN_LAZY_LOAD_DEFAULT")
	);
}
$arTemplateParameters["LOAD_ON_SCROLL"] = array(
	"PARENT" => "PAGER_SETTINGS",
	"NAME" => GetMessage("CP_BC_TPL_LOAD_ON_SCROLL"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N"
);

$arTemplateParameters["MESS_BTN_BUY"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("CP_BC_TPL_MESS_BTN_BUY"),
	"TYPE" => "STRING",
	"DEFAULT" => GetMessage("CP_BC_TPL_MESS_BTN_BUY_DEFAULT")
);
$arTemplateParameters["MESS_BTN_ADD_TO_BASKET"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("CP_BC_TPL_MESS_BTN_ADD_TO_BASKET"),
	"TYPE" => "STRING",
	"DEFAULT" => GetMessage("CP_BC_TPL_MESS_BTN_ADD_TO_BASKET_DEFAULT")
);
$arTemplateParameters["MESS_BTN_COMPARE"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("CP_BC_TPL_MESS_BTN_COMPARE"),
	"TYPE" => "STRING",
	"DEFAULT" => GetMessage("CP_BC_TPL_MESS_BTN_COMPARE_DEFAULT")
);
$arTemplateParameters["MESS_BTN_DETAIL"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("CP_BC_TPL_MESS_BTN_DETAIL"),
	"TYPE" => "STRING",
	"DEFAULT" => GetMessage("CP_BC_TPL_MESS_BTN_DETAIL_DEFAULT")
);
$arTemplateParameters["MESS_NOT_AVAILABLE"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("CP_BC_TPL_MESS_NOT_AVAILABLE"),
	"TYPE" => "STRING",
	"DEFAULT" => GetMessage("CP_BC_TPL_MESS_NOT_AVAILABLE_DEFAULT")
);
$arTemplateParameters["MESS_BTN_SUBSCRIBE"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("CP_BC_TPL_MESS_BTN_SUBSCRIBE"),
	"TYPE" => "STRING",
	"DEFAULT" => GetMessage("CP_BC_TPL_MESS_BTN_SUBSCRIBE_DEFAULT")
);

if(ModuleManager::isModuleInstalled("sale")) {
	$arTemplateParameters["USE_BIG_DATA"] = array(
		"PARENT" => "BIG_DATA_SETTINGS",
		"NAME" => GetMessage("CP_BC_TPL_USE_BIG_DATA"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"REFRESH" => "Y"
	);
	if(!isset($arCurrentValues["USE_BIG_DATA"]) || $arCurrentValues["USE_BIG_DATA"] == "Y") {
		$rcmTypeList = array(
			"bestsell" => GetMessage("CP_BC_TPL_RCM_BESTSELLERS"),
			"personal" => GetMessage("CP_BC_TPL_RCM_PERSONAL"),
			"similar_sell" => GetMessage("CP_BC_TPL_RCM_SOLD_WITH"),
			"similar_view" => GetMessage("CP_BC_TPL_RCM_VIEWED_WITH"),
			"similar" => GetMessage("CP_BC_TPL_RCM_SIMILAR"),
			"any_similar" => GetMessage("CP_BC_TPL_RCM_SIMILAR_ANY"),
			"any_personal" => GetMessage("CP_BC_TPL_RCM_PERSONAL_WBEST"),
			"any" => GetMessage("CP_BC_TPL_RCM_RAND")
		);
		$arTemplateParameters["BIG_DATA_RCM_TYPE"] = array(
			"PARENT" => "BIG_DATA_SETTINGS",
			"NAME" => GetMessage("CP_BC_TPL_BIG_DATA_RCM_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $rcmTypeList
		);
		unset($rcmTypeList);
	}
}

$arTemplateParameters["SHOW_TOP_ELEMENTS"] = array(
	"HIDDEN" => "Y"
);
if(isset($arCurrentValues["SHOW_TOP_ELEMENTS"]) && "Y" == $arCurrentValues["SHOW_TOP_ELEMENTS"]) {
	$arTemplateParameters["TOP_ELEMENT_COUNT"] = array(
		"HIDDEN" => "Y"
	);
	$arTemplateParameters["TOP_LINE_ELEMENT_COUNT"] = array(
		"HIDDEN" => "Y"
	);
	$arTemplateParameters["TOP_ELEMENT_SORT_FIELD"] = array(
		"HIDDEN" => "Y"
	);
	$arTemplateParameters["TOP_ELEMENT_SORT_ORDER"] = array(
		"HIDDEN" => "Y"
	);
	$arTemplateParameters["TOP_ELEMENT_SORT_FIELD2"] = array(
		"HIDDEN" => "Y"
	);
	$arTemplateParameters["TOP_ELEMENT_SORT_ORDER2"] = array(
		"HIDDEN" => "Y"
	);
	$arTemplateParameters["TOP_PROPERTY_CODE"] = array(
		"HIDDEN" => "Y"
	);
	$arTemplateParameters["TOP_OFFERS_FIELD_CODE"] = array(
		"HIDDEN" => "Y"
	);
	$arTemplateParameters["TOP_OFFERS_PROPERTY_CODE"] = array(
		"HIDDEN" => "Y"
	);
	$arTemplateParameters["TOP_OFFERS_LIMIT"] = array(
		"HIDDEN" => "Y"
	);
}

$arReviewsIBlock = array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), array("TYPE" => $arCurrentValues["REVIEWS_IBLOCK_TYPE"], "ACTIVE" => "Y"));
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

$compatibleMode = isset($arCurrentValues["COMPATIBLE_MODE"]) && $arCurrentValues["COMPATIBLE_MODE"] == "Y";
if(!$compatibleMode) {
	$arTemplateParameters["USE_REVIEW"] = array(
		"PARENT" => "REVIEW_SETTINGS",
		"NAME" => GetMessage("CP_BC_TPL_USE_REVIEW"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"REFRESH" => "Y",
	);
}
if(isset($arCurrentValues["USE_REVIEW"]) && $arCurrentValues["USE_REVIEW"] == "Y") {
	if($compatibleMode) {
		$arTemplateParameters["MESSAGES_PER_PAGE"] = Array(
			"HIDDEN" => "Y"
		);	
		$arTemplateParameters["USE_CAPTCHA"] = Array(
			"HIDDEN" => "Y"
		);	
		$arTemplateParameters["REVIEW_AJAX_POST"] = Array(
			"HIDDEN" => "Y"
		);	
		$arTemplateParameters["PATH_TO_SMILE"] = Array(
			"HIDDEN" => "Y"
		);	
		$arTemplateParameters["FORUM_ID"] = Array(
			"HIDDEN" => "Y"
		);	
		$arTemplateParameters["URL_TEMPLATES_READ"] = Array(
			"HIDDEN" => "Y"
		);	
		$arTemplateParameters["SHOW_LINK_TO_FORUM"] = Array(
			"HIDDEN" => "Y"
		);
	}
	$arTemplateParameters["REVIEWS_IBLOCK_TYPE"] = array(		
		"PARENT" => "REVIEW_SETTINGS",
		"NAME" => GetMessage("CP_BC_TPL_REVIEWS_IBLOCK_TYPE"),
		"TYPE" => "LIST",		
		"REFRESH" => "Y",
		"VALUES" => CIBlockParameters::GetIBlockTypes(),
	);
	$arTemplateParameters["REVIEWS_IBLOCK_ID"] = array(		
		"PARENT" => "REVIEW_SETTINGS",
		"NAME" => GetMessage("CP_BC_TPL_REVIEWS_IBLOCK_ID"),
		"TYPE" => "LIST",
		"REFRESH" => "Y",		
		"VALUES" => $arReviewsIBlock,
		"ADDITIONAL_VALUES" => "Y",
	);
	$arTemplateParameters["REVIEWS_NEWS_COUNT"] = array(		
		"PARENT" => "REVIEW_SETTINGS",
		"NAME" => GetMessage("CP_BC_TPL_REVIEWS_NEWS_COUNT"),
		"TYPE" => "STRING",
		"DEFAULT" => "5",
	);	
	$arTemplateParameters["REVIEWS_SORT_BY1"] = array(		
		"PARENT" => "REVIEW_SETTINGS",
		"NAME" => GetMessage("CP_BC_TPL_REVIEWS_SORT_BY1"),
		"TYPE" => "LIST",
		"DEFAULT" => "sort",
		"VALUES" => CIBlockParameters::GetElementSortFields(
			array("SHOWS", "SORT", "TIMESTAMP_X", "NAME", "ID", "ACTIVE_FROM", "ACTIVE_TO"),
			array("KEY_LOWERCASE" => "Y")
		),
		"ADDITIONAL_VALUES" => "Y",
	);	
	$arTemplateParameters["REVIEWS_SORT_ORDER1"] = array(		
		"PARENT" => "REVIEW_SETTINGS",
		"NAME" => GetMessage("CP_BC_TPL_REVIEWS_SORT_ORDER1"),
		"TYPE" => "LIST",
		"DEFAULT" => "asc",
		"VALUES" => array(
			"asc" => GetMessage("CP_BC_TPL_REVIEWS_SORT_ORDER_ASC"),
			"desc" => GetMessage("CP_BC_TPL_REVIEWS_SORT_ORDER_DESC"),
		),
		"ADDITIONAL_VALUES" => "Y",
	);	
	$arTemplateParameters["REVIEWS_SORT_BY2"] = array(
		"PARENT" => "REVIEW_SETTINGS",
		"NAME" => GetMessage("CP_BC_TPL_REVIEWS_SORT_BY2"),
		"TYPE" => "LIST",
		"DEFAULT" => "active_from",
		"VALUES" => CIBlockParameters::GetElementSortFields(
			array("SHOWS", "SORT", "TIMESTAMP_X", "NAME", "ID", "ACTIVE_FROM", "ACTIVE_TO"),
			array("KEY_LOWERCASE" => "Y")
		),
		"ADDITIONAL_VALUES" => "Y",
	);	
	$arTemplateParameters["REVIEWS_SORT_ORDER2"] = array(
		"PARENT" => "REVIEW_SETTINGS",
		"NAME" => GetMessage("CP_BC_TPL_REVIEWS_SORT_ORDER2"),
		"TYPE" => "LIST",
		"DEFAULT" => "desc",
		"VALUES" => array(
			"asc" => GetMessage("CP_BC_TPL_REVIEWS_SORT_ORDER_ASC"),
			"desc" => GetMessage("CP_BC_TPL_REVIEWS_SORT_ORDER_DESC"),
		),
		"ADDITIONAL_VALUES" => "Y",
	);
	$arTemplateParameters["REVIEWS_ACTIVE_DATE_FORMAT"] = CIBlockParameters::GetDateFormat(GetMessage("CP_BC_TPL_REVIEWS_ACTIVE_DATE_FORMAT"), "REVIEW_SETTINGS");
	$arTemplateParameters["REVIEWS_PROPERTY_CODE"] = array(		
		"PARENT" => "REVIEW_SETTINGS",	
		"NAME" => GetMessage("CP_BC_TPL_REVIEWS_PROPERTY_CODE"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arReviewsProperty_LNS,
		"ADDITIONAL_VALUES" => "Y",
	);
	$arTemplateParameters["MESS_REVIEWS_TAB"] = array(
		"PARENT" => "REVIEW_SETTINGS",
		"NAME" => GetMessage("CP_BC_TPL_MESS_REVIEWS_TAB"),
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage("CP_BC_TPL_MESS_REVIEWS_TAB_DEFAULT")
	);
}

$arTemplateParameters["USE_ALSO_BUY"] = array(
	"HIDDEN" => "Y"
);
if(isset($arCurrentValues["USE_ALSO_BUY"]) && $arCurrentValues["USE_ALSO_BUY"] == "Y") {
	$arTemplateParameters["ALSO_BUY_ELEMENT_COUNT"] = array(
		"HIDDEN" => "Y"
	);
	$arTemplateParameters["ALSO_BUY_MIN_BUYES"] = array(
		"HIDDEN" => "Y"
	);
}

$arTemplateParameters["USE_GIFTS_MAIN_PR_SECTION_LIST"] = array(
	"HIDDEN" => "Y"
);
$useGiftsDetail = isset($arCurrentValues["USE_GIFTS_DETAIL"]) && $arCurrentValues["USE_GIFTS_DETAIL"] == "Y";
$useGiftsSection = isset($arCurrentValues["USE_GIFTS_SECTION"]) && $arCurrentValues["USE_GIFTS_SECTION"] == "Y";
$useGiftsMainPrSectionList = isset($arCurrentValues["USE_GIFTS_MAIN_PR_SECTION_LIST"]) && $arCurrentValues["USE_GIFTS_MAIN_PR_SECTION_LIST"] == "Y";
if($useGiftsDetail || $useGiftsSection || $useGiftsMainPrSectionList) {
	if($useGiftsDetail || $useGiftsSection) {
		$arTemplateParameters["GIFTS_SHOW_DISCOUNT_PERCENT"] = array(
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["GIFTS_SHOW_OLD_PRICE"] = array(
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["GIFTS_SHOW_NAME"] = array(
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["GIFTS_SHOW_IMAGE"] = array(
			"HIDDEN" => "Y"
		);
	}
	if($useGiftsMainPrSectionList) {
		$arTemplateParameters["GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT"] = array(
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["GIFTS_MAIN_PRODUCT_DETAIL_HIDE_BLOCK_TITLE"] = array(
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE"] = array(
			"HIDDEN" => "Y"
		);
	}
}

if(isset($arCurrentValues["USE_STORE"]) && $arCurrentValues["USE_STORE"] == "Y") {	
	$arTemplateParameters["STORE_PATH"] = array(
		"HIDDEN" => "Y"
	);
}

$arTemplateParameters["USE_ENHANCED_ECOMMERCE"] = array(
	"PARENT" => "ANALYTICS_SETTINGS",
	"NAME" => GetMessage("CP_BC_TPL_USE_ENHANCED_ECOMMERCE"),
	"TYPE" => "CHECKBOX",
	"REFRESH" => "Y",
	"DEFAULT" => "N"
);
if(isset($arCurrentValues["USE_ENHANCED_ECOMMERCE"]) && $arCurrentValues["USE_ENHANCED_ECOMMERCE"] === "Y") {
	$arTemplateParameters["DATA_LAYER_NAME"] = array(
		"PARENT" => "ANALYTICS_SETTINGS",
		"NAME" => GetMessage("CP_BC_TPL_DATA_LAYER_NAME"),
		"TYPE" => "STRING",
		"DEFAULT" => "dataLayer"
	);
	$arTemplateParameters["BRAND_PROPERTY"] = array(
		"PARENT" => "ANALYTICS_SETTINGS",
		"NAME" => GetMessage("CP_BC_TPL_BRAND_PROPERTY"),
		"TYPE" => "LIST",
		"MULTIPLE" => "N",
		"DEFAULT" => "",
		"VALUES" => $defaultValue + $arAllPropList
	);
}

$arTemplateParameters["USE_RATIO_IN_RANGES"] = array(
	"PARENT" => "PRICES",
	"NAME" => GetMessage("CP_BC_TPL_USE_RATIO_IN_RANGES"),
	"TYPE" => "CHECKBOX",
	"HIDDEN" => isset($arCurrentValues["USE_PRICE_COUNT"]) && $arCurrentValues["USE_PRICE_COUNT"] === "Y" ? "N" : "Y",
	"DEFAULT" => "Y"
);

$arCollectionsIBlock = array();
$rsIBlock = CIBlock::GetList(array("sort" => "asc"), array("TYPE" => $arCurrentValues["COLLECTIONS_IBLOCK_TYPE"], "ACTIVE" => "Y"));
while($arr = $rsIBlock->Fetch()) {
	$arCollectionsIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}
unset($arr, $rsIBlock);

$arCollectionsProperty_LNS = array();
$rsProps = CIBlockProperty::GetList(
	array("SORT" => "ASC", "ID" => "ASC"),
	array("IBLOCK_ID" => $arCurrentValues["COLLECTIONS_IBLOCK_ID"], "ACTIVE" => "Y")
);
while($arProp = $rsProps->Fetch()) {
	if(in_array($arProp["PROPERTY_TYPE"], array("L", "N", "S", "E"))) {
		$arCollectionsProperty_LNS[$arProp["CODE"]] = "[".$arProp["CODE"]."] ".$arProp["NAME"];
	}
}
unset($arProp, $rsProps);

$arTemplateParameters["SHOW_COLLECTIONS"] = array(
	"NAME" => GetMessage("CP_BC_TPL_SHOW_COLLECTIONS"),
	"TYPE" => "CHECKBOX",
	"REFRESH" => "Y",
	"DEFAULT" => "Y"
);

if(isset($arCurrentValues["SHOW_COLLECTIONS"]) && $arCurrentValues["SHOW_COLLECTIONS"] == "Y") {
	$arTemplateParameters["COLLECTIONS_IBLOCK_TYPE"] = array(		
		"NAME" => GetMessage("CP_BC_TPL_COLLECTIONS_IBLOCK_TYPE"),
		"TYPE" => "LIST",		
		"REFRESH" => "Y",
		"VALUES" => CIBlockParameters::GetIBlockTypes(),
	);
	$arTemplateParameters["COLLECTIONS_IBLOCK_ID"] = array(		
		"NAME" => GetMessage("CP_BC_TPL_COLLECTIONS_IBLOCK_ID"),
		"TYPE" => "LIST",
		"REFRESH" => "Y",		
		"VALUES" => $arCollectionsIBlock,
		"ADDITIONAL_VALUES" => "Y",
	);
	$arTemplateParameters["COLLECTIONS_NEWS_COUNT"] = array(		
		"NAME" => GetMessage("CP_BC_TPL_COLLECTIONS_NEWS_COUNT"),
		"TYPE" => "STRING",
		"DEFAULT" => $pageElementCount,
	);	
	$arTemplateParameters["COLLECTIONS_SORT_BY1"] = array(		
		"NAME" => GetMessage("CP_BC_TPL_COLLECTIONS_SORT_BY1"),
		"TYPE" => "LIST",
		"DEFAULT" => "SORT",
		"VALUES" => CIBlockParameters::GetElementSortFields(
			array("SHOWS", "SORT", "TIMESTAMP_X", "NAME", "ID", "ACTIVE_FROM", "ACTIVE_TO"),
			array("KEY_LOWERCASE" => "Y")
		),
		"ADDITIONAL_VALUES" => "Y",
	);	
	$arTemplateParameters["COLLECTIONS_SORT_ORDER1"] = array(		
		"NAME" => GetMessage("CP_BC_TPL_COLLECTIONS_SORT_ORDER1"),
		"TYPE" => "LIST",
		"DEFAULT" => "ASC",
		"VALUES" => array(
			"asc" => GetMessage("CP_BC_TPL_COLLECTIONS_SORT_ORDER_ASC"),
			"desc" => GetMessage("CP_BC_TPL_COLLECTIONS_SORT_ORDER_DESC"),
		),
		"ADDITIONAL_VALUES" => "Y",
	);	
	$arTemplateParameters["COLLECTIONS_SORT_BY2"] = array(
		"NAME" => GetMessage("CP_BC_TPL_COLLECTIONS_SORT_BY2"),
		"TYPE" => "LIST",
		"DEFAULT" => "ACTIVE_FROM",
		"VALUES" => CIBlockParameters::GetElementSortFields(
			array("SHOWS", "SORT", "TIMESTAMP_X", "NAME", "ID", "ACTIVE_FROM", "ACTIVE_TO"),
			array("KEY_LOWERCASE" => "Y")
		),
		"ADDITIONAL_VALUES" => "Y",
	);	
	$arTemplateParameters["COLLECTIONS_SORT_ORDER2"] = array(
		"NAME" => GetMessage("CP_BC_TPL_COLLECTIONS_SORT_ORDER2"),
		"TYPE" => "LIST",
		"DEFAULT" => "DESC",
		"VALUES" => array(
			"asc" => GetMessage("CP_BC_TPL_COLLECTIONS_SORT_ORDER_ASC"),
			"desc" => GetMessage("CP_BC_TPL_COLLECTIONS_SORT_ORDER_DESC"),
		),
		"ADDITIONAL_VALUES" => "Y",
	);	
	$arTemplateParameters["COLLECTIONS_PROPERTY_CODE"] = array(		
		"NAME" => GetMessage("CP_BC_TPL_COLLECTIONS_PROPERTY_CODE"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arCollectionsProperty_LNS,
		"ADDITIONAL_VALUES" => "Y",
	);
	$arTemplateParameters["COLLECTIONS_SHOW_MIN_PRICE"] = array(
		"NAME" => GetMessage("CP_BC_TPL_COLLECTIONS_SHOW_MIN_PRICE"),
		"TYPE" => "CHECKBOX",		
		"DEFAULT" => "Y",
	);
}

$arTemplateParameters["SET_ITEMS_COUNT"] = array(
	"NAME" => GetMessage("CP_BC_TPL_SET_ITEMS_COUNT"),
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
	"NAME" => GetMessage("CP_BC_TPL_OBJECTS_USE_REVIEW"),
	"TYPE" => "CHECKBOX",
	"REFRESH" => "Y",
	"DEFAULT" => "Y"
);

if(isset($arCurrentValues["OBJECTS_USE_REVIEW"]) && $arCurrentValues["OBJECTS_USE_REVIEW"] == "Y") {
	$arTemplateParameters["OBJECTS_REVIEWS_IBLOCK_TYPE"] = array(
		"NAME" => GetMessage("CP_BC_TPL_OBJECTS_REVIEWS_IBLOCK_TYPE"),
		"TYPE" => "LIST",		
		"REFRESH" => "Y",
		"VALUES" => CIBlockParameters::GetIBlockTypes(),
	);		
	$arTemplateParameters["OBJECTS_REVIEWS_IBLOCK_ID"] = array(
		"NAME" => GetMessage("CP_BC_TPL_OBJECTS_REVIEWS_IBLOCK_ID"),
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
	"NAME" => GetMessage("CP_BC_TPL_CONTACTS_IBLOCK_TYPE"),
	"TYPE" => "LIST",		
	"REFRESH" => "Y",
	"VALUES" => CIBlockParameters::GetIBlockTypes(),
);		
$arTemplateParameters["CONTACTS_IBLOCK_ID"] = array(
	"NAME" => GetMessage("CP_BC_TPL_CONTACTS_IBLOCK_ID"),
	"TYPE" => "LIST",
	"REFRESH" => "Y",		
	"VALUES" => $arContactsIBlock,
	"ADDITIONAL_VALUES" => "Y",
);
$arTemplateParameters["CONTACTS_USE_REVIEW"] = array(
	"NAME" => GetMessage("CP_BC_TPL_CONTACTS_USE_REVIEW"),
	"TYPE" => "CHECKBOX",
	"REFRESH" => "Y",
	"DEFAULT" => "Y"
);

if(isset($arCurrentValues["CONTACTS_USE_REVIEW"]) && $arCurrentValues["CONTACTS_USE_REVIEW"] == "Y") {
	$arTemplateParameters["CONTACTS_REVIEWS_IBLOCK_TYPE"] = array(
		"NAME" => GetMessage("CP_BC_TPL_CONTACTS_REVIEWS_IBLOCK_TYPE"),
		"TYPE" => "LIST",		
		"REFRESH" => "Y",
		"VALUES" => CIBlockParameters::GetIBlockTypes(),
	);		
	$arTemplateParameters["CONTACTS_REVIEWS_IBLOCK_ID"] = array(
		"NAME" => GetMessage("CP_BC_TPL_CONTACTS_REVIEWS_IBLOCK_ID"),
		"TYPE" => "LIST",
		"REFRESH" => "Y",		
		"VALUES" => $arContactsReviewsIBlock,
		"ADDITIONAL_VALUES" => "Y",
	);
	$arTemplateParameters["CONTACTS_REVIEWS_PAGE_LINK"] = array(
		"NAME" => GetMessage("CP_BC_TPL_CONTACTS_REVIEWS_PAGE_LINK"),
		"TYPE" => "STRING",
		"DEFAULT" => '={SITE_DIR."about/reviews/"}'
	);
}

$arFaqIBlock = array();
$rsIBlock = CIBlock::GetList(array("sort" => "asc"), array("TYPE" => $arCurrentValues["FAQ_IBLOCK_TYPE"], "ACTIVE" => "Y"));
while($arr = $rsIBlock->Fetch()) {
	$arFaqIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}
unset($arr, $rsIBlock);

$arTemplateParameters["FAQ_IBLOCK_TYPE"] = array(		
	"NAME" => GetMessage("CP_BC_TPL_FAQ_IBLOCK_TYPE"),
	"TYPE" => "LIST",		
	"REFRESH" => "Y",
	"VALUES" => CIBlockParameters::GetIBlockTypes(),
);
$arTemplateParameters["FAQ_IBLOCK_ID"] = array(		
	"NAME" => GetMessage("CP_BC_TPL_FAQ_IBLOCK_ID"),
	"TYPE" => "LIST",
	"REFRESH" => "Y",		
	"VALUES" => $arFaqIBlock,
	"ADDITIONAL_VALUES" => "Y",
);
$arTemplateParameters["FAQ_NEWS_COUNT"] = array(		
	"NAME" => GetMessage("CP_BC_TPL_FAQ_NEWS_COUNT"),
	"TYPE" => "STRING",
	"DEFAULT" => $pageElementCount,
);	
$arTemplateParameters["FAQ_SORT_BY1"] = array(		
	"NAME" => GetMessage("CP_BC_TPL_FAQ_SORT_BY1"),
	"TYPE" => "LIST",
	"DEFAULT" => "SORT",
	"VALUES" => CIBlockParameters::GetElementSortFields(
		array("SHOWS", "SORT", "TIMESTAMP_X", "NAME", "ID", "ACTIVE_FROM", "ACTIVE_TO"),
		array("KEY_LOWERCASE" => "Y")
	),
	"ADDITIONAL_VALUES" => "Y",
);	
$arTemplateParameters["FAQ_SORT_ORDER1"] = array(		
	"NAME" => GetMessage("CP_BC_TPL_FAQ_SORT_ORDER1"),
	"TYPE" => "LIST",
	"DEFAULT" => "ASC",
	"VALUES" => array(
		"asc" => GetMessage("CP_BC_TPL_FAQ_SORT_ORDER_ASC"),
		"desc" => GetMessage("CP_BC_TPL_FAQ_SORT_ORDER_DESC"),
	),
	"ADDITIONAL_VALUES" => "Y",
);	
$arTemplateParameters["FAQ_SORT_BY2"] = array(
	"NAME" => GetMessage("CP_BC_TPL_FAQ_SORT_BY2"),
	"TYPE" => "LIST",
	"DEFAULT" => "ACTIVE_FROM",
	"VALUES" => CIBlockParameters::GetElementSortFields(
		array("SHOWS", "SORT", "TIMESTAMP_X", "NAME", "ID", "ACTIVE_FROM", "ACTIVE_TO"),
		array("KEY_LOWERCASE" => "Y")
	),
	"ADDITIONAL_VALUES" => "Y",
);	
$arTemplateParameters["FAQ_SORT_ORDER2"] = array(
	"NAME" => GetMessage("CP_BC_TPL_FAQ_SORT_ORDER2"),
	"TYPE" => "LIST",
	"DEFAULT" => "DESC",
	"VALUES" => array(
		"asc" => GetMessage("CP_BC_TPL_FAQ_SORT_ORDER_ASC"),
		"desc" => GetMessage("CP_BC_TPL_FAQ_SORT_ORDER_DESC"),
	),
	"ADDITIONAL_VALUES" => "Y",
);