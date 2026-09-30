<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;?>

<div class="product-item">
	<div class="product-item-image-wrapper" data-entity="image-wrapper">		
		<?//PREVIEW_PICTURE//?>
		<a target="_self" class="product-item-image" id="<?=$itemIds['PICT_ID']?>" href="<?=$item['DETAIL_PAGE_URL']?>" title="<?=$imgTitle?>"<?=($arParams["QUICK_VIEW"] == "FULL" ? " data-entity='quickView'" : "")?>>
			<?if(is_array($item['PREVIEW_PICTURE'])) {?>
				<img src="<?=$item['PREVIEW_PICTURE']['SRC']?>" width="<?=$item['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$item['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$imgAlt?>" title="<?=$imgTitle?>" />			
			<?} else {?>
				<img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo.png" width="222" height="222" alt="<?=$imgAlt?>" title="<?=$imgTitle?>" />
			<?}
			//MARKERS//?>
			<div class="product-item-markers<?=((!$object || ($object && $objectContacts)) && !$partnersUrl && (!$haveOffers || ($haveOffers && ($arParams['OFFERS_VIEW'] == 'PROPS' || $arParams['OFFERS_VIEW'] == 'DROPDOWN_LIST') && $arParams['PRODUCT_DISPLAY_MODE'] == 'Y')) ? ' product-item-markers-icons' : '')?>">
				<?if($arParams['SHOW_DISCOUNT_PERCENT'] == 'Y') {?>
					<span class="product-item-marker-container<?=($price['PERCENT'] > 0 ? '' : ' product-item-marker-container-hidden')?>" id="<?=$itemIds['DISCOUNT_PERCENT_ID']?>">
						<span class="product-item-marker product-item-marker-discount product-item-marker-14px"><span data-entity="dsc-perc-val"><?=-$price['PERCENT']?>%</span></span>
					</span>
				<?}
				if(!empty($item['PROPERTIES']['MARKER']['FULL_VALUE'])) {
					foreach($item['PROPERTIES']['MARKER']['FULL_VALUE'] as $key => $arMarker) {
						if($key <= 4) {?>
							<span class="product-item-marker-container">
								<span class="product-item-marker<?=(!empty($arMarker['FONT_SIZE']) ? ' product-item-marker-'.$arMarker['FONT_SIZE'] : '')?>"<?=(!empty($arMarker['BACKGROUND_1']) && !empty($arMarker['BACKGROUND_2']) ? ' style="background: '.$arMarker['BACKGROUND_2'].'; background: -webkit-linear-gradient(left, '.$arMarker['BACKGROUND_1'].', '.$arMarker['BACKGROUND_2'].'); background: -moz-linear-gradient(left, '.$arMarker['BACKGROUND_1'].', '.$arMarker['BACKGROUND_2'].'); background: -o-linear-gradient(left, '.$arMarker['BACKGROUND_1'].', '.$arMarker['BACKGROUND_2'].'); background: -ms-linear-gradient(left, '.$arMarker['BACKGROUND_1'].', '.$arMarker['BACKGROUND_2'].'); background: linear-gradient(to right, '.$arMarker['BACKGROUND_1'].', '.$arMarker['BACKGROUND_2'].');"' : (!empty($arMarker['BACKGROUND_1']) && empty($arMarker['BACKGROUND_2']) ? ' style="background: '.$arMarker['BACKGROUND_1'].';"' : (empty($arMarker['BACKGROUND_1']) && !empty($arMarker['BACKGROUND_2']) ? ' style="background: '.$arMarker['BACKGROUND_2'].';"' : '')))?>><?=(!empty($arMarker['ICON']) ? '<i class="'.$arMarker['ICON'].'"></i>' : '')?><span><?=$arMarker['NAME']?></span></span>
							</span>
						<?} else {
							break;
						}
					}
					unset($key, $arMarker);
				}?>
			</div>			
		</a>
		<?//QUICK_VIEW//
		if($arParams['QUICK_VIEW'] != 'OFF') {?>
			<div class="hidden-xs hidden-sm product-item-quick-view"<?=($arParams["QUICK_VIEW"] == "FULL" ? " data-entity='quickView'" : " id='".$itemIds['QUICK_VIEW_LINK']."'")?>><i class="icon-eye"></i></div>
		<?}?>
	</div>
	<div class="product-item-info">
		<?//TITLE//?>
		<div class="product-item-title">
			<a target="_self" href="<?=$item['DETAIL_PAGE_URL']?>" title="<?=$item['NAME']?>"<?=($arParams["QUICK_VIEW"] == "FULL" ? " data-entity='quickView'" : "")?>><?=$item['NAME']?></a>
		</div>
		<?//ARTICLE//
		if($haveOffers && (!$object || ($object && $objectContacts)) && !$partnersUrl && ($arParams['OFFERS_VIEW'] == 'PROPS' || $arParams['OFFERS_VIEW'] == 'DROPDOWN_LIST') && $arParams['PRODUCT_DISPLAY_MODE'] == 'Y') {
			$article = !empty($actualItem["PROPERTIES"]["ARTNUMBER"]["VALUE"]) ? $actualItem["PROPERTIES"]["ARTNUMBER"]["VALUE"] : false;?>
			<div class="hidden-xs hidden-sm product-item-article" id="<?=$itemIds['ARTICLE_ID']?>">
				<span class="product-item-article-name" data-entity="article-name"<?=($article ? '' : ' style="display:none;"')?>><?=Loc::getMessage('CT_BCI_TPL_MESS_ARTICLE')?></span>
				<span class="product-item-article-val" data-entity="article-val"<?=($article ? '' : ' style="display:none;"')?>><?=($article ? $article : '')?></span>
			</div>
		<?} elseif(!empty($item["PROPERTIES"]["ARTNUMBER"]["VALUE"])) {?>
			<div class="hidden-xs hidden-sm product-item-article">
				<span class="product-item-article-name"><?=Loc::getMessage('CT_BCI_TPL_MESS_ARTICLE')?></span>
				<span class="product-item-article-val"><?=$item["PROPERTIES"]["ARTNUMBER"]["VALUE"]?></span>
			</div>
		<?}
		//RATING//
		if(intval($item['REVIEWS_COUNT']) > 0) {?>
			<div class="hidden-xs hidden-sm product-item-rating">
				<div class="product-item-rating-val"<?=($item['RATING_VALUE'] <= 4.4 ? ' data-rate="'.intval($item['RATING_VALUE']).'"' : '')?>><?=$item['RATING_VALUE']?></div>
				<div class="product-item-rating-reviews-count"><?=$item['REVIEWS_COUNT'].' '.$item['REVIEWS_DECLENSION']?></div>
			</div>
		<?}?>
	</div>
	<?//SKU//
	if((!$object || ($object && $objectContacts)) && !$partnersUrl && $haveOffers && ($arParams['OFFERS_VIEW'] == 'PROPS' || $arParams['OFFERS_VIEW'] == 'DROPDOWN_LIST') && $arParams['PRODUCT_DISPLAY_MODE'] == 'Y' && !empty($item['OFFERS_PROP'])) {?>
		<div class="hidden-xs hidden-sm product-item-scu-col" id="<?=$itemIds['TREE_ID']?>">
			<?foreach($arParams['SKU_PROPS'] as $skuProperty) {
				$propertyId = $skuProperty['ID'];
				$skuProperty['NAME'] = htmlspecialcharsbx($skuProperty['NAME']);
				if(!isset($item['SKU_TREE_VALUES'][$propertyId]))
					continue;?>
				<div data-entity="sku-block">
					<div class="product-item-scu-container" data-entity="sku-line-block">
						<div class="product-item-scu-title"><?=$skuProperty['NAME'].($arParams['OFFERS_VIEW'] == 'PROPS' && $skuProperty['SHOW_MODE'] == 'PICT' ? '<span data-entity="current-option"></span>' : '')?></div>
						<?if($arParams['OFFERS_VIEW'] == 'PROPS') {?>
							<div class="product-item-scu-block">
								<div class="product-item-scu-list">
									<ul class="product-item-scu-item-list">
										<?foreach($skuProperty['VALUES'] as $value) {
											if(!isset($item['SKU_TREE_VALUES'][$propertyId][$value['ID']]))
												continue;

											$value['NAME'] = htmlspecialcharsbx($value['NAME']);

											if($skuProperty['SHOW_MODE'] == 'PICT') {?>
												<li class="product-item-scu-item-color" title="<?=$value['NAME']?>" data-treevalue="<?=$propertyId?>_<?=$value['ID']?>" data-onevalue="<?=$value['ID']?>" style="<?=(!empty($value['CODE']) ? 'background-color: #'.$value['CODE'].';' : (!empty($value['PICT']) ? 'background-image: url('.$value['PICT']['SRC'].');' : ''));?>"></li>
											<?} else {?>
												<li class="product-item-scu-item-text" title="<?=$value['NAME']?>" data-treevalue="<?=$propertyId?>_<?=$value['ID']?>" data-onevalue="<?=$value['ID']?>">
													<?=$value['NAME']?>
												</li>
											<?}
										}
										unset($value);?>
									</ul>											
								</div>
							</div>
						<?} else {?>
							<div class="product-item-basket-props-block">
								<div class="product-item-basket-props-drop-down" onclick="<?=$obName?>.showOfferBasketPropsDropDownPopup(this, '<?=$propertyId?>');">
									<div class="drop-down-text" data-entity="current-option">-</div>
									<div class="drop-down-arrow"><i class="icon-arrow-down"></i></div>
									<div class="drop-down-popup" data-entity="dropdownContent" style="display: none;">
										<ul>
											<?foreach($skuProperty['VALUES'] as $value) {
												if(!isset($item['SKU_TREE_VALUES'][$propertyId][$value['ID']]))
													continue;

												$value['NAME'] = htmlspecialcharsbx($value['NAME']);?>

												<li data-treevalue="<?=$propertyId?>_<?=$value['ID']?>" data-onevalue="<?=$value['ID']?>" onclick="<?=$obName?>.selectOfferBasketPropsDropDownPopupItem(this);"><span><?=$value['NAME']?></span></li>
											<?}
											unset($value);?>
										</ul>
									</div>
								</div>
							</div>
						<?}?>
					</div>
				</div>
			<?}
			unset($skuProperty);?>
		</div>
		<?foreach($arParams['SKU_PROPS'] as $skuProperty) {
			if(!isset($item['OFFERS_PROP'][$skuProperty['CODE']]))
				continue;

			$skuProps[] = array(
				'ID' => $skuProperty['ID'],
				'SHOW_MODE' => $skuProperty['SHOW_MODE'],
				'VALUES' => $skuProperty['VALUES'],
				'VALUES_COUNT' => $skuProperty['VALUES_COUNT']
			);
		}
		unset($skuProperty);
	}
	//BASKET_PROPERTIES//
	if((!$object || ($object && $objectContacts)) && !$partnersUrl && !$haveOffers) {
		if($arParams['ADD_PROPERTIES_TO_BASKET'] == 'Y' && !empty($item['PRODUCT_PROPERTIES'])) {?>
			<div class="hidden-xs hidden-sm product-item-basket-props-col" id="<?=$itemIds['BASKET_PROP_DIV']?>">
				<?if(!empty($item['PRODUCT_PROPERTIES_FILL'])) {
					foreach($item['PRODUCT_PROPERTIES_FILL'] as $propId => $propInfo) {?>
						<input type="hidden" name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$propId?>]" value="<?=htmlspecialcharsbx($propInfo['ID'])?>" />
						<?unset($item['PRODUCT_PROPERTIES'][$propID]);
					}
					unset($propId, $propInfo);
				}
				if(!empty($item['PRODUCT_PROPERTIES'])) {
					foreach($item['PRODUCT_PROPERTIES'] as $propId => $propInfo) {?>
						<div class="product-item-basket-props-container">
							<div class="product-item-basket-props-title"><?=$item['PROPERTIES'][$propId]['NAME']?></div>
							<div class="product-item-basket-props-block">
								<?if($item['PROPERTIES'][$propId]['PROPERTY_TYPE'] == 'L' && $item['PROPERTIES'][$propId]['LIST_TYPE'] == 'C') {?>
									<div class="product-item-basket-props-input-radio">
										<?foreach($propInfo['VALUES'] as $valueId => $value) {?>
											<label>
												<input type="radio" name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$propId?>]" value="<?=$valueId?>"<?=($valueId == $propInfo['SELECTED'] ? ' checked="checked"' : '');?> />
												<span class="check-container">
													<span class="check"><i class="icon-ok-b"></i></span>
												</span>
												<span class="text" title="<?=$value?>"><?=$value?></span>
											</label>
										<?}
										unset($valueId, $value);?>
									</div>
								<?} else {?>
									<div class="product-item-basket-props-drop-down" onclick="<?=$obName?>.showBasketPropsDropDownPopup(this, '<?=$propId?>');">
										<?$currId = $currVal = false;
										foreach($propInfo['VALUES'] as $valueId => $value) {
											if($valueId == $propInfo['SELECTED']) {
												$currId = $valueId;
												$currVal = $value;
											}
										}
										unset($valueId, $value);?>
										<input type="hidden" name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$propId?>]" value="<?=(!empty($currId) ? $currId : '');?>" />
										<div class="drop-down-text" data-entity="current-option"><?=(!empty($currVal) ? $currVal : '');?></div>
										<?unset($currVal, $currId);?>
										<div class="drop-down-arrow"><i class="icon-arrow-down"></i></div>
										<div class="drop-down-popup" data-entity="dropdownContent" style="display: none;">
											<ul>
												<?foreach($propInfo['VALUES'] as $valueId => $value) {?>
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
	}?>
	<div class="product-item-sale">
		<div class="product-item-sale-col">
			<?//PRICE//?>
			<div class="product-item-price-container" data-entity="price-block">
				<div class="product-item-price" id="<?=$itemIds['PRICE_ID']?>">
					<?if(!empty($price)) {
						if($haveOffers && (($object && !$objectContacts) || $partnersUrl || ($arParams['OFFERS_VIEW'] != 'PROPS' && $arParams['OFFERS_VIEW'] != 'DROPDOWN_LIST') || $arParams['PRODUCT_DISPLAY_MODE'] == 'N')) {
							if(($arParams['OFFERS_VIEW'] == 'LIST' && $price['SQ_M_PRICE'] > 0) || $price['PRICE'] > 0) {?>
								<span class="product-item-price-from"><?=Loc::getMessage('CT_BCI_TPL_MESS_PRICE_FROM')?></span>
								<span class="product-item-price-current"><?=($arParams['OFFERS_VIEW'] == 'LIST' && $price['SQ_M_PRICE'] > 0 ? $price['SQ_M_PRINT_PRICE'] : $price['PRINT_PRICE'])?></span>
								<?if($arParams['OFFERS_VIEW'] == 'LIST') {?>
									<span class="product-item-price-measure">/<?=($price['SQ_M_PRICE'] > 0 ? Loc::getMessage('CT_BCI_TPL_MESS_PRICE_MEASURE_SQ_M') : $actualItem['ITEM_MEASURE']['TITLE'])?></span>
								<?}
								if($arParams['USE_PRICE_COUNT']) {?>
									<span class="product-item-price-ranges-icon" data-entity="price-ranges-icon"<?=(count($actualItem['ITEM_QUANTITY_RANGES']) > 1 ? '' : ' style="display:none;"')?>><i class="icon-question"></i></span>
								<?}
							} else {?>
								<span class="product-item-price-not-set"><?=Loc::getMessage('CT_BCI_TPL_MESS_PRICE_NOT_SET')?></span>
							<?}
						} else {?>
							<span class="product-item-price-not-set" data-entity="price-current-not-set"<?=($price['SQ_M_PRICE'] > 0 ? ' style="display:none;"' : ($price['PRICE'] > 0 ? ' style="display:none;"' : ''))?>><?=Loc::getMessage('CT_BCI_TPL_MESS_PRICE_NOT_SET')?></span>
							<span class="product-item-price-current" data-entity="price-current"<?=($price['SQ_M_PRICE'] > 0 ? '' : ($price['PRICE'] > 0 ? '' : ' style="display:none;"'))?>><?=($price['SQ_M_PRICE'] > 0 ? $price['SQ_M_PRINT_PRICE'] : $price['PRINT_PRICE'])?></span>
							<span class="product-item-price-measure" data-entity="price-measure"<?=($price['SQ_M_PRICE'] > 0 ? '' : ($price['PRICE'] > 0 ? '' : ' style="display:none;"'))?>>/<?=($price['SQ_M_PRICE'] > 0 ? Loc::getMessage('CT_BCI_TPL_MESS_PRICE_MEASURE_SQ_M') : $actualItem['ITEM_MEASURE']['TITLE'])?></span>
							<?if($arParams['USE_PRICE_COUNT']) {?>
								<span class="product-item-price-ranges-icon" data-entity="price-ranges-icon"<?=(count($actualItem['ITEM_QUANTITY_RANGES']) > 1 ? '' : ' style="display:none;"')?>><i class="icon-question"></i></span>
							<?}
						}
					}?>
				</div>
				<?if($arParams['SHOW_OLD_PRICE'] == 'Y') {?>
					<div class="product-item-price-old" id="<?=$itemIds['OLD_PRICE_ID']?>"<?=($price['PERCENT'] > 0 ? '' : ' style="display:none;"')?>><?=($price['PERCENT'] > 0 ? ($price['SQ_M_BASE_PRICE'] > 0 ? $price['SQ_M_PRINT_BASE_PRICE'] : $price['PRINT_BASE_PRICE']) : '')?></div>
					<div class="hidden-xs hidden-sm product-item-price-economy" id="<?=$itemIds['DISCOUNT_PRICE_ID']?>"<?=($price['PERCENT'] > 0 ? '' : ' style="display:none;"')?>><?=($price['PERCENT'] > 0 ? Loc::getMessage('CT_BCI_TPL_MESS_PRICE_ECONOMY', array('#ECONOMY#' => ($price['SQ_M_DISCOUNT'] > 0 ? $price['SQ_M_PRINT_DISCOUNT'] : $price['PRINT_DISCOUNT']))) : '')?></div>
				<?}
				//PRICE_RANGES//
				if($arParams['USE_PRICE_COUNT']) {
					$showRanges = count($actualItem['ITEM_QUANTITY_RANGES']) > 1;?>
					<div class="product-item-ranges-container" data-entity="price-ranges-block"<?=($showRanges ? '' : ' style="display:none;"')?>>
						<div class="product-item-ranges" data-entity="price-ranges-body">
							<?if($showRanges) {
								foreach($actualItem['ITEM_QUANTITY_RANGES'] as $range) {
									if($range['HASH'] != 'ZERO-INF') {
										$itemPrice = false;
										foreach($actualItem['ITEM_PRICES'] as $itemPrice) {
											if($itemPrice['QUANTITY_HASH'] == $range['HASH']) {
												break;
											}
										}
										if($itemPrice) {?>
											<div class="product-item-properties">
												<div class="product-item-properties-name">
													<?if(is_infinite($range['SORT_TO'])) {
														echo Loc::getMessage('CT_BCI_TPL_MESS_RANGE_FROM', array('#FROM#' => $range['SORT_FROM'].' '.$actualItem['ITEM_MEASURE']['TITLE']));
													} else {
														echo $range['SORT_FROM'].($range['SORT_TO'] != $range['SORT_FROM'] ? ' - '.$range['SORT_TO'] : '').' '.$actualItem['ITEM_MEASURE']['TITLE'];
													}?>
												</div>
												<div class="product-item-properties-val">
													<?=($arParams['USE_RATIO_IN_RANGES'] == 'Y' ? $itemPrice['PRINT_RATIO_PRICE'] : $itemPrice['PRINT_PRICE'])?>
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
					<?unset($showRanges);
				}?>
			</div>
			<?//OFFERS_COUNT//
			if($haveOffers && $arParams['OFFERS_VIEW'] == 'OBJECTS') {
				$offersDeclension = new Bitrix\Main\Grid\Declension(Loc::getMessage("CT_BCI_TPL_MESS_OFFER"), Loc::getMessage("CT_BCI_TPL_MESS_OFFERS_1"), Loc::getMessage("CT_BCI_TPL_MESS_OFFERS_2"));?>
				<div class="product-item-offers-count"><?=count($item['OFFERS']).' '.$offersDeclension->get(count($item['OFFERS']))?></div>
			<?}
			//QUANTITY_LIMIT//
			if($arParams['SHOW_MAX_QUANTITY'] !== 'N' && (!$object || ($object && $objectContacts)) && !$partnersUrl && (!$haveOffers || ($haveOffers && ($arParams['OFFERS_VIEW'] == 'PROPS' || $arParams['OFFERS_VIEW'] == 'DROPDOWN_LIST') && $arParams['PRODUCT_DISPLAY_MODE'] == 'Y'))) {
				if($haveOffers) {?>
					<div class="hidden-xs hidden-sm" id="<?=$itemIds['QUANTITY_LIMIT']?>" style="display: none;">
						<div class="product-item-quantity">
							<i class="icon-ok-b product-item-quantity-icon"></i>
							<span class="product-item-quantity-val">
								<?=$arParams['MESS_SHOW_MAX_QUANTITY'].'&nbsp;'?>
								<span data-entity="quantity-limit-value"></span>
							</span>
						</div>
					</div>								
					<div class="hidden-xs hidden-sm" id="<?=$itemIds['QUANTITY_LIMIT_NOT_AVAILABLE']?>" style="display: none;">
						<div class="product-item-quantity product-item-quantity-not-avl">
							<i class="icon-close-b product-item-quantity-icon"></i>
							<span class="product-item-quantity-val"><?=$arParams['MESS_NOT_AVAILABLE']?></span>
						</div>
					</div>
				<?} else {?>
					<div class="hidden-xs hidden-sm" id="<?=$itemIds['QUANTITY_LIMIT']?>">
						<div class="product-item-quantity<?=($actualItem['CAN_BUY'] ? '' : ' product-item-quantity-not-avl')?>">
							<i class="icon-<?=($actualItem['CAN_BUY'] ? 'ok' : 'close')?>-b product-item-quantity-icon"></i>
							<span class="product-item-quantity-val">
								<?if($actualItem['CAN_BUY']) {
									echo $arParams['MESS_SHOW_MAX_QUANTITY'].'&nbsp;';
									if($measureRatio && (float)$actualItem['CATALOG_QUANTITY'] > 0 && $actualItem['CATALOG_QUANTITY_TRACE'] == 'Y' && $actualItem['CATALOG_CAN_BUY_ZERO'] == 'N') {
										if($arParams['SHOW_MAX_QUANTITY'] == 'M') {
											if((float)$actualItem['CATALOG_QUANTITY'] / $measureRatio >= $arParams['RELATIVE_QUANTITY_FACTOR']) {
												echo $arParams['MESS_RELATIVE_QUANTITY_MANY'];
											} else {
												echo $arParams['MESS_RELATIVE_QUANTITY_FEW'];
											}
										} else {
											echo $actualItem['CATALOG_QUANTITY'];
										}
									}?>
								<?} else {
									echo $arParams['MESS_NOT_AVAILABLE'];
								}?>
							</span>
						</div>
					</div>
				<?}
			}
			//TOTAL_COST//
			if($arParams['USE_PRODUCT_QUANTITY'] && (!$object || ($object && $objectContacts)) && !$partnersUrl && ((!$haveOffers && $actualItem['CAN_BUY']) || ($haveOffers && ($arParams['OFFERS_VIEW'] == 'PROPS' || $arParams['OFFERS_VIEW'] == 'DROPDOWN_LIST') && $arParams['PRODUCT_DISPLAY_MODE'] == 'Y'))) {?>
				<div class="hidden-xs hidden-sm product-item-total-cost" id="<?=$itemIds['TOTAL_COST_ID']?>"<?=($price['MIN_QUANTITY'] != 1 || (!empty($item['PROPERTIES']['M2_COUNT']['VALUE']) && ($price['PC_MIN_QUANTITY'] != 1 || $price['SQ_M_MIN_QUANTITY'] != 1)) ? '' : ' style="display:none;"')?>><?=Loc::getMessage('CT_BCI_TPL_MESS_TOTAL_COST')?><span data-entity="total-cost"><?=($price['MIN_QUANTITY'] != 1 || (!empty($item['PROPERTIES']['M2_COUNT']['VALUE']) && ($price['PC_MIN_QUANTITY'] != 1 || $price['SQ_M_MIN_QUANTITY'] != 1)) ? $price['PRINT_RATIO_PRICE'] : '')?></span></div>
			<?}?>
		</div>
		<?//QUANTITY//
		if($arParams['USE_PRODUCT_QUANTITY'] && (!$object || ($object && $objectContacts)) && !$partnersUrl && ((!$haveOffers && $actualItem['CAN_BUY']) || ($haveOffers && ($arParams['OFFERS_VIEW'] == 'PROPS' || $arParams['OFFERS_VIEW'] == 'DROPDOWN_LIST') && $arParams['PRODUCT_DISPLAY_MODE'] == 'Y'))) {?>
			<div class="hidden-xs hidden-sm" data-entity="quantity-block">
				<?if(!empty($item['PROPERTIES']['M2_COUNT']['VALUE'])) {?>
					<div class="product-item-amount"<?=($isMeasurePc || $isMeasureSqM ? '' : ' style="display: none;"')?>>
						<a class="product-item-amount-btn-minus" id="<?=$itemIds['PC_QUANTITY_DOWN_ID']?>" href="javascript:void(0)" rel="nofollow">-</a>
						<input class="product-item-amount-input" id="<?=$itemIds['PC_QUANTITY_ID']?>" type="tel" value="<?=$price['PC_MIN_QUANTITY']?>" />
						<a class="product-item-amount-btn-plus" id="<?=$itemIds['PC_QUANTITY_UP_ID']?>" href="javascript:void(0)" rel="nofollow">+</a>
						<div class="product-item-amount-measure"><?=Loc::getMessage('CT_BCI_TPL_MESS_MEASURE_PC')?></div>
					</div>
					<div class="product-item-amount"<?=($isMeasurePc || $isMeasureSqM ? '' : ' style="display: none;"')?>>
						<a class="product-item-amount-btn-minus" id="<?=$itemIds['SQ_M_QUANTITY_DOWN_ID']?>" href="javascript:void(0)" rel="nofollow">-</a>
						<input class="product-item-amount-input" id="<?=$itemIds['SQ_M_QUANTITY_ID']?>" type="tel" value="<?=$price['SQ_M_MIN_QUANTITY']?>" />
						<a class="product-item-amount-btn-plus" id="<?=$itemIds['SQ_M_QUANTITY_UP_ID']?>" href="javascript:void(0)" rel="nofollow">+</a>
						<div class="product-item-amount-measure"><?=Loc::getMessage('CT_BCI_TPL_MESS_MEASURE_SQ_M')?></div>
					</div>
					<?if($haveOffers) {?>
						<div class="product-item-amount"<?=($isMeasurePc || $isMeasureSqM ? ' style="display: none;"' : '')?>>
							<a class="product-item-amount-btn-minus" id="<?=$itemIds['QUANTITY_DOWN_ID']?>" href="javascript:void(0)" rel="nofollow">-</a>
							<input class="product-item-amount-input" id="<?=$itemIds['QUANTITY_ID']?>" type="tel" value="<?=$price['MIN_QUANTITY']?>" />
							<a class="product-item-amount-btn-plus" id="<?=$itemIds['QUANTITY_UP_ID']?>" href="javascript:void(0)" rel="nofollow">+</a>
							<div class="product-item-amount-measure" id="<?=$itemIds['QUANTITY_MEASURE']?>"><?=$actualItem["ITEM_MEASURE"]["TITLE"]?></div>
						</div>
					<?}
				} else {?>
					<div class="product-item-amount">								
						<a class="product-item-amount-btn-minus" id="<?=$itemIds['QUANTITY_DOWN_ID']?>" href="javascript:void(0)" rel="nofollow">-</a>
						<input class="product-item-amount-input" id="<?=$itemIds['QUANTITY_ID']?>" type="tel" name="<?=$arParams['PRODUCT_QUANTITY_VARIABLE']?>" value="<?=$price['MIN_QUANTITY']?>" />
						<a class="product-item-amount-btn-plus" id="<?=$itemIds['QUANTITY_UP_ID']?>" href="javascript:void(0)" rel="nofollow">+</a>
						<div class="product-item-amount-measure" id="<?=$itemIds['QUANTITY_MEASURE']?>"><?=$actualItem['ITEM_MEASURE']['TITLE']?></div>								
					</div>
				<?}?>
			</div>
		<?}
		//BUTTONS//?>
		<div class="product-item-button-container<?=($haveOffers && (!$object || ($object && $objectContacts)) && !$partnersUrl && ($arParams['OFFERS_VIEW'] == 'PROPS' || $arParams['OFFERS_VIEW'] == 'DROPDOWN_LIST') && $arParams['PRODUCT_DISPLAY_MODE'] == 'Y' && !empty($item['OFFERS_PROP']) ? ' product-item-sku-mode' : '').(!$haveOffers && (!$object || ($object && $objectContacts)) && !$partnersUrl && $arParams['ADD_PROPERTIES_TO_BASKET'] == 'Y' && !empty($item['PRODUCT_PROPERTIES']) ? ' product-item-props-mode' : '')?>" data-entity="buttons-block">			
			<?if(($object && !$objectContacts) || $partnersUrl || ($haveOffers && (($arParams['OFFERS_VIEW'] != 'PROPS' && $arParams['OFFERS_VIEW'] != 'DROPDOWN_LIST') || $arParams['PRODUCT_DISPLAY_MODE'] != 'Y')) || $arParams['DISABLE_BASKET']) {?>
				<a target="<?=$item['TARGET']?>" class="btn btn-buy" href="<?=$item['DETAIL_PAGE_URL']?>" title="<?=$arParams['MESS_BTN_DETAIL']?>"<?=($arParams["QUICK_VIEW"] == "FULL" ? " data-entity='quickView'" : "")?>><i class="icon-arrow-right"></i><span><?=$arParams['MESS_BTN_DETAIL']?></span></a>
			<?} else {
				if(!$haveOffers) {
					if(($actualItem['CAN_BUY'] && ($price['RATIO_PRICE'] > 0 || !$arParams['ASK_PRICE'])) || (!$actualItem['CAN_BUY'] && !$arParams['UNDER_ORDER'] && !$showSubscribe)) {?>
						<div id="<?=$itemIds['BASKET_ACTIONS_ID']?>">
							<button type="button" class="btn btn-buy" id="<?=$itemIds['BUY_LINK']?>" title="<?=($arParams['ADD_TO_BASKET_ACTION'] == 'BUY' ? $arParams['MESS_BTN_BUY'] : $arParams['MESS_BTN_ADD_TO_BASKET'])?>"<?=($actualItem['CAN_BUY'] && $price['RATIO_PRICE'] > 0 ? '' : ' disabled="disabled"')?>><i class="icon-cart"></i><span><?=($arParams['ADD_TO_BASKET_ACTION'] == 'BUY' ? $arParams['MESS_BTN_BUY'] : $arParams['MESS_BTN_ADD_TO_BASKET'])?></span></button>
						</div>
					<?} elseif($actualItem['CAN_BUY'] && $price['RATIO_PRICE'] <= 0 && $arParams['ASK_PRICE']) {?>
						<button type="button" class="btn btn-default" id="<?=$itemIds['ASK_PRICE_LINK']?>" title="<?=Loc::getMessage('CT_BCI_TPL_MESS_ASK_PRICE')?>"><i class="icon-comment"></i><span><?=Loc::getMessage('CT_BCI_TPL_MESS_ASK_PRICE')?></span></button>
					<?} elseif(!$actualItem['CAN_BUY']) {
						if($arParams['UNDER_ORDER']) {?>
							<button type="button" class="btn btn-default" id="<?=$itemIds['NOT_AVAILABLE_LINK']?>" title="<?=Loc::getMessage('CT_BCI_TPL_MESS_UNDER_ORDER')?>"><i class="icon-clock"></i><span><?=Loc::getMessage('CT_BCI_TPL_MESS_UNDER_ORDER')?></span></button>
						<?} elseif($showSubscribe) {?>
							<?$APPLICATION->IncludeComponent('bitrix:catalog.product.subscribe', '',
								array(
									'PRODUCT_ID' => $actualItem['ID'],
									'BUTTON_ID' => $itemIds['SUBSCRIBE_LINK'],
									'BUTTON_CLASS' => 'btn btn-buy',
									'DEFAULT_DISPLAY' => true,
									'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],
								),
								$component,
								array('HIDE_ICONS' => 'Y')
							);?>
						<?}
					}
					if($arParams['ADD_PROPERTIES_TO_BASKET'] == 'Y' && !empty($item['PRODUCT_PROPERTIES'])) {?>
						<a target="_blank" class="btn btn-buy" id="<?=$itemIds['MORE_LINK']?>" href="<?=$item['DETAIL_PAGE_URL']?>" title="<?=$arParams['MESS_BTN_DETAIL']?>"<?=($arParams["QUICK_VIEW"] == "FULL" ? " data-entity='quickView'" : "")?> style="display: none;"><i class="icon-arrow-right"></i><span><?=$arParams['MESS_BTN_DETAIL']?></span></a>
					<?}
				} else {?>
					<div id="<?=$itemIds['BASKET_ACTIONS_ID']?>">
						<button type="button" class="btn btn-buy" id="<?=$itemIds['BUY_LINK']?>" title="<?=($arParams['ADD_TO_BASKET_ACTION'] == 'BUY' ? $arParams['MESS_BTN_BUY'] : $arParams['MESS_BTN_ADD_TO_BASKET'])?>"<?=(!$offerPartnersUrl ? ($actualItem['CAN_BUY'] ? ($price['RATIO_PRICE'] > 0 ? '' : ($arParams['ASK_PRICE'] ? ' style="display: none;"' : ' disabled="disabled"')) : ($arParams['UNDER_ORDER'] || $showSubscribe ? ' style="display: none;"' : ' disabled="disabled"')) : ' style="display: none;"')?>><i class="icon-cart"></i><span><?=($arParams['ADD_TO_BASKET_ACTION'] == 'BUY' ? $arParams['MESS_BTN_BUY'] : $arParams['MESS_BTN_ADD_TO_BASKET'])?></span></button>
					</div>
					<?if($arParams['ASK_PRICE']) {?>
						<button type="button" class="btn btn-default" id="<?=$itemIds['ASK_PRICE_LINK']?>" title="<?=Loc::getMessage('CT_BCI_TPL_MESS_ASK_PRICE')?>"<?=(!$offerPartnersUrl && $actualItem['CAN_BUY'] && $price['RATIO_PRICE'] <= 0 ? '' : ' style="display: none;"')?>><i class="icon-comment"></i><span><?=Loc::getMessage('CT_BCI_TPL_MESS_ASK_PRICE')?></span></button>
					<?}
					if($arParams['UNDER_ORDER']) {?>
						<button type="button" class="btn btn-default" id="<?=$itemIds['NOT_AVAILABLE_LINK']?>" title="<?=Loc::getMessage('CT_BCI_TPL_MESS_UNDER_ORDER')?>"<?=(!$offerPartnersUrl && !$actualItem['CAN_BUY'] ? '' : ' style="display: none;"')?>><i class="icon-clock"></i><span><?=Loc::getMessage('CT_BCI_TPL_MESS_UNDER_ORDER')?></span></button>
					<?}?>
					<a target="_blank" class="btn btn-buy" id="<?=$itemIds['MORE_LINK']?>" href="<?=$item['DETAIL_PAGE_URL']?>" title="<?=$arParams['MESS_BTN_DETAIL']?>"<?=($arParams["QUICK_VIEW"] == "FULL" ? " data-entity='quickView'" : "").($offerPartnersUrl && $actualItem["CAN_BUY"] ? '' : ' style="display: none;"')?>><i class="icon-arrow-right"></i><span><?=$arParams['MESS_BTN_DETAIL']?></span></a>
					<?if($showSubscribe) {?>
						<?$APPLICATION->IncludeComponent('bitrix:catalog.product.subscribe', '',
							array(
								'PRODUCT_ID' => $actualItem['ID'],
								'BUTTON_ID' => $itemIds['SUBSCRIBE_LINK'],
								'BUTTON_CLASS' => 'btn btn-buy',
								'DEFAULT_DISPLAY' => !$actualItem['CAN_BUY'] && !$arParams["UNDER_ORDER"],
								'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],
							),
							$component,
							array('HIDE_ICONS' => 'Y')
						);?>
					<?}
				}
			}?>
		</div>
	</div>
	<?//DELAY//
	if(!$arParams['DISABLE_DELAY']) {?>
		<div class="hidden-xs hidden-sm product-item-icons-container">
			<?if((!$object || ($object && $objectContacts)) && !$partnersUrl && (!$haveOffers || ($haveOffers && ($arParams['OFFERS_VIEW'] == 'PROPS' || $arParams['OFFERS_VIEW'] == 'DROPDOWN_LIST') && $arParams['PRODUCT_DISPLAY_MODE'] == 'Y'))) {?>
				<div class="product-item-delay" id="<?=$itemIds['DELAY_LINK']?>" title="<?=$arParams['MESS_BTN_DELAY']?>" style="display: <?=(!$offerPartnersUrl && $actualItem['CAN_BUY'] && $price['RATIO_PRICE'] > 0 ? '' : 'none')?>;">
					<i class="icon-heart" data-entity="delay-icon"></i>
				</div>
			<?}?>
		</div>
	<?}
	//COMPARE//
	if($arParams['DISPLAY_COMPARE'] && (!$haveOffers || ($haveOffers && (!$object || ($object && $objectContacts)) && !$partnersUrl && ($arParams['OFFERS_VIEW'] == 'PROPS' || $arParams['OFFERS_VIEW'] == 'DROPDOWN_LIST') && $arParams['PRODUCT_DISPLAY_MODE'] == 'Y'))) {?>
		<div class="hidden-xs hidden-sm product-item-compare">
			<label id="<?=$itemIds['COMPARE_LINK']?>" title="<?=$arParams['MESS_BTN_COMPARE']?>">
				<input type="checkbox" data-entity="compare-checkbox">
				<span class="product-item-compare-checkbox"><i class="icon-ok-b"></i></span>
			</label>
		</div>
	<?}?>
</div>