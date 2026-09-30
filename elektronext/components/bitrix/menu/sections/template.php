<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

if(count($arResult) < 1)
	return;?>

<div class="catalog-sections-wrapper">	
	<div class="container-ws">
		<div class="h1">
			<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
				array(
					"AREA_FILE_SHOW" => "file",
					"PATH" => SITE_DIR."include/block_catalog_sections_title.php"
				),
				false
			);?>
		</div>
		<div class="row block-catalog-sections">
			<?foreach($arResult as $arItem) {
				if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1)
					continue;?>
				<div class="col-xs-12 col-md-2">
					<a href="<?=$arItem['LINK']?>" class="catalog-section-item">
						<?if($arItem['PARAMS']['ELEMENT_CNT'] > 0) {?>
							<span class="catalog-section-item__count"><?=$arItem["PARAMS"]["ELEMENT_CNT"]?></span>
						<?}?>
						<span class="catalog-section-item__graph-wrapper">
							<span class="catalog-section-item__graph<?=(!empty($arItem["PARAMS"]["ICON"]) || is_array($arItem["PARAMS"]["PICTURE"]) ? '' : ' empty')?>">
								<?if(!empty($arItem["PARAMS"]["ICON"])) {?>									
									<i class="<?=$arItem['PARAMS']['ICON']?>" aria-hidden="true"></i>									
								<?} elseif(is_array($arItem["PARAMS"]["PICTURE"])) {?>									
									<img src="<?=$arItem['PARAMS']['PICTURE']['SRC']?>" width="<?=$arItem['PARAMS']['PICTURE']['WIDTH']?>" height="<?=$arItem['PARAMS']['PICTURE']['HEIGHT']?>" alt="<?=$arItem['PARAMS']['PICTURE']['ALT']?>" title="<?=$arItem['PARAMS']['PICTURE']['TITLE']?>" />									
								<?} else {?>
									<img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo.png" width="134" height="134" alt="<?=$arItem['PARAMS']['PICTURE']['ALT']?>" title="<?=$arItem['PARAMS']['PICTURE']['TITLE']?>" />
								<?}?>
							</span>
						</span>
						<span class="catalog-section-item__title"><?=$arItem["TEXT"]?></span>
					</a>	
				</div>
			<?}?>
		</div>
	</div>
</div>