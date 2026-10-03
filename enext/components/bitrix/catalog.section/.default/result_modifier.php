<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Config\Option,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\Type\Collection,	
	Bitrix\Catalog\Product\Price;

$component = $this->getComponent();
$arParams = $component->applyTemplateModifications();

$arSettings = CEnext::GetFrontParametrsValues(SITE_ID);

//QUICK_VIEW//
if(!isset($arParams['QUICK_VIEW']))
	$arParams['QUICK_VIEW'] = $arSettings['QUICK_VIEW'];

//PRODUCTS_VIEW//
if(!isset($arParams['PRODUCTS_VIEW']))
	$arParams['PRODUCTS_VIEW'] = 'CARD';
	
//PRODUCTS_LIST_VIEW_MOBILE//
$arParams['PRODUCTS_LIST_VIEW_MOBILE'] = $arSettings['PRODUCTS_LIST_VIEW_MOBILE'];

//OFFERS_VIEW//
$arParams['OFFERS_VIEW'] = $arSettings['OFFERS_VIEW'];

//ASK_PRICE//
$arParams["ASK_PRICE"] = true;
if($arSettings["ASK_PRICE"] != "Y")
	$arParams["ASK_PRICE"] = false;

//UNDER_ORDER//
$arParams["UNDER_ORDER"] = true;
if($arSettings["UNDER_ORDER"] != "Y")
	$arParams["UNDER_ORDER"] = false;

//DISABLE_BASKET//
$arParams['DISABLE_BASKET'] = false;
if($arSettings['DISABLE_BASKET'] == 'Y') {
	$arParams['DISABLE_BASKET'] = true;
	if($arParams['USE_PRODUCT_QUANTITY'])
		$arParams['USE_PRODUCT_QUANTITY'] = false;
}

//DISABLE_DELAY//
$arParams['DISABLE_DELAY'] = false;
if($arSettings['DISABLE_DELAY'] == 'Y')
	$arParams['DISABLE_DELAY'] = true;

//SHOW_SUBSCRIBE//
if($arParams['PRODUCT_SUBSCRIPTION'] == 'Y') {
	$saleNotifyOption = Option::get('sale', 'subscribe_prod');
	if(strlen($saleNotifyOption) > 0)
		$saleNotifyOption = unserialize($saleNotifyOption);
	$saleNotifyOption = is_array($saleNotifyOption) ? $saleNotifyOption : array();
	foreach($saleNotifyOption as $siteId => $data) {
		if($siteId == SITE_ID && $data['use'] != 'Y')
			$arParams['PRODUCT_SUBSCRIPTION'] = 'N';
	}
}

//OLD_PRICE//
foreach($arResult['ITEMS'] as $key => &$item) {	
	if(!empty($item["OFFERS"])) {
		foreach($item["OFFERS"] as $keyOffer => &$arOffer) {			
			if(!empty($arOffer['PROPERTIES']['OLD_PRICE']['VALUE']) || !empty($item['PROPERTIES']['OLD_PRICE']['VALUE'])) {
				$oldPrice = !empty($arOffer['PROPERTIES']['OLD_PRICE']['VALUE']) ? str_replace(',', '.', $arOffer['PROPERTIES']['OLD_PRICE']['VALUE']) : str_replace(',', '.', $item['PROPERTIES']['OLD_PRICE']['VALUE']);
				foreach($arOffer['ITEM_PRICES'] as $keyPrice => &$arPrice) {
					if($arPrice['PRICE'] > 0 && $arPrice['PERCENT'] == 0) {
						$arPrice['UNROUND_BASE_PRICE'] = $oldPrice;
						$arPrice['BASE_PRICE'] = Price::roundPrice($arPrice['PRICE_TYPE_ID'], $oldPrice, $arPrice['CURRENCY']);
						$arPrice['PRINT_BASE_PRICE'] = CCurrencyLang::CurrencyFormat($arPrice['BASE_PRICE'], $arPrice['CURRENCY'], true);
						$arPrice['RATIO_BASE_PRICE'] = Price::roundPrice($arPrice['PRICE_TYPE_ID'], $arPrice['BASE_PRICE'] * $arPrice['MIN_QUANTITY'], $arPrice['CURRENCY']);
						$arPrice['PRINT_RATIO_BASE_PRICE'] = CCurrencyLang::CurrencyFormat($arPrice['RATIO_BASE_PRICE'], $arPrice['CURRENCY'], true);
						$arPrice['DISCOUNT'] = Price::roundPrice($arPrice['PRICE_TYPE_ID'], $arPrice['BASE_PRICE'] - $arPrice['PRICE'], $arPrice['CURRENCY']);
						$arPrice['PRINT_DISCOUNT'] = CCurrencyLang::CurrencyFormat($arPrice['DISCOUNT'], $arPrice['CURRENCY'], true);
						$arPrice['RATIO_DISCOUNT'] = Price::roundPrice($arPrice['PRICE_TYPE_ID'], $arPrice['RATIO_BASE_PRICE'] - $arPrice['RATIO_PRICE'], $arPrice['CURRENCY']);
						$arPrice['PRINT_RATIO_DISCOUNT'] = CCurrencyLang::CurrencyFormat($arPrice['RATIO_DISCOUNT'], $arPrice['CURRENCY'], true);
						$arPrice['PERCENT'] = roundEx(100 * $arPrice['DISCOUNT'] / $arPrice['BASE_PRICE'], 0);
						
						$arResult['ITEMS'][$key]['JS_OFFERS'][$keyOffer]['ITEM_PRICES'][$keyPrice]['UNROUND_BASE_PRICE'] = $arPrice['UNROUND_BASE_PRICE'];
						$arResult['ITEMS'][$key]['JS_OFFERS'][$keyOffer]['ITEM_PRICES'][$keyPrice]['BASE_PRICE'] = $arPrice['BASE_PRICE'];
						$arResult['ITEMS'][$key]['JS_OFFERS'][$keyOffer]['ITEM_PRICES'][$keyPrice]['PRINT_BASE_PRICE'] = $arPrice['PRINT_BASE_PRICE'];
						$arResult['ITEMS'][$key]['JS_OFFERS'][$keyOffer]['ITEM_PRICES'][$keyPrice]['RATIO_BASE_PRICE'] = $arPrice['RATIO_BASE_PRICE'];
						$arResult['ITEMS'][$key]['JS_OFFERS'][$keyOffer]['ITEM_PRICES'][$keyPrice]['PRINT_RATIO_BASE_PRICE'] = $arPrice['PRINT_RATIO_BASE_PRICE'];
						$arResult['ITEMS'][$key]['JS_OFFERS'][$keyOffer]['ITEM_PRICES'][$keyPrice]['DISCOUNT'] = $arPrice['DISCOUNT'];
						$arResult['ITEMS'][$key]['JS_OFFERS'][$keyOffer]['ITEM_PRICES'][$keyPrice]['PRINT_DISCOUNT'] = $arPrice['PRINT_DISCOUNT'];
						$arResult['ITEMS'][$key]['JS_OFFERS'][$keyOffer]['ITEM_PRICES'][$keyPrice]['RATIO_DISCOUNT'] = $arPrice['RATIO_DISCOUNT'];
						$arResult['ITEMS'][$key]['JS_OFFERS'][$keyOffer]['ITEM_PRICES'][$keyPrice]['PRINT_RATIO_DISCOUNT'] = $arPrice['PRINT_RATIO_DISCOUNT'];
						$arResult['ITEMS'][$key]['JS_OFFERS'][$keyOffer]['ITEM_PRICES'][$keyPrice]['PERCENT'] = $arPrice['PERCENT'];
					}
				}
				unset($keyPrice, $arPrice, $oldPrice);
			}
		}
		unset($keyOffer, $arOffer);
	} elseif(!empty($item['PROPERTIES']['OLD_PRICE']['VALUE'])) {
		$oldPrice = str_replace(',', '.', $item['PROPERTIES']['OLD_PRICE']['VALUE']);
		foreach($item['ITEM_PRICES'] as &$arPrice) {
			if($arPrice['PRICE'] > 0 && $arPrice['PERCENT'] == 0) {
				$arPrice['UNROUND_BASE_PRICE'] = $oldPrice;
				$arPrice['BASE_PRICE'] = Price::roundPrice($arPrice['PRICE_TYPE_ID'], $oldPrice, $arPrice['CURRENCY']);
				$arPrice['PRINT_BASE_PRICE'] = CCurrencyLang::CurrencyFormat($arPrice['BASE_PRICE'], $arPrice['CURRENCY'], true);
				$arPrice['RATIO_BASE_PRICE'] = Price::roundPrice($arPrice['PRICE_TYPE_ID'], $arPrice['BASE_PRICE'] * $arPrice['MIN_QUANTITY'], $arPrice['CURRENCY']);
				$arPrice['PRINT_RATIO_BASE_PRICE'] = CCurrencyLang::CurrencyFormat($arPrice['RATIO_BASE_PRICE'], $arPrice['CURRENCY'], true);
				$arPrice['DISCOUNT'] = Price::roundPrice($arPrice['PRICE_TYPE_ID'], $arPrice['BASE_PRICE'] - $arPrice['PRICE'], $arPrice['CURRENCY']);
				$arPrice['PRINT_DISCOUNT'] = CCurrencyLang::CurrencyFormat($arPrice['DISCOUNT'], $arPrice['CURRENCY'], true);
				$arPrice['RATIO_DISCOUNT'] = Price::roundPrice($arPrice['PRICE_TYPE_ID'], $arPrice['RATIO_BASE_PRICE'] - $arPrice['RATIO_PRICE'], $arPrice['CURRENCY']);
				$arPrice['PRINT_RATIO_DISCOUNT'] = CCurrencyLang::CurrencyFormat($arPrice['RATIO_DISCOUNT'], $arPrice['CURRENCY'], true);
				$arPrice['PERCENT'] = roundEx(100 * $arPrice['DISCOUNT'] / $arPrice['BASE_PRICE'], 0);
			}
		}
		unset($arPrice, $oldPrice);
	}
}
unset($item);

//MEASURE//
$measureIds = $arMeasureList = array();

foreach($arResult['ITEMS'] as $item) {
	if(!empty($item['OFFERS'])) {
		foreach($item['OFFERS'] as $arOffer) {
			$measureIds[] = $arOffer['ITEM_MEASURE']['ID'];
		}
		unset($arOffer);
	} else {
		$measureIds[] = $item['ITEM_MEASURE']['ID'];
	}
}
unset($item);

if(count($measureIds) > 0) {
	$rsMeasures = CCatalogMeasure::getList(array(), array('ID' => array_unique($measureIds)), false, false, array('ID', 'SYMBOL_INTL'));
	while($arMeasure = $rsMeasures->GetNext()) {
		$arMeasureList[$arMeasure['ID']] = $arMeasure['SYMBOL_INTL'];
	}
	unset($arMeasure, $rsMeasures);

	foreach($arResult['ITEMS'] as $key => &$item) {
		if(!empty($item['OFFERS'])) {
			foreach($item['OFFERS'] as $keyOffer => &$arOffer) {
				if(array_key_exists($arOffer['ITEM_MEASURE']['ID'], $arMeasureList)) {
					$arOffer['ITEM_MEASURE']['SYMBOL_INTL'] = $arMeasureList[$arOffer['ITEM_MEASURE']['ID']];
					$arResult['ITEMS'][$key]['JS_OFFERS'][$keyOffer]['ITEM_MEASURE'] = $arOffer['ITEM_MEASURE'];
				}
			}
			unset($keyOffer, $arOffer);
		} else {
			if(array_key_exists($item['ITEM_MEASURE']['ID'], $arMeasureList))
				$item['ITEM_MEASURE']['SYMBOL_INTL'] = $arMeasureList[$item['ITEM_MEASURE']['ID']];
		}
	}
	unset($key, $item);
}
unset($arMeasureList, $measureIds);

//SQ_M_PRICE//
//PC_PRICE//
foreach($arResult['ITEMS'] as $key => &$item) {
	if(!empty($item['PROPERTIES']['M2_COUNT']['VALUE'])) {
		$sqMCount = str_replace(',', '.', $item['PROPERTIES']['M2_COUNT']['VALUE']);	
		if(!empty($item['OFFERS'])) {
			foreach($item['OFFERS'] as $keyOffer => &$arOffer) {
				$measureRatio = $arOffer['ITEM_MEASURE_RATIOS'][$arOffer['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'];
				if($arOffer['ITEM_MEASURE']['SYMBOL_INTL'] == 'pc. 1') {
					$arOffer['PC_MAX_QUANTITY'] = $arOffer['CATALOG_QUANTITY'];
					$arOffer['PC_STEP_QUANTITY'] = $measureRatio;				
					$arOffer['SQ_M_MAX_QUANTITY'] = round($arOffer['CATALOG_QUANTITY'] / $sqMCount, 4);
					$arOffer['SQ_M_STEP_QUANTITY'] = round($measureRatio / $sqMCount, 4);

					$arResult['ITEMS'][$key]['JS_OFFERS'][$keyOffer]['PC_MAX_QUANTITY'] = $arOffer['PC_MAX_QUANTITY'];
					$arResult['ITEMS'][$key]['JS_OFFERS'][$keyOffer]['PC_STEP_QUANTITY'] = $arOffer['PC_STEP_QUANTITY'];				
					$arResult['ITEMS'][$key]['JS_OFFERS'][$keyOffer]['SQ_M_MAX_QUANTITY'] = $arOffer['SQ_M_MAX_QUANTITY'];
					$arResult['ITEMS'][$key]['JS_OFFERS'][$keyOffer]['SQ_M_STEP_QUANTITY'] = $arOffer['SQ_M_STEP_QUANTITY'];
					
					foreach($arOffer['ITEM_PRICES'] as $keyPrice => &$arPrice) {
						$arPrice['SQ_M_BASE_PRICE'] = Price::roundPrice($arPrice['PRICE_TYPE_ID'], $arPrice['BASE_PRICE'] * $sqMCount, $arPrice['CURRENCY']);
						$arPrice['SQ_M_PRINT_BASE_PRICE'] = CCurrencyLang::CurrencyFormat($arPrice['SQ_M_BASE_PRICE'], $arPrice['CURRENCY'], true);
						$arPrice['SQ_M_PRICE'] = Price::roundPrice($arPrice['PRICE_TYPE_ID'], $arPrice['PRICE'] * $sqMCount, $arPrice['CURRENCY']);	
						$arPrice['SQ_M_PRINT_PRICE'] = CCurrencyLang::CurrencyFormat($arPrice['SQ_M_PRICE'], $arPrice['CURRENCY'], true);
						$arPrice['SQ_M_DISCOUNT'] = Price::roundPrice($arPrice['PRICE_TYPE_ID'], $arPrice['SQ_M_BASE_PRICE'] - $arPrice['SQ_M_PRICE'], $arPrice['CURRENCY']);
						$arPrice['SQ_M_PRINT_DISCOUNT'] = CCurrencyLang::CurrencyFormat($arPrice['SQ_M_DISCOUNT'], $arPrice['CURRENCY'], true);
						$arPrice['PC_MIN_QUANTITY'] = $arPrice['MIN_QUANTITY'];
						$arPrice['SQ_M_MIN_QUANTITY'] = round($arPrice['MIN_QUANTITY'] / $sqMCount, 4);

						$arResult['ITEMS'][$key]['JS_OFFERS'][$keyOffer]['ITEM_PRICES'][$keyPrice]['SQ_M_BASE_PRICE'] = $arPrice['SQ_M_BASE_PRICE'];
						$arResult['ITEMS'][$key]['JS_OFFERS'][$keyOffer]['ITEM_PRICES'][$keyPrice]['SQ_M_PRINT_BASE_PRICE'] = $arPrice['SQ_M_PRINT_BASE_PRICE'];
						$arResult['ITEMS'][$key]['JS_OFFERS'][$keyOffer]['ITEM_PRICES'][$keyPrice]['SQ_M_PRICE'] = $arPrice['SQ_M_PRICE'];
						$arResult['ITEMS'][$key]['JS_OFFERS'][$keyOffer]['ITEM_PRICES'][$keyPrice]['SQ_M_PRINT_PRICE'] = $arPrice['SQ_M_PRINT_PRICE'];
						$arResult['ITEMS'][$key]['JS_OFFERS'][$keyOffer]['ITEM_PRICES'][$keyPrice]['SQ_M_DISCOUNT'] = $arPrice['SQ_M_DISCOUNT'];
						$arResult['ITEMS'][$key]['JS_OFFERS'][$keyOffer]['ITEM_PRICES'][$keyPrice]['SQ_M_PRINT_DISCOUNT'] = $arPrice['SQ_M_PRINT_DISCOUNT'];
						$arResult['ITEMS'][$key]['JS_OFFERS'][$keyOffer]['ITEM_PRICES'][$keyPrice]['PC_MIN_QUANTITY'] = $arPrice['PC_MIN_QUANTITY'];
						$arResult['ITEMS'][$key]['JS_OFFERS'][$keyOffer]['ITEM_PRICES'][$keyPrice]['SQ_M_MIN_QUANTITY'] = $arPrice['SQ_M_MIN_QUANTITY'];
					}
					unset($keyPrice, $arPrice);
				} elseif($arOffer['ITEM_MEASURE']['SYMBOL_INTL'] == 'm2') {
					$arOffer['PC_MAX_QUANTITY'] = floor($arOffer['CATALOG_QUANTITY'] / $measureRatio);
					$arOffer['PC_STEP_QUANTITY'] = 1;
					$arOffer['SQ_M_MAX_QUANTITY'] = $arOffer['CATALOG_QUANTITY'];
					$arOffer['SQ_M_STEP_QUANTITY'] = $measureRatio;

					$arResult['ITEMS'][$key]['JS_OFFERS'][$keyOffer]['PC_MAX_QUANTITY'] = $arOffer['PC_MAX_QUANTITY'];
					$arResult['ITEMS'][$key]['JS_OFFERS'][$keyOffer]['PC_STEP_QUANTITY'] = $arOffer['PC_STEP_QUANTITY'];
					$arResult['ITEMS'][$key]['JS_OFFERS'][$keyOffer]['SQ_M_MAX_QUANTITY'] = $arOffer['SQ_M_MAX_QUANTITY'];
					$arResult['ITEMS'][$key]['JS_OFFERS'][$keyOffer]['SQ_M_STEP_QUANTITY'] = $arOffer['SQ_M_STEP_QUANTITY'];

					foreach($arOffer['ITEM_PRICES'] as $keyPrice => &$arPrice) {
						$arPrice['PC_MIN_QUANTITY'] = 1;
						$arPrice['SQ_M_MIN_QUANTITY'] = $arPrice['MIN_QUANTITY'];

						$arResult['ITEMS'][$key]['JS_OFFERS'][$keyOffer]['ITEM_PRICES'][$keyPrice]['PC_MIN_QUANTITY'] = $arPrice['PC_MIN_QUANTITY'];
						$arResult['ITEMS'][$key]['JS_OFFERS'][$keyOffer]['ITEM_PRICES'][$keyPrice]['SQ_M_MIN_QUANTITY'] = $arPrice['SQ_M_MIN_QUANTITY'];
					}
					unset($keyPrice, $arPrice);
				}
			}
			unset($keyOffer, $arOffer, $measureRatio);
		} else {
			if($item['ITEM_MEASURE']['SYMBOL_INTL'] == 'pc. 1') {
				foreach($item['ITEM_PRICES'] as &$arPrice) {
					$arPrice['SQ_M_BASE_PRICE'] = Price::roundPrice($arPrice['PRICE_TYPE_ID'], $arPrice['BASE_PRICE'] * $sqMCount, $arPrice['CURRENCY']);
					$arPrice['SQ_M_PRINT_BASE_PRICE'] = CCurrencyLang::CurrencyFormat($arPrice['SQ_M_BASE_PRICE'], $arPrice['CURRENCY'], true);
					$arPrice['SQ_M_PRICE'] = Price::roundPrice($arPrice['PRICE_TYPE_ID'], $arPrice['PRICE'] * $sqMCount, $arPrice['CURRENCY']);	
					$arPrice['SQ_M_PRINT_PRICE'] = CCurrencyLang::CurrencyFormat($arPrice['SQ_M_PRICE'], $arPrice['CURRENCY'], true);
					$arPrice['SQ_M_DISCOUNT'] = Price::roundPrice($arPrice['PRICE_TYPE_ID'], $arPrice['SQ_M_BASE_PRICE'] - $arPrice['SQ_M_PRICE'], $arPrice['CURRENCY']);
					$arPrice['SQ_M_PRINT_DISCOUNT'] = CCurrencyLang::CurrencyFormat($arPrice['SQ_M_DISCOUNT'], $arPrice['CURRENCY'], true);
					$arPrice['PC_MIN_QUANTITY'] = $arPrice['MIN_QUANTITY'];
					$arPrice['SQ_M_MIN_QUANTITY'] = round($arPrice['MIN_QUANTITY'] / $sqMCount, 4);
				}
				unset($arPrice);
			} elseif($item['ITEM_MEASURE']['SYMBOL_INTL'] == 'm2') {
				foreach($item['ITEM_PRICES'] as &$arPrice) {
					$arPrice['PC_MIN_QUANTITY'] = 1;
					$arPrice['SQ_M_MIN_QUANTITY'] = $arPrice['MIN_QUANTITY'];
				}
				unset($arPrice);
			}
		}
		unset($sqMCount);
	}
}
unset($item);

//SECTION_PATH//
$rsPath = CIBlockSection::GetNavChain($arResult['IBLOCK_ID'], $arResult['ID'], array('ID', 'IBLOCK_ID', 'NAME', 'SECTION_PAGE_URL'));
while($arPath = $rsPath->GetNext()) {
	$arResult['SECTION_PATH'][] = $arPath;
}
unset($arPath, $rsPath);

//MARKERS_BRANDS_OBJECTS_ADVANTAGES//
foreach($arResult['ITEMS'] as $item) {
	foreach($item['PROPERTIES'] as $prop) {
		if($prop['CODE'] == 'MARKER' && !empty($prop['VALUE'])) {
			if(!is_array($prop['VALUE'])) {
				$markersIds[] = $prop['VALUE'];
			} else {
				foreach($prop['VALUE'] as $val) {
					$markersIds[] = $val;
				}
				unset($val);
			}
		} elseif($prop['CODE'] == 'BRAND' && !empty($prop['VALUE'])) {
			$brandsIds[] = $prop['VALUE'];
		} elseif($prop['CODE'] == 'OBJECT' && !empty($prop['VALUE'])) {
			$objectsIds[] = $prop['VALUE'];
		} elseif($prop['CODE'] == 'ADVANTAGES' && $arParams['PRODUCTS_VIEW'] == 'LIST') {
			if(empty($prop['VALUE'])) {
				foreach($arResult['SECTION_PATH'] as $arSectionPath) {
					$sectionIds[] = $arSectionPath['ID'];
				}
				unset($arSectionPath);
				
				if(!empty($sectionIds)) {		
					$rsSections = CIBlockSection::GetList(array('DEPTH_LEVEL' => 'DESC'), array('ID' => $sectionIds, 'IBLOCK_ID' => $arResult['IBLOCK_ID']), false, array('ID', 'UF_ADVANTAGES'));
					while($arSection = $rsSections->GetNext()) {
						if(empty($prop['VALUE']) && !empty($arSection['UF_ADVANTAGES']))
							$prop['VALUE'] = $arSection['UF_ADVANTAGES'];
					}
					unset($arSection, $rsSections);
				}
				unset($sectionIds);
			}

			if(!empty($prop['VALUE'])) {
				if(!is_array($prop['VALUE'])) {
					$advantagesIds[] = $prop['VALUE'];
				} else {
					foreach($prop['VALUE'] as $val) {
						$advantagesIds[] = $val;
					}
					unset($val);
				}
			}
		}
	}
	unset($prop);
}
unset($item);

//MARKERS//
if(!empty($markersIds)) {	
	$rsElements = CIBlockElement::GetList(array(), array('ID' => array_unique($markersIds), 'ACTIVE' => 'Y'), false, false, array('ID', 'IBLOCK_ID', 'NAME', 'SORT'));	
	while($obElement = $rsElements->GetNextElement()) {
		$arElement = $obElement->GetFields();
		$arElement['PROPERTIES'] = $obElement->GetProperties();
		
		$arMarkers[$arElement['ID']] = array(
			'NAME' => $arElement['NAME'],
			'SORT' => $arElement['SORT'],
			'BACKGROUND_1' => $arElement['PROPERTIES']['BACKGROUND_1']['VALUE'],
			'BACKGROUND_2' => $arElement['PROPERTIES']['BACKGROUND_2']['VALUE'],
			'ICON' => $arElement['PROPERTIES']['ICON']['VALUE'],
			'FONT_SIZE' => $arElement['PROPERTIES']['FONT_SIZE']['VALUE_XML_ID']
		);
	}
	unset($arElement, $obElement, $rsElements);

	if(!empty($arMarkers)) {
		foreach($arResult['ITEMS'] as &$item) {
			foreach($item['PROPERTIES'] as &$prop) {
				if($prop['CODE'] == 'MARKER' && !empty($prop['VALUE'])) {
					if(!is_array($prop['VALUE'])) {
						if(array_key_exists($prop['VALUE'], $arMarkers))
							$prop['FULL_VALUE'][] = $arMarkers[$prop['VALUE']];
					} else {
						foreach($prop['VALUE'] as $val) {
							if(array_key_exists($val, $arMarkers))
								$prop['FULL_VALUE'][] = $arMarkers[$val];
						}
						unset($val);
					}
					
					if(!empty($prop['FULL_VALUE']))
						Collection::sortByColumn($prop['FULL_VALUE'], array('SORT' => SORT_NUMERIC, 'NAME' => SORT_ASC));
				}
			}
			unset($prop);
		}
		unset($item);
	}
	unset($arMarkers);
}
unset($markersIds);

//BRANDS//
if(!empty($brandsIds)) {
	$rsElements = CIBlockElement::GetList(array(), array('ID' => array_unique($brandsIds)), false, false, array('ID', 'IBLOCK_ID', 'NAME', 'PREVIEW_PICTURE'));
	while($arElement = $rsElements->GetNext()) {
		$arBrands[$arElement['ID']] = array(
			'NAME' => $arElement['NAME'],
			'PREVIEW_PICTURE' => $arElement['PREVIEW_PICTURE'] > 0 ? CFile::GetFileArray($arElement['PREVIEW_PICTURE']) : array()
		);
	}
	unset($arElement, $rsElements);
	
	if(!empty($arBrands)) {
		foreach($arResult['ITEMS'] as &$item) {		
			foreach($item['PROPERTIES'] as &$prop) {
				if($prop['CODE'] == 'BRAND' && !empty($prop['VALUE'])) {
					if(array_key_exists($prop['VALUE'], $arBrands))
						$prop['FULL_VALUE'] = $arBrands[$prop['VALUE']];
				}
			}
			unset($prop);
		}
		unset($item);
	}
	unset($arBrands);
}
unset($brandsIds);

//OBJECTS//
if(!empty($objectsIds)) {
	$rsElements = CIBlockElement::GetList(array(), array('ID' => array_unique($objectsIds)), false, false, array('ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_PHONE_SMS', 'PROPERTY_EMAIL_EMAIL'));
	while($arElement = $rsElements->GetNext()) {
		$arObjects[$arElement['ID']] = array(
			'ID' => $arElement['ID'],
			'NAME' => $arElement['NAME'],
			'PHONE_SMS' => !empty($arElement['PROPERTY_PHONE_SMS_VALUE']),
			'EMAIL_EMAIL' => !empty($arElement['PROPERTY_EMAIL_EMAIL_VALUE'])
		);
	}
	unset($arElement, $rsElements);
	
	if(!empty($arObjects)) {
		foreach($arResult['ITEMS'] as &$item) {		
			foreach($item['PROPERTIES'] as &$prop) {
				if($prop['CODE'] == 'OBJECT' && !empty($prop['VALUE'])) {
					if(array_key_exists($prop['VALUE'], $arObjects))
						$prop['FULL_VALUE'] = $arObjects[$prop['VALUE']];
				}
			}
			unset($prop);
		}
		unset($item);
	}
	unset($arObjects);
}
unset($objectsIds);

//ADVANTAGES//
if(!empty($advantagesIds)) {
	$rsElements = CIBlockElement::GetList(array(), array('ID' => array_unique($advantagesIds)), false, false, array('ID', 'IBLOCK_ID', 'NAME', 'SORT', 'PREVIEW_PICTURE'));
	while($arElement = $rsElements->GetNext()) {
		$arAdvantages[$arElement['ID']] = array(
			'NAME' => $arElement['NAME'],
			'SORT' => $arElement['SORT'],
			'PREVIEW_PICTURE' => $arElement['PREVIEW_PICTURE'] > 0 ? CFile::GetFileArray($arElement['PREVIEW_PICTURE']) : array()
		);
	}
	unset($arElement, $rsElements);

	if(!empty($arAdvantages)) {
		foreach($arResult['ITEMS'] as &$item) {
			foreach($item['PROPERTIES'] as &$prop) {
				if($prop['CODE'] == 'ADVANTAGES' && !empty($prop['VALUE'])) {
					if(!is_array($prop['VALUE'])) {
						if(array_key_exists($prop['VALUE'], $arAdvantages))
							$prop['FULL_VALUE'][] = $arAdvantages[$prop['VALUE']];
					} else {
						foreach($prop['VALUE'] as $val) {
							if(array_key_exists($val, $arAdvantages))
								$prop['FULL_VALUE'][] = $arAdvantages[$val];
						}
						unset($val);
					}
					
					if(!empty($prop['FULL_VALUE']))
						Collection::sortByColumn($prop['FULL_VALUE'], array('SORT' => SORT_NUMERIC, 'NAME' => SORT_ASC));
				}
			}
			unset($prop);
		}
		unset($item);
	}
	unset($arAdvantages);
}
unset($advantagesIds);

//DISPLAY_PROPERTIES//
foreach($arResult['ITEMS'] as &$item) {
	if(!empty($item['DISPLAY_PROPERTIES'])) {
		foreach($item['DISPLAY_PROPERTIES'] as &$prop) {
			if($prop['CODE'] == 'BRAND') {
				$prop['DISPLAY_VALUE'] = preg_replace('/<a([^>]*)>/', '<a$1 target="_blank">', $prop['DISPLAY_VALUE']);
			} elseif($prop['CODE'] == 'COLLECTION') {			
				$prop['DISPLAY_VALUE'] = strip_tags($prop['DISPLAY_VALUE']);
				$rsElement = CIBlockElement::GetList(array(), array('ID' => $prop['VALUE'], 'IBLOCK_ID' => $prop['LINK_IBLOCK_ID']), false, false, array('ID', 'IBLOCK_ID', 'CODE', 'NAME', 'PROPERTY_BRAND'));	
				if($arElement = $rsElement->GetNext()) {
					if(!empty($arElement['PROPERTY_BRAND_VALUE'])) {
						$rsBrand = CIBlockElement::GetList(array(), array('ID' => $arElement['PROPERTY_BRAND_VALUE']), false, false, array('ID', 'IBLOCK_ID', 'DETAIL_PAGE_URL'));
						if($arBrand = $rsBrand->GetNext()) {
							$prop['DISPLAY_VALUE'] = '<a target="_blank" href="'.$arBrand['~DETAIL_PAGE_URL'].$arElement['CODE'].'/">'.$arElement['NAME'].'</a>';
						}
						unset($arBrand, $rsBrand);
					}
				}
				unset($arElement, $rsElement);
			} else {
				$prop['DISPLAY_VALUE'] = is_array($prop['DISPLAY_VALUE']) ? implode(' / ', $prop['DISPLAY_VALUE']) : strip_tags($prop['DISPLAY_VALUE']);
			}
		}
		unset($prop);
	}
}
unset($item);

//TARGET//
//OFFERS_OBJECTS//
//OFFERS_PARTNERS_URL//
foreach($arResult['ITEMS'] as $key => &$item) {
	//TARGET//
	$item['TARGET'] = '_self';	
	if(!empty($item['OFFERS'])) {
		//OFFERS_OBJECTS//
		$item['OFFERS_OBJECTS'] = false;
		foreach($item['OFFERS'] as $arOffer) {
			if(!empty($arOffer['PROPERTIES']['OBJECT']['VALUE'])) {
				$item['OFFERS_OBJECTS'] = true;
				break;
			}
		}
		unset($arOffer);		
		//OFFERS_PARTNERS_URL//
		foreach($item['OFFERS'] as $keyOffer => $arOffer) {
			$arResult['ITEMS'][$key]['JS_OFFERS'][$keyOffer]['PARTNERS_URL'] = false;
		}
		unset($keyOffer, $arOffer);
	}
}
unset($key, $item);

//UF_CODE//
$isSkuProps = false;
foreach($arResult['ITEMS'] as $item) {
	if(!empty($item['OFFERS']) && !empty($item['OFFERS_PROP'])) {
		$isSkuProps = true;
		break;
	}
}
unset($item);

if(($arParams['OFFERS_VIEW'] == 'PROPS' || $arParams['OFFERS_VIEW'] == 'DROPDOWN_LIST') && $arParams['PRODUCT_DISPLAY_MODE'] === 'Y' && !!$isSkuProps) {
	foreach($arResult['SKU_PROPS'][$arResult['IBLOCK_ID']] as &$skuProperty) {
		if($skuProperty['SHOW_MODE'] == 'PICT') {
			$entity = $skuProperty['USER_TYPE_SETTINGS']['ENTITY'];
			if(!($entity instanceof Bitrix\Main\Entity\Base))
				continue;

			$entityFields = $entity->getFields();
			if(!array_key_exists('UF_CODE', $entityFields))
				continue;

			$entityDataClass = $entity->getDataClass();
			
			$directorySelect = array('ID', 'UF_CODE');
			$directoryOrder = array();
			
			$entityGetList = array(
				'select' => $directorySelect,
				'order' => $directoryOrder
			);
			$propEnums = $entityDataClass::getList($entityGetList);
			while($oneEnum = $propEnums->fetch()) {
				$values[$oneEnum['ID']] = $oneEnum['UF_CODE'];
			}

			foreach($skuProperty['VALUES'] as &$val) {				
				if(isset($values[$val['ID']]))
					$val['CODE'] = $values[$val['ID']];
			}
			unset($val, $values);
		}
	}
	unset($skuProperty);
}
unset($isSkuProps);

//RATING_REVIEWS_COUNT//
if($arParams['USE_REVIEW'] != 'N' && intval($arParams['REVIEWS_IBLOCK_ID']) > 0) {
	foreach($arResult['ITEMS'] as $item) {
		$itemIds[] = $item['ID'];
		
		$ratingSum[$item['ID']] = 0;
		$reviewsCount[$item['ID']] = 0;
	}
	unset($item);

	if(!empty($itemIds)) {
		$rsElements = CIBlockElement::GetList(array(), array('ACTIVE' => 'Y', 'IBLOCK_ID' => $arParams['REVIEWS_IBLOCK_ID'], 'PROPERTY_PRODUCT_ID' => array_unique($itemIds)), false, false, array('ID', 'IBLOCK_ID'));
		while($obElement = $rsElements->GetNextElement()) {
			$arElement = $obElement->GetFields();
			$arProps = $obElement->GetProperties();

			$ratingSum[$arProps['PRODUCT_ID']['VALUE']] += $arProps['RATING']['VALUE_XML_ID'];
			
			$reviewsCount[$arProps['PRODUCT_ID']['VALUE']]++;
		}
		unset($arProps, $arElement, $obElement, $rsElements);

		$reviewsDeclension = new Bitrix\Main\Grid\Declension(Loc::getMessage("CT_BCS_TPL_MESS_REVIEW"), Loc::getMessage("CT_BCS_TPL_MESS_REVIEWS_1"), Loc::getMessage("CT_BCS_TPL_MESS_REVIEWS_2"));

		foreach($arResult['ITEMS'] as &$item) {
			$item['RATING_VALUE'] = $reviewsCount[$item['ID']] > 0 ? sprintf('%.1f', round($ratingSum[$item['ID']] / $reviewsCount[$item['ID']], 1)) : 0;
			$item['REVIEWS_COUNT'] = $reviewsCount[$item['ID']];
			$item['REVIEWS_DECLENSION'] = $reviewsDeclension->get($reviewsCount[$item['ID']]);
		}
		unset($item, $reviewsDeclension, $reviewsCount, $ratingSum);
	}
	unset($itemIds);
}