<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

if(!Loader::includeModule("iblock"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arContactsIBlock = array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), array("TYPE" => $arCurrentValues["CONTACTS_IBLOCK_TYPE"], "ACTIVE" => "Y"));
while($arr = $rsIBlock->Fetch()) {
	$arContactsIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}
unset($arr, $rsIBlock);

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