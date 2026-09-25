<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

//COLLECTIONS_IDS//
if($arParams["SHOW_COLLECTIONS"] != "N") {
	$rsElements = CIBlockElement::GetList(array(), array("ACTIVE" => "Y", "IBLOCK_ID" => $arParams["COLLECTIONS_IBLOCK_ID"], "PROPERTY_BRAND" => $arResult["ID"]), false, false, array("ID", "IBLOCK_ID"));	
	while($arElement = $rsElements->GetNext()) {
		$arResult["COLLECTIONS_IDS"][] = $arElement["ID"];
	}
	unset($arElement, $rsElements);
}

//SECTIONS//
//PRODUCTS_IDS//
$rsElements = CIBlockElement::GetList(array(), array("ACTIVE" => "Y", "IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"], "SECTION_GLOBAL_ACTIVE" => "Y", "PROPERTY_BRAND" => $arResult["ID"]), false, false, array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID"));	
while($arElement = $rsElements->GetNext()) {	
	if(!empty($arElement["IBLOCK_SECTION_ID"]))
		$arResult["SECTIONS_IDS"][] = $arElement["IBLOCK_SECTION_ID"];
	$arResult["PRODUCTS_IDS"][] = $arElement["ID"];
}
unset($arElement, $rsElements);

if(!empty($arResult["SECTIONS_IDS"])) {
	$arCount = array_count_values($arResult["SECTIONS_IDS"]);
	$rsSections = CIBlockSection::GetList(array("NAME" => "ASC"), array("ID" => array_unique($arResult["SECTIONS_IDS"])), false, array("ID", "IBLOCK_ID", "NAME"));	
	while($arSection = $rsSections->GetNext()) {
		$arResult["SECTIONS"][] = array(
			"ID" => $arSection["ID"],
			"NAME" => $arSection["NAME"],
			"COUNT" => $arCount[$arSection["ID"]]
		);
	}
}