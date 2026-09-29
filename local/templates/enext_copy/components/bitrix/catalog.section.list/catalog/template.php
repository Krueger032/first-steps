<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

/* отображать стандартные карточки категорий (по умолчанию - да)*/
$show_classic = true;

/* отображать карточки категорий в виде тегов (по умолчанию - нет)*/
$show_modified = false;

/*URL теущей страницы*/
$page = $APPLICATION->GetCurPage(false);

/* Проверяем, относится ли страница дочерней каталога */
$is_catalog_page = strripos($page, 'catalog');
if($is_catalog_page !== false && strlen($page) > 9) {
	$show_classic = false;
	$show_modified = true;
}

if($arResult["SECTIONS_COUNT"] < 1)
	return;?>

<div class="catalog-section-list">
	
	<div class="row catalog-sections with-spoiler <?if($show_modified === true) {echo 'categories-min';}?>">
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
			<? if($show_classic === true) : ?>
			<div class="col-xs-12 col-md-3<?=($arParams['SECTION_ROW'] != 4 ? ' col-lg-2' : '')?>">
				<a <?=($arParams["ADD_SECTION_TARGET"] == "Y" ? " target='_self'" : "")?> class="catalog-section-item " id="<?=$this->GetEditAreaId($arSection['ID'])?>" href="<?=$arSection['SECTION_PAGE_URL']?>" title="<?=$sectionTitle?>">
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
			<? endif; ?>
			
			<? if($show_modified === true) : ?>
			<div class="catalog-section-links category-link">
				<a <?=($arParams["ADD_SECTION_TARGET"] == "Y" ? " target='_self'" : "")?> class="catalog-section-link" id="<?=$this->GetEditAreaId($arSection['ID'])?>" href="<?=$arSection['SECTION_PAGE_URL']?>" title="<?=$sectionTitle?>">
					<?if(!empty($arSection["UF_ICON"])) {?>
						<i class="<?=$arSection['UF_ICON']?>" aria-hidden="true"></i>
					<?} elseif(is_array($arSection["PICTURE"])) {?>								
						<img class="catalog-section-link__img" src="<?=$arSection['PICTURE']['SRC']?>" width="<?=$arSection['PICTURE']['WIDTH']?>" height="<?=$arSection['PICTURE']['HEIGHT']?>" alt="<?=$imgAlt?>" title="<?=$imgTitle?>" />
					<?}?>
					<?if($arParams["HIDE_SECTION_NAME"] != "Y") {?>
						&nbsp;&nbsp;<?=$arSection["NAME"]?>
					<?}?>
				</a>
			</div>
			<? endif; ?>
		<?}
		unset($arSection);?>
	</div>
	<div class="catalog-section-links category-link show_more">
		<a class="catalog-section-link with-spoiler-button">Показать все</a>
	</div>
</div>
<!-- <script>
$(document).ready(function(){
    $('.catalog-section-links.category-link.show_more a').click(function(e){
		e.preventDefault();
		if ($('.row.catalog-sections.categories-min').hasClass('with-spoiler')) {
			$('.catalog-section-links.category-link.show_more a').text('Скрыть всe');
			$('.row.catalog-sections.categories-min').removeClass('with-spoiler');
		} else {
			$('.catalog-section-links.category-link.show_more a').text('Показать все');
			$('.row.catalog-sections.categories-min').addClass('with-spoiler');
		}
	});
});
</script> -->
