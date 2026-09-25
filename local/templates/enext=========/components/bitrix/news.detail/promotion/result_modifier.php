<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

//DISPLAY_ACTIVE_TO//
if(!isset($arResult["DISPLAY_ACTIVE_TO"]) && !empty($arResult["ACTIVE_TO"]))
	$arResult["DISPLAY_ACTIVE_TO"] = CIBlockFormatProperties::DateFormat($arParams["ACTIVE_DATE_FORMAT"], MakeTimeStamp($arResult["ACTIVE_TO"], CSite::GetDateFormat()));

foreach($arResult["DISPLAY_PROPERTIES"] as $arProp) {
	//MARKERS//
	if($arProp["CODE"] == "MARKER" && !empty($arProp["VALUE"])) {
		$rsElement = CIBlockElement::GetList(array(), array("ID" => $arProp["VALUE"], "IBLOCK_ID" => $arProp["LINK_IBLOCK_ID"], "ACTIVE" => "Y"), false, false, array("ID", "IBLOCK_ID", "NAME", "SORT"));	
		while($obElement = $rsElement->GetNextElement()) {
			$arElement = $obElement->GetFields();
			$arElement["PROPERTIES"] = $obElement->GetProperties();

			$arResult["MARKER"][] = array(
				"NAME" => $arElement["NAME"],
				"SORT" => $arElement["SORT"],
				"BACKGROUND_1" => $arElement["PROPERTIES"]["BACKGROUND_1"]["VALUE"],
				"BACKGROUND_2" => $arElement["PROPERTIES"]["BACKGROUND_2"]["VALUE"],
				"ICON" => $arElement["PROPERTIES"]["ICON"]["VALUE"],
				"FONT_SIZE" => $arElement["PROPERTIES"]["FONT_SIZE"]["VALUE_XML_ID"]
			);
		}
		unset($arElement, $obElement, $rsElement);

		if(!empty($arResult["MARKER"]))
			Bitrix\Main\Type\Collection::sortByColumn($arResult["MARKER"], array("SORT" => SORT_NUMERIC, "NAME" => SORT_ASC));
	//SHOW_TIMER//
	} elseif($arProp["CODE"] == "SHOW_TIMER" && !empty($arProp["VALUE"])) {
		$arResult["SHOW_TIMER"] = $arProp["VALUE"];
	//SALE_DICOUNT_ID//
	} elseif($arProp["CODE"] == "SALE_DICOUNT_ID" && !empty($arProp["VALUE"])) {
		if(Bitrix\Main\Loader::includeModule("sale")) {
			$conditionLogics = array(
				"Equal" => "=",
				"Not" => "!",
				"Great" => ">",
				"Less" => "<",
				"EqGr" => ">=",
				"EqLs" => "<="
			);
			
			$arSelect = array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID");
			
			$rsActions = Bitrix\Sale\Internals\DiscountTable::getList(array(
				'select' => array("ID", "ACTIONS_LIST"),
				'filter' => array(
					"ACTIVE" => "Y",					
					"USE_COUPONS" => "N",
					"DISCOUNT_TYPE" => "P",
					"LID" => SITE_ID,
					"ID" => $arProp["VALUE"],
					array(
						"LOGIC" => "OR",
						array(
							"<=ACTIVE_FROM" => $DB->FormatDate(date("Y-m-d H:i:s"), "YYYY-MM-DD HH:MI:SS", CSite::GetDateFormat("FULL")),
							">=ACTIVE_TO" => $DB->FormatDate(date("Y-m-d H:i:s"), "YYYY-MM-DD HH:MI:SS", CSite::GetDateFormat("FULL"))
						),
						array(
							"=ACTIVE_FROM" => false,
							">=ACTIVE_TO" => $DB->FormatDate(date("Y-m-d H:i:s"),"YYYY-MM-DD HH:MI:SS", CSite::GetDateFormat("FULL"))
						),
						array(
							"<=ACTIVE_FROM" => $DB->FormatDate(date("Y-m-d H:i:s"),"YYYY-MM-DD HH:MI:SS", CSite::GetDateFormat("FULL")),
							"=ACTIVE_TO" => false
						),
						array(
							"=ACTIVE_FROM"=> false,
							"=ACTIVE_TO" => false
						)
					)
				)
			));
			while($arAction = $rsActions->fetch()) {
				$arActions[$arAction['ID']] = $arAction;
			}
			unset($arAction, $rsActions);

			if(!empty($arActions)) {
				foreach($arActions as $actionId => $action) {
					$arPredFilter = array("ACTIVE" => "Y", "ACTIVE_DATE" => "Y", "CAN_BUY" => "Y");
					$arFilter = $arPredFilter;
					$arAddFilter = $arPredFilter;
					$arAddFilter["=XML_ID"] = array();
					
					foreach($action['ACTIONS_LIST']['CHILDREN'] as $condition) {				
						foreach($condition['CHILDREN'] as $keyConditionSub => $conditionSub) {
							$cs = $conditionSub['DATA']['value'];
							$cls = $conditionLogics[$conditionSub['DATA']['logic']];					
							$classId = explode(':', $conditionSub['CLASS_ID']);
							
							if($classId[0] == 'ActSaleSubGrp') {
								foreach($conditionSub['CHILDREN'] as $keyConditionSubElem => $conditionSubElem) {
									$cse = $conditionSubElem['DATA']['value'];
									$clse = $conditionLogics[$conditionSubElem['DATA']['logic']];							
									$classIdEl = explode(':', $conditionSubElem['CLASS_ID']);
									
									if($classIdEl[0] == 'CondIBProp') {
										$arFilter["IBLOCK_ID"] = $classIdEl[1];
										$arFilter[$clse."PROPERTY_".$classIdEl[2]] = array_merge((array)$arFilter[$clse."PROPERTY_".$classIdEl[2]], (array)$cse);
										$arFilter[$clse."PROPERTY_".$classIdEl[2]] = array_unique($arFilter[$clse."PROPERTY_".$classIdEl[2]]);
									} elseif($classIdEl[0] == 'CondIBName') {
										$arFilter[$clse."NAME"] = array_merge((array)$arFilter[$clse."NAME"], (array)$cse);
										$arFilter[$clse."NAME"] = array_unique($arFilter[$clse."NAME"]);
									} elseif($classIdEl[0] == 'CondIBElement') {
										$arFilter[$clse."ID"] = array_merge((array)$arFilter[$clse."ID"], (array)$cse);
										$arFilter[$clse."ID"] = array_unique($arFilter[$clse."ID"]);
									} elseif($classIdEl[0] == 'CondIBTags') {
										$arFilter[$clse."TAGS"] = array_merge((array)$arFilter[$clse."TAGS"], (array)$cse);
										$arFilter[$clse."TAGS"] = array_unique($arFilter[$clse."TAGS"]);
									} elseif($classIdEl[0] == 'CondIBSection') {
										$arFilter[$clse."SECTION_ID"] = array_merge((array)$arFilter[$clse."SECTION_ID"], (array)$cse);
										$arFilter[$clse."SECTION_ID"] = array_unique($arFilter[$clse."SECTION_ID"]);
										$arFilter["INCLUDE_SUBSECTIONS"] = "Y";
									} elseif($classIdEl[0] == 'CondIBXmlID') {
										$arFilter[$clse."XML_ID"] = array_merge((array)$arFilter[$clse."XML_ID"], (array)$cse);
										$arFilter[$clse."XML_ID"] = array_unique($arFilter[$clse."XML_ID"]);
									} elseif($classIdEl[0] == 'CondBsktAppliedDiscount') {
										foreach($arActions as $tempAction) {
											if(($tempAction['SORT'] < $action['SORT'] && $tempAction['PRIORITY'] > $action['PRIORITY'] && $cse == 'N') || ($tempAction['SORT'] > $action['SORT'] && $tempAction['PRIORITY'] < $action['PRIORITY'] && $cse == 'Y')) {
												$arFilter = false;
												break 4;
											}
										}
										unset($tempAction);
									}
								}
								unset($clse, $cse, $classIdEl, $keyConditionSubElem, $conditionSubElem);
							} elseif($classId[0] == 'CondIBProp') {
								$arFilter["IBLOCK_ID"] = $classId[1];
								$arFilter[$cls."PROPERTY_".$classId[2]] = array_merge((array)$arFilter[$cls."PROPERTY_".$classId[2]], (array)$cs);
								$arFilter[$cls."PROPERTY_".$classId[2]] = array_unique($arFilter[$cls."PROPERTY_".$classId[2]]);
							} elseif($classId[0] == 'CondIBName') {
								$arFilter[$cls."NAME"] = array_merge((array)$arFilter[$cls."NAME"], (array)$cs);
								$arFilter[$cls."NAME"] = array_unique($arFilter[$cls."NAME"]);
							} elseif($classId[0] == 'CondIBElement') {
								$arFilter[$cls."ID"] = array_merge((array)$arFilter[$cls."ID"], (array)$cs);
								$arFilter[$cls."ID"] = array_unique($arFilter[$cls."ID"]);
							} elseif($classId[0] == 'CondIBTags') {
								$arFilter[$cls."TAGS"] = array_merge((array)$arFilter[$cls."TAGS"], (array)$cs);
								$arFilter[$cls."TAGS"] = array_unique($arFilter[$cls."TAGS"]);
							} elseif($classId[0] == 'CondIBSection') {
								$arFilter[$cls."SECTION_ID"] = array_merge((array)$arFilter[$cls."SECTION_ID"], (array)$cs);
								$arFilter[$cls."SECTION_ID"] = array_unique($arFilter[$cls."SECTION_ID"]);
								$arFilter["INCLUDE_SUBSECTIONS"] = "Y";
							} elseif($classId[0] == 'CondIBXmlID') {
								$arFilter[$cls."XML_ID"] = array_merge((array)$arFilter[$cls."XML_ID"], (array)$cs);
								$arFilter[$cls."XML_ID"] = array_unique($arFilter[$cls."XML_ID"]);
							} elseif($classId[0] == 'CondBsktAppliedDiscount') {
								foreach($arActions as $tempAction) {
									if(($tempAction['SORT'] < $action['SORT'] && $tempAction['PRIORITY'] > $action['PRIORITY'] && $cs == 'N') || ($tempAction['SORT'] > $action['SORT'] && $tempAction['PRIORITY'] < $action['PRIORITY'] && $cs == 'Y')) {
										$arFilter = false;
										break 3;
									}
								}
								unset($tempAction);
							}
						}
						unset($cls, $cs, $classId, $keyConditionSub, $conditionSub);
					}
					unset($condition);
					
					if($arFilter !== false && $arFilter != $arPredFilter) {
						if(!isset($arFilter['=XML_ID'])) {
							$rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
							while($arElement = $rsElements->GetNext()) {
								$mxResult = CCatalogSku::GetProductInfo($arElement["ID"]);
								if(is_array($mxResult)) {
									$productIds[] = $mxResult["ID"];
								} else {
									if(!empty($arElement["IBLOCK_SECTION_ID"]))
										$arResult["SECTIONS_IDS"][] = $arElement["IBLOCK_SECTION_ID"];
									$arResult["PRODUCTS_IDS"][] = $arElement["ID"];
								}
								unset($mxResult);
							}
							unset($arElement, $rsElements);
						} elseif(!empty($arFilter['=XML_ID'])) {
							$arAddFilter['=XML_ID'] = array_unique(array_merge($arFilter['=XML_ID'], $arAddFilter['=XML_ID']));
						}
					}
				}
				unset($arFilter, $arPredFilter, $actionId, $action);
				
				if(isset($arAddFilter) && !empty($arAddFilter['=XML_ID'])) {
					$rsElements = CIBlockElement::GetList(array(), $arAddFilter, false, array("nTopCount" => count($arAddFilter['=XML_ID'])), $arSelect);
					while($arElement = $rsElements->GetNext()) {
						$mxResult = CCatalogSku::GetProductInfo($arElement["ID"]);
						if(is_array($mxResult)) {
							$productIds[] = $mxResult["ID"];
						} else {						
							if(!empty($arElement["IBLOCK_SECTION_ID"]))
								$arResult["SECTIONS_IDS"][] = $arElement["IBLOCK_SECTION_ID"];
							$arResult["PRODUCTS_IDS"][] = $arElement["ID"];
						}
						unset($mxResult);
					}
					unset($arElement, $rsElements);
				}
				unset($arAddFilter);

				if(!empty($productIds)) {
					$rsElements = CIBlockElement::GetList(array(), array("ID" => array_unique($productIds)), false, false, $arSelect);
					while($arElement = $rsElements->GetNext()) {
						if(!empty($arElement["IBLOCK_SECTION_ID"]))
							$arResult["SECTIONS_IDS"][] = $arElement["IBLOCK_SECTION_ID"];
						$arResult["PRODUCTS_IDS"][] = $arElement["ID"];
					}
					unset($arElement, $rsElements);
				}
				unset($productIds);
			}
			unset($arActions);
		}
	//SECTIONS//
	} elseif($arProp["CODE"] == "SECTIONS" && !empty($arProp["VALUE"])) {
		$arFilter = array(
			"IBLOCK_ID" => $arProp["LINK_IBLOCK_ID"],
			"SECTION_ID" => $arProp["VALUE"],
			"INCLUDE_SUBSECTIONS" => "Y"
		);
		if(!empty($arResult["DISPLAY_PROPERTIES"]["BRANDS"]["VALUE"]))
			$arFilter["PROPERTY_BRAND"] = $arResult["DISPLAY_PROPERTIES"]["BRANDS"]["VALUE"];
		
		$rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID"));	
		while($arElement = $rsElements->GetNext()) {	
			if(!empty($arElement["IBLOCK_SECTION_ID"]))
				$arResult["SECTIONS_IDS"][] = $arElement["IBLOCK_SECTION_ID"];
			$arResult["PRODUCTS_IDS"][] = $arElement["ID"];
		}
		unset($arElement, $rsElements, $arFilter);
	//BRANDS//
	} elseif($arProp["CODE"] == "BRANDS" && !empty($arProp["VALUE"])) {		
		if(!empty($arResult["DISPLAY_PROPERTIES"]["SECTIONS"]["VALUE"]))
			continue;

		$rsElements = CIBlockElement::GetList(
			array(),
			array(
				"IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"],
				"PROPERTY_BRAND" => $arProp["VALUE"]
			),
			false,
			false,
			array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID")
		);	
		while($arElement = $rsElements->GetNext()) {	
			if(!empty($arElement["IBLOCK_SECTION_ID"]))
				$arResult["SECTIONS_IDS"][] = $arElement["IBLOCK_SECTION_ID"];
			$arResult["PRODUCTS_IDS"][] = $arElement["ID"];
		}
		unset($arElement, $rsElements);
	//PRODUCTS//
	} elseif($arProp["CODE"] == "PRODUCTS" && !empty($arProp["VALUE"])) {
		$rsElements = CIBlockElement::GetList(
			array(),
			array(
				"IBLOCK_ID" => $arProp["LINK_IBLOCK_ID"],
				"ID" => $arProp["VALUE"]
			),
			false,
			false,
			array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID")
		);	
		while($arElement = $rsElements->GetNext()) {	
			if(!empty($arElement["IBLOCK_SECTION_ID"]))
				$arResult["SECTIONS_IDS"][] = $arElement["IBLOCK_SECTION_ID"];
			$arResult["PRODUCTS_IDS"][] = $arElement["ID"];
		}
		unset($arElement, $rsElements);
	//OBJECT//
	} elseif($arProp["CODE"] == "OBJECT" && !empty($arProp["VALUE"])) {
		if($arParams["SHOW_OBJECT"] != "N") {
			$arResult["OBJECT"] = array(
				"IBLOCK_TYPE" => $arProp["LINK_ELEMENT_VALUE"][$arProp["VALUE"]]["IBLOCK_TYPE_ID"],
				"IBLOCK_ID" => $arProp["LINK_ELEMENT_VALUE"][$arProp["VALUE"]]["IBLOCK_ID"],
				"ID" => $arProp["LINK_ELEMENT_VALUE"][$arProp["VALUE"]]["ID"]
			);
		}
	}
}
unset($arProp);

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

if(!empty($arResult["PRODUCTS_IDS"]))
	$arResult["PRODUCTS_IDS"] = array_unique($arResult["PRODUCTS_IDS"]);

//CACHE_KEYS//
$this->__component->SetResultCacheKeys(
	array(
		"ID",
		"NAME",
		"ACTIVE_TO",
		"DISPLAY_ACTIVE_TO",		
		"PREVIEW_TEXT",
		"DETAIL_PICTURE",
		"DETAIL_TEXT",		
		"MARKER",
		"SHOW_TIMER",
		"SECTIONS",
		"PRODUCTS_IDS",
		"OBJECT"
	)
);?>