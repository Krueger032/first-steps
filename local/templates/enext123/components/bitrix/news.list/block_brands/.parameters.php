<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arTemplateParameters = array(
	"SHOW_ALL_LINK" => array(
		"NAME" => GetMessage("CP_BNL_SHOW_ALL_LINK"),
		"TYPE" => "CHECKBOX",
		"REFRESH" => "Y",
		"DEFAULT" => "Y",
	)
);

if(isset($arCurrentValues["SHOW_ALL_LINK"]) && $arCurrentValues["SHOW_ALL_LINK"] == "Y") {
	$arTemplateParameters["ALL_LINK_TITLE"] = array(
		"NAME" => GetMessage("CP_BNL_ALL_LINK_TITLE"),
		"TYPE" => "TEXT",
		"DEFAULT" => GetMessage("CP_BNL_ALL_LINK_TITLE_DEFAULT"),
	);
	$arTemplateParameters["ALL_LINK_URL"] = CIBlockParameters::GetPathTemplateParam(
		"LIST",
		"IBLOCK_URL",
		GetMessage("CP_BNL_ALL_LINK_URL"),
		"",
		"ADDITIONAL_SETTINGS"
	);
}