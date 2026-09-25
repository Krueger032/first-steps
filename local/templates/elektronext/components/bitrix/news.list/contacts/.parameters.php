<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

if(!Loader::includeModule("iblock"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arObjectsIBlock = array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), array("TYPE" => $arCurrentValues["OBJECTS_IBLOCK_TYPE"], "ACTIVE" => "Y"));
while($arr = $rsIBlock->Fetch()) {
	$arObjectsIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}
unset($arr, $rsIBlock);

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