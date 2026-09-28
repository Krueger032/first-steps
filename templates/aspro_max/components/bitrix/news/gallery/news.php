<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?global $isHideLeftBlock, $arTheme, $APPLICATION;?>


<?$arTheme['LEFT_BLOCK']['VALUE'] = $isHideLeftBlock;?>

<?// intro text?>
<?ob_start();?>
	<?$APPLICATION->IncludeComponent(
		"bitrix:main.include",
		"",
		Array(
			"AREA_FILE_SHOW" => "page",
			"AREA_FILE_SUFFIX" => "inc",
			"EDIT_TEMPLATE" => ""
		)
	);?>
	<?$html = ob_get_clean();?>

<?if($html):?>	
	<div class="text_before_items">
		<?=$html?>
	</div>
<?endif;?>

<?
$arItemFilter = CMax::GetIBlockAllElementsFilter($arParams);

if ($arParams['CACHE_GROUPS'] == 'Y') {
	$arItemFilter['CHECK_PERMISSIONS'] = 'Y';
	$arItemFilter['GROUPS'] = $GLOBALS["USER"]->GetGroups();
}

$itemsCnt = CMaxCache::CIblockElement_GetList(array("CACHE" => array("TAG" => CMaxCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), $arItemFilter, array());?>

<?if (!$itemsCnt):?>
	<div class="alert alert-warning"><?=GetMessage("SECTION_EMPTY")?></div>
<?else:?>
	<?CMax::CheckComponentTemplatePageBlocksParams($arParams, __DIR__);?>

	<?// rss?>
	<?$this->SetViewTarget('product_share');?>
		<?if($arParams['USE_RSS'] !== 'N'):?>
			<div class="colored_theme_hover_bg-block">
				<?=CMax::ShowRSSIcon($arResult['FOLDER'].$arResult['URL_TEMPLATES']['rss']);?>
			</div>
		<?endif;?>
	<?$this->EndViewTarget();?>

	<?// show filter?>
	<?include('include/filter.php')?>

	<?if (CMax::checkAjaxRequest()):?>
		<?$APPLICATION->RestartBuffer()?>
	<?endif;?>

	<?// section elements?>
	<?$sViewElementsTemplate = ($arParams["SECTION_ELEMENTS_TYPE_VIEW"] == "FROM_MODULE" ? $arTheme["GALLERY_LIST_PAGE"]["VALUE"] : $arParams["SECTION_ELEMENTS_TYPE_VIEW"]);?>
	<?@include_once('page_blocks/'.$sViewElementsTemplate.'.php');?>

	<?if (CMax::checkAjaxRequest()):?>
		<?die()?>
	<?endif;?>
<?endif;?>