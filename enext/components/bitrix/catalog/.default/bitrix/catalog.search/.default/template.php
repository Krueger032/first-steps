<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !==true) die();

use Bitrix\Main\Loader,
	Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

global $arSettings;

$APPLICATION->SetPageProperty("wideScreenMode", "-ws");

$catalogIncluded = Loader::includeModule("catalog");

$arSKU = $catalogIncluded ? CCatalogSKU::GetInfoByProductIBlock($arParams["IBLOCK_ID"]) : false;
if(is_array($arSKU))
	$arIBlockList = array($arParams["IBLOCK_ID"], $arSKU["IBLOCK_ID"]);
else
	$arIBlockList = array($arParams["IBLOCK_ID"]);?>

<?$arElements = $APPLICATION->IncludeComponent("bitrix:search.page", ".default",
	array(
		"RESTART" => $arParams["RESTART"],
		"NO_WORD_LOGIC" => $arParams["NO_WORD_LOGIC"],
		"USE_LANGUAGE_GUESS" => $arParams["USE_LANGUAGE_GUESS"],
		"CHECK_DATES" => $arParams["CHECK_DATES"],
		"arrFILTER" => array("iblock_".$arParams["IBLOCK_TYPE"]),
		"arrFILTER_iblock_".$arParams["IBLOCK_TYPE"] => $arIBlockList,
		"USE_TITLE_RANK" => "N",
		"DEFAULT_SORT" => "rank",
		"FILTER_NAME" => "",
		"SHOW_WHERE" => "N",
		"arrWHERE" => array(),
		"SHOW_WHEN" => "N",
		"PAGE_RESULT_COUNT" => $arParams["PAGE_RESULT_COUNT"],
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_TITLE" => "",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "N",
	),
	$component->__parent,
	array("HIDE_ICONS" => "Y")
);?>

<?if(!empty($arElements)) {
	foreach($arElements as $arElement) {
		$mxResult = $catalogIncluded ? CCatalogSku::GetProductInfo($arElement) : false;
		if(is_array($mxResult))
			$arElementsNew[] = $mxResult["ID"];
		else
			$arElementsNew[] = $arElement;
	}
	unset($arElement);
	$arElements = array_unique($arElementsNew);
}
unset($arElementsNew);?>

<div class="catalog-section-container">
	<?if(!empty($arElements)) {
		//SECTION_SORT//	
		$arAvailableSort = array(
			"default" => array(					
				"FIELD" => !empty($arParams["ELEMENT_SORT_FIELD"]) ? $arParams["ELEMENT_SORT_FIELD"] : "SORT",
				"ORDER" => !empty($arParams["ELEMENT_SORT_ORDER"]) ? $arParams["ELEMENT_SORT_ORDER"] : "ASC",
				"VALUE" => Loc::getMessage("CT_BCSE_SORT_DEFAULT")
			),
			"cheap" => array(					
				"FIELD" => "PROPERTY_MINIMUM_PRICE_1",
				"ORDER" => "ASC",
				"VALUE" => Loc::getMessage("CT_BCSE_SORT_CHEAP")
			),
			"expensive" => array(
				"FIELD" => "PROPERTY_MAXIMUM_PRICE_1",
				"ORDER" => "DESC",
				"VALUE" => Loc::getMessage("CT_BCSE_SORT_EXPENSIVE")
			)
		);

		$catalogSortField = $APPLICATION->get_cookie("ELEMENT_SORT") ? $APPLICATION->get_cookie("ELEMENT_SORT") : "default";
		
		$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
		$getSort = $request->get("sort");

		if(!empty($getSort) && !empty($arAvailableSort[$getSort])) {
			$APPLICATION->set_cookie("ELEMENT_SORT", $getSort, false, "/", SITE_SERVER_NAME);
			$arParams["ELEMENT_SORT_FIELD"] = $arAvailableSort[$getSort]["FIELD"];
			$arParams["ELEMENT_SORT_ORDER"] = $arAvailableSort[$getSort]["ORDER"];
			$arAvailableSort[$getSort]["CHECKED"] = "Y";
		} elseif(!empty($catalogSortField) && !empty($arAvailableSort[$catalogSortField])) {
			$arParams["ELEMENT_SORT_FIELD"] = $arAvailableSort[$catalogSortField]["FIELD"];
			$arParams["ELEMENT_SORT_ORDER"] = $arAvailableSort[$catalogSortField]["ORDER"];
			$arAvailableSort[$catalogSortField]["CHECKED"] = "Y";
		}
		
		if(!empty($arAvailableSort)) {							
			$this->SetViewTarget("CATALOG_SECTION_PANEL");?>
			<div class="catalog-section-panel-wrapper">
				<div class="container-ws">
					<div class="row">
						<div class="col-xs-12">
							<div class="catalog-section-panel">
								<div class="catalog-section-panel-block catalog-section-panel-block-reverse">
									<div class="catalog-section-sort-container">										
										<div class="catalog-section-sort" data-role="catalogSectionSort">
											<div class="catalog-section-sort-block">
												<div class="catalog-section-sort-text">
													<?=Loc::getMessage("CT_BCSE_SORT");?>
													<span>
														<?foreach($arAvailableSort as $value) {
															if($value["CHECKED"]) {
																echo $value["VALUE"];
																break;
															}
														}
														unset($value);?>
													</span>
												</div>
												<div class="catalog-section-sort-arrow"><i class="icon-arrow-down"></i></div>
											</div>
											<div class="catalog-section-sort-popup" data-role="dropdownContent" style="display: none;">
												<ul>
													<?foreach($arAvailableSort as $val => $ar) {?>
														<li<?=($ar["CHECKED"] ? " class='active'" : "")?>>
															<a href="<?=$APPLICATION->GetCurPageParam('sort='.$val, array('sort'))?>"><?=$ar["VALUE"]?></a>
														</li>
													<?}?>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?$this->EndViewTarget();
		}
		
		//SECTION//
		global $searchFilter;
		$searchFilter = array(
			"=ID" => $arElements,
		);?>
		<?$APPLICATION->IncludeComponent("bitrix:catalog.section", ".default",
			array(
				"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
				"ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
				"ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
				"ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
				"PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
				"LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
				"PROPERTY_CODE" => $arParams["PROPERTY_CODE"],				
				"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
				"OFFERS_FIELD_CODE" => $arParams["OFFERS_FIELD_CODE"],
				"OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
				"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
				"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
				"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
				"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
				"OFFERS_LIMIT" => $arParams["OFFERS_LIMIT"],
				"SECTION_URL" => $arParams["SECTION_URL"],
				"DETAIL_URL" => $arParams["DETAIL_URL"],
				"BASKET_URL" => $arParams["BASKET_URL"],
				"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
				"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
				"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
				"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
				"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"DISPLAY_COMPARE" => $arParams["DISPLAY_COMPARE"],
				"PRICE_CODE" => $arParams["~PRICE_CODE"],
				"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
				"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
				"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
				"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
				"USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],
				"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ""),
				"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ""),
				"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
				"CURRENCY_ID" => $arParams["CURRENCY_ID"],
				"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
				"HIDE_NOT_AVAILABLE_OFFERS" => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
				"DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
				"DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
				"PAGER_TITLE" => $arParams["PAGER_TITLE"],
				"PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
				"PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
				"PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
				"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
				"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
				"LAZY_LOAD" => (isset($arParams["LAZY_LOAD"]) ? $arParams["LAZY_LOAD"] : "N"),
				"MESS_BTN_LAZY_LOAD" => (isset($arParams["~MESS_BTN_LAZY_LOAD"]) ? $arParams["~MESS_BTN_LAZY_LOAD"] : ""),
				"LOAD_ON_SCROLL" => (isset($arParams["LOAD_ON_SCROLL"]) ? $arParams["LOAD_ON_SCROLL"] : "N"),
				"FILTER_NAME" => "searchFilter",
				"SECTION_ID" => "",
				"SECTION_CODE" => "",
				"SECTION_USER_FIELDS" => array(),
				"INCLUDE_SUBSECTIONS" => "Y",
				"SHOW_ALL_WO_SECTION" => "Y",
				"META_KEYWORDS" => "",
				"META_DESCRIPTION" => "",
				"BROWSER_TITLE" => "",
				"ADD_SECTIONS_CHAIN" => "N",
				"SET_TITLE" => "N",
				"SET_STATUS_404" => "N",
				"CACHE_FILTER" => "N",
				"CACHE_GROUPS" => "N",			
				
				"PRODUCT_DISPLAY_MODE" => (isset($arParams["PRODUCT_DISPLAY_MODE"]) ? $arParams["PRODUCT_DISPLAY_MODE"] : ""),				
				"PRODUCT_ROW_VARIANTS" => (isset($arParams["PRODUCT_ROW_VARIANTS"]) ? $arParams["PRODUCT_ROW_VARIANTS"] : ""),
					
				"OFFER_TREE_PROPS" => (isset($arParams["OFFER_TREE_PROPS"]) ? $arParams["OFFER_TREE_PROPS"] : array()),
				"PRODUCT_SUBSCRIPTION" => (isset($arParams["PRODUCT_SUBSCRIPTION"]) ? $arParams["PRODUCT_SUBSCRIPTION"] : ""),
				"SHOW_DISCOUNT_PERCENT" => (isset($arParams["SHOW_DISCOUNT_PERCENT"]) ? $arParams["SHOW_DISCOUNT_PERCENT"] : ""),
				"SHOW_OLD_PRICE" => (isset($arParams["SHOW_OLD_PRICE"]) ? $arParams["SHOW_OLD_PRICE"] : ""),
				"SHOW_MAX_QUANTITY" => (isset($arParams["SHOW_MAX_QUANTITY"]) ? $arParams["SHOW_MAX_QUANTITY"] : ""),
				"MESS_SHOW_MAX_QUANTITY" => (isset($arParams["~MESS_SHOW_MAX_QUANTITY"]) ? $arParams["~MESS_SHOW_MAX_QUANTITY"] : ""),
				"RELATIVE_QUANTITY_FACTOR" => (isset($arParams["RELATIVE_QUANTITY_FACTOR"]) ? $arParams["RELATIVE_QUANTITY_FACTOR"] : ""),
				"MESS_RELATIVE_QUANTITY_MANY" => (isset($arParams["~MESS_RELATIVE_QUANTITY_MANY"]) ? $arParams["~MESS_RELATIVE_QUANTITY_MANY"] : ""),
				"MESS_RELATIVE_QUANTITY_FEW" => (isset($arParams["~MESS_RELATIVE_QUANTITY_FEW"]) ? $arParams["~MESS_RELATIVE_QUANTITY_FEW"] : ""),
				"MESS_BTN_BUY" => (isset($arParams["~MESS_BTN_BUY"]) ? $arParams["~MESS_BTN_BUY"] : ""),
				"MESS_BTN_ADD_TO_BASKET" => (isset($arParams["~MESS_BTN_ADD_TO_BASKET"]) ? $arParams["~MESS_BTN_ADD_TO_BASKET"] : ""),
				"MESS_BTN_SUBSCRIBE" => (isset($arParams["~MESS_BTN_SUBSCRIBE"]) ? $arParams["~MESS_BTN_SUBSCRIBE"] : ""),
				"MESS_BTN_DETAIL" => (isset($arParams["~MESS_BTN_DETAIL"]) ? $arParams["~MESS_BTN_DETAIL"] : ""),
				"MESS_NOT_AVAILABLE" => (isset($arParams["~MESS_NOT_AVAILABLE"]) ? $arParams["~MESS_NOT_AVAILABLE"] : ""),
				"MESS_BTN_COMPARE" => (isset($arParams["~MESS_BTN_COMPARE"]) ? $arParams["~MESS_BTN_COMPARE"] : ""),

				"USE_ENHANCED_ECOMMERCE" => (isset($arParams["USE_ENHANCED_ECOMMERCE"]) ? $arParams["USE_ENHANCED_ECOMMERCE"] : ""),
				"DATA_LAYER_NAME" => (isset($arParams["DATA_LAYER_NAME"]) ? $arParams["DATA_LAYER_NAME"] : ""),
				"BRAND_PROPERTY" => (isset($arParams["BRAND_PROPERTY"]) ? $arParams["BRAND_PROPERTY"] : ""),
				
				"ADD_TO_BASKET_ACTION" => (isset($arParams["ADD_TO_BASKET_ACTION"]) ? $arParams["ADD_TO_BASKET_ACTION"] : ""),
				"COMPARE_PATH" => (isset($arParams["COMPARE_PATH"]) ? $arParams["COMPARE_PATH"] : ""),
				"COMPARE_NAME" => (isset($arParams["COMPARE_NAME"]) ? $arParams["COMPARE_NAME"] : ""),
					
				"DETAIL_ADD_PICT_PROP" => $arParams["DETAIL_ADD_PICT_PROP"],				
				"DETAIL_OFFER_ADD_PICT_PROP" => $arParams["DETAIL_OFFER_ADD_PICT_PROP"],
				"DETAIL_USE_RATIO_IN_RANGES" => $arParams["DETAIL_USE_RATIO_IN_RANGES"],
				"DETAIL_PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],				
				"DETAIL_OFFERS_FIELD_CODE" => $arParams["DETAIL_OFFERS_FIELD_CODE"],
				"DETAIL_OFFERS_PROPERTY_CODE" => $arParams["DETAIL_OFFERS_PROPERTY_CODE"],
				"DETAIL_MAIN_BLOCK_PROPERTY_CODE" => $arParams["DETAIL_MAIN_BLOCK_PROPERTY_CODE"],
				"DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE" => $arParams["DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE"],	
				"DETAIL_IMAGE_RESOLUTION" => $arParams["DETAIL_IMAGE_RESOLUTION"],				
				"DETAIL_ADD_DETAIL_TO_SLIDER" => $arParams["DETAIL_ADD_DETAIL_TO_SLIDER"],
				"DETAIL_DETAIL_PICTURE_MODE" => $arParams["DETAIL_DETAIL_PICTURE_MODE"],
				"DETAIL_SHOW_SLIDER" => $arParams["DETAIL_SHOW_SLIDER"],
				"DETAIL_SLIDER_INTERVAL" => $arParams["DETAIL_SLIDER_INTERVAL"],
				"DETAIL_SLIDER_PROGRESS" => $arParams["DETAIL_SLIDER_PROGRESS"],

				"USE_GIFTS_DETAIL" => $arParams["USE_GIFTS_DETAIL"],
				"GIFTS_DETAIL_PAGE_ELEMENT_COUNT" => $arParams["GIFTS_DETAIL_PAGE_ELEMENT_COUNT"],
				"GIFTS_DETAIL_HIDE_BLOCK_TITLE" => $arParams["GIFTS_DETAIL_HIDE_BLOCK_TITLE"],
				"GIFTS_DETAIL_BLOCK_TITLE" => $arParams["GIFTS_DETAIL_BLOCK_TITLE"],
				"GIFTS_DETAIL_TEXT_LABEL_GIFT" => $arParams["GIFTS_DETAIL_TEXT_LABEL_GIFT"],
				"GIFTS_MESS_BTN_BUY" => $arParams["~GIFTS_MESS_BTN_BUY"],

				"USE_STORE" => $arParams["USE_STORE"],
				"STORE_PATH" => $arParams["STORE_PATH"],
				"STORES" => $arParams["STORES"],
				"USE_MIN_AMOUNT" => $arParams["USE_MIN_AMOUNT"],
				"USER_FIELDS" => $arParams["USER_FIELDS"],
				"FIELDS" => $arParams["FIELDS"],
				"MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
				"SHOW_EMPTY_STORE" => $arParams["SHOW_EMPTY_STORE"],
				"SHOW_GENERAL_STORE_INFORMATION" => $arParams["SHOW_GENERAL_STORE_INFORMATION"],
				"MAIN_TITLE" => $arParams["~MAIN_TITLE"],

				"USE_REVIEW" => $arParams["USE_REVIEW"],
				"REVIEWS_IBLOCK_TYPE" => $arParams["REVIEWS_IBLOCK_TYPE"],
				"REVIEWS_IBLOCK_ID" => $arParams["REVIEWS_IBLOCK_ID"],
				"REVIEWS_NEWS_COUNT" => $arParams["REVIEWS_NEWS_COUNT"],
				"REVIEWS_SORT_BY1" => $arParams["REVIEWS_SORT_BY1"],
				"REVIEWS_SORT_ORDER1" => $arParams["REVIEWS_SORT_ORDER1"],
				"REVIEWS_SORT_BY2" => $arParams["REVIEWS_SORT_BY2"],
				"REVIEWS_SORT_ORDER2" => $arParams["REVIEWS_SORT_ORDER2"],
				"REVIEWS_ACTIVE_DATE_FORMAT" => $arParams["REVIEWS_ACTIVE_DATE_FORMAT"],
				"REVIEWS_PROPERTY_CODE" => $arParams["REVIEWS_PROPERTY_CODE"],
				"MESS_REVIEWS_TAB" => $arParams["MESS_REVIEWS_TAB"],

				"SET_ITEMS_COUNT" => $arParams["SET_ITEMS_COUNT"],

				"OBJECTS_USE_REVIEW" => $arParams["OBJECTS_USE_REVIEW"],
				"OBJECTS_REVIEWS_IBLOCK_ID" => $arParams["OBJECTS_REVIEWS_IBLOCK_ID"],
				"CONTACTS_IBLOCK_ID" => $arParams["CONTACTS_IBLOCK_ID"],
				"CONTACTS_USE_REVIEW" => $arParams["CONTACTS_USE_REVIEW"],
				"CONTACTS_REVIEWS_IBLOCK_ID" => $arParams["CONTACTS_REVIEWS_IBLOCK_ID"],
				"CONTACTS_REVIEWS_PAGE_LINK" => $arParams["CONTACTS_REVIEWS_PAGE_LINK"],

				"QUICK_VIEW_PREV_NEXT" => $arSettings["QUICK_VIEW"]["VALUE"] != "OFF" ? "Y" : "N"
			),
			$component,
			array("HIDE_ICONS" => "Y")
		);?>
	<?} else {
		echo Loc::getMessage("CT_BCSE_NOT_FOUND");
	}?>
</div>

<?$obName = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $this->GetEditAreaId($this->randString()));?>

<script type="text/javascript">
	var <?=$obName?> = new JCCatalogComponent();
</script>

<?//BREADCRUMBS//
$APPLICATION->AddChainItem(Loc::getMessage("CT_BCSE_TITLE"), "");

//PAGE_TITLE//
$APPLICATION->SetTitle(Loc::getMessage("CT_BCSE_TITLE").": ".$_REQUEST["q"]);
if(!empty($_REQUEST["PAGEN_3"]) && $_REQUEST["PAGEN_3"] > 1) {
	$APPLICATION->SetPageProperty("title", Loc::getMessage("CT_BCSE_TITLE").": ".$_REQUEST["q"]." | ".Loc::getMessage("CT_BCSE_PAGE")." ".$_REQUEST["PAGEN_3"]);
	$APPLICATION->SetPageProperty("keywords", "");
	$APPLICATION->SetPageProperty("description", "");
}