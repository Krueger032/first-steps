<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main;

$this->setFrameMode(true);

if(isset($arResult['ITEM'])) {
	$item = $arResult['ITEM'];
	$areaId = $arResult['AREA_ID'];
	$itemIds = array(
		'ID' => $areaId,
		'PICT_ID' => $areaId.'_pict',
		'ARTICLE_ID' => $areaId."_article",
		'QUANTITY_ID' => $areaId.'_quantity',
		'QUANTITY_DOWN_ID' => $areaId.'_quant_down',
		'QUANTITY_UP_ID' => $areaId.'_quant_up',
		'PC_QUANTITY_ID' => $areaId.'_pc_quantity',
		'PC_QUANTITY_DOWN_ID' => $areaId.'_pc_quant_down',
		'PC_QUANTITY_UP_ID' => $areaId.'_pc_quant_up',
		'SQ_M_QUANTITY_ID' => $areaId.'_sq_m_quantity',
		'SQ_M_QUANTITY_DOWN_ID' => $areaId.'_sq_m_quant_down',
		'SQ_M_QUANTITY_UP_ID' => $areaId.'_sq_m_quant_up',
		'QUANTITY_MEASURE' => $areaId.'_quant_measure',
		'QUANTITY_LIMIT' => $areaId.'_quant_limit',
		'QUANTITY_LIMIT_NOT_AVAILABLE' => $areaId.'_quant_limit_not_avl',		
		'BUY_LINK' => $areaId.'_buy_link',
		'BASKET_ACTIONS_ID' => $areaId.'_basket_actions',
		'ASK_PRICE_LINK' => $areaId.'_ask_price_link',
		'NOT_AVAILABLE_LINK' => $areaId.'_not_available_link',
		'MORE_LINK' => $areaId.'_more_link',
		'SUBSCRIBE_LINK' => $areaId.'_subscribe',		
		'DELAY_LINK' => $areaId.'_delay_link',
		'QUICK_VIEW_LINK' => $areaId.'_quick_view_link',
		'COMPARE_LINK' => $areaId.'_compare_link',
		'PRICE_ID' => $areaId.'_price',
		'OLD_PRICE_ID' => $areaId.'_price_old',
		'DISCOUNT_PRICE_ID' => $areaId.'_price_discount',		
		'DISCOUNT_PERCENT_ID' => $areaId.'_dsc_perc',
		'TOTAL_COST_ID' => $areaId.'_total_cost',
		'TREE_ID' => $areaId.'_sku_tree',		
		'BASKET_PROP_DIV' => $areaId.'_basket_prop',
		'DISPLAY_PROP_DIV' => $areaId.'_sku_prop'
	);
	$obName = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $areaId);
	
	$productTitle = isset($item['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) && $item['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] != ''
		? $item['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
		: $item['NAME'];

	$imgTitle = isset($item['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']) && $item['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE'] != ''
		? $item['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']
		: $item['NAME'];
	
	$imgAlt = isset($item['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_ALT']) && $item['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_ALT'] != ''
		? $item['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_ALT']
		: $item['NAME'];

	$skuProps = array();

	$haveOffers = !empty($item['OFFERS']);
	if($haveOffers) {
		$actualItem = isset($item['OFFERS'][$item['OFFERS_SELECTED']]) ? $item['OFFERS'][$item['OFFERS_SELECTED']] : reset($item['OFFERS']);
	} else {
		$actualItem = $item;
	}
	
	$object = !empty($item['PROPERTIES']['OBJECT']['FULL_VALUE']) ? $item['PROPERTIES']['OBJECT']['FULL_VALUE'] : false;
	$objectContacts = $object['PHONE_SMS'] || $object['EMAIL_EMAIL'] ? true : false;
	
	$partnersUrl = !empty($item['PROPERTIES']['PARTNERS_URL']['VALUE']) ? true : false;
	if($haveOffers && ($arParams['OFFERS_VIEW'] == 'PROPS' || $arParams['OFFERS_VIEW'] == 'DROPDOWN_LIST'))
		$offerPartnersUrl = !empty($actualItem['PROPERTIES']['PARTNERS_URL']['VALUE']) ? true : false;
	
	$price = $actualItem['ITEM_PRICES'][$actualItem['ITEM_PRICE_SELECTED']];
	$measureRatio = $actualItem['ITEM_MEASURE_RATIOS'][$actualItem['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'];
	
	$isMeasurePc = $isMeasureSqM = false;
	if($actualItem['ITEM_MEASURE']['SYMBOL_INTL'] == 'pc. 1')
		$isMeasurePc = true;
	elseif($actualItem['ITEM_MEASURE']['SYMBOL_INTL'] == 'm2')
		$isMeasureSqM = true;
	
	$showSubscribe = $arParams['PRODUCT_SUBSCRIPTION'] == 'Y' && ($item['CATALOG_SUBSCRIBE'] == 'Y' || $haveOffers);?>
	
	<div class="product-item-container" id="<?=$areaId?>" data-entity="item">
		<?$documentRoot = Main\Application::getDocumentRoot();
		$templatePath = strtolower($arResult['TYPE']).'/template.php';
		$file = new Main\IO\File($documentRoot.$templateFolder.'/'.$templatePath);
		if($file->isExists()) {
			include($file->getPath());
		}

		if(isset($arParams['REINIT_ADD_BUY_URL_TEMPLATE']) && $arParams['REINIT_ADD_BUY_URL_TEMPLATE'] == 'Y') {
			$addUrlTemplate = $item['DETAIL_PAGE_URL'].'?'.$arParams['ACTION_VARIABLE'].'=ADD2BASKET&'.$arParams['PRODUCT_ID_VARIABLE'].'=#ID#';
			$buyUrlTemplate = $item['DETAIL_PAGE_URL'].'?'.$arParams['ACTION_VARIABLE'].'=BUY&'.$arParams['PRODUCT_ID_VARIABLE'].'=#ID#';
		} else {
			$addUrlTemplate = $arParams['~ADD_URL_TEMPLATE'];
			$buyUrlTemplate = $arParams['~BUY_URL_TEMPLATE'];
		}

		if(!$haveOffers) {
			$jsParams = array(
				'PRODUCT_TYPE' => $item['CATALOG_TYPE'],
				'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
				'SHOW_ADD_BASKET_BTN' => false,
				'SHOW_BUY_BTN' => true,
				'SHOW_ABSENT' => true,
				'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'] == 'Y',
				'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],				
				'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'] == 'Y',
				'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
				'BIG_DATA' => $item['BIG_DATA'],				
				'VIEW_MODE' => $arResult['TYPE'],
				'USE_SUBSCRIBE' => $showSubscribe,
				'PRODUCT' => array(
					'ID' => $item['ID'],
					'IBLOCK_ID' => $item['IBLOCK_ID'],
					'NAME' => $productTitle,
					'DETAIL_PAGE_URL' => $item['DETAIL_PAGE_URL'],
					'PICT' => $item['PREVIEW_PICTURE'],
					'CAN_BUY' => $item['CAN_BUY'],
					'CHECK_QUANTITY' => $item['CHECK_QUANTITY'],
					'MAX_QUANTITY' => $item['CATALOG_QUANTITY'],
					'STEP_QUANTITY' => $measureRatio,
					'QUANTITY_FLOAT' => is_float($measureRatio),
					'ITEM_PRICE_MODE' => $item['ITEM_PRICE_MODE'],
					'ITEM_PRICES' => $item['ITEM_PRICES'],
					'ITEM_PRICE_SELECTED' => $item['ITEM_PRICE_SELECTED'],
					'ITEM_QUANTITY_RANGES' => $item['ITEM_QUANTITY_RANGES'],
					'ITEM_QUANTITY_RANGE_SELECTED' => $item['ITEM_QUANTITY_RANGE_SELECTED'],
					'ITEM_MEASURE_RATIOS' => $item['ITEM_MEASURE_RATIOS'],
					'ITEM_MEASURE_RATIO_SELECTED' => $item['ITEM_MEASURE_RATIO_SELECTED'],
					'ITEM_MEASURE' => $item['ITEM_MEASURE']
				),
				'BASKET' => array(					
					'ADD_PROPS' => $arParams['ADD_PROPERTIES_TO_BASKET'] == 'Y',
					'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
					'PROPS' => $arParams['PRODUCT_PROPS_VARIABLE'],
					'EMPTY_PROPS' => empty($item['PRODUCT_PROPERTIES']),
					'ADD_URL_TEMPLATE' => $addUrlTemplate,
					'BUY_URL_TEMPLATE' => $buyUrlTemplate
				),
				'VISUAL' => $itemIds
			);

			if(!empty($item['PROPERTIES']['M2_COUNT']['VALUE'])) {		
				if($isMeasurePc) {
					$jsParams['PRODUCT']['PC_MAX_QUANTITY'] = $item['CATALOG_QUANTITY'];
					$jsParams['PRODUCT']['PC_STEP_QUANTITY'] = $measureRatio;

					$jsParams['PRODUCT']['SQ_M_MAX_QUANTITY'] = round($item['CATALOG_QUANTITY'] / str_replace(',', '.', $item['PROPERTIES']['M2_COUNT']['VALUE']), 4);			
					$jsParams['PRODUCT']['SQ_M_STEP_QUANTITY'] = round($measureRatio / str_replace(',', '.', $item['PROPERTIES']['M2_COUNT']['VALUE']), 4);
				} elseif($isMeasureSqM) {
					$jsParams['PRODUCT']['PC_MAX_QUANTITY'] = floor($item['CATALOG_QUANTITY'] / $measureRatio);			
					$jsParams['PRODUCT']['PC_STEP_QUANTITY'] = 1;

					$jsParams['PRODUCT']['SQ_M_MAX_QUANTITY'] = $item['CATALOG_QUANTITY'];
					$jsParams['PRODUCT']['SQ_M_STEP_QUANTITY'] = $measureRatio;
				}
			}
		} else {
			$jsParams = array(
				'PRODUCT_TYPE' => $item['CATALOG_TYPE'],
				'OFFERS_VIEW' => $arParams['OFFERS_VIEW'],
				'SHOW_QUANTITY' => false,				
				'SHOW_ADD_BASKET_BTN' => false,
				'SHOW_BUY_BTN' => true,
				'SHOW_ABSENT' => true,				
				'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'] == 'Y',
				'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
				'RELATIVE_QUANTITY_FACTOR' => $arParams['RELATIVE_QUANTITY_FACTOR'],
				'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'] == 'Y',
				'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
				'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
				'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
				'BIG_DATA' => $item['BIG_DATA'],				
				'VIEW_MODE' => $arResult['TYPE'],
				'USE_SUBSCRIBE' => $showSubscribe,
				'DEFAULT_PICTURE' => array(
					'PICTURE' => $item['PRODUCT_PREVIEW']
				),
				'VISUAL' => $itemIds,
				'BASKET' => array(					
					'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
					'PROPS' => $arParams['PRODUCT_PROPS_VARIABLE'],
					'SKU_PROPS' => $item['OFFERS_PROP_CODES'],
					'ADD_URL_TEMPLATE' => $addUrlTemplate,
					'BUY_URL_TEMPLATE' => $buyUrlTemplate
				),
				'PRODUCT' => array(
					'ID' => $item['ID'],
					'IBLOCK_ID' => $item['IBLOCK_ID'],
					'NAME' => $productTitle,
					'DETAIL_PAGE_URL' => $item['DETAIL_PAGE_URL']
				),
				'OFFERS' => array(),
				'OFFER_SELECTED' => 0,
				'TREE_PROPS' => array()
			);

			if((!$object || ($object && $objectContacts)) && !$partnersUrl && ($arParams['OFFERS_VIEW'] == 'PROPS' || $arParams['OFFERS_VIEW'] == 'DROPDOWN_LIST') && $arParams['PRODUCT_DISPLAY_MODE'] == 'Y' && !empty($item['OFFERS_PROP'])) {
				$jsParams['SHOW_QUANTITY'] = $arParams['USE_PRODUCT_QUANTITY'];				
				$jsParams['OFFERS'] = $item['JS_OFFERS'];				
				$jsParams['OFFER_SELECTED'] = $item['OFFERS_SELECTED'];
				$jsParams['TREE_PROPS'] = $skuProps;
			}
		}
		
		$jsParams['AJAX_PATH'] = $templateFolder.'/ajax.php';

		if($arParams['QUICK_VIEW'] != 'OFF') {
			$signer = new Bitrix\Main\Security\Sign\Signer;
			$signedParams = $signer->sign(base64_encode(serialize($arParams)), 'catalog.item');
			
			$jsParams['QUICK_VIEW'] = array(
				'QUICK_VIEW_PARAMETERS' => $signedParams
			);
			if(isset($arParams['QUICK_VIEW_PREV_NEXT']) && $arParams['QUICK_VIEW_PREV_NEXT'] == 'Y')
				$jsParams['QUICK_VIEW']['QUICK_VIEW_PREV_NEXT'] = true;
		}

		if($arParams['DISPLAY_COMPARE']) {
			$jsParams['COMPARE'] = array(				
				'COMPARE_NAME' => $arParams['COMPARE_NAME'],
				'COMPARE_PATH' => $arParams['COMPARE_PATH'],
				'COMPARE_URL_TEMPLATE' => $arParams['~COMPARE_URL_TEMPLATE'],
				'COMPARE_DELETE_URL_TEMPLATE' => $arParams['~COMPARE_DELETE_URL_TEMPLATE']
			);
		}

		if($object) {
			$jsParams['OBJECT'] = array(
				'ID' => $object['ID']
			);
		}
		
		if($item['BIG_DATA']) {
			$jsParams['PRODUCT']['RCM_ID'] = $item['RCM_ID'];
		}

		$jsParams['PRODUCT_DISPLAY_MODE'] = $haveOffers && (($object && !$objectContacts) || $partnersUrl || ($arParams['OFFERS_VIEW'] != 'PROPS' && $arParams['OFFERS_VIEW'] != 'DROPDOWN_LIST')) ? 'N' : $arParams['PRODUCT_DISPLAY_MODE'];
		$jsParams['USE_ENHANCED_ECOMMERCE'] = $arParams['USE_ENHANCED_ECOMMERCE'];
		$jsParams['DATA_LAYER_NAME'] = $arParams['DATA_LAYER_NAME'];
		$jsParams['BRAND_PROPERTY'] = !empty($item['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']])
			? $item['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']]['DISPLAY_VALUE']
			: null;?>

		<script type="text/javascript">
			var <?=$obName?> = new JCCatalogItem(<?=CUtil::PhpToJSObject($jsParams, false, true)?>);
		</script>
	</div>
	<?unset($item, $actualItem, $itemIds, $jsParams);
}