<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if(count($arResult["ITEMS"]) < 1)
	return;

//MARKERS//
foreach($arResult["ITEMS"] as $arItem) {
	if(!empty($arItem["IBLOCK_SECTION_ID"]))
		$sectionsIds[] = $arItem["IBLOCK_SECTION_ID"];
}
unset($arItem);

if(!empty($sectionsIds)) {
	$rsSections = CIBlockSection::GetList(array("NAME" => "ASC"), array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ID" => array_unique($sectionsIds)), false, array("ID", "IBLOCK_ID", "NAME", "UF_BACKGROUND_1", "UF_BACKGROUND_2"));	
	while($arSection = $rsSections->GetNext()) {
		$arSections[$arSection["ID"]] = array(
			"NAME" => $arSection["NAME"],
			"BACKGROUND_1" => $arSection["UF_BACKGROUND_1"],
			"BACKGROUND_2" => $arSection["UF_BACKGROUND_2"]
		);
	}
	unset($arSection, $rsSections);

	if(!empty($arSections)) {
		foreach($arResult["ITEMS"] as &$arItem) {
			if(!empty($arItem["IBLOCK_SECTION_ID"]) && array_key_exists($arItem["IBLOCK_SECTION_ID"], $arSections))
				$arItem["MARKER"] = $arSections[$arItem["IBLOCK_SECTION_ID"]];
		}
		unset($arItem);
	}
	unset($arSections);
}
unset($sectionsIds);