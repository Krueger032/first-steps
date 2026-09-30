<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;?>

<div class="product-item">	
	<div class="product-item-image-wrapper" data-entity="image-wrapper">		
		<?//PREVIEW_PICTURE//?>
		<a target="_self" class="product-item-image" id="<?=$itemIds['PICT_ID']?>" href="<?=$item['DETAIL_PAGE_URL']?>" title="<?=$imgTitle?>"<?=($arParams["QUICK_VIEW"] == "FULL" ? " data-entity='quickView'" : "")?>>
			<?if(is_array($item['PREVIEW_PICTURE'])) {?>
				<img src="<?=$item['PREVIEW_PICTURE']['SRC']?>" width="<?=$item['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$item['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$imgAlt?>" title="<?=$imgTitle?>" />			
			<?} else {?>
				<img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo.png" width="222" height="222" alt="<?=$imgAlt?>" title="<?=$imgTitle?>" />
			<?}
			//MARKER//?>
			<div class="product-item-markers">
				<span class="product-item-marker-container">
					<span class="product-item-marker product-item-marker-discount product-item-marker-14px"><span><?=$arParams["TEXT_LABEL_GIFT"]?></span></span>
				</span>
			</div>
			<?//BRAND//			
			if(!empty($item['PROPERTIES']['BRAND']['FULL_VALUE']['PREVIEW_PICTURE'])) {?>
				<div class="product-item-brand">
					<img src="<?=$item['PROPERTIES']['BRAND']['FULL_VALUE']['PREVIEW_PICTURE']['SRC']?>" width="<?=$item['PROPERTIES']['BRAND']['FULL_VALUE']['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$item['PROPERTIES']['BRAND']['FULL_VALUE']['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$item['PROPERTIES']['BRAND']['FULL_VALUE']['NAME']?>" title="<?=$item['PROPERTIES']['BRAND']['FULL_VALUE']['NAME']?>" />
				</div>
			<?}?>
		</a>
		<?//QUICK_VIEW//
		if($arParams['QUICK_VIEW'] == 'CLASSICAL') {?>
			<div class="hidden-xs hidden-sm product-item-quick-view" id="<?=$itemIds['QUICK_VIEW_LINK']?>"><i class="icon-eye"></i><span><?=Loc::getMessage('CT_BCI_GIFT_TPL_MESS_QUICK_VIEW')?></span></div>
		<?}?>
	</div>
	<?//ARTICLE//
	if($haveOffers && (!$object || ($object && $objectContacts)) && !$partnersUrl && ($arParams['OFFERS_VIEW'] == 'PROPS' || $arParams['OFFERS_VIEW'] == 'DROPDOWN_LIST') && $arParams['PRODUCT_DISPLAY_MODE'] == 'Y') {
		$article = !empty($actualItem["PROPERTIES"]["ARTNUMBER"]["VALUE"]) ? $actualItem["PROPERTIES"]["ARTNUMBER"]["VALUE"] : false;?>
		<div class="product-item-article" id="<?=$itemIds['ARTICLE_ID']?>">
			<span class="product-item-article-name" data-entity="article-name"<?=($article ? '' : ' style="display:none;"')?>><?=Loc::getMessage('CT_BCI_GIFT_TPL_MESS_ARTICLE')?></span>
			<span class="product-item-article-val" data-entity="article-val"<?=($article ? '' : ' style="display:none;"')?>><?=($article ? $article : '')?></span>
		</div>
	<?} else {?>
		<div class="product-item-article">
			<?if(!empty($item["PROPERTIES"]["ARTNUMBER"]["VALUE"])) {?>
				<span class="product-item-article-name"><?=Loc::getMessage('CT_BCI_GIFT_TPL_MESS_ARTICLE')?></span>
				<span class="product-item-article-val"><?=$item["PROPERTIES"]["ARTNUMBER"]["VALUE"]?></span>
			<?}?>
		</div>
	<?}
	//TITLE//?>
	<div class="product-item-title">
		<a target="_self" href="<?=$item['DETAIL_PAGE_URL']?>" title="<?=$item['NAME']?>"<?=($arParams["QUICK_VIEW"] == "FULL" ? " data-entity='quickView'" : "")?>><?=$item['NAME']?></a>
	</div>	
	<div class="product-item-info-container">
		<div class="product-item-info-block">
			<?//SKU//
			if((!$object || ($object && $objectContacts)) && !$partnersUrl && $haveOffers && ($arParams['OFFERS_VIEW'] == 'PROPS' || $arParams['OFFERS_VIEW'] == 'DROPDOWN_LIST') && $arParams['PRODUCT_DISPLAY_MODE'] == 'Y' && !empty($item['OFFERS_PROP'])) {?>
				<div id="<?=$itemIds['TREE_ID']?>">
					<?foreach($arParams['SKU_PROPS'] as $skuProperty) {
						$propertyId = $skuProperty['ID'];
						$skuProperty['NAME'] = htmlspecialcharsbx($skuProperty['NAME']);
						if(!isset($item['SKU_TREE_VALUES'][$propertyId]))
							continue;?>
						<div class="product-item-hidden" data-entity="sku-block">
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

													if($skuProperty['SHOW_MODE'] === 'PICT') {?>
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
				if($arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y' && !empty($item['PRODUCT_PROPERTIES'])) {?>
					<div class="product-item-hidden" id="<?=$itemIds['BASKET_PROP_DIV']?>">
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
										<?if($item['PROPERTIES'][$propId]['PROPERTY_TYPE'] === 'L' && $item['PROPERTIES'][$propId]['LIST_TYPE'] === 'C') {?>
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
			<div class="product-item-info">
				<div class="product-item-blocks">
					<?//PRICE//?>
					<div class="product-item-price-container" data-entity="price-block">
						<div id="<?=$itemIds['PRICE_ID']?>">
							<?if(!empty($price)) {
								if($haveOffers && (($object && !$objectContacts) || $partnersUrl || ($arParams['OFFERS_VIEW'] != 'PROPS' && $arParams['OFFERS_VIEW'] != 'DROPDOWN_LIST') || $arParams['PRODUCT_DISPLAY_MODE'] == 'N')) {?>
									<span class="product-item-price-from"><?=Loc::getMessage('CT_BCI_GIFT_TPL_MESS_PRICE_FROM')?></span>
									<span class="product-item-price-current"><?=$price['PRINT_PRICE']?></span>
									<?if($arParams['OFFERS_VIEW'] == 'LIST') {?>
										<span class="product-item-price-measure">/<?=$actualItem['ITEM_MEASURE']['TITLE']?></span>
									<?}
								} else {?>
									<span class="product-item-price-current" data-entity="price-current"><?=$price['PRINT_PRICE']?></span>
									<span class="product-item-price-measure" data-entity="price-measure">/<?=$actualItem['ITEM_MEASURE']['TITLE']?></span>
								<?}
							}?>
						</div>
						<div class="product-item-price-old" id="<?=$itemIds['OLD_PRICE_ID']?>"<?=($price['BASE_PRICE'] > 0 ? '' : ' style="display:none;"')?>><?=($price['BASE_PRICE'] > 0 ? $price['PRINT_BASE_PRICE'] : '')?></div>
					</div>
					<?//OFFERS_COUNT//
					if($haveOffers && $arParams['OFFERS_VIEW'] == 'OBJECTS') {
						$offersDeclension = new Bitrix\Main\Grid\Declension(Loc::getMessage("CT_BCI_GIFT_TPL_MESS_OFFER"), Loc::getMessage("CT_BCI_GIFT_TPL_MESS_OFFERS_1"), Loc::getMessage("CT_BCI_GIFT_TPL_MESS_OFFERS_2"));?>
						<div class="product-item-offers-count"><?=count($item['OFFERS']).' '.$offersDeclension->get(count($item['OFFERS']))?></div>
					<?}?>
				</div>
				<?//BUTTONS//?>
				<div class="product-item-button-container" data-entity="buttons-block">			
					<?if(($object && !$objectContacts) || $partnersUrl || ($haveOffers && (($arParams['OFFERS_VIEW'] != 'PROPS' && $arParams['OFFERS_VIEW'] != 'DROPDOWN_LIST') || $arParams['PRODUCT_DISPLAY_MODE'] != 'Y')) || $arParams['DISABLE_BASKET']) {?>
						<a target="<?=$item['TARGET']?>" class="btn btn-buy" href="<?=$item['DETAIL_PAGE_URL']?>" title="<?=$arParams['MESS_BTN_DETAIL']?>"<?=($arParams["QUICK_VIEW"] == "FULL" ? " data-entity='quickView'" : "")?>><i class="icon-arrow-right"></i><span><?=$arParams['MESS_BTN_DETAIL']?></span></a>
					<?} else {
						if(!$haveOffers) {
							if($actualItem['CAN_BUY'] || (!$actualItem['CAN_BUY'] && !$arParams['UNDER_ORDER'] && !$showSubscribe)) {?>
								<div id="<?=$itemIds['BASKET_ACTIONS_ID']?>">
									<button type="button" class="btn btn-buy" id="<?=$itemIds['BUY_LINK']?>" title="<?=($arParams['ADD_TO_BASKET_ACTION'] === 'BUY' ? $arParams['GIFTS_MESS_BTN_BUY'] : $arParams['GIFTS_MESS_BTN_ADD_TO_BASKET'])?>"<?=($actualItem['CAN_BUY'] ? '' : ' disabled="disabled"')?>><i class="icon-cart"></i><span><?=($arParams['ADD_TO_BASKET_ACTION'] == 'BUY' ? $arParams['GIFT_MESS_BTN_BUY'] : $arParams['GIFT_MESS_BTN_ADD_TO_BASKET'])?></span></button>
								</div>
							<?} elseif(!$actualItem['CAN_BUY']) {
								if($arParams['UNDER_ORDER']) {?>
									<button type="button" class="btn btn-default" id="<?=$itemIds['NOT_AVAILABLE_LINK']?>" title="<?=Loc::getMessage('CT_BCI_GIFT_TPL_MESS_UNDER_ORDER')?>"><i class="icon-clock"></i><span><?=Loc::getMessage('CT_BCI_GIFT_TPL_MESS_UNDER_ORDER')?></span></button>
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
						} else {?>
							<div id="<?=$itemIds['BASKET_ACTIONS_ID']?>">
								<button type="button" class="btn btn-buy" id="<?=$itemIds['BUY_LINK']?>" title="<?=($arParams['ADD_TO_BASKET_ACTION'] == 'BUY' ? $arParams['GIFTS_MESS_BTN_BUY'] : $arParams['GIFTS_MESS_BTN_ADD_TO_BASKET'])?>"<?=(!$offerPartnersUrl ? ($actualItem['CAN_BUY'] ? '' : ($arParams['UNDER_ORDER'] || $showSubscribe ? ' style="display: none;"' : ' disabled="disabled"')) : ' style="display: none;"')?>><i class="icon-cart"></i><span><?=($arParams['ADD_TO_BASKET_ACTION'] == 'BUY' ? $arParams['GIFTS_MESS_BTN_BUY'] : $arParams['GIFTS_MESS_BTN_ADD_TO_BASKET'])?></span></button>
							</div>
							<?if($arParams['UNDER_ORDER']) {?>
								<button type="button" class="btn btn-default" id="<?=$itemIds['NOT_AVAILABLE_LINK']?>" title="<?=Loc::getMessage('CT_BCI_GIFT_TPL_MESS_UNDER_ORDER')?>"<?=(!$offerPartnersUrl && !$actualItem['CAN_BUY'] ? '' : ' style="display: none;"')?>><i class="icon-clock"></i><span><?=Loc::getMessage('CT_BCI_GIFT_TPL_MESS_UNDER_ORDER')?></span></button>
							<?}?>
							<a target="_blank" class="btn btn-buy" id="<?=$itemIds['MORE_LINK']?>" href="<?=$item['DETAIL_PAGE_URL']?>" title="<?=$arParams['MESS_BTN_DETAIL']?>"<?=($arParams["QUICK_VIEW"] == "FULL" ? " data-entity='quickView'" : "").($offerPartnersUrl && $actualItem["CAN_BUY"] ? '' : ' style="display: none;"')?>><i class="icon-arrow-right"></i></a>
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
		</div>
	</div>
</div>