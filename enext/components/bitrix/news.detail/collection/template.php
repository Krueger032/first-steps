<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc,
	Bitrix\Main\Grid\Declension;

$this->setFrameMode(true);

$this->addExternalCss(SITE_TEMPLATE_PATH."/js/owlCarousel/owl.carousel.min.css");
$this->addExternalJS(SITE_TEMPLATE_PATH."/js/owlCarousel/owl.carousel.min.js");

$this->addExternalCss(SITE_TEMPLATE_PATH."/js/fancybox/jquery.fancybox.css");
$this->addExternalJS(SITE_TEMPLATE_PATH."/js/fancybox/jquery.fancybox.pack.js");

$mainId = $this->GetEditAreaId($arResult['ID']);
$obName = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $mainId);?>

<div class="collections-detail" id="<?=$mainId?>">
	<?//TABS//?>
	<div class="collections-detail-tabs-container">
		<div class="collections-detail-tabs-block" data-entity="tabs">
			<div class="collections-detail-tabs-scroll">
				<ul class="collections-detail-tabs-list">
					<li class="collections-detail-tab active" data-entity="tab" data-value="collection"><?=Loc::getMessage("COLLECTIONS_ITEM_DETAIL_TAB_COLLECTION")?></li>
					<?if(!empty($arResult["PRODUCTS_IDS"])) {?>
						<li class="collections-detail-tab" data-entity="tab" data-value="products"><?=Loc::getMessage("COLLECTIONS_ITEM_DETAIL_TAB_PRODUCTS")?><span><?=count($arResult["PRODUCTS_IDS"])?></span></li>
					<?}
					if(!empty($arResult["DETAIL_TEXT"])) {?>
						<li class="collections-detail-tab" data-entity="tab" data-value="description"><?=Loc::getMessage("COLLECTIONS_ITEM_DETAIL_TAB_DESCRIPTION")?></li>
					<?}?>
				</ul>
			</div>
		</div>
	</div>
	<div class="collections-detail-tabs-content">
		<?//ITEM//?>
		<div class="collections-item-detail-container" data-entity="tab-container" data-value="collection">
			<div class="collections-item-detail<?=(is_array($arResult['DETAIL_PICTURE']) ? ' collections-item-detail-full' : '').(!empty($arResult['MORE_PHOTO']) ? ' collections-item-detail-is-slider' : '')?>">
				<?//ITEM_PIC//?>
				<div class="collections-item-detail-pic-container">
					<div class="collections-item-detail-pic" id="<?=$itemIds['PIC_ID']?>">
						<?if(is_array($arResult["DETAIL_PICTURE"])) {?>
							<img src="<?=$arResult['DETAIL_PICTURE']['SRC']?>" width="<?=$arResult['DETAIL_PICTURE']['WIDTH']?>" height="<?=$arResult['DETAIL_PICTURE']['HEIGHT']?>" alt="<?=$arResult['DETAIL_PICTURE']['ALT']?>" />
						<?}?>
					</div>
				</div>
				<?//ITEM_BLOCK//?>
				<div class="collections-item-detail-block">
					<div class="collections-item-detail-title"><?=$arResult["NAME"]?></div>
					<?if(!empty($arResult["MIN_PRICE"])) {?>
						<div class="collections-item-detail-price"><?=Loc::getMessage("COLLECTIONS_ITEM_DETAIL_PRICE", array("#PRICE#" => $arResult["MIN_PRICE"]))?></div>
					<?}
					if(!empty($arResult["BRAND"])) {?>
						<div class="collections-item-detail-brand"><?=implode(", ", $arResult["BRAND"])?></div>
					<?}?>
				</div>
				<?//ITEM_ICONS//?>
				<div class="collections-item-detail-icons<?=(!empty($arResult['MARKER']) && (!isset($arResult['COLORS']) || empty($arResult['COLORS'])) ? ' collections-item-detail-icons-left' : (!empty($arResult['COLORS']) && (!isset($arResult['MARKER']) || empty($arResult['MARKER'])) ? ' collections-item-detail-icons-right' : ''))?>">
					<?if(!empty($arResult["MARKER"])) {?>
						<div class="collections-item-detail-icon">
							<?foreach($arResult["MARKER"] as $key => $arMarker) {
								if($key <= 2) {?>
									<div class="collections-item-detail-marker-container">
										<div class="collections-item-detail-marker<?=(!empty($arMarker['FONT_SIZE']) ? ' collections-item-detail-marker-'.$arMarker['FONT_SIZE'] : '')?>"<?=(!empty($arMarker["BACKGROUND_1"]) && !empty($arMarker["BACKGROUND_2"]) ? " style='background: ".$arMarker["BACKGROUND_2"]."; background: -webkit-linear-gradient(left, ".$arMarker["BACKGROUND_1"].", ".$arMarker["BACKGROUND_2"]."); background: -moz-linear-gradient(left, ".$arMarker["BACKGROUND_1"].", ".$arMarker["BACKGROUND_2"]."); background: -o-linear-gradient(left, ".$arMarker["BACKGROUND_1"].", ".$arMarker["BACKGROUND_2"]."); background: -ms-linear-gradient(left, ".$arMarker["BACKGROUND_1"].", ".$arMarker["BACKGROUND_2"]."); background: linear-gradient(to right, ".$arMarker["BACKGROUND_1"].", ".$arMarker["BACKGROUND_2"].");'" : (!empty($arMarker["BACKGROUND_1"]) && empty($arMarker["BACKGROUND_2"]) ? " style='background: ".$arMarker["BACKGROUND_1"].";'" : (empty($arMarker["BACKGROUND_1"]) && !empty($arMarker["BACKGROUND_2"]) ? " style='background: ".$arMarker["BACKGROUND_2"].";'" : "")))?>><?=(!empty($arMarker["ICON"]) ? "<i class='".$arMarker["ICON"]."'></i>" : "")?><span><?=$arMarker["NAME"]?></span></div>
									</div>
								<?} else {
									break;
								}
							}
							unset($key, $arMarker);?>
						</div>
					<?}
					if(!empty($arResult["COLORS"])) {?>
						<div class="collections-item-detail-icon">
							<div class="collections-item-detail-colors">
								<?foreach($arResult["COLORS"] as $arColor) {?>
									<div class="collections-item-detail-color" title="<?=$arColor['NAME']?>" style="<?=(!empty($arColor['CODE']) ? 'background-color: #'.$arColor['CODE'].';' : (!empty($arColor['FILE']) ? 'background-image: url('.$arColor['FILE'].');' : ''));?>"></div>
								<?}								
								unset($arColor);?>
							</div>
						</div>
					<?}?>					
				</div>
				<?//ITEM_MORE_PHOTO//
				if(!empty($arResult["MORE_PHOTO"])) {?>
					<div class="collections-item-detail-slider-container">
						<div class="collections-item-detail-slider">
							<?foreach($arResult["MORE_PHOTO"] as $photo) {?>
								<a class="collections-item-detail-slider-item fancyimage" title="<?=(!empty($arResult['BRAND']) ? $arResult['BRAND']['NAME'].' ' : '').(!empty($arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) ? $arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] : $arResult['NAME'])?>" href="<?=$photo['SRC']?>" data-fancybox-group="gallery">
									<span class="collections-item-detail-slider-item-pic">
										<img src="<?=$photo['SRC']?>" width="<?=$photo['WIDTH']?>" height="<?=$photo['HEIGHT']?>" alt="<?=$titleAlt?>" />
									</span>
								</a>
							<?}
							unset($photo);?>
						</div>
					</div>
				<?}?>
			</div>
			<?//ITEM_SEPARATE//
			if(!empty($arResult["MORE_PHOTO"])) {?>
				<div class="collections-item-detail-separate"></div>
			<?}
			//ITEM_PREVIEW_TEXT//
			if(!empty($arResult["PREVIEW_TEXT"])) {?>
				<div class="collections-item-detail-preview-text"><?=$arResult["PREVIEW_TEXT"]?></div>
			<?}?>
		</div>
		<?//SECTIONS_PRODUCTS//
		if(!empty($arResult["PRODUCTS_IDS"])) {?>			
			<div class="collections-detail-products-container" data-entity="tab-container" data-value="products">
				<div class="h2"><?=Loc::getMessage("COLLECTIONS_ITEM_DETAIL_PRODUCTS")?></div>
				<?//SECTIONS//?>
				<div class="collections-detail-sections-links" data-entity="links">
					<div class="collections-detail-section-link active" data-section-id="0"><?=Loc::getMessage("COLLECTIONS_ITEM_DETAIL_PRODUCTS_SECTIONS_ALL")?><span><?=count($arResult["PRODUCTS_IDS"])?></span></div>
					<?if(!empty($arResult["SECTIONS"])) {
						foreach($arResult["SECTIONS"] as $arSection) {?>
							<div class="collections-detail-section-link" data-section-id="<?=$arSection['ID']?>"><?=$arSection["NAME"]?><span><?=$arSection["COUNT"]?></span></div>
						<?}
						unset($arSection);
					}?>
				</div>
				<div class="collections-detail-products">
					<?//PRODUCTS//
					$GLOBALS["arCollectProdFilter"] = array("ID" => $arResult["PRODUCTS_IDS"]);?>
					<?$APPLICATION->IncludeComponent("bitrix:catalog.section", ".default", 
						array(
							"COMPONENT_TEMPLATE" => ".default",
							"IBLOCK_TYPE" => $arParams["CATALOG_IBLOCK_TYPE"],
							"IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"],
							"SECTION_ID" => "",
							"SECTION_CODE" => "",
							"SECTION_USER_FIELDS" => array(),
							"FILTER_NAME" => "arCollectProdFilter",
							"INCLUDE_SUBSECTIONS" => $arParams["CATALOG_INCLUDE_SUBSECTIONS"],
							"SHOW_ALL_WO_SECTION" => "Y",
							"CUSTOM_FILTER" => "",
							"HIDE_NOT_AVAILABLE" => $arParams["CATALOG_HIDE_NOT_AVAILABLE"],
							"HIDE_NOT_AVAILABLE_OFFERS" => $arParams["CATALOG_HIDE_NOT_AVAILABLE_OFFERS"],
							"ELEMENT_SORT_FIELD" => $arParams["CATALOG_ELEMENT_SORT_FIELD"],
							"ELEMENT_SORT_ORDER" => $arParams["CATALOG_ELEMENT_SORT_ORDER"],
							"ELEMENT_SORT_FIELD2" => $arParams["CATALOG_ELEMENT_SORT_FIELD2"],
							"ELEMENT_SORT_ORDER2" => $arParams["CATALOG_ELEMENT_SORT_ORDER2"],
							"OFFERS_SORT_FIELD" => $arParams["CATALOG_OFFERS_SORT_FIELD"],
							"OFFERS_SORT_ORDER" => $arParams["CATALOG_OFFERS_SORT_ORDER"],
							"OFFERS_SORT_FIELD2" => $arParams["CATALOG_OFFERS_SORT_FIELD2"],
							"OFFERS_SORT_ORDER2" => $arParams["CATALOG_OFFERS_SORT_ORDER2"],
							"PAGE_ELEMENT_COUNT" => "12",
							"LINE_ELEMENT_COUNT" => "4",
							"PROPERTY_CODE" => $arParams["CATALOG_PROPERTY_CODE"],
							"OFFERS_FIELD_CODE" => $arParams["CATALOG_OFFERS_FIELD_CODE"],
							"OFFERS_PROPERTY_CODE" => $arParams["CATALOG_OFFERS_PROPERTY_CODE"],
							"OFFERS_LIMIT" => $arParams["CATALOG_OFFERS_LIMIT"],							
							"PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false}]",
							"PRODUCT_DISPLAY_MODE" => $arParams["CATALOG_PRODUCT_DISPLAY_MODE"],
							"OFFER_TREE_PROPS" => $arParams["CATALOG_OFFER_TREE_PROPS"],
							"PRODUCT_SUBSCRIPTION" => $arParams["CATALOG_PRODUCT_SUBSCRIPTION"],
							"SHOW_DISCOUNT_PERCENT" => $arParams["CATALOG_SHOW_DISCOUNT_PERCENT"],
							"SHOW_OLD_PRICE" => $arParams["CATALOG_SHOW_OLD_PRICE"],
							"SHOW_MAX_QUANTITY" => $arParams["CATALOG_SHOW_MAX_QUANTITY"],
							"MESS_SHOW_MAX_QUANTITY" => $arParams["CATALOG_MESS_SHOW_MAX_QUANTITY"],
							"RELATIVE_QUANTITY_FACTOR" => $arParams["CATALOG_RELATIVE_QUANTITY_FACTOR"],
							"MESS_RELATIVE_QUANTITY_MANY" => $arParams["CATALOG_MESS_RELATIVE_QUANTITY_MANY"],
							"MESS_RELATIVE_QUANTITY_FEW" => $arParams["CATALOG_MESS_RELATIVE_QUANTITY_FEW"],
							"MESS_BTN_BUY" => $arParams["CATALOG_MESS_BTN_BUY"],
							"MESS_BTN_ADD_TO_BASKET" => $arParams["CATALOG_MESS_BTN_ADD_TO_BASKET"],
							"MESS_BTN_SUBSCRIBE" => $arParams["CATALOG_MESS_BTN_SUBSCRIBE"],
							"MESS_BTN_DETAIL" => $arParams["CATALOG_MESS_BTN_DETAIL"],
							"MESS_NOT_AVAILABLE" => $arParams["CATALOG_MESS_NOT_AVAILABLE"],
							"RCM_TYPE" => "personal",
							"RCM_PROD_ID" => "",
							"SHOW_FROM_SECTION" => "N",
							"SECTION_URL" => "",
							"DETAIL_URL" => "",
							"SECTION_ID_VARIABLE" => "SECTION_ID",
							"SEF_MODE" => "N",
							"AJAX_MODE" => "N",
							"AJAX_OPTION_JUMP" => "N",
							"AJAX_OPTION_STYLE" => "Y",
							"AJAX_OPTION_HISTORY" => "N",
							"AJAX_OPTION_ADDITIONAL" => "",
							"CACHE_TYPE" => $arParams["CACHE_TYPE"],
							"CACHE_TIME" => $arParams["CACHE_TIME"],
							"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
							"SET_TITLE" => "N",
							"SET_BROWSER_TITLE" => "N",
							"BROWSER_TITLE" => "-",
							"SET_META_KEYWORDS" => "N",
							"META_KEYWORDS" => "-",
							"SET_META_DESCRIPTION" => "N",
							"META_DESCRIPTION" => "-",
							"SET_LAST_MODIFIED" => "N",
							"USE_MAIN_ELEMENT_SECTION" => $arParams["CATALOG_USE_MAIN_ELEMENT_SECTION"],
							"ADD_SECTIONS_CHAIN" => "N",
							"CACHE_FILTER" => $arParams["CACHE_FILTER"],
							"USE_REVIEW" => $arParams["CATALOG_USE_REVIEW"],
							"REVIEWS_IBLOCK_TYPE" => $arParams["CATALOG_REVIEWS_IBLOCK_TYPE"],
							"REVIEWS_IBLOCK_ID" => $arParams["CATALOG_REVIEWS_IBLOCK_ID"],
							"REVIEWS_NEWS_COUNT" => $arParams["CATALOG_REVIEWS_NEWS_COUNT"],
							"REVIEWS_SORT_BY1" => $arParams["CATALOG_REVIEWS_SORT_BY1"],
							"REVIEWS_SORT_ORDER1" => $arParams["CATALOG_REVIEWS_SORT_ORDER1"],
							"REVIEWS_SORT_BY2" => $arParams["CATALOG_REVIEWS_SORT_BY2"],
							"REVIEWS_SORT_ORDER2" => $arParams["CATALOG_REVIEWS_SORT_ORDER2"],
							"REVIEWS_ACTIVE_DATE_FORMAT" => $arParams["CATALOG_REVIEWS_ACTIVE_DATE_FORMAT"],
							"REVIEWS_PROPERTY_CODE" => $arParams["CATALOG_REVIEWS_PROPERTY_CODE"],
							"MESS_REVIEWS_TAB" => $arParams["CATALOG_MESS_REVIEWS_TAB"],
							"ACTION_VARIABLE" => "action",
							"PRODUCT_ID_VARIABLE" => "id",								
							"PRICE_CODE" => $arParams["CATALOG_PRICE_CODE"],
							"USE_PRICE_COUNT" => $arParams["CATALOG_USE_PRICE_COUNT"],
							"SHOW_PRICE_COUNT" => $arParams["CATALOG_SHOW_PRICE_COUNT"] ? "Y" : "N",
							"PRICE_VAT_INCLUDE" => $arParams["CATALOG_PRICE_VAT_INCLUDE"],
							"CONVERT_CURRENCY" => $arParams["CATALOG_CONVERT_CURRENCY"],
							"CURRENCY_ID" => $arParams["CATALOG_CURRENCY_ID"],
							"BASKET_URL" => $arParams["CATALOG_BASKET_URL"],
							"USE_PRODUCT_QUANTITY" => $arParams["CATALOG_USE_PRODUCT_QUANTITY"],
							"PRODUCT_QUANTITY_VARIABLE" => "quantity",
							"ADD_PROPERTIES_TO_BASKET" => $arParams["CATALOG_ADD_PROPERTIES_TO_BASKET"],
							"PRODUCT_PROPS_VARIABLE" => "prop",
							"PARTIAL_PRODUCT_PROPERTIES" => $arParams["CATALOG_PARTIAL_PRODUCT_PROPERTIES"],
							"PRODUCT_PROPERTIES" => $arParams["CATALOG_PRODUCT_PROPERTIES"],
							"OFFERS_CART_PROPERTIES" => $arParams["CATALOG_OFFERS_CART_PROPERTIES"],
							"ADD_TO_BASKET_ACTION" => $arParams["CATALOG_ADD_TO_BASKET_ACTION"],
							"DISPLAY_COMPARE" => $arParams["CATALOG_DISPLAY_COMPARE"],
							"COMPARE_PATH" => $arParams["CATALOG_COMPARE_PATH"],
							"MESS_BTN_COMPARE" => $arParams["CATALOG_MESS_BTN_COMPARE"],
							"COMPARE_NAME" => $arParams["CATALOG_COMPARE_NAME"],
							"USE_ENHANCED_ECOMMERCE" => "N",
							"PAGER_TEMPLATE" => "arrows",
							"DISPLAY_TOP_PAGER" => "N",
							"DISPLAY_BOTTOM_PAGER" => "N",
							"PAGER_TITLE" => "",
							"PAGER_SHOW_ALWAYS" => "N",
							"PAGER_DESC_NUMBERING" => "N",
							"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
							"PAGER_SHOW_ALL" => "N",
							"PAGER_BASE_LINK_ENABLE" => "N",
							"LAZY_LOAD" => "Y",
							"LOAD_ON_SCROLL" => "N",
							"SET_STATUS_404" => "N",
							"SHOW_404" => "N",
							"MESSAGE_404" => "",
							"COMPATIBLE_MODE" => "N",
							"DISABLE_INIT_JS_IN_COMPONENT" => "N",
							"DETAIL_ADD_PICT_PROP" => $arParams["CATALOG_DETAIL_ADD_PICT_PROP"],
							"DETAIL_OFFER_ADD_PICT_PROP" => $arParams["CATALOG_DETAIL_OFFER_ADD_PICT_PROP"],
							"DETAIL_USE_RATIO_IN_RANGES" => $arParams["CATALOG_DETAIL_USE_RATIO_IN_RANGES"],
							"DETAIL_PROPERTY_CODE" => $arParams["CATALOG_DETAIL_PROPERTY_CODE"],				
							"DETAIL_OFFERS_FIELD_CODE" => $arParams["CATALOG_DETAIL_OFFERS_FIELD_CODE"],
							"DETAIL_OFFERS_PROPERTY_CODE" => $arParams["CATALOG_DETAIL_OFFERS_PROPERTY_CODE"],
							"DETAIL_MAIN_BLOCK_PROPERTY_CODE" => $arParams["CATALOG_DETAIL_MAIN_BLOCK_PROPERTY_CODE"],
							"DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE" => $arParams["CATALOG_DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE"],	
							"DETAIL_IMAGE_RESOLUTION" => $arParams["CATALOG_DETAIL_IMAGE_RESOLUTION"],
							"DETAIL_ADD_DETAIL_TO_SLIDER" => $arParams["CATALOG_DETAIL_ADD_DETAIL_TO_SLIDER"],
							"DETAIL_DETAIL_PICTURE_MODE" => $arParams["CATALOG_DETAIL_DETAIL_PICTURE_MODE"],
							"DETAIL_SHOW_SLIDER" => $arParams["CATALOG_DETAIL_SHOW_SLIDER"],
							"DETAIL_SLIDER_INTERVAL" => $arParams["CATALOG_DETAIL_SLIDER_INTERVAL"],
							"DETAIL_SLIDER_PROGRESS" => $arParams["CATALOG_DETAIL_SLIDER_PROGRESS"],
							"USE_GIFTS_DETAIL" => $arParams["CATALOG_USE_GIFTS_DETAIL"],
							"GIFTS_DETAIL_PAGE_ELEMENT_COUNT" => $arParams["CATALOG_GIFTS_DETAIL_PAGE_ELEMENT_COUNT"],
							"GIFTS_DETAIL_HIDE_BLOCK_TITLE" => $arParams["CATALOG_GIFTS_DETAIL_HIDE_BLOCK_TITLE"],
							"GIFTS_DETAIL_BLOCK_TITLE" => $arParams["CATALOG_GIFTS_DETAIL_BLOCK_TITLE"],
							"GIFTS_DETAIL_TEXT_LABEL_GIFT" => $arParams["CATALOG_GIFTS_DETAIL_TEXT_LABEL_GIFT"],
							"GIFTS_MESS_BTN_BUY" => $arParams["CATALOG_GIFTS_MESS_BTN_BUY"],
							"USE_STORE" => $arParams["CATALOG_USE_STORE"],
							"STORE_PATH" => $arParams["CATALOG_STORE_PATH"],
							"STORES" => $arParams["CATALOG_STORES"],
							"USE_MIN_AMOUNT" => $arParams["CATALOG_USE_MIN_AMOUNT"],
							"USER_FIELDS" => $arParams["CATALOG_USER_FIELDS"],
							"FIELDS" => $arParams["CATALOG_FIELDS"],
							"MIN_AMOUNT" => $arParams["CATALOG_MIN_AMOUNT"],
							"SHOW_EMPTY_STORE" => $arParams["CATALOG_SHOW_EMPTY_STORE"],
							"SHOW_GENERAL_STORE_INFORMATION" => $arParams["CATALOG_SHOW_GENERAL_STORE_INFORMATION"],
							"MAIN_TITLE" => $arParams["CATALOG_MAIN_TITLE"],
							"SET_ITEMS_COUNT" => $arParams["CATALOG_SET_ITEMS_COUNT"],
							"REINIT_ADD_BUY_URL_TEMPLATE" => $arParams["CATALOG_REINIT_ADD_BUY_URL_TEMPLATE"],
							"OBJECTS_USE_REVIEW" => $arParams["OBJECTS_USE_REVIEW"],
							"OBJECTS_REVIEWS_IBLOCK_ID" => $arParams["OBJECTS_REVIEWS_IBLOCK_ID"],
							"CONTACTS_IBLOCK_ID" => $arParams["CONTACTS_IBLOCK_ID"],
							"CONTACTS_USE_REVIEW" => $arParams["CONTACTS_USE_REVIEW"],
							"CONTACTS_REVIEWS_IBLOCK_ID" => $arParams["CONTACTS_REVIEWS_IBLOCK_ID"],
							"CONTACTS_REVIEWS_PAGE_LINK" => $arParams["CONTACTS_REVIEWS_PAGE_LINK"]
						),
						$component,
						array("HIDE_ICONS" => "Y")
					);?>
				</div>
			</div>
		<?}
		//DETAIL_TEXT//
		if(!empty($arResult["DETAIL_TEXT"])) {?>
			<div class="collections-detail-detail-text" data-entity="tab-container" data-value="description"><?=$arResult["DETAIL_TEXT"]?></div>
		<?}?>
	</div>
</div>

<?$arParamsCatalog = array(
	"IBLOCK_TYPE" => $arParams["CATALOG_IBLOCK_TYPE"],
	"IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"],
	"INCLUDE_SUBSECTIONS" => $arParams["CATALOG_INCLUDE_SUBSECTIONS"],
	"HIDE_NOT_AVAILABLE" => $arParams["CATALOG_HIDE_NOT_AVAILABLE"],
	"HIDE_NOT_AVAILABLE_OFFERS" => $arParams["CATALOG_HIDE_NOT_AVAILABLE_OFFERS"],
	"ELEMENT_SORT_FIELD" => $arParams["CATALOG_ELEMENT_SORT_FIELD"],
	"ELEMENT_SORT_ORDER" => $arParams["CATALOG_ELEMENT_SORT_ORDER"],
	"ELEMENT_SORT_FIELD2" => $arParams["CATALOG_ELEMENT_SORT_FIELD2"],
	"ELEMENT_SORT_ORDER2" => $arParams["CATALOG_ELEMENT_SORT_ORDER2"],
	"OFFERS_SORT_FIELD" => $arParams["CATALOG_OFFERS_SORT_FIELD"],
	"OFFERS_SORT_ORDER" => $arParams["CATALOG_OFFERS_SORT_ORDER"],
	"OFFERS_SORT_FIELD2" => $arParams["CATALOG_OFFERS_SORT_FIELD2"],
	"OFFERS_SORT_ORDER2" => $arParams["CATALOG_OFFERS_SORT_ORDER2"],
	"PROPERTY_CODE" => $arParams["CATALOG_PROPERTY_CODE"],
	"OFFERS_FIELD_CODE" => $arParams["CATALOG_OFFERS_FIELD_CODE"],
	"OFFERS_PROPERTY_CODE" => $arParams["CATALOG_OFFERS_PROPERTY_CODE"],
	"OFFERS_LIMIT" => $arParams["CATALOG_OFFERS_LIMIT"],
	"PRODUCT_DISPLAY_MODE" => $arParams["CATALOG_PRODUCT_DISPLAY_MODE"],	
	"OFFER_TREE_PROPS" => $arParams["CATALOG_OFFER_TREE_PROPS"],
	"PRODUCT_SUBSCRIPTION" => $arParams["CATALOG_PRODUCT_SUBSCRIPTION"],
	"SHOW_DISCOUNT_PERCENT" => $arParams["CATALOG_SHOW_DISCOUNT_PERCENT"],		
	"SHOW_OLD_PRICE" => $arParams["CATALOG_SHOW_OLD_PRICE"],
	"SHOW_MAX_QUANTITY" => $arParams["CATALOG_SHOW_MAX_QUANTITY"],
	"MESS_SHOW_MAX_QUANTITY" => $arParams["CATALOG_MESS_SHOW_MAX_QUANTITY"],
	"RELATIVE_QUANTITY_FACTOR" => $arParams["CATALOG_RELATIVE_QUANTITY_FACTOR"],
	"MESS_RELATIVE_QUANTITY_MANY" => $arParams["CATALOG_MESS_RELATIVE_QUANTITY_MANY"],
	"MESS_RELATIVE_QUANTITY_FEW" => $arParams["CATALOG_MESS_RELATIVE_QUANTITY_FEW"],		
	"MESS_BTN_BUY" => $arParams["CATALOG_MESS_BTN_BUY"],
	"MESS_BTN_ADD_TO_BASKET" => $arParams["CATALOG_MESS_BTN_ADD_TO_BASKET"],
	"MESS_BTN_SUBSCRIBE" => $arParams["CATALOG_MESS_BTN_SUBSCRIBE"],
	"MESS_BTN_DETAIL" => $arParams["CATALOG_MESS_BTN_DETAIL"],
	"MESS_NOT_AVAILABLE" => $arParams["CATALOG_MESS_NOT_AVAILABLE"],
	"CACHE_TYPE" => $arParams["CACHE_TYPE"],
	"CACHE_TIME" => $arParams["CACHE_TIME"],
	"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
	"USE_MAIN_ELEMENT_SECTION" => $arParams["CATALOG_USE_MAIN_ELEMENT_SECTION"],
	"CACHE_FILTER" => $arParams["CACHE_FILTER"],	
	"USE_REVIEW" => $arParams["CATALOG_USE_REVIEW"],
	"REVIEWS_IBLOCK_TYPE" => $arParams["CATALOG_REVIEWS_IBLOCK_TYPE"],
	"REVIEWS_IBLOCK_ID" => $arParams["CATALOG_REVIEWS_IBLOCK_ID"],
	"REVIEWS_NEWS_COUNT" => $arParams["CATALOG_REVIEWS_NEWS_COUNT"],
	"REVIEWS_SORT_BY1" => $arParams["CATALOG_REVIEWS_SORT_BY1"],
	"REVIEWS_SORT_ORDER1" => $arParams["CATALOG_REVIEWS_SORT_ORDER1"],
	"REVIEWS_SORT_BY2" => $arParams["CATALOG_REVIEWS_SORT_BY2"],
	"REVIEWS_SORT_ORDER2" => $arParams["CATALOG_REVIEWS_SORT_ORDER2"],
	"REVIEWS_ACTIVE_DATE_FORMAT" => $arParams["CATALOG_REVIEWS_ACTIVE_DATE_FORMAT"],
	"REVIEWS_PROPERTY_CODE" => $arParams["CATALOG_REVIEWS_PROPERTY_CODE"],
	"MESS_REVIEWS_TAB" => $arParams["CATALOG_MESS_REVIEWS_TAB"],
	"PRICE_CODE" => $arParams["CATALOG_PRICE_CODE"],
	"USE_PRICE_COUNT" => $arParams["CATALOG_USE_PRICE_COUNT"],
	"SHOW_PRICE_COUNT" => $arParams["CATALOG_SHOW_PRICE_COUNT"],
	"PRICE_VAT_INCLUDE" => $arParams["CATALOG_PRICE_VAT_INCLUDE"],
	"CONVERT_CURRENCY" => $arParams["CATALOG_CONVERT_CURRENCY"],
	"CURRENCY_ID" => $arParams["CATALOG_CURRENCY_ID"],
	"BASKET_URL" => $arParams["CATALOG_BASKET_URL"],
	"USE_PRODUCT_QUANTITY" => $arParams["CATALOG_USE_PRODUCT_QUANTITY"],
	"ADD_PROPERTIES_TO_BASKET" => $arParams["CATALOG_ADD_PROPERTIES_TO_BASKET"],
	"PARTIAL_PRODUCT_PROPERTIES" => $arParams["CATALOG_PARTIAL_PRODUCT_PROPERTIES"],
	"PRODUCT_PROPERTIES" => $arParams["CATALOG_PRODUCT_PROPERTIES"],
	"OFFERS_CART_PROPERTIES" => $arParams["CATALOG_OFFERS_CART_PROPERTIES"],
	"ADD_TO_BASKET_ACTION" => $arParams["CATALOG_ADD_TO_BASKET_ACTION"],
	"DISPLAY_COMPARE" => $arParams["CATALOG_DISPLAY_COMPARE"],
	"COMPARE_PATH" => $arParams["CATALOG_COMPARE_PATH"],
	"MESS_BTN_COMPARE" => $arParams["CATALOG_MESS_BTN_COMPARE"],
	"COMPARE_NAME" => $arParams["CATALOG_COMPARE_NAME"],
	"DETAIL_ADD_PICT_PROP" => $arParams["CATALOG_DETAIL_ADD_PICT_PROP"],
	"DETAIL_OFFER_ADD_PICT_PROP" => $arParams["CATALOG_DETAIL_OFFER_ADD_PICT_PROP"],
	"DETAIL_USE_RATIO_IN_RANGES" => $arParams["CATALOG_DETAIL_USE_RATIO_IN_RANGES"],
	"DETAIL_PROPERTY_CODE" => $arParams["CATALOG_DETAIL_PROPERTY_CODE"],				
	"DETAIL_OFFERS_FIELD_CODE" => $arParams["CATALOG_DETAIL_OFFERS_FIELD_CODE"],
	"DETAIL_OFFERS_PROPERTY_CODE" => $arParams["CATALOG_DETAIL_OFFERS_PROPERTY_CODE"],
	"DETAIL_MAIN_BLOCK_PROPERTY_CODE" => $arParams["CATALOG_DETAIL_MAIN_BLOCK_PROPERTY_CODE"],
	"DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE" => $arParams["CATALOG_DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE"],	
	"DETAIL_IMAGE_RESOLUTION" => $arParams["CATALOG_DETAIL_IMAGE_RESOLUTION"],				
	"DETAIL_ADD_DETAIL_TO_SLIDER" => $arParams["CATALOG_DETAIL_ADD_DETAIL_TO_SLIDER"],
	"DETAIL_DETAIL_PICTURE_MODE" => $arParams["CATALOG_DETAIL_DETAIL_PICTURE_MODE"],
	"DETAIL_SHOW_SLIDER" => $arParams["CATALOG_DETAIL_SHOW_SLIDER"],
	"DETAIL_SLIDER_INTERVAL" => $arParams["CATALOG_DETAIL_SLIDER_INTERVAL"],
	"DETAIL_SLIDER_PROGRESS" => $arParams["CATALOG_DETAIL_SLIDER_PROGRESS"],
	"USE_GIFTS_DETAIL" => $arParams["CATALOG_USE_GIFTS_DETAIL"],
	"GIFTS_DETAIL_PAGE_ELEMENT_COUNT" => $arParams["CATALOG_GIFTS_DETAIL_PAGE_ELEMENT_COUNT"],
	"GIFTS_DETAIL_HIDE_BLOCK_TITLE" => $arParams["CATALOG_GIFTS_DETAIL_HIDE_BLOCK_TITLE"],
	"GIFTS_DETAIL_BLOCK_TITLE" => $arParams["CATALOG_GIFTS_DETAIL_BLOCK_TITLE"],
	"GIFTS_DETAIL_TEXT_LABEL_GIFT" => $arParams["CATALOG_GIFTS_DETAIL_TEXT_LABEL_GIFT"],
	"GIFTS_MESS_BTN_BUY" => $arParams["CATALOG_GIFTS_MESS_BTN_BUY"],
	"USE_STORE" => $arParams["CATALOG_USE_STORE"],
	"STORE_PATH" => $arParams["CATALOG_STORE_PATH"],
	"STORES" => $arParams["CATALOG_STORES"],
	"USE_MIN_AMOUNT" => $arParams["CATALOG_USE_MIN_AMOUNT"],
	"USER_FIELDS" => $arParams["CATALOG_USER_FIELDS"],
	"FIELDS" => $arParams["CATALOG_FIELDS"],
	"MIN_AMOUNT" => $arParams["CATALOG_MIN_AMOUNT"],
	"SHOW_EMPTY_STORE" => $arParams["CATALOG_SHOW_EMPTY_STORE"],
	"SHOW_GENERAL_STORE_INFORMATION" => $arParams["CATALOG_SHOW_GENERAL_STORE_INFORMATION"],
	"MAIN_TITLE" => $arParams["CATALOG_MAIN_TITLE"],
	"SET_ITEMS_COUNT" => $arParams["CATALOG_SET_ITEMS_COUNT"],	
	"REINIT_ADD_BUY_URL_TEMPLATE" => $arParams["CATALOG_REINIT_ADD_BUY_URL_TEMPLATE"],
	"OBJECTS_USE_REVIEW" => $arParams["OBJECTS_USE_REVIEW"],
	"OBJECTS_REVIEWS_IBLOCK_ID" => $arParams["OBJECTS_REVIEWS_IBLOCK_ID"],
	"CONTACTS_IBLOCK_ID" => $arParams["CONTACTS_IBLOCK_ID"],
	"CONTACTS_USE_REVIEW" => $arParams["CONTACTS_USE_REVIEW"],
	"CONTACTS_REVIEWS_IBLOCK_ID" => $arParams["CONTACTS_REVIEWS_IBLOCK_ID"],
	"CONTACTS_REVIEWS_PAGE_LINK" => $arParams["CONTACTS_REVIEWS_PAGE_LINK"]
);

$signer = new \Bitrix\Main\Security\Sign\Signer;
$signedParams = $signer->sign(base64_encode(serialize($arParamsCatalog)), "news.detail");
$signedProductsIds = $signer->sign(base64_encode(serialize($arResult["PRODUCTS_IDS"])), "news.detail");

$arJSParams = array(				
	"CONFIG" => array(
		"PARAMS" => $signedParams
	),
	"ITEM" => array(
		"PRODUCTS_IDS" => $signedProductsIds
	),
	"VISUAL" => array(
		"ID" => $mainId
	)
);?>

<script type="text/javascript">
	BX.message({
		COLLECTIONS_ITEM_DETAIL_PRODUCTS_SECTIONS_ALL: '<?=GetMessageJS("COLLECTIONS_ITEM_DETAIL_PRODUCTS_SECTIONS_ALL")?>',
		COLLECTIONS_ITEM_DETAIL_PRODUCTS_SECTIONS_SHOW_ALL: '<?=GetMessageJS("COLLECTIONS_ITEM_DETAIL_PRODUCTS_SECTIONS_SHOW_ALL")?>',
		COLLECTIONS_ITEM_DETAIL_PRODUCTS_SECTIONS_HIDE: '<?=GetMessageJS("COLLECTIONS_ITEM_DETAIL_PRODUCTS_SECTIONS_HIDE")?>',
		COLLECTION_TEMPLATE_PATH: "<?=$templateFolder?>"
	});
	var <?=$obName;?> = new JCNewsDetailCollection(<?=CUtil::PhpToJSObject($arJSParams, false, true);?>);
</script>