<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

if(!empty($arParams["MAIN_TITLE"])) {?>
	<div class="h2"><?=$arParams["MAIN_TITLE"]?></div>
<?}
if(!empty($arResult["STORES"])) {?>
	<script type="text/javascript">
		BX.message({
			CSA_ITEM_24_HOURS: '<?=GetMessageJS("CSA_ITEM_24_HOURS")?>',
			CSA_ITEM_OFF: '<?=GetMessageJS("CSA_ITEM_OFF")?>',
			CSA_ITEM_BREAK: '<?=GetMessageJS("CSA_ITEM_BREAK")?>',
			CSA_LOADING: '<?=GetMessageJS("CSA_LOADING")?>',
			CSA_TEMPLATE_PATH: '<?=CUtil::JSEscape($templateFolder)?>'
		});
	</script>
	<div class="catalog-store-amount-items">
		<?foreach($arResult["STORES"] as $arItem) {
			$strMainID = $this->GetEditAreaId($arItem["ID"]);
			$strObName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);?>

			<div id="<?=$strMainID?>" class="catalog-store-amount-item" style="<?=($arParams['SHOW_EMPTY_STORE'] == 'N' && isset($arItem['REAL_AMOUNT']) && $arItem['REAL_AMOUNT'] <= 0 ? 'display: none;' : '')?>">
				<?if($arParams["SHOW_GENERAL_STORE_INFORMATION"] != "Y") {?>
					<div class="catalog-store-amount-item-col catalog-store-amount-item-col-image">
						<<?=(!empty($arItem["OBJECT"]) ? "a target='_blank' href='".$arItem["OBJECT"]["DETAIL_PAGE_URL"]."'" : "div")?> class="catalog-store-amount-item-image">
							<?if(!empty($arItem["OBJECT"]) && is_array($arItem["OBJECT"]["PREVIEW_PICTURE"])) {?>
								<img src="<?=$arItem['OBJECT']['PREVIEW_PICTURE']['SRC']?>" width="<?=$arItem['OBJECT']['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arItem['OBJECT']['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arItem['OBJECT']['NAME']?>" />
							<?} elseif(is_array($arItem["PREVIEW_PICTURE"])) {?>
								<img src="<?=$arItem['PREVIEW_PICTURE']['src']?>" width="<?=$arItem['PREVIEW_PICTURE']['width']?>" height="<?=$arItem['PREVIEW_PICTURE']['height']?>" alt="<?=$arItem['TITLE']?>" />
							<?} else {?>
								<img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo.png" width="222" height="222" alt="<?=$arItem['TITLE']?>" />
							<?}?>
						</<?=(!empty($arItem["OBJECT"]) ? "a" : "div")?>>
					</div>
				<?}?>
				<div class="catalog-store-amount-item-col catalog-store-amount-item-col-info">
					<<?=(!empty($arItem["OBJECT"]) ? "a target='_blank' href='".$arItem["OBJECT"]["DETAIL_PAGE_URL"]."'" : "div")?> class="catalog-store-amount-item-title"><?=($arParams["SHOW_GENERAL_STORE_INFORMATION"] == "Y" ? Loc::getMessage("CSA_TOTAL_BALANCE") : (!empty($arItem["OBJECT"]) ? $arItem["OBJECT"]["NAME"] : $arItem["TITLE"]))?></<?=(!empty($arItem["OBJECT"]) ? "a" : "div")?>>
					<?if(!empty($arItem["OBJECT"])) {
						if(!empty($arItem["OBJECT"]["ADDRESS"])) {?>
							<div class="catalog-store-amount-item-address"><i class="icon-map-marker"></i><span><?=$arItem["OBJECT"]["ADDRESS"]?></span></div>
						<?}?>
						<div class="catalog-store-amount-item-hours catalog-store-amount-item-hours-hidden"></div>
						<?$arJSParams = array(				
							"ITEM" => array(
								"TIMEZONE" => $arItem["OBJECT"]["TIMEZONE"],
								"WORKING_HOURS" => $arItem["OBJECT"]["WORKING_HOURS"]
							),
							"VISUAL" => array(
								"ID" => $strMainID
							)
						);?>
						<script type="text/javascript">							
							var <?=$strObName;?> = new JCCatalogStore(<?=CUtil::PhpToJSObject($arJSParams, false, true);?>);
						</script>
					<?} else {
						if(!empty($arItem["SCHEDULE"])) {?>
							<div class="catalog-store-amount-item-prop"><?=$arItem["SCHEDULE"]?></div>
						<?}
						if(!empty($arItem["PHONE"])) {?>
							<div class="catalog-store-amount-item-prop"><a href="tel:<?=preg_replace('/[^0-9+]/', '', $arItem['PHONE'])?>"><?=$arItem["PHONE"]?></a></div>
						<?}
						if(!empty($arItem["EMAIL"])) {?>
							<div class="catalog-store-amount-item-prop"><a href="mailto:<?=$arItem['EMAIL']?>"><?=$arItem["EMAIL"]?></a></div>
						<?}
						if(!empty($arItem["USER_FIELDS"]) && is_array($arItem["USER_FIELDS"])) {
							foreach($arItem["USER_FIELDS"] as $codeUserField => $userField) {
								if($codeUserField != "UF_OBJECT" && !empty($userField["CONTENT"])) {?>
									<div class="catalog-store-amount-item-prop"><?=$userField["TITLE"].": ".$userField["CONTENT"]?></div>
								<?}
							}
							unset($codeUserField, $userField);
						}
						if(!empty($arItem["DESCRIPTION"])) {?>
							<div class="catalog-store-amount-item-prop"><?=$arItem["DESCRIPTION"]?></div>
						<?}
					}?>
				</div>
				<div class="catalog-store-amount-item-col">
					<div<?=($arResult["IS_SKU"] ? " id='".$arResult["JS"]["ID"]."_".$arItem["ID"]."'" : "")?> class="catalog-store-amount-item-quantity<?=(isset($arItem['REAL_AMOUNT']) ? ($arItem['REAL_AMOUNT'] > 0 ? '' : ' catalog-store-amount-item-quantity-not-avl') : ($arItem['AMOUNT'] > 0 || $arItem['AMOUNT'] != Loc::getMessage("ABSENT") ? '' : ' catalog-store-amount-item-quantity-not-avl'))?>">
						<i class="icon-<?=(isset($arItem['REAL_AMOUNT']) ? ($arItem['REAL_AMOUNT'] > 0 ? 'ok' : 'close') : ($arItem['AMOUNT'] > 0 || $arItem['AMOUNT'] != Loc::getMessage("ABSENT") ? 'ok' : 'close'))?>-b catalog-store-amount-item-quantity-icon"></i>
						<span class="catalog-store-amount-item-quantity-val"><?=(isset($arItem['REAL_AMOUNT']) ? ($arItem["REAL_AMOUNT"] > 0 ? Loc::getMessage("CSA_AVAILABLE")." ".$arItem["AMOUNT"] : Loc::getMessage("CSA_NOT_AVAILABLE")) : ($arItem['AMOUNT'] > 0 || $arItem['AMOUNT'] != Loc::getMessage("ABSENT") ? Loc::getMessage("CSA_AVAILABLE")." ".$arItem["AMOUNT"] : Loc::getMessage("CSA_NOT_AVAILABLE")))?></span>
					</div>
				</div>
			</div>
		<?}
		unset($arItem);?>
	</div>
	
	<script type="text/javascript">
		BX.message({
			CSA_AVAILABLE: '<?=GetMessageJS("CSA_AVAILABLE")?>',
			CSA_NOT_AVAILABLE: '<?=GetMessageJS("CSA_NOT_AVAILABLE")?>'
		});
		<?if($arResult["IS_SKU"]) {?>
			var obStoreAmount = new JCCatalogStoreSKU(<?=CUtil::PhpToJSObject($arResult["JS"], false, true);?>);
		<?}?>
	</script>
<?}