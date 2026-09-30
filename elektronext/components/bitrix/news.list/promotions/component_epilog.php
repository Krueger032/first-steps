<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if(!isset($arResult["CACHE_ITEMS"]) || empty($arResult["CACHE_ITEMS"]))
	return;

use Bitrix\Main\Loader,
	Bitrix\Main\Localization\Loc;

if(!Loader::includeModule("iblock"))
	return;

Loc::loadMessages(__FILE__);

$currentDateTime = time() + CTimeZone::GetOffset();

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

//CACHE_ITEMS//
$elementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
$elementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
$elementDeleteParams = array("CONFIRM" => Loc::getMessage("PROMOTIONS_ITEM_DELETE_CONFIRM"));

$obName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $this->GetEditAreaId($navParams["NavNum"]));
$containerName = "container-".$navParams["NavNum"];?>

<div class="row promotions" data-entity="<?=$containerName?>">
	<!-- items-container -->
	<?foreach($arResult["CACHE_ITEMS"] as $arItem) {
		$this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], $elementEdit);
		$this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], $elementDelete, $elementDeleteParams);

		$strMainID = $this->GetEditAreaId($arItem["ID"]);		
		$strObName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);

		$itemCompleted = false;
		if(!empty($arItem["ACTIVE_TO"]) && $currentDateTime >= strtotime($arItem["ACTIVE_TO"])) {
			$itemCompleted = true;
		}?>

		<div class="col-xs-12 col-md-4" id="<?=$strMainID?>" data-entity="item">			
			<a class="promotions-item<?=($itemCompleted ? ' promotions-item-completed' : '')?>" href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>">
				<span class="promotions-item-pic">
					<?if(is_array($arItem["PREVIEW_PICTURE"])) {?>
						<img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" width="<?=$arItem['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arItem['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arItem['NAME']?>" />
					<?}?>
				</span>
				<span class="promotions-item-block-container">
					<span class="promotions-item-block">
						<span class="promotions-item-title"><?=$arItem["NAME"]?></span>
						<span class="promotions-item-date">
							<?if(!$itemCompleted) {
								echo Loc::getMessage("PROMOTIONS_ITEM_RUNNING")." ".(!empty($arItem["DISPLAY_ACTIVE_TO"]) ? Loc::getMessage("PROMOTIONS_ITEM_UNTIL")." ".$arItem["DISPLAY_ACTIVE_TO"] : Loc::getMessage("PROMOTIONS_ITEM_ALWAYS"));
							} else {
								echo Loc::getMessage("PROMOTIONS_ITEM_COMPLETED")." ".$arItem["DISPLAY_ACTIVE_TO"];
							}?>
						</span>
					</span>
				</span>
				<span class="promotions-item-icons">
					<span class="promotions-item-icon">
						<?if(!empty($arItem["MARKER"])) {
							foreach($arItem["MARKER"] as $key => $arMarker) {
								if($key <= 2) {?>
									<span class="promotions-item-marker-container">
										<span class="promotions-item-marker<?=(!empty($arMarker['FONT_SIZE']) ? ' promotions-item-marker-'.$arMarker['FONT_SIZE'] : '')?>"<?=(!empty($arMarker["BACKGROUND_1"]) && !empty($arMarker["BACKGROUND_2"]) ? " style='background: ".$arMarker["BACKGROUND_2"]."; background: -webkit-linear-gradient(left, ".$arMarker["BACKGROUND_1"].", ".$arMarker["BACKGROUND_2"]."); background: -moz-linear-gradient(left, ".$arMarker["BACKGROUND_1"].", ".$arMarker["BACKGROUND_2"]."); background: -o-linear-gradient(left, ".$arMarker["BACKGROUND_1"].", ".$arMarker["BACKGROUND_2"]."); background: -ms-linear-gradient(left, ".$arMarker["BACKGROUND_1"].", ".$arMarker["BACKGROUND_2"]."); background: linear-gradient(to right, ".$arMarker["BACKGROUND_1"].", ".$arMarker["BACKGROUND_2"].");'" : (!empty($arMarker["BACKGROUND_1"]) && empty($arMarker["BACKGROUND_2"]) ? " style='background: ".$arMarker["BACKGROUND_1"].";'" : (empty($arMarker["BACKGROUND_1"]) && !empty($arMarker["BACKGROUND_2"]) ? " style='background: ".$arMarker["BACKGROUND_2"].";'" : "")))?>><?=(!empty($arMarker["ICON"]) ? "<i class='".$arMarker["ICON"]."'></i>" : "")?><span><?=$arMarker["NAME"]?></span></span>
									</span>
								<?} else {
									break;
								}
							}
							unset($key, $arMarker);
						}?>
					</span>
					<span class="promotions-item-icon">
						<?if(!$itemCompleted) {
							if(!empty($arItem["ACTIVE_TO"])) {
								$jsActiveTo = CUtil::PhpToJSObject(ParseDateTime($arItem['ACTIVE_TO'], FORMAT_DATETIME), false, true);
								if($arItem["SHOW_TIMER"] != false) {?>
									<span class="promotions-item-timer"><i class="icon-clock"></i><span data-entity="timer" data-active-to="<?=$jsActiveTo?>"></span></span>
								<?} else {
									$daysLeft = ceil((strtotime($arItem["ACTIVE_TO"]) - $currentDateTime) / 86400);					
									if($daysLeft > 1 && $daysLeft <= 3) {?>
										<span class="promotions-item-timer"><i class="icon-clock"></i><span><?=Loc::getMessage("PROMOTIONS_ITEM_DAYS_LEFT", array("#DAYS_COUNT#" => $daysLeft))?></span></span>
									<?} elseif($daysLeft == 1) {
										$hoursLeft = floor((strtotime($arItem["ACTIVE_TO"]) - $currentDateTime) / 3600);
										if($hoursLeft >= 3) {?>
											<span class="promotions-item-timer"><i class="icon-clock"></i><span><?=Loc::getMessage("PROMOTIONS_ITEM_DAY_LEFT", array("#DAYS_COUNT#" => $daysLeft))?></span></span>
										<?} else {?>
											<span class="promotions-item-timer"><i class="icon-clock"></i><span data-entity="timer" data-active-to="<?=$jsActiveTo?>"></span></span>
										<?}
									}
								}
								unset($jsActiveTo);
							}
						} else {?>
							<span class="promotions-item-timer"><i class="icon-clock"></i><span><?=Loc::getMessage("PROMOTIONS_ITEM_COMPLETED")?></span></span>
						<?}?>
					</span>
				</span>
			</a>			
		</div>
	<?}?>
	<!-- items-container -->
</div>
	
<?if($showLazyLoad) {?>
	<div class="promotions-more" data-entity="promotions-show-more-container">
		<button type="button" class="btn btn-more" data-use="show-more-<?=$navParams['NavNum']?>"><?=Loc::getMessage("PROMOTIONS_SHOW_MORE_ITEMS")?></button>
	</div>
<?}

if($showBottomPager) {?>
	<div class="promotions-pagination" data-pagination-num="<?=$navParams['NavNum']?>">
		<!-- pagination-container -->
		<?=$arResult["NAV_STRING"]?>
		<!-- pagination-container -->
	</div>
<?}

if(!empty($GLOBALS[$arParams["FILTER_NAME"]]))
	$arParams["GLOBAL_FILTER"] = $GLOBALS[$arParams["FILTER_NAME"]];

if(!isset($arParams["PARENT_NAME"]) && $parent = $this->getParent()) {
	$arParams["PARENT_NAME"] = $parent->getName();
	$arParams["PARENT_TEMPLATE_NAME"] = $parent->getTemplateName();
	$arParams["PARENT_TEMPLATE_PAGE"] = $parent->getTemplatePage();
}

$signer = new \Bitrix\Main\Security\Sign\Signer;
$signedTemplate = $signer->sign($templateName, "news.list");
$signedParams = $signer->sign(base64_encode(serialize($arParams)), "news.list");?>

<script type="text/javascript">	
	BX.message({
		PROMOTIONS_ITEM_COMPLETED: '<?=GetMessageJS("PROMOTIONS_ITEM_COMPLETED");?>',
		PROMOTIONS_LOADING: '<?=GetMessageJS("PROMOTIONS_LOADING");?>'
	});
	var <?=$obName?> = new JCNewsListPromoComponent({		
		siteId: '<?=CUtil::JSEscape(SITE_ID)?>',
		templatePath: '<?=CUtil::JSEscape($templateFolder)?>',
		navParams: <?=CUtil::PhpToJSObject($navParams)?>,
		lazyLoad: '<?=$showLazyLoad?>',		
		template: '<?=CUtil::JSEscape($signedTemplate)?>',
		parameters: '<?=CUtil::JSEscape($signedParams)?>',
		container: '<?=$containerName?>'
	});
</script>