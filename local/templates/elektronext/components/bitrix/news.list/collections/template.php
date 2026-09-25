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
$containerName = "container-".$navParams["NavNum"];

$getCollectionTemplatePropValues = function($value) {
	if(empty($value))
		return array();
	if(!is_array($value))
		return array($value);

	$result = array();
	foreach($value as $val) {
		if(!empty($val))
			$result[] = $val;
	}
	unset($val);

	return $result;
};

$getCollectionTemplateBrandId = function($brandValues) use (&$arParams) {
	$brandValues = array_map("intval", $brandValues);
	$contextBrandId = 0;

	if(!empty($GLOBALS["arCurrentBrandCollection"]["BRAND_ID"]))
		$contextBrandId = (int)$GLOBALS["arCurrentBrandCollection"]["BRAND_ID"];

	if($contextBrandId <= 0 && !empty($arParams["FILTER_NAME"]) && !empty($GLOBALS[$arParams["FILTER_NAME"]]["PROPERTY_BRAND"])) {
		$filterBrand = $GLOBALS[$arParams["FILTER_NAME"]]["PROPERTY_BRAND"];
		if(is_array($filterBrand))
			$filterBrand = reset($filterBrand);
		$contextBrandId = (int)$filterBrand;
	}

	if($contextBrandId > 0 && (empty($brandValues) || in_array($contextBrandId, $brandValues)))
		return $contextBrandId;

	return !empty($brandValues) ? (int)reset($brandValues) : 0;
};

$collectionElementBrandValues = array();
$getCollectionTemplateElementBrandValues = function($collectionId) use (&$arParams, &$collectionElementBrandValues) {
	$collectionId = (int)$collectionId;
	if($collectionId <= 0 || empty($arParams["IBLOCK_ID"]) || !Bitrix\Main\Loader::includeModule("iblock"))
		return array();

	if(!array_key_exists($collectionId, $collectionElementBrandValues)) {
		$collectionElementBrandValues[$collectionId] = array();
		$rsBrandProp = CIBlockElement::GetProperty($arParams["IBLOCK_ID"], $collectionId, array(), array("CODE" => "BRAND"));
		while($arBrandProp = $rsBrandProp->Fetch()) {
			if(!empty($arBrandProp["VALUE"]))
				$collectionElementBrandValues[$collectionId][] = (int)$arBrandProp["VALUE"];
		}
		unset($arBrandProp, $rsBrandProp);
	}

	return $collectionElementBrandValues[$collectionId];
};

$collectionBrandUrls = array();
$collectionFallbackPictures = array();?>

<div class="row collections" data-entity="<?=$containerName?>">	
	<!-- items-container -->
	<?foreach($arResult["ITEMS"] as $arItem) {
		$collectionDetailUrl = $arItem["DETAIL_PAGE_URL"];
		$collectionBrandId = 0;
		$collectionBrandValues = array();

		if(!empty($arItem["DISPLAY_PROPERTIES"]["BRAND"]["VALUE"]))
			$collectionBrandValues = $getCollectionTemplatePropValues($arItem["DISPLAY_PROPERTIES"]["BRAND"]["VALUE"]);
		elseif(!empty($arItem["PROPERTIES"]["BRAND"]["VALUE"]))
			$collectionBrandValues = $getCollectionTemplatePropValues($arItem["PROPERTIES"]["BRAND"]["VALUE"]);

		if(empty($collectionBrandValues))
			$collectionBrandValues = $getCollectionTemplateElementBrandValues($arItem["ID"]);

		$collectionBrandId = $getCollectionTemplateBrandId($collectionBrandValues);

		if(empty($collectionDetailUrl) && $collectionBrandId > 0) {
			if(!array_key_exists($collectionBrandId, $collectionBrandUrls)) {
				$collectionBrandUrls[$collectionBrandId] = "";
				$rsBrandElement = CIBlockElement::GetList(array(), array("ID" => $collectionBrandId), false, false, array("ID", "DETAIL_PAGE_URL"));
				if($arBrandElement = $rsBrandElement->GetNext())
					$collectionBrandUrls[$collectionBrandId] = $arBrandElement["~DETAIL_PAGE_URL"];
				unset($arBrandElement, $rsBrandElement);
			}

			if(!empty($collectionBrandUrls[$collectionBrandId]))
				$collectionDetailUrl = $collectionBrandUrls[$collectionBrandId].$arItem["CODE"]."/";
		}

		if(empty($arItem["PREVIEW_PICTURE"]) && !empty($arParams["CATALOG_IBLOCK_ID"]) && !empty($arItem["ID"])) {
			if(!array_key_exists($arItem["ID"], $collectionFallbackPictures)) {
				$collectionFallbackPictures[$arItem["ID"]] = false;
				$arPictureFilter = array(
					"ACTIVE" => "Y",
					"IBLOCK_ID" => $arParams["CATALOG_IBLOCK_ID"],
					"SECTION_GLOBAL_ACTIVE" => "Y",
					"PROPERTY_COLLECTION" => $arItem["ID"]
				);
				if($collectionBrandId > 0)
					$arPictureFilter["PROPERTY_BRAND"] = $collectionBrandId;

				$rsPictureElement = CIBlockElement::GetList(array("SORT" => "ASC", "ID" => "ASC"), $arPictureFilter, false, array("nTopCount" => 20), array("ID", "PREVIEW_PICTURE", "DETAIL_PICTURE"));
				while($arPictureElement = $rsPictureElement->GetNext()) {
					$pictureId = !empty($arPictureElement["PREVIEW_PICTURE"]) ? $arPictureElement["PREVIEW_PICTURE"] : $arPictureElement["DETAIL_PICTURE"];
					if($pictureId > 0) {
						$arFile = CFile::GetFileArray($pictureId);
						if(is_array($arFile)) {
							$collectionFallbackPictures[$arItem["ID"]] = $arFile;
							unset($arFile);
							break;
						}
						unset($arFile);
					}
				}
				unset($arPictureElement, $rsPictureElement, $arPictureFilter);
			}

			if(is_array($collectionFallbackPictures[$arItem["ID"]]))
				$arItem["PREVIEW_PICTURE"] = $collectionFallbackPictures[$arItem["ID"]];
		}

		$this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], $elementEdit);
		$this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], $elementDelete, $elementDeleteParams);
		?>
		<div class="col-xs-12 col-md-3" id="<?=$this->GetEditAreaId($arItem['ID'])?>" data-entity="item">
			<a class="collections-item-new" href="<?=$collectionDetailUrl?>" title="<?=$arItem['NAME']?>">
				<span class="collections-item-pic-new">
					<?if(is_array($arItem["PREVIEW_PICTURE"])) {?>
						<img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" width="<?=$arItem['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arItem['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arItem['NAME']?>" title="<?=$arItem['NAME']?>" />
					<?}?>
				</span>
				<span class="collections-item-block-container-new">
					<span class="collections-item-block-new">
						<span class="collections-item-title-new"><?=$arItem["NAME"]?></span>
						<?if(!empty($arItem["MIN_PRICE"])) {?>
							<span class="collections-item-price-new"><?=Loc::getMessage("COLLECTIONS_ITEM_PRICE", array("#PRICE#" => $arItem["MIN_PRICE"]))?></span>
						<?}?>
					</span>
				</span>
				<span class="collections-item-icons<?=(!empty($arItem['MARKER']) && (!isset($arItem['COLORS']) || empty($arItem['COLORS'])) ? ' collections-item-icons-left' : (!empty($arItem['COLORS']) && (!isset($arItem['MARKER']) || empty($arItem['MARKER'])) ? ' collections-item-icons-right' : ''))?>">
					<?if(!empty($arItem["MARKER"])) {?>
						<span class="collections-item-icon-new">
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
						<span class="collections-item-icon-new">
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
<span class="collections-item-btn">В коллекцию</span>
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
