<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;

$elementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
$elementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
$elementDeleteParams = array("CONFIRM" => GetMessage("BRANDS_ITEM_DELETE_CONFIRM"));?>

<div class="brands-wrapper">
	<div class="container-ws">
		<div class="h1">
			<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
				array(
					"AREA_FILE_SHOW" => "file",
					"PATH" => SITE_DIR."include/block_brands_title.php"
				),
				$component
			);?>
		</div>
		<div class="row block-brands">			
			<?foreach($arResult["ITEMS"] as $arItem) {
				$this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], $elementEdit);
				$this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], $elementDelete, $elementDeleteParams);?>
				<div class="col-xs-6 col-md-2" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
					<a class="brands-item" href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>">
						<span class="brands-item-image">
							<?if(is_array($arItem["PREVIEW_PICTURE"])) {?>									
								<img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" width="<?=$arItem['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arItem['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arItem['NAME']?>" />
							<?}?>
						</span>
						<?if(!empty($arItem["MARKER"])) {?>
							<span class="brands-item-markers">
								<?foreach($arItem["MARKER"] as $key => $arMarker) {
									if($key <= 1) {?>
										<span class="brands-item-marker-container">
											<span class="brands-item-marker<?=(!empty($arMarker['FONT_SIZE']) ? ' brands-item-marker-'.$arMarker['FONT_SIZE'] : '')?>"<?=(!empty($arMarker["BACKGROUND_1"]) && !empty($arMarker["BACKGROUND_2"]) ? " style='background: ".$arMarker["BACKGROUND_2"]."; background: -webkit-linear-gradient(left, ".$arMarker["BACKGROUND_1"].", ".$arMarker["BACKGROUND_2"]."); background: -moz-linear-gradient(left, ".$arMarker["BACKGROUND_1"].", ".$arMarker["BACKGROUND_2"]."); background: -o-linear-gradient(left, ".$arMarker["BACKGROUND_1"].", ".$arMarker["BACKGROUND_2"]."); background: -ms-linear-gradient(left, ".$arMarker["BACKGROUND_1"].", ".$arMarker["BACKGROUND_2"]."); background: linear-gradient(to right, ".$arMarker["BACKGROUND_1"].", ".$arMarker["BACKGROUND_2"].");'" : (!empty($arMarker["BACKGROUND_1"]) && empty($arMarker["BACKGROUND_2"]) ? " style='background: ".$arMarker["BACKGROUND_1"].";'" : (empty($arMarker["BACKGROUND_1"]) && !empty($arMarker["BACKGROUND_2"]) ? " style='background: ".$arMarker["BACKGROUND_2"].";'" : "")))?>><?=(!empty($arMarker["ICON"]) ? "<i class='".$arMarker["ICON"]."'></i>" : "")?><span><?=$arMarker["NAME"]?></span></span>
										</span>
									<?} else {
										break;
									}
								}
								unset($key, $arMarker);?>
							</span>
						<?}?>
						<span class="brands-item-title"><?=$arItem["NAME"]?></span>						
						<?if(!empty($arItem["DISPLAY_PROPERTIES"]["COUNTRY"])) {?>
							<span class="brands-item-text"><?=strip_tags($arItem["DISPLAY_PROPERTIES"]["COUNTRY"]["DISPLAY_VALUE"])?></span>
						<?}?>
					</a>
				</div>
			<?}?>			
		</div>
		<?if($arParams["SHOW_ALL_LINK"] == "Y") {?>
			<div class="brands-buttons">
				<a class="btn btn-default" href="<?=$arParams['ALL_LINK_URL']?>" role="button"><?=$arParams["ALL_LINK_TITLE"]?></a>
			</div>
		<?}?>
	</div>
</div>