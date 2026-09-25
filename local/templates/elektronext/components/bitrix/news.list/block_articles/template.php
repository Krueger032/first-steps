<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;

$elementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
$elementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
$elementDeleteParams = array("CONFIRM" => Loc::getMessage("ARTICLES_ITEM_DELETE_CONFIRM"));?>

<div class="articles-wrapper">
	<div class="container">				
		<div class="row articles">
			<div class="col-xs-12">
				<div class="h1">
					<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
						array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => SITE_DIR."include/block_articles_title.php"
						),
						$component
					);?>
				</div>
			</div>
			<?foreach($arResult["ITEMS"] as $arItem) {
				$this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], $elementEdit);
				$this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], $elementDelete, $elementDeleteParams);?>
				<div class="col-xs-12 col-md-4" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
					<a class="articles-item" href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>">
						<span class="articles-item-pic-container">
							<span class="articles-item-pic">
								<?if(is_array($arItem["PREVIEW_PICTURE"])) {?>
									<img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" width="<?=$arItem['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arItem['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arItem['NAME']?>" />
								<?}?>
							</span>
						</span>
						<span class="articles-item-background"></span>
						<span class="articles-item-block">
							<span class="articles-item-title"><?=$arItem["NAME"]?></span>
							<?if(!empty($arItem["PREVIEW_TEXT"])) {?>
								<span class="articles-item-text"><?=$arItem["PREVIEW_TEXT"]?></span>
							<?}?>
						</span>
						<?if(!empty($arItem["MARKER"])) {?>
							<span class="articles-item-marker-container">
								<span class="articles-item-marker"<?=(!empty($arItem["MARKER"]["BACKGROUND_1"]) && !empty($arItem["MARKER"]["BACKGROUND_2"]) ? " style='background: ".$arItem["MARKER"]["BACKGROUND_2"]."; background: -webkit-linear-gradient(left, ".$arItem["MARKER"]["BACKGROUND_1"].", ".$arItem["MARKER"]["BACKGROUND_2"]."); background: -moz-linear-gradient(left, ".$arItem["MARKER"]["BACKGROUND_1"].", ".$arItem["MARKER"]["BACKGROUND_2"]."); background: -o-linear-gradient(left, ".$arItem["MARKER"]["BACKGROUND_1"].", ".$arItem["MARKER"]["BACKGROUND_2"]."); background: -ms-linear-gradient(left, ".$arItem["MARKER"]["BACKGROUND_1"].", ".$arItem["MARKER"]["BACKGROUND_2"]."); background: linear-gradient(to right, ".$arItem["MARKER"]["BACKGROUND_1"].", ".$arItem["MARKER"]["BACKGROUND_2"].");'" : (!empty($arItem["MARKER"]["BACKGROUND_1"]) && empty($arItem["MARKER"]["BACKGROUND_2"]) ? " style='background: ".$arItem["MARKER"]["BACKGROUND_1"].";'" : (empty($arItem["MARKER"]["BACKGROUND_1"]) && !empty($arItem["MARKER"]["BACKGROUND_2"]) ? " style='background: ".$arItem["MARKER"]["BACKGROUND_2"].";'" : "")))?>><span><?=$arItem["MARKER"]["NAME"]?></span></span>
							</span>
						<?}?>
					</a>
				</div>
			<?}
			unset($arItem);
			if($arParams["SHOW_ALL_LINK"] == "Y") {?>
				<div class="col-xs-12">					
					<div class="articles-buttons">
						<a class="btn btn-default" href="<?=$arParams['ALL_LINK_URL']?>" role="button"><?=$arParams["ALL_LINK_TITLE"]?></a>
					</div>
				</div>
			<?}?>
		</div>
	</div>
</div>