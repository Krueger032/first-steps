<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

if(!empty($arResult["DISPLAY_PROPERTIES"]["GALLERY"]["FULL_VALUE"])) {
	$this->addExternalCss(SITE_TEMPLATE_PATH."/js/fancybox/jquery.fancybox.css");
	$this->addExternalJS(SITE_TEMPLATE_PATH."/js/fancybox/jquery.fancybox.pack.js");
}

$mainId = $this->GetEditAreaId($arResult['ID']);
$obName = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $mainId);?>

<div class="news-detail" id="<?=$mainId?>">
	<?//ITEM//
	if(!empty($arResult["PREVIEW_TEXT"]) || is_array($arResult["DETAIL_PICTURE"]) || !empty($arResult["DETAIL_TEXT"])) {?>
		<div class="news-item-detail">
			<?//ITEM_PREVIEW_TEXT//
			if(!empty($arResult["PREVIEW_TEXT"])) {?>
				<div class="news-detail-preview-text"><?=$arResult["PREVIEW_TEXT"]?></div>
			<?}
			//ITEM_PIC//
			if(is_array($arResult["DETAIL_PICTURE"])) {?>				
				<div class="news-detail-pic-container">
					<div class="news-detail-pic">
						<img src="<?=$arResult['DETAIL_PICTURE']['SRC']?>" width="<?=$arResult['DETAIL_PICTURE']['WIDTH']?>" height="<?=$arResult['DETAIL_PICTURE']['HEIGHT']?>" alt="<?=$arResult['DETAIL_PICTURE']['ALT']?>" />
					</div>
				</div>
			<?}
			//ITEM_DETAIL_TEXT//
			if(!empty($arResult["DETAIL_TEXT"])) {?>
				<div class="news-detail-detail-text"><?=$arResult["DETAIL_TEXT"]?></div>
			<?}?>
		</div>
	<?}
	//GALLERY//
	if(!empty($arResult["DISPLAY_PROPERTIES"]["GALLERY"]["FULL_VALUE"])) {?>
		<div class="news-detail-gallery">
			<div class="container">
				<div class="row news-detail-gallery-items">
					<div class="col-xs-12">
						<div class="h2"><?=(!empty($arResult["DISPLAY_PROPERTIES"]["GALLERY_TITLE"]["VALUE"]) ? $arResult["DISPLAY_PROPERTIES"]["GALLERY_TITLE"]["VALUE"] : $arResult["DISPLAY_PROPERTIES"]["GALLERY"]["NAME"])?></div>
					</div>
					<?foreach($arResult["DISPLAY_PROPERTIES"]["GALLERY"]["FULL_VALUE"] as $val) {?>
						<div class="col-xs-6 col-md-3">				
							<a class="news-detail-gallery-item fancyimage" title="<?=$val['DESCRIPTION']?>" href="<?=$val['SRC']?>" data-fancybox-group="gallery">
								<span class="news-detail-gallery-item-image">
									<?if(!empty($val["SRC"])) {?>
										<img src="<?=$val['SRC']?>" width="<?=$val['WIDTH']?>" height="<?=$val['HEIGHT']?>" alt="<?=$val['DESCRIPTION']?>" />
									<?}?>
								</span>
								<?if(!empty($val["DESCRIPTION"])) {?>
									<span class="news-detail-gallery-item-caption-wrap">
										<span class="news-detail-gallery-item-caption">
											<span class="news-detail-gallery-item-title"><?=$val["DESCRIPTION"]?></span>
										</span>
									</span>
								<?}?>
							</a>
						</div>
					<?}
					unset($val);?>
				</div>
			</div>
		</div>
	<?}
	//FILES_DOCS//
	if(!empty($arResult["DISPLAY_PROPERTIES"]["FILES_DOCS"]["FULL_VALUE"])) {?>
		<div class="news-detail-files-docs">
			<div class="container">
				<div class="row news-detail-files-docs-items">
					<div class="col-xs-12">
						<div class="h2"><?=$arResult["DISPLAY_PROPERTIES"]["FILES_DOCS"]["NAME"]?></div>
					</div>
					<?foreach($arResult["DISPLAY_PROPERTIES"]["FILES_DOCS"]["FULL_VALUE"] as $val) {?><!--
						--><div class="col-xs-12 col-md-3">
							<a class="news-detail-files-docs-item" href="<?=$val['SRC']?>" target="_blank">
								<div class="news-detail-files-docs-icon" data-type="<?=$val['TYPE']?>"></div>
								<div class="news-detail-files-docs-block">
									<span class="news-detail-files-docs-name"><?=!empty($val["DESCRIPTION"]) ? $val["DESCRIPTION"] : $val["NAME"]?></span>
									<span class="news-detail-files-docs-size"><?=Loc::getMessage("NEWS_ITEM_DETAIL_SIZE").$val["SIZE"]?></span>
								</div>
							</a>
						</div><!--
					--><?}
					unset($val);?>
				</div>
			</div>
		</div>
	<?}
	//LAST_NEWS//
	if($arParams["SHOW_LAST_NEWS"] == "Y") {?>
		<div class="news-detail-last-news">
			<?$APPLICATION->IncludeComponent("bitrix:news.list", "block_news",
				array(
					"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
					"IBLOCK_ID" => $arParams["IBLOCK_ID"],
					"NEWS_COUNT" => "3",
					"SORT_BY1" => "SORT",
					"SORT_ORDER1" => "ASC",
					"SORT_BY2" => "ACTIVE_FROM",
					"SORT_ORDER2" => "DESC",
					"FILTER_NAME" => "",
					"FIELD_CODE" => array(),
					"PROPERTY_CODE" => array(),
					"CHECK_DATES" => $arParams["CHECK_DATES"],
					"DETAIL_URL" => "",
					"AJAX_MODE" => "",
					"AJAX_OPTION_SHADOW" => "",
					"AJAX_OPTION_JUMP" => "",
					"AJAX_OPTION_STYLE" => "",
					"AJAX_OPTION_HISTORY" => "",
					"CACHE_TYPE" => $arParams["CACHE_TYPE"],
					"CACHE_TIME" => $arParams["CACHE_TIME"],
					"CACHE_FILTER" => $arParams["CACHE_FILTER"],
					"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
					"PREVIEW_TRUNCATE_LEN" => "",
					"ACTIVE_DATE_FORMAT" => $arParams["LIST_ACTIVE_DATE_FORMAT"],
					"DISPLAY_PANEL" => "",
					"SET_TITLE" => "N",
					"SET_BROWSER_TITLE" => "N",
					"SET_META_KEYWORDS" => "N",
					"SET_META_DESCRIPTION" => "N",
					"SET_STATUS_404" => "N",
					"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
					"ADD_SECTIONS_CHAIN" => "",
					"HIDE_LINK_WHEN_NO_DETAIL" => "",
					"PARENT_SECTION" => "",
					"PARENT_SECTION_CODE" => "",
					"DISPLAY_NAME" => "",
					"DISPLAY_DATE" => "",
					"DISPLAY_TOP_PAGER" => "",
					"DISPLAY_BOTTOM_PAGER" => "",
					"PAGER_SHOW_ALWAYS" => "",
					"PAGER_TEMPLATE" => "",
					"PAGER_DESC_NUMBERING" => "",
					"PAGER_DESC_NUMBERING_CACHE_TIME" => "",
					"PAGER_SHOW_ALL" => "",
					"AJAX_OPTION_ADDITIONAL" => ""
				),
				$component,
				array("HIDE_ICONS" => "Y")
			);?>
		</div>
	<?}?>
</div>

<?$arJSParams = array(
	"VISUAL" => array(
		"ID" => $mainId
	)
);?>

<script type="text/javascript">
	var <?=$obName?> = new JCNewsDetail(<?=CUtil::PhpToJSObject($arJSParams, false, true)?>);
</script>

<?$component->arResult["RESULT_HTML"] = ob_get_clean();
$component->SetResultCacheKeys(
	array(
		"RESULT_HTML" => "RESULT_HTML"
	)
);