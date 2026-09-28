<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);

global $arTheme, $APPLICATION;

$APPLICATION->SetPageProperty('MENU', 'N');

// get section items count and subsections
$arItemFilter = CMax::GetCurrentSectionElementFilter($arResult["VARIABLES"], $arParams);
$arSectionFilter = CMax::GetCurrentSectionFilter($arResult["VARIABLES"], $arParams);
$itemsCnt = CMaxCache::CIBLockElement_GetList(array("CACHE" => array("TAG" => CMaxCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), $arItemFilter, array());
$arSection = CMaxCache::CIblockSection_GetList(array("CACHE" => array("TAG" => CMaxCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]), "MULTI" => "N")), $arSectionFilter, false, array('ID', 'NAME', 'DESCRIPTION', 'PICTURE', 'DETAIL_PICTURE', 'IBLOCK_ID'));
?>
<?if(!$arSection && $arParams['SET_STATUS_404'] !== 'Y'):?>
	<div class="alert alert-warning"><?=GetMessage("SECTION_NOTFOUND")?></div>
<?elseif(!$arSection && $arParams['SET_STATUS_404'] === 'Y'):?>
	<?CMax::goto404Page();?>
<?else:?>
	<?CMax::CheckComponentTemplatePageBlocksParams($arParams, __DIR__);?>

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
	<?$sViewElementsTemplate = ($arParams["SECTION_ELEMENTS_TYPE_VIEW"] == "FROM_MODULE" ? $arTheme["NEWS_PAGE"]["VALUE"] : $arParams["SECTION_ELEMENTS_TYPE_VIEW"]);?>
	<?@include_once('page_blocks/'.$sViewElementsTemplate.'.php');?>

	<?if (CMax::checkAjaxRequest()):?>
		<?die()?>
	<?endif;?>
<?endif;?>