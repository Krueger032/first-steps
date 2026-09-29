<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

if(isset($arResult['ITEM'])) {
	$item = $arResult['ITEM'];
	$areaId = $arResult['AREA_ID'];
	
	$productTitle = isset($item['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) && $item['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] != '' ? $item['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] : $item['NAME'];

	$imgTitle = isset($item['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']) && $item['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE'] != '' ? $item['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE'] : $item['NAME'];

	$imgAlt = isset($item["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"]) && $item["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] != "" ? $item["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] : $item["NAME"];?>
	
	<a class="product-item-viewed" id="<?=$areaId?>" href="<?=$item['DETAIL_PAGE_URL']?>">
		<?//PREVIEW_PICTURE//?>
		<span class="product-item-viewed-image">
			<?if(is_array($item["PREVIEW_PICTURE"])) {?>
				<img src="<?=$item['PREVIEW_PICTURE']['SRC']?>" width="<?=$item['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$item['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$imgAlt?>" />
			<?} else {?>
				<img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo.png" width="70" height="70" alt="<?=$imgAlt?>" title="<?=$imgTitle?>" />
			<?}?>
		</span>
		<?//TOOLTIP//?>
		<span class="visible-md visible-lg product-item-viewed-tooltip"><?=$productTitle?></span>
	</a>
	<?unset($item);
}