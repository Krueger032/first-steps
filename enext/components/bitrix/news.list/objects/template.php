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

$slidePanelMode = isset($arParams["SLIDE_PANEL_MODE"]) && $arParams["SLIDE_PANEL_MODE"] == "Y" ? true : false;
$prefix = !!$slidePanelMode ? "slide-panel-" : "";

$showBottomPager = false;
$showLazyLoad = false;
if($arParams["NEWS_COUNT"] > 0 && $navParams["NavPageCount"] > 1) {
	if(!$slidePanelMode)
		$showBottomPager = $arParams["DISPLAY_BOTTOM_PAGER"];
	$showLazyLoad = $navParams["NavPageNomer"] != $navParams["NavPageCount"];
}

//ITEMS//
$elementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
$elementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
$elementDeleteParams = array("CONFIRM" => Loc::getMessage("OBJECT_ITEM_DELETE_CONFIRM"));

$obName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $this->GetEditAreaId($navParams["NavNum"]));
$containerName = $prefix."container-".$navParams["NavNum"];?>

<script type="text/javascript">	
	BX.message({
		OBJECT_ITEM_TODAY: '<?=GetMessageJS("OBJECT_ITEM_TODAY")?>',
		OBJECT_ITEM_24_HOURS: '<?=GetMessageJS("OBJECT_ITEM_24_HOURS")?>',
		OBJECT_ITEM_OFF: '<?=GetMessageJS("OBJECT_ITEM_OFF")?>',
		OBJECT_ITEM_BREAK: '<?=GetMessageJS("OBJECT_ITEM_BREAK")?>',
		OBJECT_ITEM_SEE_REVIEWS: '<?=GetMessageJS("OBJECT_ITEM_SEE_REVIEWS")?>',
		OBJECT_ITEM_ADD_REVIEW: '<?=GetMessageJS("OBJECT_ITEM_ADD_REVIEW")?>',
		OBJECTS_LOADING: '<?=GetMessageJS("OBJECTS_LOADING")?>',
		OBJECTS_TEMPLATE_PATH: '<?=CUtil::JSEscape($templateFolder)?>'
	});
</script>

<div class="row objects<?=(!!$slidePanelMode ? ' '.$prefix.'objects' : '')?>" data-entity="<?=$containerName?>">	
	<?if(!empty($arParams["ITEMS_TITLE"])) {?>
		<div class="col-xs-12">
			<div class="h2"><?=$arParams["ITEMS_TITLE"]?></div>
		</div>
	<?}?>
	<!-- items-container -->
	<?foreach($arResult["ITEMS"] as $arItem) {
		$this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], $elementEdit);
		$this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], $elementDelete, $elementDeleteParams);		
		
		$strMainID = $this->GetEditAreaId($arItem["ID"]);		
		$strObName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);?>
		
		<div class="col-xs-12" id="<?=$strMainID?>" data-entity="item">
			<div class="object-item" data-object-id="<?=$arItem['ID']?>">
				<div class="object-item-image">
					<?if(!empty($arItem["PREVIEW_PICTURE"])) {?>
						<img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" width="<?=$arItem['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arItem['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arItem['NAME']?>" />
					<?} else {?>
						<img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo.png" width="120" height="120" alt="<?=$arItem['NAME']?>" />
					<?}?>
				</div>
				<div class="object-item-caption">
					<div class="object-item-title">
						<?if(!$arItem["HIDE_IN_OBJECTS_LIST"]) {?>
							<a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem["NAME"]?></a>
						<?} else {?>
							<span><?=$arItem["NAME"]?></span>
						<?}?>
					</div>
					<?if(!empty($arItem["ADDRESS"])) {?>
						<div class="object-item-address"><i class="icon-map-marker"></i><span><?=$arItem["ADDRESS"]?></span></div>
					<?}?>
					<div class="object-item-hours object-item-hours-hidden"></div>
					<?if(isset($arItem["REVIEWS_COUNT"]) && $arItem["REVIEWS_COUNT"] > 0) {?>
						<div class="object-item-rating">
							<div class="object-item-rating-val"<?=($arItem["RATING_VALUE"] <= 4.4 ? " data-rate='".intval($arItem["RATING_VALUE"])."'" : "")?>><?=$arItem["RATING_VALUE"]?></div>
							<div class="object-item-rating-reviews-count"><?=$arItem["REVIEWS_COUNT"]." ".$arItem["REVIEWS_DECLENSION"]?></div>
						</div>
					<?}
					if(!empty($arItem["PREVIEW_TEXT"])) {?>
						<div class="object-item-descr"><?=$arItem["PREVIEW_TEXT"]?></div>
					<?}
					if(!$slidePanelMode && !$arItem["HIDE_IN_OBJECTS_LIST"] && (!empty($arItem["PROMOTIONS_IDS"]) || !empty($arItem["PRODUCTS_IDS"]) || !empty($arItem["TOUR_3D"]) || !empty($arItem["AFFILIATES"]) || (isset($arItem["REVIEWS_COUNT"]) && $arItem["REVIEWS_COUNT"] > 0))) {?>
						<div class="object-item-links">						
							<?if(!empty($arItem["PROMOTIONS_IDS"])) {?>
								<a class="object-item-link" href="<?=$arItem['DETAIL_PAGE_URL']?>#promotions"><span><?=Loc::getMessage("OBJECT_ITEM_PROMOTIONS")?></span><span class="object-item-count"><?=count($arItem["PROMOTIONS_IDS"])?></span></a>
							<?}
							if(!empty($arItem["PRODUCTS_IDS"])) {?>
								<a class="object-item-link" href="<?=$arItem['DETAIL_PAGE_URL']?>#products"><span><?=Loc::getMessage("OBJECT_ITEM_PRODUCTS")?></span><span class="object-item-count"><?=count($arItem["PRODUCTS_IDS"])?></span></a>
							<?}
							if(!empty($arItem["TOUR_3D"])) {?>
								<a class="object-item-link" href="<?=$arItem['DETAIL_PAGE_URL']?>#3d-tour"><span><?=$arItem["TOUR_3D"]["NAME"]?></span></a>
							<?}
							if(!empty($arItem["AFFILIATES"])) {?>
								<a class="object-item-link" href="<?=$arItem['DETAIL_PAGE_URL']?>#affiliates"><span><?=$arItem["AFFILIATES"]["NAME"]?></span><span class="object-item-count"><?=count($arItem["AFFILIATES"]["VALUE"])?></span></a>
							<?}
							if(isset($arItem["REVIEWS_COUNT"]) && $arItem["REVIEWS_COUNT"] > 0) {?>
								<a class="object-item-link" href="<?=$arItem['DETAIL_PAGE_URL']?>#reviews"><span><?=Loc::getMessage("OBJECT_ITEM_REVIEWS")?></span><span class="object-item-count"><?=$arItem["REVIEWS_COUNT"]?></span></a>
							<?}?>
						</div>
					<?}?>
				</div>
				<div class="object-item-contacts">
					<button type="button" class="object-item-btn"><i class="icon-phone-call"></i></button>
				</div>
			</div>
			<?$arJSParams = array(				
				"ITEM" => array(
					"ID" => $arItem["ID"],
					"NAME" => $arItem["NAME"],
					"ADDRESS" => $arItem["ADDRESS"],
					"TIMEZONE" => $arItem["TIMEZONE"],
					"WORKING_HOURS" => $arItem["WORKING_HOURS"],					
					"PHONE" => $arItem["PHONE"],
					"WHATSAPP" => $arItem["WHATSAPP"],
					"VIBER" => $arItem["VIBER"],
					"TELEGRAM" => $arItem["TELEGRAM"],
					"INSTAGRAM" => $arItem["INSTAGRAM"],
					"EMAIL" => $arItem["EMAIL"],
					"SKYPE" => $arItem["SKYPE"],
					"CALLBACK_FORM" => $arItem["PHONE_SMS"] || $arItem["EMAIL_EMAIL"] ? true : false
				),
				"VISUAL" => array(
					"ID" => $strMainID
				)
			);
			if($arParams["USE_REVIEW"] != "N") {
				$arJSParams["ITEM"]["BTN_ADD_REVIEW"] = true;
				$arJSParams["ITEM"]["REVIEWS_PAGE_LINK"] = $arItem["DETAIL_PAGE_URL"]."#reviews";
				if(isset($arItem["REVIEWS_COUNT"]) && $arItem["REVIEWS_COUNT"] > 0) {
					$arJSParams["ITEM"]["RATING_VALUE"] = $arItem["RATING_VALUE"];
					$arJSParams["ITEM"]["REVIEWS_COUNT"] = $arItem["REVIEWS_COUNT"];
					$arJSParams["ITEM"]["REVIEWS_DECLENSION"] = $arItem["REVIEWS_DECLENSION"];
				}
			}?>
			<script type="text/javascript">
				var <?=$strObName;?> = new JCNewsListObjects(<?=CUtil::PhpToJSObject($arJSParams, false, true);?>);
			</script>
		</div>		
	<?}?>
	<!-- items-container -->	
</div>

<?if($showLazyLoad) {?>
	<div class="<?=$prefix?>objects-more" data-entity="<?=$prefix?>objects-show-more-container">
		<button type="button" class="btn btn-more" data-use="<?=$prefix?>objects-show-more-<?=$navParams['NavNum']?>"><?=Loc::getMessage("OBJECTS_SHOW_MORE_ITEMS")?></button>
	</div>
<?}

if($showBottomPager) {?>
	<div class="objects-pagination" data-pagination-num="<?=$navParams['NavNum']?>">
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
	var <?=$obName?> = new JCNewsListObjectsComponent({		
		siteId: '<?=CUtil::JSEscape(SITE_ID)?>',		
		navParams: <?=CUtil::PhpToJSObject($navParams)?>,
		lazyLoad: '<?=$showLazyLoad?>',
		prefix: '<?=$prefix?>',
		template: '<?=CUtil::JSEscape($signedTemplate)?>',
		parameters: '<?=CUtil::JSEscape($signedParams)?>',
		container: '<?=$containerName?>'
	});
</script>