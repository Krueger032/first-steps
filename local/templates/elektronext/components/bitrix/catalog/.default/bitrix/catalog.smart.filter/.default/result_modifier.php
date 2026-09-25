<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('highloadblock'))
	return;

use Bitrix\Highloadblock as HL;

foreach($arResult["ITEMS"] as $key => &$arItem) {
	if(empty($arItem["VALUES"]) || isset($arItem["PRICE"]))
		continue;
	if($arItem["DISPLAY_TYPE"] == "A" && ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"] <= 0))
		continue;
	
	//CHECKBOXES_WITH_PICTURES//
	//CHECKBOXES_WITH_PICTURES_AND_LABELS//
	//DROPDOWN_WITH_PICTURES_AND_LABELS//
	if($arItem["DISPLAY_TYPE"] == "G" || $arItem["DISPLAY_TYPE"] == "H" || $arItem["DISPLAY_TYPE"] == "R") {		
		$hlblock = HL\HighloadBlockTable::getList(array('filter' => array('TABLE_NAME' => $arItem['USER_TYPE_SETTINGS']['TABLE_NAME'])))->fetch();		
		if(empty($hlblock))
			continue;
		
		$entity = HL\HighloadBlockTable::compileEntity($hlblock);
		
		$entityFields = $entity->getFields();
		if(!array_key_exists('UF_CODE', $entityFields))
			continue;
		
		$entityDataClass = $entity->getDataClass();

		$directorySelect = array('ID', 'UF_XML_ID', 'UF_CODE');
		$directoryOrder = array();

		$entityGetList = array(
			'select' => $directorySelect,
			'order' => $directoryOrder
		);
		$propEnums = $entityDataClass::getList($entityGetList);
		while($oneEnum = $propEnums->fetch()) {			
			$values[$oneEnum['UF_XML_ID']] = $oneEnum['UF_CODE'];
		}
		
		foreach($arItem['VALUES'] as &$val) {			
			if(isset($values[$val['URL_ID']]))
				$val['CODE'] = $values[$val['URL_ID']];
		}
		unset($val, $values);
	}
}
unset($arItem);