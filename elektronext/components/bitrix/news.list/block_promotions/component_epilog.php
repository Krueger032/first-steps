<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if(!isset($arResult["CACHE_ITEMS"]) || empty($arResult["CACHE_ITEMS"]))
	return;

use Bitrix\Main\Loader,
	Bitrix\Main\Localization\Loc;

if(!Loader::includeModule('iblock'))
	return;

Loc::loadMessages(__FILE__);

$currentDateTime = time() + CTimeZone::GetOffset();

//CACHE_ITEMS//
$elementEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
$elementDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE');
$elementDeleteParams = array('CONFIRM' => Loc::getMessage('PROMOTIONS_ITEM_DELETE_CONFIRM'));

$obName = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $this->GetEditAreaId($this->randString()));
$containerName = 'promotions-'.$obName;?>

<div class="promotions-wrapper">
	<div class="promotions-items-container">
		<div class="promotions-items" id="<?=$containerName?>">
			<?foreach($arResult["CACHE_ITEMS"] as $arItem) {
				$this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], $elementEdit);
				$this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], $elementDelete, $elementDeleteParams);

				$strMainID = $this->GetEditAreaId($arItem["ID"]);		
				$strObName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);?>
				
				<a class="promotions-item" id="<?=$strMainID?>" href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>">
					<span class="promotions-item-pic">
						<?if(is_array($arItem["PREVIEW_PICTURE"])) {?>
							<img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" width="<?=$arItem['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arItem['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arItem['NAME']?>" />
						<?}?>
					</span>
					<span class="promotions-item-block-container">
						<span class="promotions-item-block">
							<span class="promotions-item-title"><?=$arItem["NAME"]?></span>
							<span class="promotions-item-date">
								<?=Loc::getMessage("PROMOTIONS_ITEM_RUNNING")." ".(!empty($arItem["DISPLAY_ACTIVE_TO"]) ? Loc::getMessage("PROMOTIONS_ITEM_UNTIL")." ".$arItem["DISPLAY_ACTIVE_TO"] : Loc::getMessage("PROMOTIONS_ITEM_ALWAYS"));?>
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
							<?if(!empty($arItem["ACTIVE_TO"])) {
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
							}?>
						</span>
					</span>
				</a>
			<?}?>
		</div>
	</div>
</div>

<script type="text/javascript">	
	BX.message({		
		PROMOTIONS_ITEM_COMPLETED: "<?=GetMessageJS('PROMOTIONS_ITEM_COMPLETED');?>"
	});
	var <?=$obName?> = new JCNewsListPromoComponent({
		container: '<?=$containerName?>',
		itemsCount: '<?=count($arResult["CACHE_ITEMS"])?>'
	});
</script>