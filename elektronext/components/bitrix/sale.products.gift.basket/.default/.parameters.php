<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Web\Json;
use Bitrix\Iblock;

if(!Loader::includeModule('iblock') || !Loader::includeModule('sale'))
	return;

$boolCatalog = Loader::includeModule('catalog');
CBitrixComponent::includeComponentClass($componentName);

$usePropertyFeatures = Iblock\Model\PropertyFeature::isEnabledFeatures();

$iblockExists = !empty($arCurrentValues['IBLOCK_ID']) && (int)$arCurrentValues['IBLOCK_ID'] > 0;

$defaultValue = array('-' => GetMessage('CP_SPGB_TPL_PROP_EMPTY'));
$arSKU = false;
$boolSKU = false;
if($boolCatalog && $iblockExists) {
	$arSKU = CCatalogSku::GetInfoByProductIBlock($arCurrentValues['IBLOCK_ID']);
	$boolSKU = !empty($arSKU) && is_array($arSKU);
}

$arAllPropList = array();
$arFilePropList = $defaultValue;

if($iblockExists) {
	$rsProps = CIBlockProperty::GetList(array('SORT' => 'ASC', 'ID' => 'ASC'), array('IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'], 'ACTIVE' => 'Y'));
	while($arProp = $rsProps->Fetch()) {
		$strPropName = '['.$arProp['ID'].']'.('' != $arProp['CODE'] ? '['.$arProp['CODE'].']' : '').' '.$arProp['NAME'];
		
		if($arProp['CODE'] == '') {
			$arProp['CODE'] = $arProp['ID'];
		}

		$arAllPropList[$arProp['CODE']] = $strPropName;

		if($arProp['PROPERTY_TYPE'] === 'F') {
			$arFilePropList[$arProp['CODE']] = $strPropName;
		}
	}
	
	$pageElementCount = (int)$arCurrentValues['PAGE_ELEMENT_COUNT'] ?: 4;

	$arTemplateParameters['PRODUCT_ROW_VARIANTS'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_SPGB_TPL_PRODUCT_ROW_VARIANTS'),
		'TYPE' => 'CUSTOM',
		'BIG_DATA' => 'N',
		'COUNT_PARAM_NAME' => 'PAGE_ELEMENT_COUNT',
		'JS_FILE' => SaleProductsGiftBasketComponent::getSettingsScript($templateFolder, 'dragdrop_add'),
		'JS_EVENT' => 'initDraggableAddControl',
		'JS_MESSAGES' => Json::encode(array(
			'variant' => GetMessage('CP_SPGB_TPL_SETTINGS_VARIANT'),
			'delete' => GetMessage('CP_SPGB_TPL_SETTINGS_DELETE'),
			'quantity' => GetMessage('CP_SPGB_TPL_SETTINGS_QUANTITY'),
			'quantityBigData' => GetMessage('CP_SPGB_TPL_SETTINGS_QUANTITY_BIG_DATA')
		)),
		'JS_DATA' => Json::encode(SaleProductsGiftBasketComponent::getTemplateVariantsMap()),
		'DEFAULT' => Json::encode(SaleProductsGiftBasketComponent::predictRowVariants(4, $pageElementCount))
	);
	
	if($boolSKU) {
		$arTemplateParameters['PRODUCT_DISPLAY_MODE'] = array(
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('CP_SPGB_TPL_PRODUCT_DISPLAY_MODE'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'N',
			'ADDITIONAL_VALUES' => 'N',
			'REFRESH' => 'Y',
			'DEFAULT' => 'Y',
			'VALUES' => array(
				'N' => GetMessage('CP_SPGB_TPL_DML_SIMPLE'),
				'Y' => GetMessage('CP_SPGB_TPL_DML_EXT')
			)
		);
	}
	
	$arTemplateParameters['ADD_PICT_PROP'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_SPGB_TPL_ADD_PICT_PROP'),
		'TYPE' => 'LIST',
		'MULTIPLE' => 'N',
		'ADDITIONAL_VALUES' => 'N',
		'REFRESH' => 'N',
		'DEFAULT' => '-',
		'VALUES' => $arFilePropList
	);
	
	if($boolSKU && isset($arCurrentValues['PRODUCT_DISPLAY_MODE']) && 'Y' == $arCurrentValues['PRODUCT_DISPLAY_MODE']) {		
		$arFileOfferPropList = $arTreeOfferPropList = $defaultValue;
		$rsProps = CIBlockProperty::GetList(array('SORT' => 'ASC', 'ID' => 'ASC'), array('IBLOCK_ID' => $arSKU['IBLOCK_ID'], 'ACTIVE' => 'Y'));
		while($arProp = $rsProps->Fetch()) {
			if($arProp['ID'] == $arSKU['SKU_PROPERTY_ID'])
				continue;
			$arProp['USER_TYPE'] = (string)$arProp['USER_TYPE'];
			$strPropName = '['.$arProp['ID'].']'.('' != $arProp['CODE'] ? '['.$arProp['CODE'].']' : '').' '.$arProp['NAME'];
			if('' == $arProp['CODE'])
				$arProp['CODE'] = $arProp['ID'];
			if('F' == $arProp['PROPERTY_TYPE'])
				$arFileOfferPropList[$arProp['CODE']] = $strPropName;
			if('N' != $arProp['MULTIPLE'])
				continue;
			if('L' == $arProp['PROPERTY_TYPE'] || 'E' == $arProp['PROPERTY_TYPE'] || ('S' == $arProp['PROPERTY_TYPE'] && 'directory' == $arProp['USER_TYPE'] && CIBlockPriceTools::checkPropDirectory($arProp)))
				$arTreeOfferPropList[$arProp['CODE']] = $strPropName;
		}
		$arTemplateParameters['OFFER_ADD_PICT_PROP'] = array(
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('CP_SPGB_TPL_OFFER_ADD_PICT_PROP'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'N',
			'ADDITIONAL_VALUES' => 'N',
			'REFRESH' => 'N',
			'DEFAULT' => '-',
			'VALUES' => $arFileOfferPropList
		);
		if(!$usePropertyFeatures) {
			$arTemplateParameters['OFFER_TREE_PROPS'] = array(
				'PARENT' => 'VISUAL',
				'NAME' => GetMessage('CP_SPGB_TPL_OFFER_TREE_PROPS'),
				'TYPE' => 'LIST',
				'MULTIPLE' => 'Y',
				'ADDITIONAL_VALUES' => 'N',
				'REFRESH' => 'N',
				'DEFAULT' => '-',
				'VALUES' => $arTreeOfferPropList
			);
		}
	}
}

if($boolCatalog) {
	$arTemplateParameters['PRODUCT_SUBSCRIPTION'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_SPGB_TPL_PRODUCT_SUBSCRIPTION'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y'
	);
	$arTemplateParameters['ADD_TO_BASKET_ACTION'] = array(
		'PARENT' => 'BASKET',
		'NAME' => GetMessage('CP_SPGB_TPL_ADD_TO_BASKET_ACTION'),
		'TYPE' => 'LIST',
		'VALUES' => array(
			'ADD' => GetMessage('ADD_TO_BASKET_ACTION_ADD'),
			'BUY' => GetMessage('ADD_TO_BASKET_ACTION_BUY')
		),
		'DEFAULT' => 'ADD',
		'REFRESH' => 'N'
	);
}

$arTemplateParameters['MESS_BTN_BUY'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_SPGB_TPL_MESS_BTN_BUY'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_SPGB_TPL_MESS_BTN_BUY_DEFAULT')
);
$arTemplateParameters['MESS_BTN_ADD_TO_BASKET'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_SPGB_TPL_MESS_BTN_ADD_TO_BASKET'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_SPGB_TPL_MESS_BTN_ADD_TO_BASKET_DEFAULT')
);
$arTemplateParameters['MESS_BTN_SUBSCRIBE'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_SPGB_TPL_MESS_BTN_SUBSCRIBE'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_SPGB_TPL_MESS_BTN_SUBSCRIBE_DEFAULT')
);
$arTemplateParameters['MESS_BTN_DETAIL'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_SPGB_TPL_MESS_BTN_DETAIL'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_SPGB_TPL_MESS_BTN_DETAIL_DEFAULT')
);
$arTemplateParameters['USE_ENHANCED_ECOMMERCE'] = array(
	'PARENT' => 'ANALYTICS_SETTINGS',
	'NAME' => GetMessage('CP_SPGB_TPL_USE_ENHANCED_ECOMMERCE'),
	'TYPE' => 'CHECKBOX',
	'REFRESH' => 'Y',
	'DEFAULT' => 'N'
);

if(isset($arCurrentValues['USE_ENHANCED_ECOMMERCE']) && $arCurrentValues['USE_ENHANCED_ECOMMERCE'] === 'Y') {
	$arTemplateParameters['DATA_LAYER_NAME'] = array(
		'PARENT' => 'ANALYTICS_SETTINGS',
		'NAME' => GetMessage('CP_SPGB_TPL_DATA_LAYER_NAME'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'dataLayer'
	);
	$arTemplateParameters['BRAND_PROPERTY'] = array(
		'PARENT' => 'ANALYTICS_SETTINGS',
		'NAME' => GetMessage('CP_SPGB_TPL_BRAND_PROPERTY'),
		'TYPE' => 'LIST',
		'MULTIPLE' => 'N',
		'DEFAULT' => '',
		'VALUES' => $defaultValue + $arAllPropList
	);
}