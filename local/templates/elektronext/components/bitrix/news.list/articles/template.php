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
	$showBottomPager = $arParams["DISPLAY_BOTTOM_PAGER"] && $arParams["DISPLAY_PAGINATION"] != "N";
	$showLazyLoad = $navParams["NavPageNomer"] != $navParams["NavPageCount"];
}

//ITEMS//
$elementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
$elementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
$elementDeleteParams = array("CONFIRM" => Loc::getMessage("ARTICLES_ITEM_DELETE_CONFIRM"));

$obName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $this->GetEditAreaId($navParams["NavNum"]));
$containerName = "container-".$navParams["NavNum"];?>

<div class="row articles-items" data-entity="<?=$containerName?>">
	<!-- items-container -->
	<?foreach($arResult["ITEMS"] as $arItem) {
		$this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], $elementEdit);
		$this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], $elementDelete, $elementDeleteParams);?>
		<div class="col-xs-12 col-md-4" id="<?=$this->GetEditAreaId($arItem['ID'])?>" data-entity="item">
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
	<?}?>
	<!-- items-container -->
</div>

<?if($showLazyLoad) {?>
	<div class="articles-more" data-entity="articles-show-more-container">
		<button type="button" class="btn btn-more" data-use="show-more-<?=$navParams['NavNum']?>"><?=Loc::getMessage("ARTICLES_SHOW_MORE_ITEMS")?></button>
	</div>
<?}

if($showBottomPager) {?>
	<div class="articles-pagination" data-pagination-num="<?=$navParams['NavNum']?>">
		<!-- pagination-container -->
		<?=$arResult["NAV_STRING"]?>
		<!-- pagination-container -->
	</div>
<?}

if(!empty($GLOBALS[$arParams["FILTER_NAME"]]))
	$arParams["GLOBAL_FILTER"] = $GLOBALS[$arParams["FILTER_NAME"]];

if(!isset($arParams["PARENT_NAME"]) && $parent = $component->getParent()) {	
	$arParams["PARENT_NAME"] = $parent->getName();
	$arParams["PARENT_TEMPLATE_NAME"] = $parent->getTemplateName();
	$arParams["PARENT_TEMPLATE_PAGE"] = $parent->getTemplatePage();
}

$signer = new \Bitrix\Main\Security\Sign\Signer;
$signedTemplate = $signer->sign($templateName, "news.list");
$signedParams = $signer->sign(base64_encode(serialize($arParams)), "news.list");?>

<script type="text/javascript">	
	BX.message({		
		ARTICLES_LOADING: '<?=GetMessageJS("ARTICLES_LOADING");?>'
	});
	var <?=$obName?> = new JCNewsListArticlesComponent({		
		siteId: '<?=CUtil::JSEscape(SITE_ID)?>',
		templatePath: '<?=CUtil::JSEscape($templateFolder)?>',
		navParams: <?=CUtil::PhpToJSObject($navParams)?>,
		lazyLoad: '<?=$showLazyLoad?>',
		template: '<?=CUtil::JSEscape($signedTemplate)?>',
		parameters: '<?=CUtil::JSEscape($signedParams)?>',
		container: '<?=$containerName?>'
	});
</script>