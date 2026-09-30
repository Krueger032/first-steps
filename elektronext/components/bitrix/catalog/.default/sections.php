<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

$APPLICATION->SetPageProperty("wideScreenMode", "-ws");

$GLOBALS[$arParams["FILTER_NAME"]] = array("UF_HIDDEN" => false);
$APPLICATION->IncludeComponent("bitrix:catalog.section.list", "catalog",
	array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"FILTER_NAME" => $arParams["FILTER_NAME"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"CACHE_FILTER" => $arParams["CACHE_FILTER"],
		"COUNT_ELEMENTS" => $arParams["SECTION_COUNT_ELEMENTS"],
		"COUNT_ELEMENTS_FILTER" => $arParams["HIDE_NOT_AVAILABLE"] == "Y" ? "CNT_AVAILABLE" : "CNT_ACTIVE",
		"TOP_DEPTH" => "1",
		"SECTION_FIELDS" => array(),
		"SECTION_USER_FIELDS" => array(
			0 => "UF_ICON"
		),
		"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"HIDE_SECTION_NAME" => (isset($arParams["SECTIONS_HIDE_SECTION_NAME"]) ? $arParams["SECTIONS_HIDE_SECTION_NAME"] : "N"),
		"ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : ""),
		"ADD_SECTION_TARGET" => "Y"
	),
	$component,
	array("HIDE_ICONS" => "Y")
);