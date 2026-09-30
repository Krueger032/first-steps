<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if($arResult["SECTIONS_COUNT"] < 1)
	return;?>

<div class="catalog-section-list">
	<div class="row catalog-sections">
		<?foreach($arResult["SECTIONS"] as $arSection) {			
			$this->AddEditAction($arSection["ID"], $arSection["EDIT_LINK"], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_EDIT"));
			$this->AddDeleteAction($arSection["ID"], $arSection["DELETE_LINK"], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage("CT_BCSL_ELEMENT_DELETE_CONFIRM")));

			$sectionTitle = $arSection["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"] != ""
				? $arSection["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"]
				: $arSection["NAME"];

			$imgTitle = $arSection["IPROPERTY_VALUES"]["SECTION_PICTURE_FILE_TITLE"] != ""
				? $arSection["IPROPERTY_VALUES"]["SECTION_PICTURE_FILE_TITLE"]
				: $arSection["NAME"];
			
			$imgAlt = $arSection["IPROPERTY_VALUES"]["SECTION_PICTURE_FILE_ALT"] != ""
				? $arSection["IPROPERTY_VALUES"]["SECTION_PICTURE_FILE_ALT"]
				: $arSection["NAME"];?>

			<div class="col-xs-12 col-md-3<?=($arParams['SECTION_ROW'] != 4 ? ' col-lg-2' : '')?>">
				<a<?=($arParams["ADD_SECTION_TARGET"] == "Y" ? " target='_self'" : "")?> class="catalog-section-item" id="<?=$this->GetEditAreaId($arSection['ID'])?>" href="<?=$arSection['SECTION_PAGE_URL']?>" title="<?=$sectionTitle?>">
					<?if($arParams["COUNT_ELEMENTS"] && $arSection["ELEMENT_CNT"] > 0) {?>
						<span class="catalog-section-item__count"><?=$arSection["ELEMENT_CNT"]?></span>
					<?}?>
					<span class="catalog-section-item__graph-wrapper">
						<span class="catalog-section-item__graph">
							<?if(!empty($arSection["UF_ICON"])) {?>
								<i class="<?=$arSection['UF_ICON']?>" aria-hidden="true"></i>
							<?} elseif(is_array($arSection["PICTURE"])) {?>								
								<img src="<?=$arSection['PICTURE']['SRC']?>" width="<?=$arSection['PICTURE']['WIDTH']?>" height="<?=$arSection['PICTURE']['HEIGHT']?>" alt="<?=$imgAlt?>" title="<?=$imgTitle?>" />
							<?} else {?>
								<img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo.png" width="134" height="134" alt="<?=$imgAlt?>" title="<?=$imgTitle?>" />
							<?}?>
						</span>
					</span>
					<?if($arParams["HIDE_SECTION_NAME"] != "Y") {?>
						<span class="catalog-section-item__title"><?=$arSection["NAME"]?></span>
					<?}?>
				</a>
			</div>
		<?}
		unset($arSection);?>
	</div>
</div>