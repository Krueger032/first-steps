<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

ob_start();

//MARKER//
if(!empty($arResult["IBLOCK_SECTION_ID"])) {
	$rsSection = CIBlockSection::GetList(array(), array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ID" => $arResult["IBLOCK_SECTION_ID"]), false, array("ID", "IBLOCK_ID", "NAME", "UF_BACKGROUND_1", "UF_BACKGROUND_2"));	
	if($arSection = $rsSection->Fetch()) {
		$arResult["MARKER"] = array(
			"NAME" => $arSection["NAME"],
			"BACKGROUND_1" => $arSection["UF_BACKGROUND_1"],
			"BACKGROUND_2" => $arSection["UF_BACKGROUND_2"]
		);
	}
	unset($arSection, $rsSection);
}

//SECTIONS//
if(!empty($arResult["PROPERTIES"]["PRODUCTS"]["VALUE"])) {
	$rsElements = CIBlockElement::GetList(array(), array("ACTIVE" => "Y", "IBLOCK_ID" => $arResult["PROPERTIES"]["PRODUCTS"]["LINK_IBLOCK_ID"], "SECTION_GLOBAL_ACTIVE" => "Y", "ID" => $arResult["PROPERTIES"]["PRODUCTS"]["VALUE"]), false, false, array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID"));	
	while($arElement = $rsElements->GetNext()) {
		$ids[] = $arElement["ID"];
		if(!empty($arElement["IBLOCK_SECTION_ID"]))
			$arResult["SECTIONS_IDS"][] = $arElement["IBLOCK_SECTION_ID"];
	}
	unset($arElement, $rsElements);

	if(!empty($ids) && count($ids) != count($arResult["PROPERTIES"]["PRODUCTS"]["VALUE"])) {
		foreach($arResult["PROPERTIES"]["PRODUCTS"]["VALUE"] as $key => $val) {
			if(!in_array($val, $ids))
				unset($arResult["PROPERTIES"]["PRODUCTS"]["VALUE"][$key]);
		}
		unset($key, $val);
	}
	unset($ids);

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
}