<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Web\Json;

if(!Loader::includeModule("iblock"))
	return;

$boolCatalog = Loader::includeModule("catalog");
CBitrixComponent::includeComponentClass($componentName);

$defaultValue = array("-" => GetMessage("CP_BCE_TPL_PROP_EMPTY"));
$arSKU = false;
$boolSKU = false;

if($boolCatalog && (isset($arCurrentValues["IBLOCK_ID"]) && 0 < intval($arCurrentValues["IBLOCK_ID"]))) {
	$arSKU = CCatalogSku::GetInfoByProductIBlock($arCurrentValues["IBLOCK_ID"]);
	$boolSKU = !empty($arSKU) && is_array($arSKU);
}

$documentRoot = Loader::getDocumentRoot();

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

		if($arProp["PROPERTY_TYPE"] === "F") {
			$arFilePropList[$arProp["CODE"]] = $strPropName;
		}
	}

	$arAllOfferPropList = array();
	$arTreeOfferPropList = $arFileOfferPropList = $defaultValue;

	if($boolSKU) {
		$rsProps = CIBlockProperty::GetList(
			array("SORT" => "ASC", "ID" => "ASC"),
			array("IBLOCK_ID" => $arSKU["IBLOCK_ID"], "ACTIVE" => "Y")
		);
		while($arProp = $rsProps->Fetch()) {
			if($arProp["ID"] == $arSKU["SKU_PROPERTY_ID"]) {
				continue;
			}

			$arProp["USER_TYPE"] = (string)$arProp["USER_TYPE"];
			$strPropName = "[".$arProp["ID"]."]".("" != $arProp["CODE"] ? "[".$arProp["CODE"]."]" : "")." ".$arProp["NAME"];

			if($arProp["CODE"] == "") {
				$arProp["CODE"] = $arProp["ID"];
			}

			$arAllOfferPropList[$arProp["CODE"]] = $strPropName;

			if($arProp["PROPERTY_TYPE"] === "F") {
				$arFileOfferPropList[$arProp["CODE"]] = $strPropName;
			}

			if($arProp["MULTIPLE"] != "N") {
				continue;
			}

			if(
				$arProp["PROPERTY_TYPE"] === "L"
				|| $arProp["PROPERTY_TYPE"] === "E"
				|| (
					$arProp["PROPERTY_TYPE"] === "S"
					&& $arProp["USER_TYPE"] === "directory"
					&& CIBlockPriceTools::checkPropDirectory($arProp)
				)
			) {
				$arTreeOfferPropList[$arProp["CODE"]] = $strPropName;
			}
		}
	}
	
	$arCurrentValues["OFFERS_PROPERTY_CODE"] = isset($arCurrentValues["OFFERS_PROPERTY_CODE"]) ? $arCurrentValues["OFFERS_PROPERTY_CODE"] : array();
	if(!empty($arCurrentValues["OFFERS_PROPERTY_CODE"])) {
		$selected = array();
		foreach($arCurrentValues["OFFERS_PROPERTY_CODE"] as $code) {
			if(isset($arAllOfferPropList[$code])) {
				$selected[$code] = $arAllOfferPropList[$code];
			}
		}

		$arTemplateParameters["MAIN_BLOCK_OFFERS_PROPERTY_CODE"] = array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("CP_BCE_TPL_MAIN_BLOCK_OFFERS_PROPERTY_CODE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"SIZE" => (count($selected) > 5 ? 8 : 3),
			"VALUES" => $selected
		);
	}

	$arTemplateParameters["ADD_PICT_PROP"] = array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("CP_BCE_TPL_ADD_PICT_PROP"),
		"TYPE" => "LIST",
		"MULTIPLE" => "N",
		"ADDITIONAL_VALUES" => "N",
		"REFRESH" => "N",
		"DEFAULT" => "-",
		"VALUES" => $arFilePropList
	);
	
	if($boolSKU) {
		$arTemplateParameters["OFFER_ADD_PICT_PROP"] = array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("CP_BCE_TPL_OFFER_ADD_PICT_PROP"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"ADDITIONAL_VALUES" => "N",
			"REFRESH" => "N",
			"DEFAULT" => "-",
			"VALUES" => $arFileOfferPropList
		);
		$arTemplateParameters["OFFER_TREE_PROPS"] = array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("CP_BCE_TPL_OFFER_TREE_PROPS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "N",
			"REFRESH" => "N",
			"DEFAULT" => "-",
			"VALUES" => $arTreeOfferPropList
		);
	}
}

$arTemplateParameters["IMAGE_RESOLUTION"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("CP_BCE_TPL_IMAGE_RESOLUTION"),
	"TYPE" => "LIST",
	"VALUES" => array(
		"16by9" => GetMessage("CP_BCE_TPL_IMAGE_RESOLUTION_16_BY_9"),
		"1by1" => GetMessage("CP_BCE_TPL_IMAGE_RESOLUTION_1_BY_1")
	),
	"DEFAULT" => "16by9"
);
$arTemplateParameters["SHOW_SLIDER"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("CP_BCE_TPL_SHOW_SLIDER"),
	"TYPE" => "CHECKBOX",
	"MULTIPLE" => "N",
	"REFRESH" => "Y",
	"DEFAULT" => "N"
);
if(isset($arCurrentValues["SHOW_SLIDER"]) && $arCurrentValues["SHOW_SLIDER"] === "Y") {
	$arTemplateParameters["SLIDER_INTERVAL"] = array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("CP_BCE_TPL_SLIDER_INTERVAL"),
		"TYPE" => "TEXT",
		"MULTIPLE" => "N",
		"REFRESH" => "N",
		"DEFAULT" => "5000"
	);
	$arTemplateParameters["SLIDER_PROGRESS"] = array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("CP_BCE_TPL_SLIDER_PROGRESS"),
		"TYPE" => "CHECKBOX",
		"MULTIPLE" => "N",
		"REFRESH" => "N",
		"DEFAULT" => "N"
	);
}
$arTemplateParameters["DETAIL_PICTURE_MODE"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("CP_BCE_TPL_DETAIL_PICTURE_MODE"),
	"TYPE" => "LIST",
	"MULTIPLE" => "Y",
	"DEFAULT" => array("POPUP", "MAGNIFIER"),
	"VALUES" => array(
		"POPUP" => GetMessage("DETAIL_PICTURE_MODE_POPUP"),
		"MAGNIFIER" => GetMessage("DETAIL_PICTURE_MODE_MAGNIFIER")
	)
);
$arTemplateParameters["ADD_DETAIL_TO_SLIDER"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("CP_BCE_TPL_ADD_DETAIL_TO_SLIDER"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N"
);

if($boolCatalog) {
	$arTemplateParameters["PRODUCT_SUBSCRIPTION"] = array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("CP_BCE_TPL_PRODUCT_SUBSCRIPTION"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y"
	);
	$arTemplateParameters["SHOW_DISCOUNT_PERCENT"] = array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("CP_BCE_TPL_SHOW_DISCOUNT_PERCENT"),
		"TYPE" => "CHECKBOX",
		"REFRESH" => "Y",
		"DEFAULT" => "N"
	);
	$arTemplateParameters["SHOW_OLD_PRICE"] = array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("CP_BCE_TPL_SHOW_OLD_PRICE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N"
	);
	$arTemplateParameters["SHOW_MAX_QUANTITY"] = array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("CP_BCE_TPL_SHOW_MAX_QUANTITY"),
		"TYPE" => "LIST",
		"REFRESH" => "Y",
		"MULTIPLE" => "N",
		"VALUES" => array(
			"N" => GetMessage("CP_BCE_TPL_SHOW_MAX_QUANTITY_N"),
			"Y" => GetMessage("CP_BCE_TPL_SHOW_MAX_QUANTITY_Y"),
			"M" => GetMessage("CP_BCE_TPL_SHOW_MAX_QUANTITY_M")
		),
		"DEFAULT" => array("N"),
	);
	if(isset($arCurrentValues["SHOW_MAX_QUANTITY"])) {
		if($arCurrentValues["SHOW_MAX_QUANTITY"] !== "N") {
			$arTemplateParameters["MESS_SHOW_MAX_QUANTITY"] = array(
				"PARENT" => "VISUAL",
				"NAME" => GetMessage("CP_BCE_TPL_MESS_SHOW_MAX_QUANTITY"),
				"TYPE" => "STRING",
				"DEFAULT" => GetMessage("CP_BCE_TPL_MESS_SHOW_MAX_QUANTITY_DEFAULT")
			);
		}
		if($arCurrentValues["SHOW_MAX_QUANTITY"] === "M") {
			$arTemplateParameters["RELATIVE_QUANTITY_FACTOR"] = array(
				"PARENT" => "VISUAL",
				"NAME" => GetMessage("CP_BCE_TPL_RELATIVE_QUANTITY_FACTOR"),
				"TYPE" => "STRING",
				"DEFAULT" => "5"
			);
			$arTemplateParameters["MESS_RELATIVE_QUANTITY_MANY"] = array(
				"PARENT" => "VISUAL",
				"NAME" => GetMessage("CP_BCE_TPL_MESS_RELATIVE_QUANTITY_MANY"),
				"TYPE" => "STRING",
				"DEFAULT" => GetMessage("CP_BCE_TPL_MESS_RELATIVE_QUANTITY_MANY_DEFAULT")
			);
			$arTemplateParameters["MESS_RELATIVE_QUANTITY_FEW"] = array(
				"PARENT" => "VISUAL",
				"NAME" => GetMessage("CP_BCE_TPL_MESS_RELATIVE_QUANTITY_FEW"),
				"TYPE" => "STRING",
				"DEFAULT" => GetMessage("CP_BCE_TPL_MESS_RELATIVE_QUANTITY_FEW_DEFAULT")
			);
		}
	}

	$basketActions = array(
		"BUY" => GetMessage("ADD_TO_BASKET_ACTION_BUY"),
		"ADD" => GetMessage("ADD_TO_BASKET_ACTION_ADD")
	);
	$arTemplateParameters["ADD_TO_BASKET_ACTION"] = array(
		"PARENT" => "BASKET",
		"NAME" => GetMessage("CP_BCE_TPL_ADD_TO_BASKET_ACTION"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $basketActions,
		"DEFAULT" => array("BUY"),
		"REFRESH" => "Y"
	);
}

$arTemplateParameters["MESS_BTN_BUY"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("CP_BCE_TPL_MESS_BTN_BUY"),
	"TYPE" => "STRING",
	"DEFAULT" => GetMessage("CP_BCE_TPL_MESS_BTN_BUY_DEFAULT")
);
$arTemplateParameters["MESS_BTN_ADD_TO_BASKET"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("CP_BCE_TPL_MESS_BTN_ADD_TO_BASKET"),
	"TYPE" => "STRING",
	"DEFAULT" => GetMessage("CP_BCE_TPL_MESS_BTN_ADD_TO_BASKET_DEFAULT")
);
$arTemplateParameters["MESS_BTN_SUBSCRIBE"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("CP_BCE_TPL_MESS_BTN_SUBSCRIBE"),
	"TYPE" => "STRING",
	"DEFAULT" => GetMessage("CP_BCE_TPL_MESS_BTN_SUBSCRIBE_DEFAULT")
);

if(isset($arCurrentValues["DISPLAY_COMPARE"]) && $arCurrentValues["DISPLAY_COMPARE"] === "Y") {
	$arTemplateParameters["MESS_BTN_COMPARE"] = array(
		"PARENT" => "COMPARE",
		"NAME" => GetMessage("CP_BCE_TPL_MESS_BTN_COMPARE"),
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage("CP_BCE_TPL_MESS_BTN_COMPARE_DEFAULT")
	);
}

$arTemplateParameters["MESS_NOT_AVAILABLE"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("CP_BCE_TPL_MESS_NOT_AVAILABLE"),
	"TYPE" => "STRING",
	"DEFAULT" => GetMessage("CP_BCE_TPL_MESS_NOT_AVAILABLE_DEFAULT")
);

$arTemplateParameters["USE_ENHANCED_ECOMMERCE"] = array(
	"PARENT" => "ANALYTICS_SETTINGS",
	"NAME" => GetMessage("CP_BCE_TPL_USE_ENHANCED_ECOMMERCE"),
	"TYPE" => "CHECKBOX",
	"REFRESH" => "Y",
	"DEFAULT" => "N"
);
if(isset($arCurrentValues["USE_ENHANCED_ECOMMERCE"]) && $arCurrentValues["USE_ENHANCED_ECOMMERCE"] === "Y") {
	$arTemplateParameters["DATA_LAYER_NAME"] = array(
		"PARENT" => "ANALYTICS_SETTINGS",
		"NAME" => GetMessage("CP_BCE_TPL_DATA_LAYER_NAME"),
		"TYPE" => "STRING",
		"DEFAULT" => "dataLayer"
	);
	$arTemplateParameters["BRAND_PROPERTY"] = array(
		"PARENT" => "ANALYTICS_SETTINGS",
		"NAME" => GetMessage("CP_BCE_TPL_BRAND_PROPERTY"),
		"TYPE" => "LIST",
		"MULTIPLE" => "N",
		"DEFAULT" => "",
		"VALUES" => $defaultValue + $arAllPropList
	);
}

$arTemplateParameters["USE_RATIO_IN_RANGES"] = array(
	"PARENT" => "PRICES",
	"NAME" => GetMessage("CP_BCE_TPL_USE_RATIO_IN_RANGES"),
	"TYPE" => "CHECKBOX",
	"HIDDEN" => isset($arCurrentValues["USE_PRICE_COUNT"]) && $arCurrentValues["USE_PRICE_COUNT"] === "Y" ? "N" : "Y",
	"DEFAULT" => "Y"
);