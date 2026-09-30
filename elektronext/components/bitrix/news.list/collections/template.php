<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc,
	Bitrix\Main\Grid\Declension;

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

$showLazyLoad = false;
if($arParams["NEWS_COUNT"] > 0 && $navParams["NavPageCount"] > 1) {	
	$showLazyLoad = $navParams["NavPageNomer"] != $navParams["NavPageCount"];
}

//ITEMS//
$elementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
$elementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
$elementDeleteParams = array("CONFIRM" => Loc::getMessage("COLLECTIONS_ITEM_DELETE_CONFIRM"));

$obName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $this->GetEditAreaId($navParams["NavNum"]));
$containerName = "container-".$navParams["NavNum"];?>

<div class="row collections" data-entity="<?=$containerName?>">	
	<!-- items-container -->
	<?foreach($arResult["ITEMS"] as $arItem) {
		$this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], $elementEdit);
		$this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], $elementDelete, $elementDeleteParams);?>
		<div class="col-xs-12 col-md-3" id="<?=$this->GetEditAreaId($arItem['ID'])?>" data-entity="item">
			<a class="collections-item" href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>">
				<span class="collections-item-pic">
					<?if(is_array($arItem["PREVIEW_PICTURE"])) {?>
						<img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" width="<?=$arItem['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arItem['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arItem['NAME']?>" />
					<?}?>
				</span>
				<span class="collections-item-block-container">
					<span class="collections-item-block">
						<span class="collections-item-title"><?=$arItem["NAME"]?></span>
						<?if(!empty($arItem["MIN_PRICE"])) {?>
							<span class="collections-item-price"><?=Loc::getMessage("COLLECTIONS_ITEM_PRICE", array("#PRICE#" => $arItem["MIN_PRICE"]))?></span>
						<?}
						if(!empty($arItem["BRAND"])) {?>
							<span class="collections-item-brand"><?=implode(", ", $arItem["BRAND"])?></span>
						<?}?>
					</span>
				</span>
				<span class="collections-item-icons<?=(!empty($arItem['MARKER']) && (!isset($arItem['COLORS']) || empty($arItem['COLORS'])) ? ' collections-item-icons-left' : (!empty($arItem['COLORS']) && (!isset($arItem['MARKER']) || empty($arItem['MARKER'])) ? ' collections-item-icons-right' : ''))?>">
					<?if(!empty($arItem["MARKER"])) {?>
						<span class="collections-item-icon">
							<?foreach($arItem["MARKER"] as $key => $arMarker) {
								if($key <= 2) {?>
									<span class="collections-item-marker-container">
										<span class="collections-item-marker<?=(!empty($arMarker['FONT_SIZE']) ? ' collections-item-marker-'.$arMarker['FONT_SIZE'] : '')?>"<?=(!empty($arMarker["BACKGROUND_1"]) && !empty($arMarker["BACKGROUND_2"]) ? " style='background: ".$arMarker["BACKGROUND_2"]."; background: -webkit-linear-gradient(left, ".$arMarker["BACKGROUND_1"].", ".$arMarker["BACKGROUND_2"]."); background: -moz-linear-gradient(left, ".$arMarker["BACKGROUND_1"].", ".$arMarker["BACKGROUND_2"]."); background: -o-linear-gradient(left, ".$arMarker["BACKGROUND_1"].", ".$arMarker["BACKGROUND_2"]."); background: -ms-linear-gradient(left, ".$arMarker["BACKGROUND_1"].", ".$arMarker["BACKGROUND_2"]."); background: linear-gradient(to right, ".$arMarker["BACKGROUND_1"].", ".$arMarker["BACKGROUND_2"].");'" : (!empty($arMarker["BACKGROUND_1"]) && empty($arMarker["BACKGROUND_2"]) ? " style='background: ".$arMarker["BACKGROUND_1"].";'" : (empty($arMarker["BACKGROUND_1"]) && !empty($arMarker["BACKGROUND_2"]) ? " style='background: ".$arMarker["BACKGROUND_2"].";'" : "")))?>><?=(!empty($arMarker["ICON"]) ? "<i class='".$arMarker["ICON"]."'></i>" : "")?><span><?=$arMarker["NAME"]?></span></span>
									</span>
								<?} else {
									break;
								}
							}
							unset($key, $arMarker);?>
						</span>
					<?}
					if(!empty($arItem["COLORS"])) {?>
						<span class="collections-item-icon">
							<?$arMaxColorsCount = !empty($arItem["MARKER"]) ? 3 : 6;
							$arColorsCount = count($arItem["COLORS"]);
							$arAddColors = $arColorsCount > $arMaxColorsCount ? $arColorsCount - $arMaxColorsCount : 0;?>							
							<span class="collections-item-colors">
								<?$arCounter = 1;
								foreach($arItem["COLORS"] as $arColor) {
									if($arCounter <= $arMaxColorsCount) {?>
										<span class="collections-item-color" title="<?=$arColor['NAME']?>" style="<?=(!empty($arColor['CODE']) ? 'background-color: #'.$arColor['CODE'].';' : (!empty($arColor['FILE']) ? 'background-image: url('.$arColor['FILE'].');' : ''));?>"></span>
									<?} else {
										break;
									}
									$arCounter++;
								}								
								unset($arColor, $arCounter);?>
							</span>
							<?if($arAddColors > 0) {
								$arColorsDeclension = new Declension(Loc::getMessage("COLLECTIONS_ITEM_COLOR"), Loc::getMessage("COLLECTIONS_ITEM_COLORS_1"), Loc::getMessage("COLLECTIONS_ITEM_COLORS_2"));?>
								<span class="collections-item-colors-add"><?="+ ".$arAddColors." ".($arColorsDeclension->get($arAddColors))?></span>
							<?}
							unset($arAddColors);?>
						</span>
					<?}?>					
				</span>
			</a>
		</div>
	<?}?>
	<!-- items-container -->
</div>

<?if($showLazyLoad) {?>
	<div class="collections-more" data-entity="collections-show-more-container">
		<button type="button" class="btn btn-more" data-use="show-more-<?=$navParams['NavNum']?>"><?=Loc::getMessage("COLLECTIONS_SHOW_MORE_ITEMS")?></button>
	</div>
<?}

if(!empty($GLOBALS[$arParams["FILTER_NAME"]]))
	$arParams["GLOBAL_FILTER"] = $GLOBALS[$arParams["FILTER_NAME"]];

$signer = new \Bitrix\Main\Security\Sign\Signer;
$signedTemplate = $signer->sign($templateName, 'news.list');
$signedParams = $signer->sign(base64_encode(serialize($arParams)), 'news.list');?>

<script type="text/javascript">	
	BX.message({
		COLLECTIONS_LOADING: '<?=GetMessageJS("COLLECTIONS_LOADING");?>'
	});
	var <?=$obName?> = new JCNewsListCollectComponent({		
		siteId: '<?=CUtil::JSEscape(SITE_ID)?>',
		templatePath: '<?=CUtil::JSEscape($templateFolder)?>',
		navParams: <?=CUtil::PhpToJSObject($navParams)?>,
		lazyLoad: '<?=$showLazyLoad?>',
		loadOnScroll: !!'<?=($arParams["LOAD_ON_SCROLL"] === "Y")?>',
		template: '<?=CUtil::JSEscape($signedTemplate)?>',
		parameters: '<?=CUtil::JSEscape($signedParams)?>',
		container: '<?=$containerName?>'
	});
</script>