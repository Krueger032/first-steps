<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(empty($arResult["ELEMENTS"]))
	return;?>

<div class="bx_searche">
	<?foreach($arResult["CATEGORIES"] as $category_id => $arCategory) {
		if($category_id === "all") {
			foreach($arCategory["ITEMS"] as $arItem) {?>
				<div class="bx_item_block all_result">					
					<div class="bx_item_element">
						<a class="btn btn-default" href="<?=$arItem['URL']?>" role="button"><?=$arItem["NAME"]?></a>
					</div>
				</div>
			<?}
			unset($arItem);
		} else {
			foreach($arResult["ELEMENTS"] as $arElement) {?>
				<div class="bx_item_block">
					<div class="bx_img_element">
						<?if(!empty($arElement["UF_ICON"])) {?>
							<i class="<?=$arElement['UF_ICON']?>" aria-hidden="true"></i>
						<?} elseif(is_array($arElement["PREVIEW_PICTURE"])) {?>
							<img src="<?=$arElement['PREVIEW_PICTURE']['src']?>" width="<?=$arElement['PREVIEW_PICTURE']['width']?>" height="<?=$arElement['PREVIEW_PICTURE']['height']?>" />
						<?} else {?>
							<img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo.png" width="50" height="50" />
						<?}?>
					</div>
					<div class="bx_item_element">
						<div class="bx_title">
							<?if(!empty($arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"])) {?>
								<div class="bx_article"><?=GetMessage("CT_BST_ARTNUMBER").$arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"]?></div>
							<?}?>
							<a target="_blank" href="<?=$arElement['URL']?>"><?=$arElement["NAME"]?></a>
							<?if(!empty($arElement["DISPLAY_PROPERTIES"])) {
								foreach($arElement["DISPLAY_PROPERTIES"] as $prop) {?>
									<div class="bx_properties"><?=$prop["NAME"].": ".strip_tags($prop["DISPLAY_VALUE"])?></div>
								<?}
								unset($prop);
							}?>
						</div>
						<?if(!empty($arElement["MIN_PRICE"])) {
							if($arElement["MIN_PRICE"]["DISCOUNT_VALUE"] < $arElement["MIN_PRICE"]["VALUE"]) {?>
								<div class="bx_price">
									<?=($arElement["MIN_PRICE"]["OFFERS"] ? GetMessage("CT_BST_PRICE_FROM") : "").$arElement["MIN_PRICE"]["PRINT_DISCOUNT_VALUE"]?>
									<div class="bx_price_old"><?=$arElement["MIN_PRICE"]["PRINT_VALUE"]?></div>
								</div>
							<?} else {?>
								<div class="bx_price"><?=$arElement["MIN_PRICE"]["PRINT_DISCOUNT_VALUE"]?></div>
							<?}
						}?>
					</div>
				</div>
			<?}
			unset($arElement);
		}
	}
	unset($category_id, $arCategory);?>
</div>