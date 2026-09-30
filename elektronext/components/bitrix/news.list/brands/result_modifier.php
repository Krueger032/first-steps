<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

//MARKERS//
foreach($arResult["ITEMS"] as $arItem) {
	foreach($arItem["DISPLAY_PROPERTIES"] as $arProp) {
		if($arProp["CODE"] == "MARKER" && !empty($arProp["VALUE"])) {
			if(!is_array($arProp["VALUE"])) {
				$markersIds[] = $arProp["VALUE"];
			} else {
				foreach($arProp["VALUE"] as $val) {
					$markersIds[] = $val;
				}
				unset($val);
			}
		}
	}
	unset($arProp);
}
unset($arItem);

if(!empty($markersIds)) {	
	$rsElements = CIBlockElement::GetList(array(), array("ID" => array_unique($markersIds), "ACTIVE" => "Y"), false, false, array("ID", "IBLOCK_ID", "NAME", "SORT"));	
	while($obElement = $rsElements->GetNextElement()) {
		$arElement = $obElement->GetFields();
		$arElement["PROPERTIES"] = $obElement->GetProperties();
		
		$arMarkers[$arElement["ID"]] = array(
			"NAME" => $arElement["NAME"],
			"SORT" => $arElement["SORT"],
			"BACKGROUND_1" => $arElement["PROPERTIES"]["BACKGROUND_1"]["VALUE"],
			"BACKGROUND_2" => $arElement["PROPERTIES"]["BACKGROUND_2"]["VALUE"],
			"ICON" => $arElement["PROPERTIES"]["ICON"]["VALUE"],
			"FONT_SIZE" => $arElement["PROPERTIES"]["FONT_SIZE"]["VALUE_XML_ID"]
		);
	}
	unset($arElement, $obElement, $rsElements);

	if(!empty($arMarkers)) {
		foreach($arResult["ITEMS"] as &$arItem) {
			foreach($arItem["DISPLAY_PROPERTIES"] as $arProp) {
				if($arProp["CODE"] == "MARKER" && !empty($arProp["VALUE"])) {
					if(!is_array($arProp["VALUE"])) {
						if(array_key_exists($arProp["VALUE"], $arMarkers))
							$arItem["MARKER"][] = $arMarkers[$arProp["VALUE"]];
					} else {
						foreach($arProp["VALUE"] as $val) {
							if(array_key_exists($val, $arMarkers))
								$arItem["MARKER"][] = $arMarkers[$val];
						}
						unset($val);
					}
				}
			}
			unset($arProp);

			if(!empty($arItem["MARKER"]))
				Bitrix\Main\Type\Collection::sortByColumn($arItem["MARKER"], array("SORT" => SORT_NUMERIC, "NAME" => SORT_ASC));
		}
		unset($arItem);
	}
	unset($arMarkers);
}
unset($markersIds);

//ITEMS_COUNT//
$arResult["ITEMS_COUNT"] = 0;

//LETTERS//
$arResult["LETTERS"] = array();
$arResult["LETTERS_COUNT"] = array();
$arLetterElements = array();

$charset = defined("SITE_CHARSET") && SITE_CHARSET != "" ? SITE_CHARSET : "UTF-8";
$isUtf = strtoupper($charset) == "UTF-8";
$normalizeBrandLetter = static function($name) use ($charset, $isUtf) {
	$name = trim((string)$name);
	if($name == "")
		return "";

	if(!$isUtf)
		$name = Bitrix\Main\Text\Encoding::convertEncoding($name, $charset, "UTF-8");

	$letter = mb_strtoupper(mb_substr($name, 0, 1, "UTF-8"), "UTF-8");
	if($letter == mb_chr(0x401, "UTF-8"))
		$letter = mb_chr(0x415, "UTF-8");

	if(preg_match("/^[0-9]$/u", $letter))
		return "0-9";

	if(preg_match("/^[A-Z]$/", $letter))
		return $letter;

	$code = mb_ord($letter, "UTF-8");
	if($code >= 0x410 && $code <= 0x42F)
		return $letter;

	return "";
};

//COUNTRIES//
$rsElements = CIBlockElement::GetList(array(), array("ACTIVE" => "Y", "IBLOCK_ID" => $arParams["IBLOCK_ID"]), false, false, array("ID", "IBLOCK_ID", "NAME", "PROPERTY_COUNTRY"));
while($arElement = $rsElements->GetNext()) {
	$arResult["ITEMS_COUNT"]++;
	if(!empty($arElement["PROPERTY_COUNTRY_VALUE"]))
		$arResult["COUNTRIES_IDS"][] = $arElement["PROPERTY_COUNTRY_VALUE"];

	if(empty($arLetterElements[$arElement["ID"]])) {
		$arLetterElements[$arElement["ID"]] = true;
		$brandLetter = $normalizeBrandLetter(isset($arElement["~NAME"]) ? $arElement["~NAME"] : $arElement["NAME"]);
		if($brandLetter != "") {
			if(!isset($arResult["LETTERS_COUNT"][$brandLetter]))
				$arResult["LETTERS_COUNT"][$brandLetter] = 0;
			$arResult["LETTERS_COUNT"][$brandLetter]++;
		}
	}
}
unset($brandLetter, $arElement, $rsElements, $arLetterElements);

$lettersOrder = array("0-9");
for($code = 65; $code <= 90; $code++)
	$lettersOrder[] = chr($code);
for($code = 0x410; $code <= 0x42F; $code++)
	$lettersOrder[] = mb_chr($code, "UTF-8");

foreach($lettersOrder as $letter) {
	if(empty($arResult["LETTERS_COUNT"][$letter]))
		continue;

	$value = $letter;
	if($letter != "0-9" && !$isUtf)
		$value = Bitrix\Main\Text\Encoding::convertEncoding($letter, "UTF-8", $charset);

	$arResult["LETTERS"][] = array(
		"VALUE" => $value,
		"COUNT" => $arResult["LETTERS_COUNT"][$letter]
	);
}
unset($letter, $value, $lettersOrder, $code, $charset, $isUtf, $normalizeBrandLetter, $arResult["LETTERS_COUNT"]);

if(!empty($arResult["COUNTRIES_IDS"])) {
	$arCount = array_count_values($arResult["COUNTRIES_IDS"]);
	$rsElements = CIBlockElement::GetList(array("SORT" => "ASC", "NAME" => "ASC"), array("ID" => array_unique($arResult["COUNTRIES_IDS"])), false, false, array("ID", "IBLOCK_ID", "NAME"));	
	while($arElement = $rsElements->GetNext()) {
		$arResult["COUNTRIES"][] = array(
			"ID" => $arElement["ID"],
			"NAME" => $arElement["NAME"],
			"COUNT" => $arCount[$arElement["ID"]]
		);
	}
	unset($arElement, $rsElements, $arCount);
}
unset($arResult["COUNTRIES_IDS"]);

//SHOW//
$arResult["SHOW_COUNTRIES"] = !empty($arResult["COUNTRIES"]);
$arResult["SHOW_ALPHABET"] = !empty($arResult["LETTERS"]);