<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;

if(!empty($arResult["NAV_RESULT"])) {
	$navParams =  array(
		"NavPageCount" => !empty($arResult["NAV_RESULT"]->NavPageCount) ? $arResult["NAV_RESULT"]->NavPageCount : 1,
		"NavPageNomer" => !empty($arResult["NAV_RESULT"]->NavPageNomer) ? $arResult["NAV_RESULT"]->NavPageNomer : 1,
		"NavNum" => !empty($arResult["NAV_RESULT"]->NavNum) ? $arResult["NAV_RESULT"]->NavNum : $this->randString()
	);
} else {
	$navParams = array(
		"NavPageCount" => 1,
		"NavPageNomer" => 1,
		"NavNum" => $this->randString()
	);
}

$showBottomPager = false;
$showLazyLoad = false;
if($arParams["NEWS_COUNT"] > 0 && $navParams["NavPageCount"] > 1) {
	$showBottomPager = $arParams["DISPLAY_BOTTOM_PAGER"];
	$showLazyLoad = $navParams["NavPageNomer"] != $navParams["NavPageCount"];
}

//ITEMS//
$elementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
$elementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
$elementDeleteParams = array("CONFIRM" => Loc::getMessage("BRANDS_ITEM_DELETE_CONFIRM"));

$obName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $this->GetEditAreaId($navParams["NavNum"]));
$containerName = "container-".$navParams["NavNum"];?>

<div class="brands-countries-links">
	<div class="brands-country-link active" data-country-id="0"><?=Loc::getMessage("BRANDS_ITEMS_LINKS_ALL")?><span><?=$arResult["ITEMS_COUNT"]?></span></div>
	<?if(!empty($arResult["COUNTRIES"])) {
		foreach($arResult["COUNTRIES"] as $arCountry) {?>
			<div class="brands-country-link" data-country-id="<?=$arCountry['ID']?>"><?=$arCountry["NAME"]?><span><?=$arCountry["COUNT"]?></span></div>
		<?}
		unset($arCountry);
	}?>
</div>

<div class="brands-items-container">
	<!-- items-container -->
	<div class="row brands-items" data-entity="<?=$containerName?>">
		<?foreach($arResult["ITEMS"] as $arItem) {
			$this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], $elementEdit);
			$this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], $elementDelete, $elementDeleteParams);?>
			<div class="col-xs-6 col-md-2" id="<?=$this->GetEditAreaId($arItem['ID'])?>" data-entity="item">
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
	<!-- items-container -->

	<?if($showLazyLoad) {?>
		<!-- show-more-container -->
		<div class="brands-more" data-entity="brands-show-more-container">
			<button type="button" class="btn btn-more" data-use="show-more-<?=$navParams['NavNum']?>"><span><?=Loc::getMessage("BRANDS_SHOW_MORE_ITEMS")?></span></button>
		</div>
		<!-- show-more-container -->
	<?}

	if($showBottomPager) {?>
		<!-- pagination-container -->
		<div class="brands-pagination" data-pagination-page-count="<?=$navParams['NavPageCount']?>" data-pagination-page-nomer="<?=$navParams['NavPageNomer']?>" data-pagination-num="<?=$navParams['NavNum']?>">		
			<?=$arResult["NAV_STRING"]?>		
		</div>
		<!-- pagination-container -->
	<?}?>
</div>

<?if(!isset($arParams["PARENT_NAME"]) && $parent = $component->getParent()) {	
	$arParams["PARENT_NAME"] = $parent->getName();
	$arParams["PARENT_TEMPLATE_NAME"] = $parent->getTemplateName();
	$arParams["PARENT_TEMPLATE_PAGE"] = $parent->getTemplatePage();
}

$signer = new \Bitrix\Main\Security\Sign\Signer;
$signedTemplate = $signer->sign($templateName, 'news.list');
$signedParams = $signer->sign(base64_encode(serialize($arParams)), 'news.list');?>

<script type="text/javascript">	
	BX.message({
		BRANDS_LOADING: '<?=GetMessageJS("BRANDS_LOADING");?>'
	});
	var <?=$obName?> = new JCNewsListBrandsComponent({		
		siteId: '<?=CUtil::JSEscape(SITE_ID)?>',
		templatePath: '<?=CUtil::JSEscape($templateFolder)?>',
		navParams: <?=CUtil::PhpToJSObject($navParams)?>,
		lazyLoad: '<?=$showLazyLoad?>',		
		template: '<?=CUtil::JSEscape($signedTemplate)?>',
		parameters: '<?=CUtil::JSEscape($signedParams)?>',
		container: '<?=$containerName?>'
	});
</script>