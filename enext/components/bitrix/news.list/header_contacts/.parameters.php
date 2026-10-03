<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader,
	Bitrix\Iblock,
	Bitrix\Catalog;

if(!Loader::includeModule("iblock"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arSort = CIBlockParameters::GetElementSortFields(
	array("SHOWS", "SORT", "TIMESTAMP_X", "NAME", "ID", "ACTIVE_FROM", "ACTIVE_TO"),
	array("KEY_LOWERCASE" => "Y")
);

$arAscDesc = array(
	"asc" => GetMessage("CP_BNL_SORT_ASC"),
	"desc" => GetMessage("CP_BNL_SORT_DESC"),
);

$arReviewsIBlock = array();
$rsIBlock = CIBlock::GetList(array("sort" => "asc"), array("TYPE" => $arCurrentValues["REVIEWS_IBLOCK_TYPE"], "ACTIVE" => "Y"));
while($arr = $rsIBlock->Fetch()) {
	$arReviewsIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}
unset($arr, $rsIBlock);

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

$arObjectsReviewsIBlock = array();
$rsIBlock = CIBlock::GetList(array("sort" => "asc"), array("TYPE" => $arCurrentValues["OBJECTS_REVIEWS_IBLOCK_TYPE"], "ACTIVE" => "Y"));
while($arr = $rsIBlock->Fetch()) {
	$arObjectsReviewsIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}
unset($arr, $rsIBlock);


$arTemplateParameters["USE_REVIEW"] = array(
	"NAME" => GetMessage("CP_BNL_USE_REVIEW"),
	"TYPE" => "CHECKBOX",
	"REFRESH" => "Y",
	"DEFAULT" => "Y",
);

if(isset($arCurrentValues["USE_REVIEW"]) && $arCurrentValues["USE_REVIEW"] == "Y") {
	$arTemplateParameters["REVIEWS_IBLOCK_TYPE"] = array(
		"NAME" => GetMessage("CP_BNL_REVIEWS_IBLOCK_TYPE"),
		"TYPE" => "LIST",		
		"REFRESH" => "Y",
		"VALUES" => $arIBlockType,
	);		
	$arTemplateParameters["REVIEWS_IBLOCK_ID"] = array(
		"NAME" => GetMessage("CP_BNL_REVIEWS_IBLOCK_ID"),
		"TYPE" => "LIST",
		"REFRESH" => "Y",		
		"VALUES" => $arReviewsIBlock,
		"ADDITIONAL_VALUES" => "Y",
	);
	$arTemplateParameters["REVIEWS_PAGE_LINK"] = array(
		"NAME" => GetMessage("CP_BNL_REVIEWS_PAGE_LINK"),
		"TYPE" => "STRING",
		"DEFAULT" => '={SITE_DIR."about/reviews/"}'
	);
}

$arTemplateParameters["SHOW_MAP"] = array(
	"NAME" => GetMessage("CP_BNL_SHOW_MAP"),
	"TYPE" => "CHECKBOX",
	"REFRESH" => "Y",
	"DEFAULT" => "Y",
);

$arTemplateParameters["SHOW_OBJECTS"] = array(
	"NAME" => GetMessage("CP_BNL_SHOW_OBJECTS"),
	"TYPE" => "CHECKBOX",
	"REFRESH" => "Y",
	"DEFAULT" => "Y",
);

if((isset($arCurrentValues["SHOW_MAP"]) && $arCurrentValues["SHOW_MAP"] == "Y") || (isset($arCurrentValues["SHOW_OBJECTS"]) && $arCurrentValues["SHOW_OBJECTS"] == "Y")) {
	$arTemplateParameters["OBJECTS_IBLOCK_TYPE"] = array(		
		"NAME" => GetMessage("CP_BNL_OBJECTS_IBLOCK_TYPE"),
		"TYPE" => "LIST",		
		"REFRESH" => "Y",
		"VALUES" => $arIBlockType,
	);
	$arTemplateParameters["OBJECTS_IBLOCK_ID"] = array(		
		"NAME" => GetMessage("CP_BNL_OBJECTS_IBLOCK_ID"),
		"TYPE" => "LIST",
		"REFRESH" => "Y",		
		"VALUES" => $arObjectsIBlock,
		"ADDITIONAL_VALUES" => "Y",
	);
}

if(isset($arCurrentValues["SHOW_OBJECTS"]) && $arCurrentValues["SHOW_OBJECTS"] == "Y") {
	$arTemplateParameters["OBJECTS_TITLE"] = array(
		"NAME" => GetMessage("CP_BNL_OBJECTS_TITLE"),
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage("CP_BNL_OBJECTS_TITLE_DEFAULT"),
	);	
	$arTemplateParameters["OBJECTS_NEWS_COUNT"] = array(		
		"NAME" => GetMessage("CP_BNL_OBJECTS_NEWS_COUNT"),
		"TYPE" => "STRING",
		"DEFAULT" => "12",
	);	
	$arTemplateParameters["OBJECTS_SORT_BY1"] = array(		
		"NAME" => GetMessage("CP_BNL_OBJECTS_SORT_BY1"),
		"TYPE" => "LIST",
		"DEFAULT" => "SORT",
		"VALUES" => $arSort,
		"ADDITIONAL_VALUES" => "Y",
	);	
	$arTemplateParameters["OBJECTS_SORT_ORDER1"] = array(		
		"NAME" => GetMessage("CP_BNL_OBJECTS_SORT_ORDER1"),
		"TYPE" => "LIST",
		"DEFAULT" => "ASC",
		"VALUES" => $arAscDesc,
		"ADDITIONAL_VALUES" => "Y",
	);	
	$arTemplateParameters["OBJECTS_SORT_BY2"] = array(
		"NAME" => GetMessage("CP_BNL_OBJECTS_SORT_BY2"),
		"TYPE" => "LIST",
		"DEFAULT" => "ACTIVE_FROM",
		"VALUES" => $arSort,
		"ADDITIONAL_VALUES" => "Y",
	);	
	$arTemplateParameters["OBJECTS_SORT_ORDER2"] = array(
		"NAME" => GetMessage("CP_BNL_OBJECTS_SORT_ORDER2"),
		"TYPE" => "LIST",
		"DEFAULT" => "DESC",
		"VALUES" => $arAscDesc,
		"ADDITIONAL_VALUES" => "Y",
	);	
	$arTemplateParameters["OBJECTS_PROPERTY_CODE"] = array(		
		"NAME" => GetMessage("CP_BNL_OBJECTS_PROPERTY_CODE"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arProperty_LNS,
		"ADDITIONAL_VALUES" => "Y",
	);
	$arTemplateParameters["OBJECTS_USE_REVIEW"] = array(
		"NAME" => GetMessage("CP_BNL_OBJECTS_USE_REVIEW"),
		"TYPE" => "CHECKBOX",
		"REFRESH" => "Y",
		"DEFAULT" => "Y",
	);	
	if(isset($arCurrentValues["OBJECTS_USE_REVIEW"]) && $arCurrentValues["OBJECTS_USE_REVIEW"] == "Y") {
		$arTemplateParameters["OBJECTS_REVIEWS_IBLOCK_TYPE"] = array(
			"NAME" => GetMessage("CP_BNL_OBJECTS_REVIEWS_IBLOCK_TYPE"),
			"TYPE" => "LIST",		
			"REFRESH" => "Y",
			"VALUES" => $arIBlockType,
		);		
		$arTemplateParameters["OBJECTS_REVIEWS_IBLOCK_ID"] = array(
			"NAME" => GetMessage("CP_BNL_OBJECTS_REVIEWS_IBLOCK_ID"),
			"TYPE" => "LIST",
			"REFRESH" => "Y",		
			"VALUES" => $arObjectsReviewsIBlock,
			"ADDITIONAL_VALUES" => "Y",
		);
	}
}