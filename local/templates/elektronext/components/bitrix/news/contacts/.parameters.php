<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader,
	Bitrix\Iblock,
	Bitrix\Catalog;

if(!Loader::includeModule("iblock"))
	return;

$catalogIncluded = Loader::includeModule("catalog");

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arSort = CIBlockParameters::GetElementSortFields(
	array("SHOWS", "SORT", "TIMESTAMP_X", "NAME", "ID", "ACTIVE_FROM", "ACTIVE_TO"),
	array("KEY_LOWERCASE" => "Y")
);

$arAscDesc = array(
	"asc" => GetMessage("CP_BN_SORT_ASC"),
	"desc" => GetMessage("CP_BN_SORT_DESC"),
);

$arObjectsIBlock = array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), array("TYPE" => $arCurrentValues["OBJECTS_IBLOCK_TYPE"], "ACTIVE" => "Y"));
while($arr = $rsIBlock->Fetch()) {
	$arObjectsIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}
unset($arr, $rsIBlock);

$arProperty_LNS = array();
$propertyIterator = Iblock\PropertyTable::getList(array(
	"select" => array("ID", "IBLOCK_ID", "NAME", "CODE", "PROPERTY_TYPE", "MULTIPLE", "LINK_IBLOCK_ID", "USER_TYPE", "SORT"),
	"filter" => array("=IBLOCK_ID" => $arCurrentValues["OBJECTS_IBLOCK_ID"], "=ACTIVE" => "Y"),
	"order" => array("SORT" => "ASC", "NAME" => "ASC")
));
while($property = $propertyIterator->fetch()) {	
	if(in_array($property["PROPERTY_TYPE"], array("L", "N", "S", "E"))) {
		$arProperty_LNS[$property["CODE"]] = "[".$property["CODE"]."] ".$property["NAME"];
	}
}
unset($property, $propertyIterator);

$arPromoIBlock = array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), array("TYPE" => $arCurrentValues["PROMOTIONS_IBLOCK_TYPE"], "ACTIVE" => "Y"));
while($arr = $rsIBlock->Fetch()) {
	$arPromoIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}
unset($arr, $rsIBlock);

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
$rsIBlock = CIBlock::GetList(array("SORT" => "ASC"), array("TYPE" => $arCurrentValues["CATALOG_IBLOCK_TYPE"], "ACTIVE" => "Y"));
while($arr = $rsIBlock->Fetch()) {
	$id = (int)$arr["ID"];
	if(isset($offersIblock[$id]))
		continue;
	$arCatalogIBlock[$id] = "[".$id."] ".$arr["NAME"];
}
unset($id, $arr, $rsIBlock);
unset($offersIblock);

$arReviewsIBlock = array();
$rsIBlock = CIBlock::GetList(array("sort" => "asc"), array("TYPE" => $arCurrentValues["OBJECTS_REVIEWS_IBLOCK_TYPE"], "ACTIVE" => "Y"));
while($arr = $rsIBlock->Fetch()) {
	$arReviewsIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
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

$arTemplateParameters["SHOW_OBJECTS"] = array(
	"NAME" => GetMessage("CP_BN_SHOW_OBJECTS"),
	"TYPE" => "CHECKBOX",
	"REFRESH" => "Y",
	"DEFAULT" => "Y",
);

if(isset($arCurrentValues["SHOW_OBJECTS"]) && $arCurrentValues["SHOW_OBJECTS"] == "Y") {
	$arTemplateParameters["OBJECTS_TITLE"] = array(
		"NAME" => GetMessage("CP_BN_OBJECTS_TITLE"),
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage("CP_BN_OBJECTS_TITLE_DEFAULT"),
	);
	$arTemplateParameters["OBJECTS_IBLOCK_TYPE"] = array(		
		"NAME" => GetMessage("CP_BN_OBJECTS_IBLOCK_TYPE"),
		"TYPE" => "LIST",		
		"REFRESH" => "Y",
		"VALUES" => $arIBlockType,
	);
	$arTemplateParameters["OBJECTS_IBLOCK_ID"] = array(		
		"NAME" => GetMessage("CP_BN_OBJECTS_IBLOCK_ID"),
		"TYPE" => "LIST",
		"REFRESH" => "Y",		
		"VALUES" => $arObjectsIBlock,
		"ADDITIONAL_VALUES" => "Y",
	);
	$arTemplateParameters["OBJECTS_NEWS_COUNT"] = array(		
		"NAME" => GetMessage("CP_BN_OBJECTS_NEWS_COUNT"),
		"TYPE" => "STRING",
		"DEFAULT" => "12",
	);	
	$arTemplateParameters["OBJECTS_SORT_BY1"] = array(		
		"NAME" => GetMessage("CP_BN_OBJECTS_SORT_BY1"),
		"TYPE" => "LIST",
		"DEFAULT" => "SORT",
		"VALUES" => $arSort,
		"ADDITIONAL_VALUES" => "Y",
	);	
	$arTemplateParameters["OBJECTS_SORT_ORDER1"] = array(		
		"NAME" => GetMessage("CP_BN_OBJECTS_SORT_ORDER1"),
		"TYPE" => "LIST",
		"DEFAULT" => "ASC",
		"VALUES" => $arAscDesc,
		"ADDITIONAL_VALUES" => "Y",
	);	
	$arTemplateParameters["OBJECTS_SORT_BY2"] = array(
		"NAME" => GetMessage("CP_BN_OBJECTS_SORT_BY2"),
		"TYPE" => "LIST",
		"DEFAULT" => "ACTIVE_FROM",
		"VALUES" => $arSort,
		"ADDITIONAL_VALUES" => "Y",
	);	
	$arTemplateParameters["OBJECTS_SORT_ORDER2"] = array(
		"NAME" => GetMessage("CP_BN_OBJECTS_SORT_ORDER2"),
		"TYPE" => "LIST",
		"DEFAULT" => "DESC",
		"VALUES" => $arAscDesc,
		"ADDITIONAL_VALUES" => "Y",
	);	
	$arTemplateParameters["OBJECTS_PROPERTY_CODE"] = array(		
		"NAME" => GetMessage("CP_BN_OBJECTS_PROPERTY_CODE"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arProperty_LNS,
		"ADDITIONAL_VALUES" => "Y",
	);
	$arTemplateParameters["OBJECTS_SHOW_PROMOTIONS"] = array(
		"NAME" => GetMessage("CP_BN_OBJECTS_SHOW_PROMOTIONS"),
		"TYPE" => "CHECKBOX",
		"REFRESH" => "Y",
		"DEFAULT" => "Y",
	);

	if(isset($arCurrentValues["OBJECTS_SHOW_PROMOTIONS"]) && $arCurrentValues["OBJECTS_SHOW_PROMOTIONS"] == "Y") {
		$arTemplateParameters["PROMOTIONS_IBLOCK_TYPE"] = array(		
			"NAME" => GetMessage("CP_BN_PROMOTIONS_IBLOCK_TYPE"),
			"TYPE" => "LIST",		
			"REFRESH" => "Y",
			"VALUES" => $arIBlockType,
		);
		$arTemplateParameters["PROMOTIONS_IBLOCK_ID"] = array(		
			"NAME" => GetMessage("CP_BN_PROMOTIONS_IBLOCK_ID"),
			"TYPE" => "LIST",
			"REFRESH" => "Y",		
			"VALUES" => $arPromoIBlock,
			"ADDITIONAL_VALUES" => "Y",
		);
	}

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

	$arTemplateParameters["OBJECTS_USE_REVIEW"] = array(
		"NAME" => GetMessage("CP_BN_OBJECTS_USE_REVIEW"),
		"TYPE" => "CHECKBOX",
		"REFRESH" => "Y",
		"DEFAULT" => "Y",
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
			"VALUES" => $arReviewsIBlock,
			"ADDITIONAL_VALUES" => "Y",
		);
	}
}