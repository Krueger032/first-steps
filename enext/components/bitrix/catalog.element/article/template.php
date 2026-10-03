<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

$arSettings = CEnext::GetFrontParametrsValues(SITE_ID);
$minOrderSum = intval($arSettings["MIN_ORDER_SUM"]);

$templateLibrary = array("popup", "fx");
$currencyList = "";

if(!empty($arResult["CURRENCIES"])) {
	$templateLibrary[] = "currency";
	$currencyList = CUtil::PhpToJSObject($arResult["CURRENCIES"], false, true, true);
}

$templateData = array(	
	"TEMPLATE_LIBRARY" => $templateLibrary,
	"CURRENCIES" => $currencyList
);
unset($currencyList, $templateLibrary);

$mainId = $this->GetEditAreaId($arResult["ID"].(!empty($arParams["RCM_ID"]) ? "_".md5($arParams["RCM_ID"]) : ""));
$itemIds = array(
	"ID" => $mainId,
	"DISCOUNT_PERCENT_ID" => $mainId."_dsc_pict",	
	"BIG_SLIDER_ID" => $mainId."_big_slider",	
	"SLIDER_CONT_ID" => $mainId."_slider_cont",
	"ARTICLE_ID" => $mainId."_article",
	"OLD_PRICE_ID" => $mainId."_old_price",
	"PRICE_ID" => $mainId."_price",
	"DISCOUNT_PRICE_ID" => $mainId."_price_discount",	
	"SLIDER_CONT_OF_ID" => $mainId."_slider_cont_",
	"QUANTITY_ID" => $mainId."_quantity",
	"QUANTITY_DOWN_ID" => $mainId."_quant_down",
	"QUANTITY_UP_ID" => $mainId."_quant_up",	
	"PC_QUANTITY_ID" => $mainId."_pc_quantity",
	"PC_QUANTITY_DOWN_ID" => $mainId."_pc_quant_down",
	"PC_QUANTITY_UP_ID" => $mainId."_pc_quant_up",	
	"SQ_M_QUANTITY_ID" => $mainId."_sq_m_quantity",
	"SQ_M_QUANTITY_DOWN_ID" => $mainId."_sq_m_quant_down",
	"SQ_M_QUANTITY_UP_ID" => $mainId."_sq_m_quant_up",
	"QUANTITY_MEASURE" => $mainId."_quant_measure",
	"QUANTITY_LIMIT" => $mainId."_quant_limit",
	"QUANTITY_LIMIT_NOT_AVAILABLE" => $mainId."_quant_limit_not_avl",
	"TOTAL_COST_ID" => $mainId."_total_cost",
	"BUY_LINK" => $mainId."_buy_link",
	"ADD_BASKET_LINK" => $mainId."_add_basket_link",	
	"BASKET_ACTIONS_ID" => $mainId."_basket_actions",
	"PARTNERS_LINK" => $mainId."_partners_link",
	"PARTNERS_ID" => $mainId."_partners",
	"ASK_PRICE_LINK" => $mainId."_ask_price",
	"NOT_AVAILABLE_MESS" => $mainId."_not_avail",
	"COMPARE_LINK" => $mainId."_compare_link",	
	"DELAY_LINK" => $mainId."_delay_link",
	"SELECT_SKU_LINK" => $mainId."_select_sku_link",
	"TREE_ID" => $mainId."_skudiv",	
	"DISPLAY_MAIN_PROP_DIV" => $mainId."_main_sku_prop",	
	"BASKET_PROP_DIV" => $mainId."_basket_prop",
	"SUBSCRIBE_LINK" => $mainId."_subscribe",		
	"SKU_ITEMS_ID" => $mainId."_sku_items"
);
if(isset($arParams["POPUP_MODE"]) && $arParams["POPUP_MODE"] == "Y")
	$itemIds["QUICK_ORDER_LINK"] = $mainId."_quick_order";

$obName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $mainId);

$name = !empty($arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"])
	? $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]
	: $arResult["NAME"];

$title = !empty($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"])
	? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"]
	: $arResult["NAME"];

$alt = !empty($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"])
	? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"]
	: $arResult["NAME"];

$haveOffers = !empty($arResult["OFFERS"]);
if($haveOffers) {
	$actualItem = isset($arResult["OFFERS"][$arResult["OFFERS_SELECTED"]]) ? $arResult["OFFERS"][$arResult["OFFERS_SELECTED"]] : reset($arResult["OFFERS"]);
	
	$showSliderControls = false;
	if($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST") {
		foreach($arResult["OFFERS"] as $offer) {
			if($offer["MORE_PHOTO_COUNT"] > 1) {
				$showSliderControls = true;
				break;
			}
		}
		unset($offer);
	} else {
		$showSliderControls = $actualItem["MORE_PHOTO_COUNT"] > 1;
	}
} else {
	$actualItem = $arResult;
	$showSliderControls = $arResult["MORE_PHOTO_COUNT"] > 1;
}

$skuProps = array();
$price = $actualItem["ITEM_PRICES"][$actualItem["ITEM_PRICE_SELECTED"]];
$measureRatio = $actualItem["ITEM_MEASURE_RATIOS"][$actualItem["ITEM_MEASURE_RATIO_SELECTED"]]["RATIO"];
$showDiscount = $price["PERCENT"] > 0;

$isMeasurePc = $isMeasureSqM = false;
if($actualItem["ITEM_MEASURE"]["SYMBOL_INTL"] == "pc. 1")
	$isMeasurePc = true;
elseif($actualItem["ITEM_MEASURE"]["SYMBOL_INTL"] == "m2")
	$isMeasureSqM = true;

$showBuyBtn = in_array("BUY", $arParams["ADD_TO_BASKET_ACTION"]);
$showAddBtn = in_array("ADD", $arParams["ADD_TO_BASKET_ACTION"]);
$showSubscribe = $arParams["PRODUCT_SUBSCRIPTION"] === "Y" && ($arResult["CATALOG_SUBSCRIBE"] === "Y" || $haveOffers);

$object = !empty($arResult["PROPERTIES"]["OBJECT"]["FULL_VALUE"]) ? $arResult["PROPERTIES"]["OBJECT"]["FULL_VALUE"] : false;
$objectContacts = $object["PHONE_SMS"] || $object["EMAIL_EMAIL"] ? true : false;

if(!$haveOffers || $arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST")
	$partnersUrl = !empty($actualItem["PROPERTIES"]["PARTNERS_URL"]["VALUE"]) ? true : false;
else
	$partnersUrl = !empty($arResult["PROPERTIES"]["PARTNERS_URL"]["VALUE"]) ? true : false;

$arParams["MESS_BTN_BUY"] = $arParams["MESS_BTN_BUY"] ?: Loc::getMessage("CT_BCE_CATALOG_BUY");
$arParams["MESS_BTN_ADD_TO_BASKET"] = $arParams["MESS_BTN_ADD_TO_BASKET"] ?: Loc::getMessage("CT_BCE_CATALOG_ADD");
$arParams["MESS_NOT_AVAILABLE"] = $arParams["MESS_NOT_AVAILABLE"] ?: Loc::getMessage("CT_BCE_CATALOG_NOT_AVAILABLE");
$arParams["MESS_BTN_COMPARE"] = $arParams["MESS_BTN_COMPARE"] ?: Loc::getMessage("CT_BCE_CATALOG_COMPARE");
$arParams["MESS_BTN_DELAY"] = $arParams["MESS_BTN_DELAY"] ?: Loc::getMessage("CT_BCE_CATALOG_DELAY");
$arParams["MESS_SHOW_MAX_QUANTITY"] = $arParams["MESS_SHOW_MAX_QUANTITY"] ?: Loc::getMessage("CT_BCE_CATALOG_SHOW_MAX_QUANTITY");
$arParams["MESS_RELATIVE_QUANTITY_MANY"] = $arParams["MESS_RELATIVE_QUANTITY_MANY"] ?: Loc::getMessage("CT_BCE_CATALOG_RELATIVE_QUANTITY_MANY");
$arParams["MESS_RELATIVE_QUANTITY_FEW"] = $arParams["MESS_RELATIVE_QUANTITY_FEW"] ?: Loc::getMessage("CT_BCE_CATALOG_RELATIVE_QUANTITY_FEW");?>

<div class="bx-catalog-element-article" id="<?=$itemIds['ID']?>">
	<div class="row">
		<div class="col-xs-12 col-md-9">
			<div class="row">
				<div class="col-xs-12 col-md-7">
					<?//SLIDER//?>
					<div class="product-item-detail-article-slider-container<?=($showSliderControls ? ' full' : '')?>" id="<?=$itemIds['BIG_SLIDER_ID']?>">
						<span class="product-item-detail-article-slider-close" data-entity="close-popup"><i class="icon-close"></i></span>
						<div class="product-item-detail-article-slider-block<?=($arParams['IMAGE_RESOLUTION'] === '1by1' ? ' product-item-detail-article-slider-block-square' : '')?>">
							<span class="product-item-detail-article-slider-left" data-entity="slider-control-left" style="display: none;"><i class="icon-arrow-left"></i></span>
							<span class="product-item-detail-article-slider-right" data-entity="slider-control-right" style="display: none;"><i class="icon-arrow-right"></i></span>
							<?//MARKERS//?>
							<div class="product-item-detail-article-markers">
								<?if($arParams["SHOW_DISCOUNT_PERCENT"] === "Y") {?>
									<span class="product-item-detail-article-marker-container<?=($showDiscount ? '' : ' product-item-detail-article-marker-container-hidden')?>" id="<?=$itemIds['DISCOUNT_PERCENT_ID']?>">
										<span class="product-item-detail-article-marker product-item-detail-article-marker-discount product-item-detail-article-marker-14px"><span data-entity="dsc-perc-val"><?=-$price["PERCENT"]?>%</span></span>
									</span>
								<?}
								if(!empty($arResult["PROPERTIES"]["MARKER"]["FULL_VALUE"])) {
									foreach($arResult["PROPERTIES"]["MARKER"]["FULL_VALUE"] as $key => $arMarker) {
										if($key <= 4) {?>
											<span class="product-item-detail-article-marker-container">
												<span class="product-item-detail-article-marker<?=(!empty($arMarker['FONT_SIZE']) ? ' product-item-detail-article-marker-'.$arMarker['FONT_SIZE'] : '')?>"<?=(!empty($arMarker["BACKGROUND_1"]) && !empty($arMarker["BACKGROUND_2"]) ? " style='background: ".$arMarker["BACKGROUND_2"]."; background: -webkit-linear-gradient(left, ".$arMarker["BACKGROUND_1"].", ".$arMarker["BACKGROUND_2"]."); background: -moz-linear-gradient(left, ".$arMarker["BACKGROUND_1"].", ".$arMarker["BACKGROUND_2"]."); background: -o-linear-gradient(left, ".$arMarker["BACKGROUND_1"].", ".$arMarker["BACKGROUND_2"]."); background: -ms-linear-gradient(left, ".$arMarker["BACKGROUND_1"].", ".$arMarker["BACKGROUND_2"]."); background: linear-gradient(to right, ".$arMarker["BACKGROUND_1"].", ".$arMarker["BACKGROUND_2"].");'" : (!empty($arMarker["BACKGROUND_1"]) && empty($arMarker["BACKGROUND_2"]) ? " style='background: ".$arMarker["BACKGROUND_1"].";'" : (empty($arMarker["BACKGROUND_1"]) && !empty($arMarker["BACKGROUND_2"]) ? " style='background: ".$arMarker["BACKGROUND_2"].";'" : "")))?>><?=(!empty($arMarker["ICON"]) ? "<i class='".$arMarker["ICON"]."'></i>" : "")?><span><?=$arMarker["NAME"]?></span></span>
											</span>
										<?} else {
											break;
										}
									}
									unset($key, $arMarker);
								}?>
							</div>
							<?//MAGNIFIER_DELAY//
							if(in_array("POPUP", $arParams["DETAIL_PICTURE_MODE"]) || (!$arParams["DISABLE_DELAY"] && (!$object || ($object && $objectContacts)) && (!$haveOffers || ($haveOffers && ($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST"))))) {?>
								<div class="product-item-detail-article-icons-container">
									<?if(in_array("POPUP", $arParams["DETAIL_PICTURE_MODE"])) {?>
										<div class="product-item-detail-article-magnifier">
											<i class="icon-scale-plus" data-entity="slider-magnifier"></i>
										</div>
									<?}
									if(!$arParams["DISABLE_DELAY"] && (!$object || ($object && $objectContacts)) && (!$haveOffers || ($haveOffers && ($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST")))) {?>
										<div class="product-item-detail-article-delay" id="<?=$itemIds['DELAY_LINK']?>" title="<?=$arParams['MESS_BTN_DELAY']?>" style="display: <?=(!$partnersUrl && $actualItem['CAN_BUY'] && $price['PRICE'] > 0 ? '' : 'none')?>;"><i class="icon-heart"></i></div>
									<?}?>
								</div>
							<?}
							//SLIDER_IMAGES//?>
							<div class="product-item-detail-article-slider-videos-images-container" data-entity="videos-images-container">
								<?if(!empty($actualItem["MORE_PHOTO"])) {
									$activeKey = 0;
									foreach($actualItem["MORE_PHOTO"] as $key => $photo) {
										if(!empty($photo["VALUE"])) {
											$activeKey++;?>
											<div class="product-item-detail-article-slider-video" data-entity="video" data-id="<?=$photo['ID']?>">
												<iframe width="640" height="480" src="<?=$arResult['SCHEME']?>://www.youtube.com/embed/<?=$photo['VALUE']?>?rel=0&showinfo=0&enablejsapi=1" frameborder="0" allowfullscreen></iframe>
											</div>
										<?} else {?>
											<div class="product-item-detail-article-slider-image<?=($key == $activeKey ? ' active' : '')?>" data-entity="image" data-id="<?=$photo['ID']?>">
												<img src="<?=$photo['SRC']?>" width="<?=$photo['WIDTH']?>" height="<?=$photo['HEIGHT']?>" alt="<?=$alt?>" title="<?=$title?>">
											</div>
										<?}
									}
									unset($key, $photo, $activeKey);
								}
								//SLIDER_PROGRESS//
								if($arParams["SLIDER_PROGRESS"] === "Y") {?>
									<div class="product-item-detail-article-slider-progress-bar" data-entity="slider-progress-bar" style="width: 0;"></div>
								<?}?>
							</div>
							<?//BRAND//
							if(!empty($arResult["PROPERTIES"]["BRAND"]["FULL_VALUE"]["PREVIEW_PICTURE"])) {?>
								<div class="product-item-detail-article-brand">
									<img src="<?=$arResult['PROPERTIES']['BRAND']['FULL_VALUE']['PREVIEW_PICTURE']['SRC']?>" width="<?=$arResult['PROPERTIES']['BRAND']['FULL_VALUE']['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arResult['PROPERTIES']['BRAND']['FULL_VALUE']['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arResult['PROPERTIES']['BRAND']['FULL_VALUE']['NAME']?>" title="<?=$arResult['PROPERTIES']['BRAND']['FULL_VALUE']['NAME']?>" />
								</div>
							<?}?>
						</div>
						<?//SLIDER_CONTROLS//
						if($showSliderControls) {
							if($haveOffers) {
								if($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST") {
									foreach($arResult["OFFERS"] as $keyOffer => $offer) {
										if(!isset($offer["MORE_PHOTO_COUNT"]) || $offer["MORE_PHOTO_COUNT"] <= 0)
											continue;
										$strVisible = $arResult["OFFERS_SELECTED"] == $keyOffer ? "" : "none";?>
										<div class="hidden-xs hidden-sm product-item-detail-article-slider-controls-block" id="<?=$itemIds['SLIDER_CONT_OF_ID'].$offer['ID']?>" style="display: <?=$strVisible?>;">
											<?$activeKeyPhoto = 0;
											foreach($offer["MORE_PHOTO"] as $keyPhoto => $photo) {
												if(!empty($photo["VALUE"])) {
													$activeKeyPhoto++;?>
													<div class="product-item-detail-article-slider-controls-video" data-entity="slider-control" data-value="<?=$offer['ID'].'_'.$photo['ID']?>">
														<div class="product-item-detail-article-slider-controls-video-image">
															<img src="<?=$arResult['SCHEME']?>://img.youtube.com/vi/<?=$photo['VALUE']?>/default.jpg" alt="<?=$alt?>" title="<?=$title?>" />
														</div>
														<div class="product-item-detail-article-slider-controls-video-play"><i class="icon-play-s"></i></div>
													</div>
												<?} else {?>
													<div class="product-item-detail-article-slider-controls-image<?=($keyPhoto == $activeKeyPhoto ? ' active' : '')?>" data-entity="slider-control" data-value="<?=$offer['ID'].'_'.$photo['ID']?>">
														<img src="<?=$photo['PREVIEW']['SRC']?>" width="<?=$photo['PREVIEW']['WIDTH']?>" height="<?=$photo['PREVIEW']['HEIGHT']?>" alt="<?=$alt?>" title="<?=$title?>" />
													</div>
												<?}
											}
											unset($keyPhoto, $photo, $activeKeyPhoto);?>
										</div>
									<?}
									unset($keyOffer, $offer);
								} else {
									$offer = isset($arResult["OFFERS"][$arResult["OFFERS_SELECTED"]]) ? $arResult["OFFERS"][$arResult["OFFERS_SELECTED"]] : reset($arResult["OFFERS"]);
									if($offer["MORE_PHOTO_COUNT"] > 0) {?>
										<div class="hidden-xs hidden-sm product-item-detail-article-slider-controls-block" id="<?=$itemIds['SLIDER_CONT_OF_ID'].$offer['ID']?>">
											<?$activeKeyPhoto = 0;
											foreach($offer["MORE_PHOTO"] as $keyPhoto => $photo) {
												if(!empty($photo["VALUE"])) {
													$activeKeyPhoto++;?>
													<div class="product-item-detail-article-slider-controls-video" data-entity="slider-control" data-value="<?=$offer['ID'].'_'.$photo['ID']?>">
														<div class="product-item-detail-article-slider-controls-video-image">
															<img src="<?=$arResult['SCHEME']?>://img.youtube.com/vi/<?=$photo['VALUE']?>/default.jpg" alt="<?=$alt?>" title="<?=$title?>" />
														</div>
														<div class="product-item-detail-article-slider-controls-video-play"><i class="icon-play-s"></i></div>
													</div>
												<?} else {?>
													<div class="product-item-detail-article-slider-controls-image<?=($keyPhoto == $activeKeyPhoto ? ' active' : '')?>" data-entity="slider-control" data-value="<?=$offer['ID'].'_'.$photo['ID']?>">
														<img src="<?=$photo['PREVIEW']['SRC']?>" width="<?=$photo['PREVIEW']['WIDTH']?>" height="<?=$photo['PREVIEW']['HEIGHT']?>" alt="<?=$alt?>" title="<?=$title?>" />
													</div>
												<?}
											}
											unset($keyPhoto, $photo, $activeKeyPhoto);?>
										</div>
									<?}
								}
							} else {?>
								<div class="hidden-xs hidden-sm product-item-detail-article-slider-controls-block" id="<?=$itemIds['SLIDER_CONT_ID']?>">
									<?if(!empty($actualItem["MORE_PHOTO"])) {
										$activeKey = 0;
										foreach($actualItem["MORE_PHOTO"] as $key => $photo) {
											if(!empty($photo["VALUE"])) {
												$activeKey++;?>
												<div class="product-item-detail-article-slider-controls-video" data-entity="slider-control" data-value="<?=$photo['ID']?>">
													<div class="product-item-detail-article-slider-controls-video-image">
														<img src="<?=$arResult['SCHEME']?>://img.youtube.com/vi/<?=$photo['VALUE']?>/default.jpg" alt="<?=$alt?>" title="<?=$title?>" />
													</div>
													<div class="product-item-detail-article-slider-controls-video-play"><i class="icon-play-s"></i></div>
												</div>
											<?} else {?>
												<div class="product-item-detail-article-slider-controls-image<?=($key == $activeKey ? ' active' : '')?>" data-entity="slider-control" data-value="<?=$photo['ID']?>">
													<img src="<?=$photo['PREVIEW']['SRC']?>" width="<?=$photo['PREVIEW']['WIDTH']?>" height="<?=$photo['PREVIEW']['HEIGHT']?>" alt="<?=$alt?>" title="<?=$title?>" />
												</div>
											<?}
										}
										unset($key, $photo, $activeKey);
									}?>
								</div>
							<?}
						}?>
					</div>
				</div>
				<div class="col-xs-12 col-md-5 product-item-detail-article-blocks">
					<?//ARTICLE//
					if($haveOffers && ($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST")) {
						$article = !empty($actualItem["PROPERTIES"]["ARTNUMBER"]["VALUE"]) ? $actualItem["PROPERTIES"]["ARTNUMBER"]["VALUE"] : false;?>
						<div class="product-item-detail-article-article" id="<?=$itemIds['ARTICLE_ID']?>"<?=($article ? '' : ' style="display:none;"')?>>
							<?=Loc::getMessage("CT_BCE_CATALOG_ARTICLE")?>
							<span data-entity="article-value"><?=($article ? $article : '')?></span>
						</div>
					<?} elseif(!empty($arResult["PROPERTIES"]["ARTNUMBER"]["VALUE"])) {?>
						<div class="product-item-detail-article-article">
							<?=Loc::getMessage("CT_BCE_CATALOG_ARTICLE")?>
							<span><?=$arResult["PROPERTIES"]["ARTNUMBER"]["VALUE"]?></span>
						</div>
					<?}
					//RATING//
					if(isset($arResult["REVIEWS_COUNT"])) {?>							
						<div class="product-item-detail-article-rating">
							<?if($arResult["RATING_VALUE"] > 0) {?>
								<div class="product-item-detail-article-rating-val"<?=($arResult["RATING_VALUE"] <= 4.4 ? " data-rate='".intval($arResult["RATING_VALUE"])."'" : "")?>><?=$arResult["RATING_VALUE"]?></div>
							<?}?>
							<div class="product-item-detail-article-rating-reviews-count"><?=($arResult["REVIEWS_COUNT"] > 0 ? $arResult["REVIEWS_COUNT"]." ".$arResult["REVIEWS_DECLENSION"] : Loc::getMessage("CT_BCE_CATALOG_NO_REVIEWS"))?></div>
						</div>
					<?}
					//PREVIEW_TEXT//
					if(!empty($arResult["PREVIEW_TEXT"])) {?>
						<div class="product-item-detail-article-preview"><?=$arResult["PREVIEW_TEXT"]?></div>
					<?}
					//PROPERTIES//
					$isMainProps = false;
					if(!empty($arResult["DISPLAY_PROPERTIES"])) {
						foreach($arResult["DISPLAY_PROPERTIES"] as $property) {
							if(isset($arParams["MAIN_BLOCK_PROPERTY_CODE"][$property["CODE"]])) {
								$isMainProps = true;
								break;
							}
						}
						unset($property);
					}
					if(!!$isMainProps || (($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST") && $arResult["SHOW_OFFERS_PROPS"])) {?>
						<div class="product-item-detail-article-main-properties-container">					
							<div class="product-item-detail-article-properties-block"<?=(($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST") && $arResult["SHOW_OFFERS_PROPS"] ? " id='".$itemIds["DISPLAY_MAIN_PROP_DIV"]."'" : "");?>>
								<?if(!empty($arResult["DISPLAY_PROPERTIES"])) {
									foreach($arResult["DISPLAY_PROPERTIES"] as $property) {
										if(isset($arParams["MAIN_BLOCK_PROPERTY_CODE"][$property["CODE"]])) {?>
											<div class="product-item-detail-article-properties">
												<div class="product-item-detail-article-properties-name"><?=$property["NAME"]?></div>
												<div class="product-item-detail-article-properties-val"><?=$property["DISPLAY_VALUE"]?></div>
											</div>
										<?}
									}
									unset($property);
								}?>
							</div>
						</div>
					<?}
					unset($isMainProps);
					//ADVANTAGES//			
					if(!empty($arResult["PROPERTIES"]["ADVANTAGES"]["FULL_VALUE"])) {?>
						<div class="product-item-detail-article-advantages">
							<?foreach($arResult["PROPERTIES"]["ADVANTAGES"]["FULL_VALUE"] as $arItem) {
								if(!empty($arItem["PREVIEW_PICTURE"])) {?>
									<div class="product-item-detail-article-advantages-item">
										<div class="product-item-detail-article-advantages-item-pic">
											<img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" width="<?=$arItem['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arItem['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arItem['NAME']?>" />
										</div>
										<div class="visible-md visible-lg product-item-detail-article-advantages-item-tooltip"><?=$arItem["NAME"]?></div>
									</div>
								<?}
							}
							unset($arItem);?>
						</div>
					<?}?>
				</div>
				<?//SKU_ITEMS//
				if($haveOffers && $arParams["OFFERS_VIEW"] != "PROPS" && $arParams["OFFERS_VIEW"] != "DROPDOWN_LIST") {?>
					<div class="col-xs-12 product-item-detail-article-scu-items-container" id="<?=$itemIds['SKU_ITEMS_ID']?>">
						<div class="h2"><?=Loc::getMessage("CT_BCE_CATALOG_SKU_ITEMS")?></div>
						<div class="product-item-detail-article-scu-items">
							<?//SKU_ITEMS_HEAD//?>
							<div class="hidden-xs hidden-sm product-item-detail-article-scu-item<?=($arParams['OFFERS_VIEW'] != 'LIST' ? '-object' : '')?> product-item-detail-article-scu-item<?=($arParams['OFFERS_VIEW'] != 'LIST' ? '-object' : '')?>-head">
								<div class="product-item-detail-article-scu-item<?=($arParams['OFFERS_VIEW'] != 'LIST' ? '-object' : '')?>-col"><?=($arParams["OFFERS_VIEW"] == "LIST" ? Loc::getMessage("CT_BCE_CATALOG_SKU_ITEMS_ITEM") : Loc::getMessage("CT_BCE_CATALOG_SKU_ITEMS_OBJECTS_ITEM"))?></div>
								<?if($arParams["OFFERS_VIEW"] != "LIST") {?>
									<div class="product-item-detail-article-scu-item-object-col product-item-detail-article-scu-item-object-col-object"></div>
								<?}?>
								<div class="product-item-detail-article-scu-item<?=($arParams['OFFERS_VIEW'] != 'LIST' ? '-object' : '')?>-col product-item-detail-article-scu-item<?=($arParams['OFFERS_VIEW'] != 'LIST' ? '-object' : '')?>-col-info"></div>
								<?if($arParams["OFFERS_VIEW"] == "LIST" && !empty($arResult["OFFERS_PROP"])) {
									foreach($arResult["SKU_PROPS"] as $skuProperty) {
										if(!isset($arResult["OFFERS_PROP"][$skuProperty["CODE"]]))
											continue;?>											
											
										<div class="product-item-detail-article-scu-item-col product-item-detail-article-scu-item-col-prop"><?=htmlspecialcharsEx($skuProperty["NAME"])?></div>
									<?}
									unset($skuProperty);
								}?>								
								<div class="product-item-detail-article-scu-item<?=($arParams['OFFERS_VIEW'] != 'LIST' ? '-object' : '')?>-col product-item-detail-article-scu-item<?=($arParams['OFFERS_VIEW'] != 'LIST' ? '-object' : '')?>-col-price"><?=Loc::getMessage("CT_BCE_CATALOG_SKU_ITEMS_PRICE")?></div>
								<?if($arParams["USE_PRODUCT_QUANTITY"] && (($arParams["OFFERS_VIEW"] == "LIST" && (!$object || ($object && $objectContacts))) || $arParams["OFFERS_VIEW"] != "LIST")) {?>
									<div class="product-item-detail-article-scu-item<?=($arParams['OFFERS_VIEW'] != 'LIST' ? '-object' : '')?>-col"></div>
								<?}?>
								<div class="product-item-detail-article-scu-item<?=($arParams['OFFERS_VIEW'] != 'LIST' ? '-object' : '')?>-col product-item-detail-article-scu-item<?=($arParams['OFFERS_VIEW'] != 'LIST' ? '-object' : '')?>-col-buttons"></div>
								<?if(!$arParams["DISABLE_DELAY"] && $arParams["OFFERS_VIEW"] == "LIST" && (!$object || ($object && $objectContacts)) && !$partnersUrl) {
									$numOffersPartnersUrl = 0;
									foreach($arResult["OFFERS"] as $arOffer) {
										if(!empty($arOffer["PROPERTIES"]["PARTNERS_URL"]["VALUE"]))
											$numOffersPartnersUrl++;
									}
									unset($arOffer);
									if($numOffersPartnersUrl != count($arResult["OFFERS"])) {?>
										<div class="product-item-detail-article-scu-item-col product-item-detail-article-scu-item-col-delay"></div>
									<?}
								}?>
							</div>
							<?//SKU_ITEMS_BODY//
							foreach($arResult["OFFERS"] as $key => $arOffer) {
								$offerName = !empty($arOffer["NAME"]) ? $arOffer["NAME"] : $arResult["NAME"];
								$offerTitle = !empty($arOffer["NAME"]) ? $arOffer["NAME"] : $title;
								$offerAlt =  !empty($arOffer["NAME"]) ? $arOffer["NAME"] : $alt;
								
								$offerPrice = $arOffer["ITEM_PRICES"][$arOffer["ITEM_PRICE_SELECTED"]];
								$offerMeasureRatio = $arOffer["ITEM_MEASURE_RATIOS"][$arOffer["ITEM_MEASURE_RATIO_SELECTED"]]["RATIO"];
								
								$offerObject = !empty($arOffer["PROPERTIES"]["OBJECT"]["FULL_VALUE"]) ? $arOffer["PROPERTIES"]["OBJECT"]["FULL_VALUE"] : false;
								$offerObjectContacts = $offerObject["PHONE_SMS"] || $offerObject["EMAIL_EMAIL"] ? true : false;
								$offerPartnersUrl = !empty($arOffer["PROPERTIES"]["PARTNERS_URL"]["VALUE"]) ? true : false;?>
								
								<div class="product-item-detail-article-scu-item<?=($arParams['OFFERS_VIEW'] != 'LIST' ? '-object' : '').($arParams['OFFERS_VIEW'] == 'LIST' && $arParams['DISPLAY_COMPARE'] ? ' product-item-detail-article-scu-item-width-compare' : '')?>" data-entity="sku-item" data-num="<?=$key?>">
									<div class="product-item-detail-article-scu-item<?=($arParams['OFFERS_VIEW'] != 'LIST' ? '-object' : '')?>-col">
										<div class="product-item-detail-article-scu-item<?=($arParams['OFFERS_VIEW'] != 'LIST' ? '-object' : '')?>-image">
											<?if($arParams["OFFERS_VIEW"] == "LIST") {
												//SKU_ITEMS_LIST_IMAGE//
												if(is_array($arOffer["PREVIEW_PICTURE"])) {?>
													<img src="<?=$arOffer['PREVIEW_PICTURE']['SRC']?>" width="<?=$arOffer['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arOffer['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$offerAlt?>" title="<?=$offerTitle?>" />
												<?} elseif(is_array($arResult["PREVIEW_PICTURE"])) {?>
													<img src="<?=$arResult['PREVIEW_PICTURE']['SRC']?>" width="<?=$arResult['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arResult['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$offerAlt?>" title="<?=$offerTitle?>" />
												<?} else {?>
													<img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo.png" width="222" height="222" alt="<?=$offerAlt?>" title="<?=$offerTitle?>" />
												<?}
												//SKU_ITEMS_LIST_COMPARE//
												if($arParams["DISPLAY_COMPARE"]) {?>
													<div class="product-item-detail-article-compare">
														<label title="<?=$arParams['MESS_BTN_COMPARE']?>" data-entity="compare">
															<input type="checkbox" data-entity="compare-checkbox">
															<span class="product-item-detail-article-compare-checkbox"><i class="icon-ok-b"></i></span>
															<span class="visible-xs visible-sm product-item-detail-article-compare-title" data-entity="compare-title"><?=$arParams["MESS_BTN_COMPARE"]?></span>
														</label>
													</div>
												<?}
											} else {
												//SKU_ITEMS_OBJECTS_IMAGE//
												if(is_array($offerObject["PREVIEW_PICTURE"])) {?>
													<img src="<?=$offerObject['PREVIEW_PICTURE']['SRC']?>" width="<?=$offerObject['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$offerObject['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$offerObject['NAME']?>" />
												<?} else {?>
													<img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo.png" width="222" height="222" alt="<?=$offerObject['NAME']?>" />
												<?}
											}?>
										</div>
									</div>
									<?if($arParams["OFFERS_VIEW"] != "LIST") {?>
										<div class="product-item-detail-article-scu-item-object-col product-item-detail-article-scu-item-object-col-object">
											<?//SKU_ITEMS_OBJECTS_TITLE//
											if($offerObject) {?>
												<a target="_blank" class="product-item-detail-article-scu-item-object-title" href="<?=$offerObject['DETAIL_PAGE_URL']?>"><span><?=$offerObject["NAME"]?></span></a>
											<?}
											//SKU_ITEMS_OBJECTS_RATING//
											if(isset($offerObject["REVIEWS_COUNT"]) && $offerObject["REVIEWS_COUNT"] > 0) {?>
												<div class="product-item-detail-article-scu-item-object-rating">
													<div class="product-item-detail-article-scu-item-object-rating-val"<?=($offerObject["RATING_VALUE"] <= 4.4 ? " data-rate='".intval($offerObject["RATING_VALUE"])."'" : "")?>><?=$offerObject["RATING_VALUE"]?></div>
													<div class="product-item-detail-article-scu-item-object-rating-reviews-count"><?=$offerObject["REVIEWS_COUNT"]." ".$offerObject["REVIEWS_DECLENSION"]?></div>
												</div>
											<?}
											//SKU_ITEMS_OBJECTS_HOURS//?>
											<div class="hidden-xs hidden-sm product-item-detail-article-scu-item-object-hours product-item-detail-article-scu-item-object-hours-hidden" data-entity="hours"></div>
										</div>
									<?}?>
									<div class="product-item-detail-article-scu-item<?=($arParams['OFFERS_VIEW'] != 'LIST' ? '-object' : '')?>-col product-item-detail-article-scu-item<?=($arParams['OFFERS_VIEW'] != 'LIST' ? '-object' : '')?>-col-info">
										<?//SKU_ITEMS_ARTICLE//
										$offerArticle = $arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"];
										if(($arParams['OFFERS_VIEW'] != 'LIST' && !empty($offerArticle)) || $arParams['OFFERS_VIEW'] == 'LIST') {?>
											<div class="<?=($arParams['OFFERS_VIEW'] == 'LIST' ? 'hidden-xs hidden-sm ' : '')?>product-item-detail-article-article">
												<?=Loc::getMessage("CT_BCE_CATALOG_ARTICLE");?>
												<span><?=(!empty($offerArticle) ? $offerArticle : "-");?></span>
											</div>
										<?}
										//SKU_ITEMS_LIST_TITLE//
										if($arParams["OFFERS_VIEW"] == "LIST") {?>
											<div class="product-item-detail-article-scu-item-title"><?=$offerName?></div>
										<?}
										//SKU_ITEMS_OBJECTS_NOTE//
										if($arParams["OFFERS_VIEW"] != "LIST" && (!empty($arOffer["PROPERTIES"]["NOTE"]["VALUE"]) || !empty($offerObject["NOTE"]))) {?>
											<div class="product-item-detail-article-scu-item-object-note"><?=(!empty($arOffer["PROPERTIES"]["NOTE"]["VALUE"]) ? $arOffer["PROPERTIES"]["NOTE"]["VALUE"] : $offerObject["NOTE"])?></div>
										<?}?>
									</div>
									<?//SKU_ITEMS_LIST_PROPS//
									if($arParams["OFFERS_VIEW"] == "LIST" && !empty($arResult["OFFERS_PROP"])) {
										foreach($arResult["SKU_PROPS"] as $skuProperty) {
											if(!isset($arResult["OFFERS_PROP"][$skuProperty["CODE"]]))
												continue;?>											
											
											<div class="product-item-detail-article-scu-item-col product-item-detail-article-scu-item-col-prop">
												<div class="visible-xs visible-sm product-item-scu-title"><?=htmlspecialcharsEx($skuProperty["NAME"])?></div>
												<?if(array_key_exists("PROP_".$skuProperty["ID"], $arOffer["TREE"])) {
													$value = $skuProperty["VALUES"][$arOffer["TREE"]["PROP_".$skuProperty["ID"]]];
													if($skuProperty["SHOW_MODE"] === "PICT") {?>
														<div class="product-item-detail-article-scu-item-color" title="<?=$value['NAME']?>" style="<?=(!empty($value['CODE']) ? 'background-color: #'.$value['CODE'].';' : (!empty($value['PICT']) ? 'background-image: url('.$value['PICT']['SRC'].');' : ''));?>"></div>
													<?} else {?>
														<div class="product-item-detail-article-scu-item-text" title="<?=$value['NAME']?>"><?=$value["NAME"]?></div>
													<?}
													unset($value);
												}?>
											</div>
										<?}
										unset($skuProperty);
									}?>									
									<div class="product-item-detail-article-scu-item<?=($arParams['OFFERS_VIEW'] != 'LIST' ? '-object' : '')?>-col product-item-detail-article-scu-item<?=($arParams['OFFERS_VIEW'] != 'LIST' ? '-object' : '')?>-col-price">
										<?//SKU_ITEMS_PRICE//?>
										<div class="product-item-detail-article-scu-item-price">
											<div class="product-item-detail-article-scu-item-price-block">
												<?if(($arParams["OFFERS_VIEW"] == "LIST" && $offerPrice["SQ_M_PRICE"] > 0) || $offerPrice["PRICE"] > 0) {?>
													<span class="product-item-detail-article-scu-item-price-current" data-entity="price-current"><?=($arParams["OFFERS_VIEW"] == "LIST" && $offerPrice["SQ_M_PRICE"] > 0 ? $offerPrice["SQ_M_PRINT_PRICE"] : $offerPrice["PRINT_PRICE"])?></span>
													<?if($arParams["OFFERS_VIEW"] == "LIST") {?>
														<span class="product-item-detail-article-price-measure">/<?=($offerPrice["SQ_M_PRICE"] > 0 ? Loc::getMessage("CT_BCE_CATALOG_MEASURE_SQ_M") : $arOffer["ITEM_MEASURE"]["TITLE"])?></span>
													<?}
													if($arParams["USE_PRICE_COUNT"] && count($arOffer["ITEM_QUANTITY_RANGES"]) > 1) {?>
														<span class="product-item-detail-article-scu-item-price-ranges-icon"><i class="icon-question"></i></span>
													<?}
												} else {?>
													<span class="product-item-detail-article-price-not-set" data-entity="price-current-not-set"><?=Loc::getMessage("CT_BCE_CATALOG_PRICE_NOT_SET")?></span>
												<?}
												if($USER->IsAdmin() && $arParams["OFFERS_VIEW"] != "LIST" && !empty($offerObject["PARSER_TAG"]) && !empty($offerObject["PARSER_CLASS"]) && !empty($arOffer["PROPERTIES"]["PARSER_LINK"]["VALUE"])) {?>
													<span class="product-item-detail-article-scu-item-price-update-icon" data-entity="updateObjectOfferPrice"><i class="icon-repeat"></i></span>
												<?}?>
											</div>
											<?if($arParams["SHOW_OLD_PRICE"] === "Y" && $offerPrice["PERCENT"] > 0) {?>
												<div class="product-item-detail-article-price-old" data-entity="price-old"><?=($offerPrice["SQ_M_BASE_PRICE"] > 0 ? $offerPrice["SQ_M_PRINT_BASE_PRICE"] : $offerPrice["PRINT_BASE_PRICE"])?></div>
												<div class="product-item-detail-article-price-economy" data-entity="price-economy"><?=Loc::getMessage("CT_BCE_CATALOG_ECONOMY_INFO2", array("#ECONOMY#" => ($offerPrice["SQ_M_DISCOUNT"] > 0 ? $offerPrice["SQ_M_PRINT_DISCOUNT"] : $offerPrice["PRINT_DISCOUNT"])))?></div>
											<?}
											//SKU_ITEMS_PRICE_RANGES//
											if($arParams["USE_PRICE_COUNT"] && count($arOffer["ITEM_QUANTITY_RANGES"]) > 1) {?>
												<div class="product-item-detail-article-scu-item-ranges-container">
													<div class="product-item-detail-article-scu-item-ranges">
														<?foreach($arOffer["ITEM_QUANTITY_RANGES"] as $range) {
															if($range["HASH"] != "ZERO-INF") {
																$itemPrice = false;
																foreach($arOffer["ITEM_PRICES"] as $itemPrice) {
																	if($itemPrice["QUANTITY_HASH"] == $range["HASH"]) {
																		break;
																	}
																}
																if($itemPrice) {?>
																	<div class="product-item-detail-article-properties">
																		<div class="product-item-detail-article-properties-name">
																			<?if(is_infinite($range["SORT_TO"])) {
																				echo Loc::getMessage("CT_BCE_CATALOG_RANGE_FROM", array("#FROM#" => $range["SORT_FROM"]." ".$arOffer["ITEM_MEASURE"]["TITLE"]));
																			} else {
																				echo $range["SORT_FROM"].($range["SORT_TO"] != $range["SORT_FROM"] ? " - ".$range["SORT_TO"] : "")." ".$arOffer["ITEM_MEASURE"]["TITLE"];
																			}?>
																		</div>
																		<div class="product-item-detail-article-properties-val">
																			<?=($arParams["USE_RATIO_IN_RANGES"] == "Y" ? $itemPrice["PRINT_RATIO_PRICE"] : $itemPrice["PRINT_PRICE"])?>
																		</div>
																	</div>
																<?}
																unset($itemPrice);
															}
														}
														unset($range);?>
													</div>
												</div>
											<?}?>
										</div>
										<?//SKU_ITEMS_QUANTITY_LIMIT//
										if($arParams["SHOW_MAX_QUANTITY"] !== "N") {?>
											<div class="product-item-detail-article-quantity<?=($arOffer['CAN_BUY'] ? '' : ' product-item-detail-article-quantity-not-avl')?>">
												<i class="icon-<?=($arOffer['CAN_BUY'] ? 'ok' : 'close')?>-b product-item-detail-article-quantity-icon"></i>
												<span class="product-item-detail-article-quantity-val">
													<?if($arOffer["CAN_BUY"]) {
														echo $arParams["MESS_SHOW_MAX_QUANTITY"]."&nbsp;";
														if($offerMeasureRatio && (float)$arOffer["CATALOG_QUANTITY"] > 0 && $arOffer["CATALOG_QUANTITY_TRACE"] === "Y" && $arOffer["CATALOG_CAN_BUY_ZERO"] === "N") {
															if($arParams["SHOW_MAX_QUANTITY"] === "M") {
																if((float)$arOffer["CATALOG_QUANTITY"] / $offerMeasureRatio >= $arParams["RELATIVE_QUANTITY_FACTOR"]) {
																	echo $arParams["MESS_RELATIVE_QUANTITY_MANY"];
																} else {
																	echo $arParams["MESS_RELATIVE_QUANTITY_FEW"];
																}
															} else {
																echo $arOffer["CATALOG_QUANTITY"];
															}
														}
													} else {
														echo $arParams["MESS_NOT_AVAILABLE"];
													}?>
												</span>
											</div>
										<?}
										//SKU_ITEMS_OBJECTS_UPDATED//
										if($arParams["OFFERS_VIEW"] != "LIST" && !empty($arOffer["TIMESTAMP_X"])) {?>
											<div class="product-item-detail-article-scu-item-object-updated">
												<?=Loc::getMessage("CT_BCE_CATALOG_UPDATED")?>
												<span<?=(!empty($offerObject["PARSER_TAG"]) && !empty($offerObject["PARSER_CLASS"]) && !empty($arOffer["PROPERTIES"]["PARSER_LINK"]["VALUE"]) ? " data-entity='timestampX'" : "")?>><?=CIBlockFormatProperties::DateFormat("SHORT", MakeTimeStamp($arOffer["TIMESTAMP_X"], CSite::GetDateFormat()))?></span>
											</div>
										<?}?>
									</div>
									<?//SKU_ITEMS_QUANTITY//
									if($arParams["USE_PRODUCT_QUANTITY"] && (($arParams["OFFERS_VIEW"] == "LIST" && (!$object || ($object && $objectContacts))) || $arParams["OFFERS_VIEW"] != "LIST")) {?>
										<div class="<?=($arParams['OFFERS_VIEW'] != 'LIST' ? 'hidden-xs hidden-sm ' : '')?>product-item-detail-article-scu-item<?=($arParams['OFFERS_VIEW'] != 'LIST' ? '-object' : '')?>-col">												
											<?if(($arParams["OFFERS_VIEW"] == "LIST" || ($arParams["OFFERS_VIEW"] != "LIST" && $offerObjectContacts)) && $arOffer["CAN_BUY"] && $offerPrice["PRICE"] > 0) {
												if(!empty($arResult["PROPERTIES"]["M2_COUNT"]["VALUE"]) && ($arOffer["ITEM_MEASURE"]["SYMBOL_INTL"] == "pc. 1" || $arOffer["ITEM_MEASURE"]["SYMBOL_INTL"] == "m2")) {?>
													<div class="product-item-detail-article-amount">
														<a class="product-item-detail-article-amount-btn-minus" href="javascript:void(0)" rel="nofollow" data-entity="pc-quantity-down">-</a>
														<input class="product-item-detail-article-amount-input" type="tel" value="<?=$offerPrice['PC_MIN_QUANTITY']?>" data-entity="pc-quantity" />
														<a class="product-item-detail-article-amount-btn-plus" href="javascript:void(0)" rel="nofollow" data-entity="pc-quantity-up">+</a>
														<div class="product-item-detail-article-amount-measure"><?=Loc::getMessage("CT_BCE_CATALOG_MEASURE_PC")?></div>
													</div>
													<div class="product-item-detail-article-amount">
														<a class="product-item-detail-article-amount-btn-minus" href="javascript:void(0)" rel="nofollow" data-entity="sq-m-quantity-down">-</a>
														<input class="product-item-detail-article-amount-input" type="tel" value="<?=$offerPrice['SQ_M_MIN_QUANTITY']?>" data-entity="sq-m-quantity" />
														<a class="product-item-detail-article-amount-btn-plus" href="javascript:void(0)" rel="nofollow" data-entity="sq-m-quantity-up">+</a>
														<div class="product-item-detail-article-amount-measure"><?=Loc::getMessage("CT_BCE_CATALOG_MEASURE_SQ_M")?></div>
													</div>
												<?} else {?>
													<div class="product-item-detail-article-amount">								
														<a class="product-item-detail-article-amount-btn-minus" href="javascript:void(0)" rel="nofollow" data-entity="quantity-down">-</a>
														<input class="product-item-detail-article-amount-input" type="tel" value="<?=$offerPrice['MIN_QUANTITY']?>" data-entity="quantity" />
														<a class="product-item-detail-article-amount-btn-plus" href="javascript:void(0)" rel="nofollow" data-entity="quantity-up">+</a>
														<div class="product-item-detail-article-amount-measure"><?=$arOffer["ITEM_MEASURE"]["TITLE"]?></div>
													</div>
												<?}
											}?>
										</div>
									<?}?>
									<div class="product-item-detail-article-scu-item<?=($arParams['OFFERS_VIEW'] != 'LIST' ? '-object' : '')?>-col product-item-detail-article-scu-item<?=($arParams['OFFERS_VIEW'] != 'LIST' ? '-object' : '')?>-col-buttons">
										<?//SKU_ITEMS_BUTTONS//
										if($arOffer["CAN_BUY"]) {
											if($offerPrice["PRICE"] > 0) {
												if(!$arParams["DISABLE_BASKET"] && (($arParams["OFFERS_VIEW"] == "LIST" && !$partnersUrl && !$offerPartnersUrl) || ($arParams["OFFERS_VIEW"] != "LIST" && !$offerPartnersUrl))) {
													if(($arParams["OFFERS_VIEW"] == "LIST" && (!$object || ($object && $objectContacts))) || ($arParams["OFFERS_VIEW"] != "LIST" && $offerObjectContacts)) {
														if($showAddBtn) {?>
															<button type="button" class="btn btn-buy" data-entity="add"><i class="icon-cart"></i><span><?=$arParams["MESS_BTN_ADD_TO_BASKET"]?></span></button>
														<?}
														if($showBuyBtn) {?>
															<button type="button" class="btn btn-buy" data-entity="buy"><i class="icon-cart"></i><span><?=$arParams["MESS_BTN_BUY"]?></span></button>
														<?}
													}
													if(($arParams["OFFERS_VIEW"] == "LIST" && $object) || ($arParams["OFFERS_VIEW"] != "LIST" && $offerObject)) {?>
														<button type="button" class="btn btn-default" data-entity="object"><i class="icon-phone-call"></i><span><?=Loc::getMessage("CT_BCE_CATALOG_CONTACTS")?></span></button>
													<?}
												} elseif(($arParams["OFFERS_VIEW"] == "LIST" && ($partnersUrl || $offerPartnersUrl)) || ($arParams["OFFERS_VIEW"] != "LIST" && $offerPartnersUrl)) {?>
													<button type="button" class="btn btn-buy" data-entity="partner-link"><i class="icon-cart"></i><span><?=$arParams["MESS_BTN_BUY"]?></span></button>
													<?if(!empty($arSettings["PARTNERS_INFO_MESSAGE"])) {?>
														<div class="hidden-xs hidden-sm product-item-detail-article-info-message"><?=$arSettings["PARTNERS_INFO_MESSAGE"]?></div>
													<?}
												}
											} else {
												if(($arParams["OFFERS_VIEW"] == "LIST" || ($arParams["OFFERS_VIEW"] != "LIST" && $offerObject)) && $arParams["ASK_PRICE"]) {?>
													<button type="button" class="btn btn-default" data-entity="ask-price"><i class="icon-comment"></i><span><?=Loc::getMessage("CT_BCE_CATALOG_ASK_PRICE")?></span></button>
												<?}
											}
										} else {
											if($arParams["OFFERS_VIEW"] == "LIST" || ($arParams["OFFERS_VIEW"] != "LIST" && $offerObject)) {
												if($arParams["UNDER_ORDER"]) {?>
													<button type="button" class="btn btn-default" data-entity="not-available"><i class="icon-clock"></i><span><?=Loc::getMessage("CT_BCE_CATALOG_UNDER_ORDER")?></span></button>
												<?}
												if($arParams["PRODUCT_SUBSCRIPTION"] === "Y" && $arOffer["CATALOG_SUBSCRIBE"] === "Y") {?>
													<?$APPLICATION->IncludeComponent("bitrix:catalog.product.subscribe", "",
														array(
															"PRODUCT_ID" => $arOffer["ID"],
															"BUTTON_ID" => $itemIds["SUBSCRIBE_LINK"]."_".$this->GetEditAreaId($arOffer["ID"]),
															"BUTTON_CLASS" => "btn btn-default",
															"DEFAULT_DISPLAY" => true,
															"MESS_BTN_SUBSCRIBE" => $arParams["~MESS_BTN_SUBSCRIBE"]
														),
														$component,
														array("HIDE_ICONS" => "Y")
													);?>
												<?}
											}
										}?>
									</div>
									<?//SKU_ITEMS_DELAY//
									if(!$arParams["DISABLE_DELAY"] && $arParams["OFFERS_VIEW"] == "LIST" && (!$object || ($object && $objectContacts)) && !$partnersUrl && $numOffersPartnersUrl != count($arResult["OFFERS"])) {?>
										<div class="hidden-xs hidden-sm product-item-detail-article-scu-item-col product-item-detail-article-scu-item-col-delay">
											<?if(!$offerPartnersUrl && $arOffer["CAN_BUY"] && $offerPrice["PRICE"] > 0) {?>
												<div class="product-item-detail-article-delay" title="<?=$arParams['MESS_BTN_DELAY']?>" data-entity="delay"><i class="icon-heart"></i></div>
											<?}?>
										</div>
									<?}?>
								</div>
								<?unset($offerName, $offerTitle, $offerAlt, $offerPrice, $offerMeasureRatio, $offerArticle, $offerObject, $offerObjectContacts, $offerPartnersUrl);
							}
							unset($key, $arOffer, $numOffersPartnersUrl);?>
						</div>
					</div>
				<?}?>
			</div>
		</div>
		<div class="col-xs-12 col-md-3">
			<div class="product-item-detail-article-ghost-top"></div>
			<div class="product-item-detail-article-pay-block">				
				<?//PRICE//?>
				<div class="product-item-detail-article-info-container">					
					<div id="<?=$itemIds['PRICE_ID']?>">
						<?if($haveOffers && $arParams["OFFERS_VIEW"] != "PROPS" && $arParams["OFFERS_VIEW"] != "DROPDOWN_LIST") {
							if(($arParams["OFFERS_VIEW"] == "LIST" && $price["SQ_M_PRICE"] > 0) || $price["PRICE"] > 0) {?>
								<span class="product-item-detail-article-price-from"><?=Loc::getMessage("CT_BCE_CATALOG_PRICE_FROM")?></span>
								<span class="product-item-detail-article-price-current"><?=($arParams["OFFERS_VIEW"] == "LIST" && $price["SQ_M_PRICE"] > 0 ? $price["SQ_M_PRINT_PRICE"] : $price["PRINT_PRICE"])?></span>
								<?if($arParams["OFFERS_VIEW"] == "LIST") {?>
									<span class="product-item-detail-article-price-measure">/<?=($price["SQ_M_PRICE"] > 0 ? Loc::getMessage("CT_BCE_CATALOG_MEASURE_SQ_M") : $actualItem["ITEM_MEASURE"]["TITLE"])?></span>
								<?}
							} else {?>
								<span class="product-item-detail-price-not-set"><?=Loc::getMessage("CT_BCE_CATALOG_PRICE_NOT_SET")?></span>
							<?}
						} else {?>
							<span class="product-item-detail-article-price-not-set" data-entity="price-current-not-set"<?=($price["SQ_M_PRICE"] > 0 ? " style='display:none;'" : ($price["PRICE"] > 0 ? " style='display:none;'" : ""))?>><?=Loc::getMessage("CT_BCE_CATALOG_PRICE_NOT_SET")?></span>
							<span class="product-item-detail-article-price-current" data-entity="price-current"<?=($price["SQ_M_PRICE"] > 0 ? "" : ($price["PRICE"] > 0 ? "" : " style='display:none;'"))?>><?=($price["SQ_M_PRICE"] > 0 ? $price["SQ_M_PRINT_PRICE"] : $price["PRINT_PRICE"])?></span>
							<span class="product-item-detail-article-price-measure" data-entity="price-measure"<?=($price["SQ_M_PRICE"] > 0 ? "" : ($price["PRICE"] > 0 ? "" : " style='display:none;'"))?>>/<?=($price["SQ_M_PRICE"] > 0 ? Loc::getMessage("CT_BCE_CATALOG_MEASURE_SQ_M") : $actualItem["ITEM_MEASURE"]["TITLE"])?></span>
						<?}?>
					</div>
					<?if($arParams["SHOW_OLD_PRICE"] === "Y") {?>
						<div class="product-item-detail-article-price-old" id="<?=$itemIds['OLD_PRICE_ID']?>"<?=($showDiscount ? "" : " style='display:none;'")?>><?=($showDiscount ? ($price["SQ_M_BASE_PRICE"] > 0 ? $price["SQ_M_PRINT_BASE_PRICE"] : $price["PRINT_BASE_PRICE"]) : "")?></div>
						<div class="product-item-detail-article-price-economy" id="<?=$itemIds['DISCOUNT_PRICE_ID']?>"<?=($showDiscount ? "" : " style='display:none;'")?>><?=($showDiscount ? Loc::getMessage("CT_BCE_CATALOG_ECONOMY_INFO2", array("#ECONOMY#" => ($price["SQ_M_DISCOUNT"] > 0 ? $price["SQ_M_PRINT_DISCOUNT"] : $price["PRINT_DISCOUNT"]))) : "")?></div>
					<?}?>
				</div>
				<?//QUANTITY_LIMIT
				if($arParams["SHOW_MAX_QUANTITY"] !== "N"){
					if($haveOffers) {
						if($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST") {?>
							<div class="product-item-detail-article-info-container" id="<?=$itemIds['QUANTITY_LIMIT']?>" style="display: none;">
								<div class="product-item-detail-article-quantity">
									<i class="icon-ok-b product-item-detail-article-quantity-icon"></i>
									<span class="product-item-detail-article-quantity-val">
										<?=$arParams["MESS_SHOW_MAX_QUANTITY"]."&nbsp;"?>
										<span data-entity="quantity-limit-value"></span>
									</span>
								</div>
							</div>
							<div class="product-item-detail-article-info-container" id="<?=$itemIds['QUANTITY_LIMIT_NOT_AVAILABLE']?>" style="display: none;">
								<div class="product-item-detail-article-quantity product-item-detail-article-quantity-not-avl">
									<i class="icon-close-b product-item-detail-article-quantity-icon"></i>
									<span class="product-item-detail-article-quantity-val"><?=$arParams["MESS_NOT_AVAILABLE"]?></span>
								</div>
							</div>
						<?} else {?>
							<div class="product-item-detail-article-info-container">
								<div class="product-item-detail-article-quantity<?=($arResult['CATALOG_QUANTITY_TRACE'] === 'N' || $arResult['CATALOG_CAN_BUY_ZERO'] === 'Y' || $arResult['OFFERS_QUANTITY'] > 0 ? '' : ' product-item-detail-article-quantity-not-avl')?>">
									<i class="icon-<?=($arResult['CATALOG_QUANTITY_TRACE'] === 'N' || $arResult['CATALOG_CAN_BUY_ZERO'] === 'Y' || $arResult['OFFERS_QUANTITY'] > 0 ? 'ok' : 'close')?>-b product-item-detail-article-quantity-icon"></i>
									<span class="product-item-detail-article-quantity-val">
										<?if($arResult["CATALOG_QUANTITY_TRACE"] === "N" || $arResult["CATALOG_CAN_BUY_ZERO"] === "Y" || $arResult["OFFERS_QUANTITY"] > 0) {
											echo $arParams["MESS_SHOW_MAX_QUANTITY"]."&nbsp;";
											if($arResult["CATALOG_QUANTITY_TRACE"] === "Y" && $arResult["CATALOG_CAN_BUY_ZERO"] === "N") {
												if($arParams["SHOW_MAX_QUANTITY"] === "M") {
													if($arResult["OFFERS_QUANTITY"] >= $arParams["RELATIVE_QUANTITY_FACTOR"]) {
														echo $arParams["MESS_RELATIVE_QUANTITY_MANY"];
													} else {
														echo $arParams["MESS_RELATIVE_QUANTITY_FEW"];
													}
												} else {
													echo $arResult["OFFERS_QUANTITY"];
												}
											}
										} else {
											echo $arParams["MESS_NOT_AVAILABLE"];
										}?>
									</span>
								</div>
							</div>
						<?}
					} else {?>
						<div class="product-item-detail-article-info-container" id="<?=$itemIds['QUANTITY_LIMIT']?>">
							<div class="product-item-detail-article-quantity<?=($actualItem['CAN_BUY'] ? '' : ' product-item-detail-article-quantity-not-avl')?>">
								<i class="icon-<?=($actualItem['CAN_BUY'] ? 'ok' : 'close')?>-b product-item-detail-article-quantity-icon"></i>
								<span class="product-item-detail-article-quantity-val">
									<?if($actualItem["CAN_BUY"]) {
										echo $arParams["MESS_SHOW_MAX_QUANTITY"]."&nbsp;";
										if($measureRatio && (float)$actualItem["CATALOG_QUANTITY"] > 0 && $actualItem["CATALOG_QUANTITY_TRACE"] === "Y" && $actualItem["CATALOG_CAN_BUY_ZERO"] === "N") {
											if($arParams["SHOW_MAX_QUANTITY"] === "M") {
												if((float)$actualItem["CATALOG_QUANTITY"] / $measureRatio >= $arParams["RELATIVE_QUANTITY_FACTOR"]) {
													echo $arParams["MESS_RELATIVE_QUANTITY_MANY"];
												} else {
													echo $arParams["MESS_RELATIVE_QUANTITY_FEW"];
												}
											} else {
												echo $actualItem["CATALOG_QUANTITY"];
											}
										}
									} else {
										echo $arParams["MESS_NOT_AVAILABLE"];
									}?>
								</span>
							</div>
						</div>
					<?}
				}
				//PRICE_RANGES//
				if($arParams["USE_PRICE_COUNT"] && (!$haveOffers || ($haveOffers && ($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST")))) {
					$showRanges = !$haveOffers && count($actualItem["ITEM_QUANTITY_RANGES"]) > 1;
					$useRatio = $arParams["USE_RATIO_IN_RANGES"] === "Y";?>
					<div class="product-item-detail-article-info-container"<?=($showRanges ? "" : " style='display: none;'");?> data-entity="price-ranges-block">
						<div class="product-item-detail-article-properties-block" data-entity="price-ranges-body">
							<?if($showRanges) {
								foreach($actualItem["ITEM_QUANTITY_RANGES"] as $range) {
									if($range["HASH"] !== "ZERO-INF") {
										$itemPrice = false;
										foreach($arResult["ITEM_PRICES"] as $itemPrice) {
											if($itemPrice["QUANTITY_HASH"] === $range["HASH"]){
												break;
											}
										}
										if($itemPrice) {?>
											<div class="product-item-detail-article-properties">
												<div class="product-item-detail-article-properties-name">													
													<?if(is_infinite($range["SORT_TO"])) {
														echo Loc::getMessage("CT_BCE_CATALOG_RANGE_FROM", array("#FROM#" => $range["SORT_FROM"]." ".$actualItem["ITEM_MEASURE"]["TITLE"]));
													} else {
														echo $range["SORT_FROM"].($range["SORT_TO"] != $range["SORT_FROM"] ? " - ".$range["SORT_TO"] : "")." ".$actualItem["ITEM_MEASURE"]["TITLE"];
													}?>
												</div>
												<div class="product-item-detail-article-properties-val">
													<?=($useRatio ? $itemPrice["PRINT_RATIO_PRICE"] : $itemPrice["PRINT_PRICE"])?>
												</div>
											</div>
										<?}
										unset($itemPrice);
									}
								}
								unset($range);
							}?>
						</div>
					</div>
					<?unset($showRanges, $useRatio);
				}
				//SKU//
				if($haveOffers && !empty($arResult["OFFERS_PROP"])) {
					if($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST") {?>
						<div class="product-item-detail-article-scu-container" id="<?=$itemIds['TREE_ID']?>">
					<?}
					foreach($arResult["SKU_PROPS"] as $skuProperty) {
						if(!isset($arResult["OFFERS_PROP"][$skuProperty["CODE"]]))
							continue;
						$propertyId = $skuProperty["ID"];
						$skuProps[] = array(
							"ID" => $propertyId,
							"SHOW_MODE" => $skuProperty["SHOW_MODE"],
							"VALUES" => $skuProperty["VALUES"],
							"VALUES_COUNT" => $skuProperty["VALUES_COUNT"]
						);
						if($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST") {?>
							<div class="product-item-detail-article-info-container" data-entity="sku-line-block">
								<div class="product-item-detail-article-scu-title"><?=htmlspecialcharsEx($skuProperty["NAME"]).($arParams["OFFERS_VIEW"] == "PROPS" && $skuProperty["SHOW_MODE"] === "PICT" ? "<span data-entity='current-option'></span>" : "")?></div>
								<?if($arParams["OFFERS_VIEW"] == "PROPS") {?>
									<div class="product-item-detail-article-scu-block">
										<div class="product-item-detail-article-scu-list">
											<ul class="product-item-detail-article-scu-item-list">
												<?foreach($skuProperty["VALUES"] as &$value) {
													$value["NAME"] = htmlspecialcharsbx($value["NAME"]);
													if($skuProperty["SHOW_MODE"] === "PICT") {?>
														<li class="product-item-detail-article-scu-item-color" title="<?=$value['NAME']?>" data-treevalue="<?=$propertyId?>_<?=$value['ID']?>" data-onevalue="<?=$value['ID']?>" style="<?=(!empty($value['CODE']) ? 'background-color: #'.$value['CODE'].';' : (!empty($value['PICT']) ? 'background-image: url('.$value['PICT']['SRC'].');' : ''));?>"></li>
													<?} else {?>
														<li class="product-item-detail-article-scu-item-text" title="<?=$value['NAME']?>" data-treevalue="<?=$propertyId?>_<?=$value['ID']?>" data-onevalue="<?=$value['ID']?>">
															<?=$value["NAME"]?>
														</li>
													<?}
												}
												unset($value);?>
											</ul>											
										</div>
									</div>
								<?} else {?>
									<div class="product-item-detail-basket-props-block">
										<div class="product-item-detail-basket-props-drop-down" onclick="<?=$obName?>.showOfferBasketPropsDropDownPopup(this, '<?=$propertyId?>');">
											<div class="drop-down-text" data-entity="current-option">-</div>
											<div class="drop-down-arrow"><i class="icon-arrow-down"></i></div>
											<div class="drop-down-popup" data-entity="dropdownContent" style="display: none;">
												<ul>
													<?foreach($skuProperty["VALUES"] as $value) {?>
														<li data-treevalue="<?=$propertyId?>_<?=$value['ID']?>" data-onevalue="<?=$value['ID']?>" onclick="<?=$obName?>.selectOfferBasketPropsDropDownPopupItem(this);"><span><?=$value["NAME"]?></span></li>
													<?}
													unset($value);?>
												</ul>
											</div>
										</div>
									</div>
								<?}?>
							</div>
						<?}
					}
					unset($skuProperty);
					if($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST") {?>
						</div>
					<?}
				}
				//BASKET_PROPERTIES//
				if(!$haveOffers) {
					$emptyProductProperties = empty($arResult["PRODUCT_PROPERTIES"]);					
					if($arParams["ADD_PROPERTIES_TO_BASKET"] === "Y" && !$emptyProductProperties) {?>
						<div class="product-item-detail-article-info-container" id="<?=$itemIds['BASKET_PROP_DIV']?>">
							<?if(!empty($arResult["PRODUCT_PROPERTIES_FILL"])) {
								foreach($arResult["PRODUCT_PROPERTIES_FILL"] as $propId => $propInfo) {?>
									<input type="hidden" name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$propId?>]" value="<?=htmlspecialcharsbx($propInfo['ID'])?>" />
									<?unset($arResult["PRODUCT_PROPERTIES"][$propId]);
								}
								unset($propId, $propInfo);
							}
							$emptyProductProperties = empty($arResult["PRODUCT_PROPERTIES"]);
							if(!$emptyProductProperties) {
								foreach($arResult["PRODUCT_PROPERTIES"] as $propId => $propInfo) {?>
									<div class="product-item-detail-article-basket-props-container">
										<div class="product-item-detail-article-basket-props-title"><?=$arResult["PROPERTIES"][$propId]["NAME"]?></div>
										<div class="product-item-detail-article-basket-props-block">
											<?if($arResult["PROPERTIES"][$propId]["PROPERTY_TYPE"] === "L" && $arResult["PROPERTIES"][$propId]["LIST_TYPE"] === "C") {?>
												<div class="product-item-detail-article-basket-props-input-radio">
													<?foreach($propInfo["VALUES"] as $valueId => $value) {?>
														<label>
															<input type="radio" name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$propId?>]" value="<?=$valueId?>"<?=($valueId == $propInfo["SELECTED"] ? " checked='checked'" : "");?> />
															<span class="check-container">
																<span class="check"><i class="icon-ok-b"></i></span>
															</span>
															<span class="text" title="<?=$value?>"><?=$value?></span>
														</label>
													<?}
													unset($valueId, $value);?>
												</div>
											<?} else {?>
												<div class="product-item-detail-article-basket-props-drop-down" onclick="<?=$obName?>.showBasketPropsDropDownPopup(this, '<?=$propId?>');">
													<?$currId = $currVal = false;
													foreach($propInfo["VALUES"] as $valueId => $value) {
														if($valueId == $propInfo["SELECTED"]) {
															$currId = $valueId;
															$currVal = $value;
														}
													}
													unset($valueId, $value);?>
													<input type="hidden" name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$propId?>]" value="<?=(!empty($currId) ? $currId : '');?>" />
													<div class="drop-down-text" data-entity="current-option"><?=(!empty($currVal) ? $currVal : "");?></div>
													<?unset($currVal, $currId);?>
													<div class="drop-down-arrow"><i class="icon-arrow-down"></i></div>
													<div class="drop-down-popup" data-entity="dropdownContent" style="display: none;">
														<ul>
															<?foreach($propInfo["VALUES"] as $valueId => $value) {?>
																<li><span onclick="<?=$obName?>.selectBasketPropsDropDownPopupItem(this, '<?=$valueId?>');"><?=$value?></span></li>
															<?}
															unset($valueId, $value);?>
														</ul>
													</div>
												</div>
											<?}?>
										</div>
									</div>
								<?}
								unset($propId, $propInfo);
							}?>
						</div>
					<?}
					unset($emptyProductProperties);
				}
				//QUANTITY//
				if($arParams["USE_PRODUCT_QUANTITY"] && (!$object || ($object && $objectContacts)) && (!$haveOffers || ($haveOffers && ($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST")))) {?>
					<div class="product-item-detail-article-info-container" style="<?=(!$actualItem['CAN_BUY'] ? 'display: none;' : '')?>" data-entity="quantity-block">
						<?if(!empty($arResult["PROPERTIES"]["M2_COUNT"]["VALUE"])) {?>
							<div class="product-item-detail-article-amount"<?=($isMeasurePc || $isMeasureSqM ? "" : " style='display: none;'")?>>
								<a class="product-item-detail-article-amount-btn-minus" id="<?=$itemIds['PC_QUANTITY_DOWN_ID']?>" href="javascript:void(0)" rel="nofollow">-</a>
								<input class="product-item-detail-article-amount-input" id="<?=$itemIds['PC_QUANTITY_ID']?>" type="tel" value="<?=$price['PC_MIN_QUANTITY']?>" />
								<a class="product-item-detail-article-amount-btn-plus" id="<?=$itemIds['PC_QUANTITY_UP_ID']?>" href="javascript:void(0)" rel="nofollow">+</a>
								<div class="product-item-detail-article-amount-measure"><?=Loc::getMessage("CT_BCE_CATALOG_MEASURE_PC")?></div>
							</div>
							<div class="product-item-detail-article-amount"<?=($isMeasurePc || $isMeasureSqM ? "" : " style='display: none;'")?>>
								<a class="product-item-detail-article-amount-btn-minus" id="<?=$itemIds['SQ_M_QUANTITY_DOWN_ID']?>" href="javascript:void(0)" rel="nofollow">-</a>
								<input class="product-item-detail-article-amount-input" id="<?=$itemIds['SQ_M_QUANTITY_ID']?>" type="tel" value="<?=$price['SQ_M_MIN_QUANTITY']?>" />
								<a class="product-item-detail-article-amount-btn-plus" id="<?=$itemIds['SQ_M_QUANTITY_UP_ID']?>" href="javascript:void(0)" rel="nofollow">+</a>
								<div class="product-item-detail-article-amount-measure"><?=Loc::getMessage("CT_BCE_CATALOG_MEASURE_SQ_M")?></div>
							</div>
							<?if($haveOffers) {?>
								<div class="product-item-detail-article-amount"<?=($isMeasurePc || $isMeasureSqM ? " style='display: none;'" : "")?>>
									<a class="product-item-detail-article-amount-btn-minus" id="<?=$itemIds['QUANTITY_DOWN_ID']?>" href="javascript:void(0)" rel="nofollow">-</a>
									<input class="product-item-detail-article-amount-input" id="<?=$itemIds['QUANTITY_ID']?>" type="tel" value="<?=$price['MIN_QUANTITY']?>" />
									<a class="product-item-detail-article-amount-btn-plus" id="<?=$itemIds['QUANTITY_UP_ID']?>" href="javascript:void(0)" rel="nofollow">+</a>
									<div class="product-item-detail-article-amount-measure" id="<?=$itemIds['QUANTITY_MEASURE']?>"><?=$actualItem["ITEM_MEASURE"]["TITLE"]?></div>
								</div>
							<?}?>
							<div class="product-item-detail-article-total-cost" id="<?=$itemIds['TOTAL_COST_ID']?>"<?=($price["MIN_QUANTITY"] != 1 || $price["PC_MIN_QUANTITY"] != 1 || $price["SQ_M_MIN_QUANTITY"] != 1 ? "" : " style='display:none;'")?>><?=Loc::getMessage("CT_BCE_CATALOG_TOTAL_COST")?><span data-entity="total-cost"><?=($price["MIN_QUANTITY"] != 1 || $price["PC_MIN_QUANTITY"] != 1 || $price["SQ_M_MIN_QUANTITY"] != 1 ? $price["PRINT_RATIO_PRICE"] : "")?></span></div>
						<?} else {?>
							<div class="product-item-detail-article-amount">								
								<a class="product-item-detail-article-amount-btn-minus" id="<?=$itemIds['QUANTITY_DOWN_ID']?>" href="javascript:void(0)" rel="nofollow">-</a>
								<input class="product-item-detail-article-amount-input" id="<?=$itemIds['QUANTITY_ID']?>" type="tel" value="<?=$price['MIN_QUANTITY']?>" />
								<a class="product-item-detail-article-amount-btn-plus" id="<?=$itemIds['QUANTITY_UP_ID']?>" href="javascript:void(0)" rel="nofollow">+</a>
								<div class="product-item-detail-article-amount-measure" id="<?=$itemIds['QUANTITY_MEASURE']?>"><?=$actualItem["ITEM_MEASURE"]["TITLE"]?></div>
							</div>
							<div class="product-item-detail-article-total-cost" id="<?=$itemIds['TOTAL_COST_ID']?>"<?=($price["MIN_QUANTITY"] != 1 ? "" : " style='display:none;'")?>><?=Loc::getMessage("CT_BCE_CATALOG_TOTAL_COST")?><span data-entity="total-cost"><?=($price["MIN_QUANTITY"] != 1 ? $price["PRINT_RATIO_PRICE"] : "")?></span></div>
						<?}?>
					</div>
				<?}
				//BUTTONS//?>
				<div class="product-item-detail-article-button-container" data-entity="main-button-container">
					<?if($haveOffers && $arParams["OFFERS_VIEW"] != "PROPS" && $arParams["OFFERS_VIEW"] != "DROPDOWN_LIST") {
						//SELECT_SKU//?>
						<button type="button" class="btn btn-default" id="<?=$itemIds['SELECT_SKU_LINK']?>"><span><?=Loc::getMessage("CT_BCE_CATALOG_SELECT_SKU_".$arParams["OFFERS_VIEW"])?></span></button>
						<?//LIST_URL//
						if(!empty($arResult["PROPERTIES"]["LIST_URL"]["VALUE"])) {
							foreach($arResult["PROPERTIES"]["LIST_URL"]["VALUE"] as $key => $val) {?>
								<a rel="nofollow" target="_blank" class="btn btn-default" href="<?=$val?>" role="button"><?=(!empty($arResult["PROPERTIES"]["LIST_URL"]["DESCRIPTION"][$key]) ? $arResult["PROPERTIES"]["LIST_URL"]["DESCRIPTION"][$key] : "")?></a>
							<?}
							unset($key, $val);
						}
						//BUY_INFO_MESSAGE//
						if(!empty($arSettings["BUY_INFO_MESSAGE"])) {?>
							<div class="product-item-detail-article-info-message"><?=$arSettings["BUY_INFO_MESSAGE"]?></div>
						<?}
					} else {
						//BUY//
						if(!$arParams["DISABLE_BASKET"] && (!$object || ($object && $objectContacts))) {?>
							<div id="<?=$itemIds['BASKET_ACTIONS_ID']?>">
								<?if($showAddBtn) {?>
									<button type="button" class="btn btn-buy" id="<?=$itemIds['ADD_BASKET_LINK']?>" style="display: <?=(!$partnersUrl ? '' : 'none')?>;"<?=($actualItem["CAN_BUY"] && $price["PRICE"] > 0 ? "" : " disabled='disabled'")?>><i class="icon-cart"></i><span><?=$arParams["MESS_BTN_ADD_TO_BASKET"]?></span></button>
								<?}
								if($showBuyBtn) {?>
									<button type="button" class="btn btn-buy" id="<?=$itemIds['BUY_LINK']?>" style="display: <?=(!$partnersUrl ? '' : 'none')?>;"<?=($actualItem["CAN_BUY"] && $price["PRICE"] > 0 ? "" : " disabled='disabled'")?>><i class="icon-cart"></i><span><?=$arParams["MESS_BTN_BUY"]?></span></button>
								<?}?>
							</div>
						<?}
						//PARTNERS_LINK//?>
						<div id="<?=$itemIds['PARTNERS_ID']?>">
							<button type="button" class="btn btn-buy" id="<?=$itemIds['PARTNERS_LINK']?>" style="display: <?=($partnersUrl ? '' : 'none')?>;"<?=($actualItem["CAN_BUY"] && $price["PRICE"] > 0 ? "" : " disabled='disabled'")?>><i class="icon-cart"></i><span><?=$arParams["MESS_BTN_BUY"]?></span></button>
							<?if(!empty($arSettings["PARTNERS_INFO_MESSAGE"])) {?>
								<div class="product-item-detail-article-info-message" style="display: <?=($partnersUrl ? '' : 'none')?>;" data-entity="partners-message"><?=$arSettings["PARTNERS_INFO_MESSAGE"]?></div>
							<?}?>
						</div>
						<?//LIST_URL//
						if(!empty($arResult["PROPERTIES"]["LIST_URL"]["VALUE"])) {
							foreach($arResult["PROPERTIES"]["LIST_URL"]["VALUE"] as $key => $val) {?>
								<a rel="nofollow" target="_blank" class="btn btn-default" href="<?=$val?>" role="button"><?=(!empty($arResult["PROPERTIES"]["LIST_URL"]["DESCRIPTION"][$key]) ? $arResult["PROPERTIES"]["LIST_URL"]["DESCRIPTION"][$key] : "")?></a>
							<?}
							unset($key, $val);
						}
						//BUY_INFO_MESSAGE//
						if(!empty($arSettings["BUY_INFO_MESSAGE"])) {?>
							<div class="product-item-detail-article-info-message"><?=$arSettings["BUY_INFO_MESSAGE"]?></div>
						<?}
						//ASK_PRICE//
						if($arParams["ASK_PRICE"]) {?>
							<button type="button" class="btn btn-default" id="<?=$itemIds['ASK_PRICE_LINK']?>" style="display: <?=($actualItem['CAN_BUY'] && $price['PRICE'] <= 0 ? '' : 'none')?>;"><i class="icon-comment"></i><span><?=Loc::getMessage("CT_BCE_CATALOG_ASK_PRICE")?></span></button>
						<?}
						//UNDER_ORDER//
						if($arParams["UNDER_ORDER"]) {?>
							<button type="button" class="btn btn-default" id="<?=$itemIds['NOT_AVAILABLE_MESS']?>" style="display: <?=(!$actualItem['CAN_BUY'] ? '' : 'none')?>;"><i class="icon-clock"></i><span><?=Loc::getMessage("CT_BCE_CATALOG_UNDER_ORDER")?></span></button>
						<?}
						//SUBSCRIBE//
						if($showSubscribe) {?>
							<?$APPLICATION->IncludeComponent("bitrix:catalog.product.subscribe", "",
								array(
									"PRODUCT_ID" => $actualItem["ID"],
									"BUTTON_ID" => $itemIds["SUBSCRIBE_LINK"],
									"BUTTON_CLASS" => "btn btn-default",
									"DEFAULT_DISPLAY" => !$actualItem["CAN_BUY"],
									"MESS_BTN_SUBSCRIBE" => $arParams["~MESS_BTN_SUBSCRIBE"]
								),
								$component,
								array("HIDE_ICONS" => "Y")
							);?>
						<?}
					}?>
				</div>
				<?//COMPARE//
				if($arParams["DISPLAY_COMPARE"] && (!$haveOffers || ($haveOffers && ($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST")))) {?>
					<div class="product-item-detail-article-compare">
						<label id="<?=$itemIds['COMPARE_LINK']?>">
							<input type="checkbox" data-entity="compare-checkbox">
							<span class="product-item-detail-article-compare-checkbox"><i class="icon-ok-b"></i></span>
							<span class="product-item-detail-article-compare-title" data-entity="compare-title"><?=$arParams["MESS_BTN_COMPARE"]?></span>
						</label>
					</div>
				<?}
				//PROPERTY_DELIVERY//
				$propDelivery = $arResult["PROPERTIES"]["DELIVERY"];
				if($propDelivery && !empty($propDelivery["VALUE"])) {?>
					<div class="product-item-detail-article-delivery">
						<div class="product-item-detail-article-delivery-name"><?=$propDelivery["NAME"]?></div> 
						<div class="product-item-detail-article-delivery-val"><?=$propDelivery["VALUE"]?></div>
					</div>
				<?}
				//QUICK_ORDER//
				if(isset($arParams["POPUP_MODE"]) && $arParams["POPUP_MODE"] == "Y") {
					if($arParams["QUICK_ORDER"] && (!$object || ($object && $objectContacts)) && (!$haveOffers || ($haveOffers && ($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST")))) {
						$quickOrderQuantityId = $itemIds["QUANTITY_ID"];
						if(!$haveOffers && !empty($arResult["PROPERTIES"]["M2_COUNT"]["VALUE"])) {
							if($isMeasurePc)
								$quickOrderQuantityId = $itemIds["PC_QUANTITY_ID"];
							elseif($isMeasureSqM)
								$quickOrderQuantityId = $itemIds["SQ_M_QUANTITY_ID"];
						}?>
						<?$APPLICATION->IncludeComponent("altop:quick.order.enext", "",
							array(
								"MODE" => "PRODUCT",
								"PRODUCT_ID" => $actualItem["ID"],							
								"CONTAINER_ID" => $itemIds["QUICK_ORDER_LINK"],
								"CONTAINER_CLASS" => "product-item-detail-article-quick-order".($minOrderSum > 0 && $price["RATIO_PRICE"] < $minOrderSum ? " product-item-detail-article-quick-order-hidden" : ""),
								"DEFAULT_DISPLAY" => !$partnersUrl && $actualItem["CAN_BUY"] && $price["PRICE"] > 0,
								"QUANTITY_ID" => $quickOrderQuantityId,
								"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
								"PARTIAL_PRODUCT_PROPERTIES" => $arParams["PARTIAL_PRODUCT_PROPERTIES"],
								"CART_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
								"BASKET_PROPS_ID" => $itemIds["BASKET_PROP_DIV"],
								"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
								"BASKET_SKU_PROPS" => $arResult["OFFERS_PROP_CODES"],
								"OBJECT_ID" => $object ? $object["ID"] : "" 
							),
							$component,
							array("HIDE_ICONS" => "Y")
						);?>
						<?unset($quickOrderQuantityId);
					}
				}?>
			</div>
			<?//OBJECT//
			if($arParams["OFFERS_VIEW"] != "OBJECTS" && $object) {?>
				<div class="product-item-detail-article-object-container">
					<a target="_blank" class="product-item-detail-article-object" href="<?=$object['DETAIL_PAGE_URL']?>">
						<span class="product-item-detail-article-object-image">
							<?if(is_array($object["PREVIEW_PICTURE"])) {?>									
								<img src="<?=$object['PREVIEW_PICTURE']['SRC']?>" width="<?=$object['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$object['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$object['NAME']?>" />
							<?} else {?>
								<img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo.png" width="222" height="222" alt="<?=$object['NAME']?>" title="<?=$object['NAME']?>" />
							<?}?>
						</span>
						<span class="product-item-detail-article-object-text"><?=$object["NAME"]?></span>
					</a>
					<div class="product-item-detail-article-object-contacts">
						<button type="button" class="product-item-detail-article-object-btn"><i class="icon-phone-call"></i></button>
					</div>
				</div>
			<?}?>
		</div>
	</div>
	<?//PREDICTION//
	if($arResult["CATALOG"] && (!$object || ($object && $objectContacts)) && (!$haveOffers || ($haveOffers && ($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST"))) && $actualItem["CAN_BUY"] && Bitrix\Main\ModuleManager::isModuleInstalled("sale")) {?>
		<?$APPLICATION->IncludeComponent("bitrix:sale.prediction.product.detail", ".default",
			array(
				"BUTTON_ID" => !$partnersUrl ? ($showBuyBtn ? $itemIds["BUY_LINK"] : $itemIds["ADD_BASKET_LINK"]) : $itemIds["PARTNERS_LINK"],
				"CUSTOM_SITE_ID" => isset($arParams["CUSTOM_SITE_ID"]) ? $arParams["CUSTOM_SITE_ID"] : null,
				"POTENTIAL_PRODUCT_TO_BUY" => array(
					"ID" => isset($arResult["ID"]) ? $arResult["ID"] : null,
					"MODULE" => isset($arResult["MODULE"]) ? $arResult["MODULE"] : "catalog",
					"PRODUCT_PROVIDER_CLASS" => isset($arResult["PRODUCT_PROVIDER_CLASS"]) ? $arResult["PRODUCT_PROVIDER_CLASS"] : "CCatalogProductProvider",
					"QUANTITY" => isset($arResult["QUANTITY"]) ? $arResult["QUANTITY"] : null,
					"IBLOCK_ID" => isset($arResult["IBLOCK_ID"]) ? $arResult["IBLOCK_ID"] : null,
					"PRIMARY_OFFER_ID" => isset($arResult["OFFERS"][0]["ID"]) ? $arResult["OFFERS"][0]["ID"] : null,
					"SECTION" => array(
						"ID" => isset($arResult["SECTION"]["ID"]) ? $arResult["SECTION"]["ID"] : null,
						"IBLOCK_ID" => isset($arResult["SECTION"]["IBLOCK_ID"]) ? $arResult["SECTION"]["IBLOCK_ID"] : null,
						"LEFT_MARGIN" => isset($arResult["SECTION"]["LEFT_MARGIN"]) ? $arResult["SECTION"]["LEFT_MARGIN"] : null,
						"RIGHT_MARGIN" => isset($arResult["SECTION"]["RIGHT_MARGIN"]) ? $arResult["SECTION"]["RIGHT_MARGIN"] : null,
					)
				)
			),
			$component,
			array("HIDE_ICONS" => "Y")
		);?>
	<?}?>
</div>

<?if(isset($arParams["REINIT_ADD_BUY_URL_TEMPLATE"]) && $arParams["REINIT_ADD_BUY_URL_TEMPLATE"] == "Y") {
	$addUrlTemplate = $arResult["DETAIL_PAGE_URL"]."?".$arParams["ACTION_VARIABLE"]."=ADD2BASKET&".$arParams["PRODUCT_ID_VARIABLE"]."=#ID#";
	$buyUrlTemplate = $arResult["DETAIL_PAGE_URL"]."?".$arParams["ACTION_VARIABLE"]."=BUY&".$arParams["PRODUCT_ID_VARIABLE"]."=#ID#";
} else {
	$addUrlTemplate = $arResult["~ADD_URL_TEMPLATE"];
	$buyUrlTemplate = $arResult["~BUY_URL_TEMPLATE"];
}

if($haveOffers) {
	$useRatio = $arParams["USE_RATIO_IN_RANGES"] === "Y";

	foreach($arResult["JS_OFFERS"] as $ind => &$jsOffer) {
		$fullOffer = $arResult["OFFERS"][$ind];
		$measureName = $fullOffer["ITEM_MEASURE"]["TITLE"];

		$strAllProps = "";
		$strMainProps = "";
		$strPriceRangesRatio = "";
		$strPriceRanges = "";

		if(($arParams["OFFERS_VIEW"] == "PROPS" || $arParams["OFFERS_VIEW"] == "DROPDOWN_LIST") && $arResult["SHOW_OFFERS_PROPS"]) {
			if(!empty($jsOffer["DISPLAY_PROPERTIES"])) {
				foreach($jsOffer["DISPLAY_PROPERTIES"] as $property) {
					$current = "
						<div class='product-item-detail-article-properties' data-entity='sku-props'>
							<div class='product-item-detail-article-properties-name'>".$property["NAME"]."</div>
							<div class='product-item-detail-article-properties-val'>".(is_array($property["VALUE"]) ? implode(" / ", $property["VALUE"]) : $property["VALUE"])."</div>
						</div>
					";
					$strAllProps .= $current;
					if(isset($arParams["MAIN_BLOCK_OFFERS_PROPERTY_CODE"][$property["CODE"]])) {
						$strMainProps .= $current;
					}
				}
				unset($current, $property);
			}
		}

		if($arParams["USE_PRICE_COUNT"] && count($jsOffer["ITEM_QUANTITY_RANGES"]) > 1) {
			foreach($jsOffer["ITEM_QUANTITY_RANGES"] as $range) {
				if($range["HASH"] !== "ZERO-INF") {
					$itemPrice = false;
					foreach($jsOffer["ITEM_PRICES"] as $itemPrice) {
						if($itemPrice["QUANTITY_HASH"] === $range["HASH"]) {
							break;
						}
					}
					if($itemPrice) {
						$strPriceRanges .= "<div class='product-item-detail-article-properties'><div class='product-item-detail-article-properties-name'>";
						if(is_infinite($range["SORT_TO"])) {
							$strPriceRanges .= Loc::getMessage("CT_BCE_CATALOG_RANGE_FROM", array("#FROM#" => $range["SORT_FROM"]." ".$measureName));
						} else {
							$strPriceRanges .= $range["SORT_FROM"].($range["SORT_TO"] != $range["SORT_FROM"] ? " - ".$range["SORT_TO"] : "")." ".$measureName;
						}
						$strPriceRanges .= "</div><div class='product-item-detail-article-properties-val'>".($useRatio ? $itemPrice["PRINT_RATIO_PRICE"] : $itemPrice["PRINT_PRICE"])."</div></div>";
					}
					unset($itemPrice);
				}
			}
			unset($range);
		}
		
		$jsOffer["ARTICLE"] = !empty($arResult["OFFERS"][$ind]["PROPERTIES"]["ARTNUMBER"]["VALUE"])
			? $arResult["OFFERS"][$ind]["PROPERTIES"]["ARTNUMBER"]["VALUE"]
			: false;
		
		$offerObject = !empty($arResult["OFFERS"][$ind]["PROPERTIES"]["OBJECT"]["FULL_VALUE"]) ? $arResult["OFFERS"][$ind]["PROPERTIES"]["OBJECT"]["FULL_VALUE"] : false;
		$offerObjectContacts = $offerObject["PHONE_SMS"] || $offerObject["EMAIL_EMAIL"] ? true : false;
		if($offerObject) {
			$jsOffer["OBJECT"] = array(
				"ID" => $offerObject["ID"],
				"NAME" => $offerObject["NAME"],
				"ADDRESS" => $offerObject["ADDRESS"],
				"TIMEZONE" => $offerObject["TIMEZONE"],
				"WORKING_HOURS" => $offerObject["WORKING_HOURS"],		
				"PHONE" => $offerObject["PHONE"],
				"WHATSAPP" => $offerObject["WHATSAPP"],
				"VIBER" => $offerObject["VIBER"],
				"TELEGRAM" => $offerObject["TELEGRAM"],
				"INSTAGRAM" => $offerObject["INSTAGRAM"],
				"EMAIL" => $offerObject["EMAIL"],
				"SKYPE" => $offerObject["SKYPE"],
				"CALLBACK_FORM" => $offerObjectContacts
			);
			if($arParams["OBJECTS_USE_REVIEW"] != "N") {
				$jsOffer["OBJECT"]["BTN_ADD_REVIEW"] = true;
				$jsOffer["OBJECT"]["REVIEWS_PAGE_LINK"] = !empty($offerObject["REVIEWS_PAGE_LINK"]) ? $offerObject["REVIEWS_PAGE_LINK"] : $offerObject["DETAIL_PAGE_URL"]."#reviews";
				if(isset($offerObject["REVIEWS_COUNT"]) && $offerObject["REVIEWS_COUNT"] > 0) {
					$jsOffer["OBJECT"]["RATING_VALUE"] = $offerObject["RATING_VALUE"];
					$jsOffer["OBJECT"]["REVIEWS_COUNT"] = $offerObject["REVIEWS_COUNT"];
					$jsOffer["OBJECT"]["REVIEWS_DECLENSION"] = $offerObject["REVIEWS_DECLENSION"];
				}
			}
			if(intval($arSettings["PRICE_UPDATE_PERIOD"]) > 0 && !empty($offerObject["PARSER_TAG"]) && !empty($offerObject["PARSER_CLASS"]) && !empty($arResult["OFFERS"][$ind]["PROPERTIES"]["PARSER_LINK"]["VALUE"])) {
				$jsOffer["TIMESTAMP_X"] = $arResult["OFFERS"][$ind]["TIMESTAMP_X"];
			}
		}
		unset($offerObjectContacts, $offerObject);
		
		$jsOffer["PARTNERS_URL"] = !empty($arResult["OFFERS"][$ind]["PROPERTIES"]["PARTNERS_URL"]["VALUE"]) ? true : (!empty($arResult["PROPERTIES"]["PARTNERS_URL"]["VALUE"]) ? true : false);
		
		$jsOffer["DISPLAY_PROPERTIES"] = $strAllProps;
		$jsOffer["DISPLAY_PROPERTIES_MAIN_BLOCK"] = $strMainProps;
		$jsOffer["PRICE_RANGES_HTML"] = $strPriceRanges;
	}
	unset($strAllProps, $strMainProps, $strPriceRangesRatio, $strPriceRanges, $ind, $jsOffer, $useRatio);
	
	$jsParams = array(
		"CONFIG" => array(
			"USE_CATALOG" => $arResult["CATALOG"],
			"SHOW_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],
			"SHOW_PRICE" => true,
			"SHOW_DISCOUNT_PERCENT" => $arParams["SHOW_DISCOUNT_PERCENT"] === "Y",
			"SHOW_OLD_PRICE" => $arParams["SHOW_OLD_PRICE"] === "Y",
			"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
			"DISPLAY_COMPARE" => $arParams["DISPLAY_COMPARE"],
			"SHOW_SKU_PROPS" => $arResult["SHOW_OFFERS_PROPS"],			
			"MAIN_PICTURE_MODE" => $arParams["DETAIL_PICTURE_MODE"],
			"ADD_TO_BASKET_ACTION" => $arParams["ADD_TO_BASKET_ACTION"],			
			"SHOW_MAX_QUANTITY" => $arParams["SHOW_MAX_QUANTITY"],
			"RELATIVE_QUANTITY_FACTOR" => $arParams["RELATIVE_QUANTITY_FACTOR"],
			"USE_SUBSCRIBE" => $showSubscribe,
			"MIN_ORDER_SUM" => $minOrderSum,
			"SHOW_SLIDER" => $arParams["SHOW_SLIDER"],
			"SLIDER_INTERVAL" => $arParams["SLIDER_INTERVAL"],
			"ALT" => $alt,
			"TITLE" => $title,
			"MAGNIFIER_ZOOM_PERCENT" => 200,
			"USE_ENHANCED_ECOMMERCE" => $arParams["USE_ENHANCED_ECOMMERCE"],
			"DATA_LAYER_NAME" => $arParams["DATA_LAYER_NAME"],
			"BRAND_PROPERTY" => !empty($arResult["DISPLAY_PROPERTIES"][$arParams["BRAND_PROPERTY"]])
				? $arResult["DISPLAY_PROPERTIES"][$arParams["BRAND_PROPERTY"]]["DISPLAY_VALUE"]
				: null
		),
		"PRODUCT_TYPE" => $arResult["CATALOG_TYPE"],
		"OFFERS_VIEW" => $arParams["OFFERS_VIEW"],
		"VISUAL" => $itemIds,
		"DEFAULT_PICTURE" => array(
			"PREVIEW_PICTURE" => $arResult["DEFAULT_PICTURE"],
			"DETAIL_PICTURE" => $arResult["DEFAULT_PICTURE"]
		),
		"PRODUCT" => array(
			"ID" => $arResult["ID"],
			"IBLOCK_ID" => $arResult["IBLOCK_ID"],
			"ACTIVE" => $arResult["ACTIVE"],
			"NAME" => $arResult["~NAME"],
			"CATEGORY" => $arResult["CATEGORY_PATH"]
		),
		"BASKET" => array(
			"QUANTITY" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
			"BASKET_URL" => $arParams["BASKET_URL"],
			"SKU_PROPS" => $arResult["OFFERS_PROP_CODES"],
			"ADD_URL_TEMPLATE" => $addUrlTemplate,
			"BUY_URL_TEMPLATE" => $buyUrlTemplate
		),
		"OFFERS" => $arResult["JS_OFFERS"],
		"OFFER_SELECTED" => $arResult["OFFERS_SELECTED"],
		"TREE_PROPS" => $skuProps
	);
} else {
	$jsParams = array(
		"CONFIG" => array(
			"USE_CATALOG" => $arResult["CATALOG"],
			"SHOW_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],
			"SHOW_PRICE" => !empty($arResult["ITEM_PRICES"]),
			"SHOW_DISCOUNT_PERCENT" => $arParams["SHOW_DISCOUNT_PERCENT"] === "Y",
			"SHOW_OLD_PRICE" => $arParams["SHOW_OLD_PRICE"] === "Y",
			"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
			"DISPLAY_COMPARE" => $arParams["DISPLAY_COMPARE"],
			"MAIN_PICTURE_MODE" => $arParams["DETAIL_PICTURE_MODE"],
			"ADD_TO_BASKET_ACTION" => $arParams["ADD_TO_BASKET_ACTION"],			
			"SHOW_MAX_QUANTITY" => $arParams["SHOW_MAX_QUANTITY"],
			"RELATIVE_QUANTITY_FACTOR" => $arParams["RELATIVE_QUANTITY_FACTOR"],
			"USE_SUBSCRIBE" => $showSubscribe,
			"MIN_ORDER_SUM" => $minOrderSum,
			"SHOW_SLIDER" => $arParams["SHOW_SLIDER"],
			"SLIDER_INTERVAL" => $arParams["SLIDER_INTERVAL"],
			"ALT" => $alt,
			"TITLE" => $title,
			"MAGNIFIER_ZOOM_PERCENT" => 200,
			"USE_ENHANCED_ECOMMERCE" => $arParams["USE_ENHANCED_ECOMMERCE"],
			"DATA_LAYER_NAME" => $arParams["DATA_LAYER_NAME"],
			"BRAND_PROPERTY" => !empty($arResult["DISPLAY_PROPERTIES"][$arParams["BRAND_PROPERTY"]])
				? $arResult["DISPLAY_PROPERTIES"][$arParams["BRAND_PROPERTY"]]["DISPLAY_VALUE"]
				: null
		),
		"VISUAL" => $itemIds,
		"PRODUCT_TYPE" => $arResult["CATALOG_TYPE"],
		"PRODUCT" => array(
			"ID" => $arResult["ID"],
			"IBLOCK_ID" => $arResult["IBLOCK_ID"],
			"ACTIVE" => $arResult["ACTIVE"],
			"PICT" => is_array($arResult["DETAIL_PICTURE"]) ? $arResult["DETAIL_PICTURE"] : $arResult["DEFAULT_PICTURE"],
			"NAME" => $arResult["~NAME"],
			"SUBSCRIPTION" => true,
			"ITEM_PRICE_MODE" => $arResult["ITEM_PRICE_MODE"],
			"ITEM_PRICES" => $arResult["ITEM_PRICES"],
			"ITEM_PRICE_SELECTED" => $arResult["ITEM_PRICE_SELECTED"],
			"ITEM_QUANTITY_RANGES" => $arResult["ITEM_QUANTITY_RANGES"],
			"ITEM_QUANTITY_RANGE_SELECTED" => $arResult["ITEM_QUANTITY_RANGE_SELECTED"],
			"ITEM_MEASURE_RATIOS" => $arResult["ITEM_MEASURE_RATIOS"],
			"ITEM_MEASURE_RATIO_SELECTED" => $arResult["ITEM_MEASURE_RATIO_SELECTED"],
			"ITEM_MEASURE" => $arResult["ITEM_MEASURE"],
			"SLIDER_COUNT" => $arResult["MORE_PHOTO_COUNT"],
			"SLIDER" => $arResult["MORE_PHOTO"],			
			"CAN_BUY" => $arResult["CAN_BUY"],
			"CHECK_QUANTITY" => $arResult["CHECK_QUANTITY"],
			"QUANTITY_FLOAT" => is_float($measureRatio),
			"MAX_QUANTITY" => $arResult["CATALOG_QUANTITY"],
			"STEP_QUANTITY" => $measureRatio,
			"CATEGORY" => $arResult["CATEGORY_PATH"]
		),
		"BASKET" => array(			
			"QUANTITY" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
			"PROPS" => $arParams["PRODUCT_PROPS_VARIABLE"],			
			"BASKET_URL" => $arParams["BASKET_URL"],
			"ADD_URL_TEMPLATE" => $addUrlTemplate,
			"BUY_URL_TEMPLATE" => $buyUrlTemplate
		)
	);
	
	if(!empty($arResult["PROPERTIES"]["M2_COUNT"]["VALUE"])) {		
		if($isMeasurePc) {
			$jsParams["PRODUCT"]["PC_MAX_QUANTITY"] = $arResult["CATALOG_QUANTITY"];
			$jsParams["PRODUCT"]["PC_STEP_QUANTITY"] = $measureRatio;

			$jsParams["PRODUCT"]["SQ_M_MAX_QUANTITY"] = round($arResult["CATALOG_QUANTITY"] / str_replace(",", ".", $arResult["PROPERTIES"]["M2_COUNT"]["VALUE"]), 4);			
			$jsParams["PRODUCT"]["SQ_M_STEP_QUANTITY"] = round($measureRatio / str_replace(",", ".", $arResult["PROPERTIES"]["M2_COUNT"]["VALUE"]), 4);
		} elseif($isMeasureSqM) {
			$jsParams["PRODUCT"]["PC_MAX_QUANTITY"] = floor($arResult["CATALOG_QUANTITY"] / $measureRatio);			
			$jsParams["PRODUCT"]["PC_STEP_QUANTITY"] = 1;

			$jsParams["PRODUCT"]["SQ_M_MAX_QUANTITY"] = $arResult["CATALOG_QUANTITY"];
			$jsParams["PRODUCT"]["SQ_M_STEP_QUANTITY"] = $measureRatio;
		}
	}
}

if($arParams["DISPLAY_COMPARE"]) {
	$jsParams["COMPARE"] = array(
		"COMPARE_NAME" => $arParams["COMPARE_NAME"],
		"COMPARE_PATH" => $arParams["COMPARE_PATH"],
		"COMPARE_URL_TEMPLATE" => $arResult["~COMPARE_URL_TEMPLATE"],
		"COMPARE_DELETE_URL_TEMPLATE" => $arResult["~COMPARE_DELETE_URL_TEMPLATE"]
	);
}

if($object) {
	$jsParams["OBJECT"] = array(
		"ID" => $object["ID"],
		"NAME" => $object["NAME"],
		"ADDRESS" => $object["ADDRESS"],
		"TIMEZONE" => $object["TIMEZONE"],
		"WORKING_HOURS" => $object["WORKING_HOURS"],		
		"PHONE" => $object["PHONE"],
		"WHATSAPP" => $object["WHATSAPP"],
		"VIBER" => $object["VIBER"],
		"TELEGRAM" => $object["TELEGRAM"],
		"INSTAGRAM" => $object["INSTAGRAM"],
		"EMAIL" => $object["EMAIL"],
		"SKYPE" => $object["SKYPE"],
		"CALLBACK_FORM" => $objectContacts
	);
	if($arParams["OBJECTS_USE_REVIEW"] != "N") {
		$jsParams["OBJECT"]["BTN_ADD_REVIEW"] = true;
		$jsParams["OBJECT"]["REVIEWS_PAGE_LINK"] = $object["DETAIL_PAGE_URL"]."#reviews";
		if(isset($object["REVIEWS_COUNT"]) && $object["REVIEWS_COUNT"] > 0) {
			$jsParams["OBJECT"]["RATING_VALUE"] = $object["RATING_VALUE"];
			$jsParams["OBJECT"]["REVIEWS_COUNT"] = $object["REVIEWS_COUNT"];
			$jsParams["OBJECT"]["REVIEWS_DECLENSION"] = $object["REVIEWS_DECLENSION"];
		}
	}
}

$signer = new Bitrix\Main\Security\Sign\Signer;
$signedParams = $signer->sign(base64_encode(serialize($arResult["ORIGINAL_PARAMETERS"])), "catalog.element");?>

<script type="text/javascript">
	BX.message({
		CATALOG_ELEMENT_ARTICLE_PRICE_NOT_SET_MESSAGE: '<?=GetMessageJS("CT_BCE_CATALOG_PRICE_NOT_SET")?>',
		CATALOG_ELEMENT_ARTICLE_SQ_M_MESSAGE: '<?=GetMessageJS("CT_BCE_CATALOG_MEASURE_SQ_M")?>',
		CATALOG_ELEMENT_ARTICLE_ECONOMY_INFO_MESSAGE: '<?=GetMessageJS("CT_BCE_CATALOG_ECONOMY_INFO2")?>',
		CATALOG_ELEMENT_ARTICLE_BASKET_URL: '<?=$arParams["BASKET_URL"]?>',
		CATALOG_ELEMENT_ARTICLE_ADD_BASKET_MESSAGE: '<?=($showBuyBtn ? $arParams["MESS_BTN_BUY"] : $arParams["MESS_BTN_ADD_TO_BASKET"])?>',
		CATALOG_ELEMENT_ARTICLE_ADD_BASKET_OK_MESSAGE: '<?=GetMessageJS("CT_BCE_CATALOG_ADD_OK")?>',		
		CATALOG_ELEMENT_ARTICLE_DELAY_MESSAGE: '<?=$arParams["MESS_BTN_DELAY"]?>',
		CATALOG_ELEMENT_ARTICLE_DELAY_OK_MESSAGE: '<?=GetMessageJS("CT_BCE_CATALOG_DELAY_OK")?>',		
		CATALOG_ELEMENT_ARTICLE_RELATIVE_QUANTITY_MANY: '<?=CUtil::JSEscape($arParams["MESS_RELATIVE_QUANTITY_MANY"])?>',
		CATALOG_ELEMENT_ARTICLE_RELATIVE_QUANTITY_FEW: '<?=CUtil::JSEscape($arParams["MESS_RELATIVE_QUANTITY_FEW"])?>',
		CATALOG_ELEMENT_ARTICLE_COMPARE_MESSAGE: '<?=$arParams["MESS_BTN_COMPARE"]?>',
		CATALOG_ELEMENT_ARTICLE_COMPARE_OK_MESSAGE: '<?=GetMessageJS("CT_BCE_CATALOG_COMPARE_OK")?>',
		CATALOG_ELEMENT_ARTICLE_OBJECT_TODAY: '<?=GetMessageJS("CT_BCE_CATALOG_OBJECT_TODAY")?>',
		CATALOG_ELEMENT_ARTICLE_OBJECT_24_HOURS: '<?=GetMessageJS("CT_BCE_CATALOG_OBJECT_24_HOURS")?>',
		CATALOG_ELEMENT_ARTICLE_OBJECT_OFF: '<?=GetMessageJS("CT_BCE_CATALOG_OBJECT_OFF")?>',
		CATALOG_ELEMENT_ARTICLE_OBJECT_BREAK: '<?=GetMessageJS("CT_BCE_CATALOG_OBJECT_BREAK")?>',
		CATALOG_ELEMENT_ARTICLE_OBJECT_SEE_REVIEWS: '<?=GetMessageJS("CT_BCE_CATALOG_OBJECT_SEE_REVIEWS")?>',
		CATALOG_ELEMENT_ARTICLE_OBJECT_ADD_REVIEW: '<?=GetMessageJS("CT_BCE_CATALOG_OBJECT_ADD_REVIEW")?>',
		CATALOG_ELEMENT_ARTICLE_OBJECT_LOADING: '<?=GetMessageJS("CT_BCE_CATALOG_OBJECT_LOADING");?>',
		CATALOG_ELEMENT_ARTICLE_TEMPLATE_PATH: '<?=$templateFolder?>',
		CATALOG_ELEMENT_ARTICLE_PARAMETERS: '<?=CUtil::JSEscape($signedParams)?>'
	});
	var <?=$obName?> = new JCCatalogElementArticle(<?=CUtil::PhpToJSObject($jsParams, false, true)?>);
</script>

<?unset($actualItem, $itemIds, $jsParams);