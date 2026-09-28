<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc,
	Bitrix\Main\Loader;

use \Bitrix\Main\IO;

use CMax as TSolution,
	\Aspro\Functions\CAsproMax as TSolutionFunctions,
	\Aspro\Max\ItemAction\Favorite,
	\Aspro\Max\Traits\Params;

Loc::loadMessages(__FILE__);
$this->setFrameMode(false);

if (!Loader::includeModule(TSolution::moduleID)) die();

global $arRegion;

$arItems = Favorite::getItems();
?>
<div class="personal__block personal__block--favorite-products ajax_load cur block">
	<?if ($arItems):?>
		<?
		$bAjax = $arParams['IS_AJAX'] == 'Y';

		$arParams['IBLOCK_ID'] = TSolution::GetFrontParametrValue('CATALOG_IBLOCK_ID');

		$arParams["PRICE_CODE"] = Params::getArrayValueFromCatalogPageByParamCode('PRICE_CODE') ?? explode(',', TSolution::GetFrontParametrValue('PRICES_TYPE'));
		$arParams["STORES"] = Params::getArrayValueFromCatalogPageByParamCode('STORES') ?? explode(',', TSolution::GetFrontParametrValue('STORES'));
		if ($arRegion) {
			if ($arRegion['LIST_PRICES'] && reset($arRegion['LIST_PRICES']) !== 'component') {
				$arParams["PRICE_CODE"] = array_keys($arRegion['LIST_PRICES']);
			}
			if ($arRegion['LIST_STORES'] && reset($arRegion['LIST_STORES']) !== 'component') {
				$arParams["STORES"] = $arRegion['LIST_STORES'];
			}
		}

		if ($arParams['PRICE_CODE']) {
			foreach ($arParams['PRICE_CODE'] as $key => $price) {
				if (!$price) {
					unset($arParams['PRICE_CODE'][$key]);
				}
			}
		}

		if ($arParams['STORES']) {
			foreach ($arParams['STORES'] as $key => $store) {
				if (!$store) {
					unset($arParams['STORES'][$key]);
				}
			}
		}
		
		// set params for props from module
		TSolutionFunctions::replacePropsParams($arParams);

		$offersID = Favorite::getOffersIDFromItems($arItems);

		$GLOBALS['arrFavoritItems'] = [
			'ID' => $arItems,
			'OFFERS_ID' => $offersID
		];
		
		?>
		<?$this->SetViewTarget("more_text_title");?>
			<div class="js_clear_favorits color-theme-hover font_xs muted"><?=Bitrix\Main\Localization\Loc::getMessage('CLEAR_FAVORITS')?></div>
		<?$this->EndViewTarget();?>
		<?
		if ($bAjax) {
			$GLOBALS['APPLICATION']->RestartBuffer();
		}
		?>

		<?$elements = $APPLICATION->IncludeComponent(
			"bitrix:catalog.section",
			"catalog_block",
			array(
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"CACHE_FILTER" => $arParams["CACHE_FILTER"],
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
				"DETAIL_URL" => "",
				"FILTER_NAME" => "arrFavoritItems",
				"HIT_PROP" => "HIT",
				"IBLOCK_TYPE" => "aspro_max_catalog",
				"IBLOCK_ID"	=> $arParams["IBLOCK_ID"],
				"PAGE_ELEMENT_COUNT" => $arParams["ELEMENT_COUNT"],
				"PROPERTY_CODE"	=> $arParams['LINKED_PROPERTY_CODE'] ?? $arParams['LIST_PROPERTY_CODE'] ?? $arParams['PROPERTY_CODE'],
				"PROPERTY_CODE"	=> Params::getArrayValueFromCatalogPageByParamCode('LIST_PROPERTY_CODE') ?? $arParams['PROPERTY_CODE'],
				"ELEMENT_SORT_FIELD" => "SORT",
				"ELEMENT_SORT_ORDER" => "ASC",
				"ELEMENT_SORT_FIELD2" => "ID",
				"ELEMENT_SORT_ORDER2" => "DESC",
				//"SECTION_ID" => "",
				//"SECTION_CODE" => "",
				"FIELD_CODE" => $arParams['LINKED_FIELD_CODE'] ?? $arParams['LIST_FIELD_CODE'] ?? $arParams['FIELD_CODE'],
				"SHOW_SECTION" => "Y",
				"COUNT_IN_LINE" => "",
				"LINE_ELEMENT_COUNT" => "",
				"SHOW_OLD_PRICE" => TSolution::GetFrontParametrValue('SHOW_OLD_PRICE'),
				"SHOW_DISCOUNT_TIME" => TSolution::GetFrontParametrValue('SHOW_DISCOUNT_TIME'),
				"SHOW_DISCOUNT_PERCENT" => TSolution::GetFrontParametrValue('SHOW_DISCOUNT_PERCENT'),
				"SHOW_PREVIEW_TEXT" => "N",
				"SHOW_GALLERY" => TSolution::GetFrontParametrValue('SHOW_CATALOG_GALLERY_IN_LIST'),
				"MAX_GALLERY_ITEMS" => TSolution::GetFrontParametrValue('MAX_GALLERY_ITEMS'),
				"ADD_PICT_PROP" => \Bitrix\Main\Config\Option::get(TSolution::moduleID, 'GALLERY_PROPERTY_CODE', 'MORE_PHOTO'),
				"OFFER_ADD_PICT_PROP" => \Bitrix\Main\Config\Option::get(TSolution::moduleID, 'GALLERY_PROPERTY_CODE', 'MORE_PHOTO'),
				"DISPLAY_TOP_PAGER"	=>	"N",
				"DISPLAY_BOTTOM_PAGER"	=>	"Y",
				"PAGER_TITLE"	=>	"",
				"PAGER_TEMPLATE"	=>	"ajax",
				"PAGER_SHOW_ALWAYS"	=>	"N",
				"PAGER_DESC_NUMBERING"	=>	"N",
				"PAGER_DESC_NUMBERING_CACHE_TIME"	=>	"36000",
				"PAGER_SHOW_ALL" => "N",
				"INCLUDE_SUBSECTIONS" => "Y",
				"SHOW_ALL_WO_SECTION" => "Y",
				"IS_CATALOG_PAGE" => 'Y',
				"META_KEYWORDS" => "",
				"META_DESCRIPTION" => "",
				"BROWSER_TITLE" => "",
				"ADD_SECTIONS_CHAIN" => "N",
				"DISPLAY_WISH_BUTTONS" => 'Y',

				"DISPLAY_COMPARE" => TSolution::GetFrontParametrValue('CATALOG_COMPARE'),
				"USE_FAST_VIEW" => TSolution::GetFrontParametrValue('USE_FAST_VIEW_PAGE_DETAIL'),
				"MANY_BUY_CATALOG_SECTIONS" => TSolution::GetFrontParametrValue('MANY_BUY_CATALOG_SECTIONS'),
				"MAX_SCU_COUNT_VIEW" => TSolution::GetFrontParametrValue('MAX_SCU_COUNT_VIEW'),

				"PRICE_CODE" => $arParams["PRICE_CODE"],
				"STORES" => $arParams["STORES"],
				
				"SHOW_ONE_CLICK_BUY" => TSolution::GetFrontParametrValue('SHOW_ONE_CLICK_BUY'),
				"USE_FAST_VIEW_PAGE_DETAIL" => TSolution::GetFrontParametrValue('USE_FAST_VIEW_PAGE_DETAIL'),
				"EXPRESSION_FOR_FAST_VIEW" => TSolution::GetFrontParametrValue('EXPRESSION_FOR_FAST_VIEW'),
				"USE_CUSTOM_RESIZE_LIST" => TSolution::GetFrontParametrValue('USE_CUSTOM_RESIZE_LIST'),

				"ADD_PROPERTIES_TO_BASKET" => 'N',
				"PARTIAL_PRODUCT_PROPERTIES" => 'Y',

				"SHOW_DISCOUNT_PERCENT_NUMBER" => Params::getStringValueFromCatalogPageByParamCode('SHOW_DISCOUNT_PERCENT_NUMBER') ?? 'Y',
				"SHOW_DISCOUNT_PERCENT" => Params::getStringValueFromCatalogPageByParamCode('SHOW_DISCOUNT_PERCENT') ?? 'Y',
				"SHOW_DISCOUNT_TIME" => Params::getStringValueFromCatalogPageByParamCode('SHOW_DISCOUNT_TIME') ?? 'Y',
				"SHOW_ONE_CLICK_BUY" => Params::getStringValueFromCatalogPageByParamCode('SHOW_ONE_CLICK_BUY') ?? 'Y',
				"SHOW_OLD_PRICE" => Params::getStringValueFromCatalogPageByParamCode('SHOW_OLD_PRICE') ?? 'Y',
				"SHOW_MEASURE" => Params::getStringValueFromCatalogPageByParamCode('SHOW_MEASURE') ?? 'Y',
				"SHOW_ARTICLE_SKU" => Params::getStringValueFromCatalogPageByParamCode('SHOW_ARTICLE_SKU') ?? 'Y',

				"SHOW_PROPS" => (TSolution::GetFrontParametrValue("SHOW_PROPS_BLOCK") == "Y" ? "Y" : "N"),
				'SHOW_POPUP_PRICE' => (TSolution::GetFrontParametrValue('SHOW_POPUP_PRICE') == 'Y' ? "Y" : "N"),
				'TYPE_VIEW_BASKET_BTN' => TSolution::GetFrontParametrValue('TYPE_VIEW_BASKET_BTN'),

				"SKU_IBLOCK_ID"	=>	$arParams["SKU_IBLOCK_ID"],
				"SKU_TREE_PROPS"	=>	$arParams["OFFER_TREE_PROPS"],
				"SKU_PROPERTY_CODE"	=>	$arParams["LIST_OFFERS_PROPERTY_CODE"],
				'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
				'OFFERS_PROPERTY_CODE' => $arParams['LIST_OFFERS_PROPERTY_CODE'],
				"OFFERS_FIELD_CODE" => array(
					0 => "ID",
					1 => "NAME",
					2 => "DETAIL_PAGE_URL",
					3 => "PREVIEW_PICTURE",
				),
				"COMPATIBLE_MODE" => "Y",
				"OFFERS_LIMIT" => Params::getStringValueFromCatalogPageByParamCode('LIST_OFFERS_LIMIT') ?? '10',
				'OFFER_SHOW_PREVIEW_PICTURE_PROPS' => $arParams['OFFER_SHOW_PREVIEW_PICTURE_PROPS'],

				"ELEMENT_IN_ROW" => $arParams['ELEMENT_IN_ROW'] ?? 4,
				"AJAX_REQUEST" => $arParams['IS_AJAX'],
				// "MOBILE_SCROLLED" => false,
				"NARROW" => "Y",
				"CHECK_REQUEST_BLOCK" => $arParams['CHECK_REQUEST_BLOCK'],
				"TYPE_SKU" => TSolution::GetFrontParametrValue("TYPE_SKU"),
				"SHOW_PREVIEW_TEXT" => "N",
				"SHOW_RATING" => Params::getStringValueFromCatalogPageByParamCode('SHOW_MEASURE') ?? "Y",
				"REVIEWS_VIEW" => TSolution::GetFrontParametrValue('REVIEWS_VIEW') ==  'EXTENDED',
				"SET_SKU_TITLE" => ((TSolution::GetFrontParametrValue("TYPE_SKU") == "TYPE_1" && TSolution::GetFrontParametrValue("CHANGE_TITLE_ITEM_LIST") == "Y") ? "Y" : ""),
				"USE_PRICE_COUNT" => "N",
				"USE_REGION" => ($arRegion ? "Y" : "N"),
				"CONVERT_CURRENCY" => TSolution::GetFrontParametrValue('CONVERT_CURRENCY'),
				"CURRENCY_ID" => TSolution::GetFrontParametrValue('CURRENCY_ID'),
				"PRICE_VAT_INCLUDE" => TSolution::GetFrontParametrValue('PRICE_VAT_INCLUDE'),
			),
			false, array('HIDE_ICONS' => 'Y')
		);?>
		
		<?
		if ($bAjax) {
			die();
		}
		?>
	<?else:?>
		<div class="alert alert-success"><?=Loc::getMessage('NO_FAVORIT_ITEMS')?></div>
	<?endif;?>
</div>