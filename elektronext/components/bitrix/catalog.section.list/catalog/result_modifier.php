<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if($arResult['SECTIONS_COUNT'] > 0) {	
	$boolClear = false;
	$arNewSections = array();
	foreach($arResult['SECTIONS'] as $arOneSection) {
		if($arOneSection['RELATIVE_DEPTH_LEVEL'] > 1) {
			$boolClear = true;
			continue;
		}
		$arNewSections[] = $arOneSection;
	}
	unset($arOneSection);
	if($boolClear) {
		$arResult['SECTIONS'] = $arNewSections;
		$arResult['SECTIONS_COUNT'] = count($arNewSections);
	}
	unset($arNewSections);
}