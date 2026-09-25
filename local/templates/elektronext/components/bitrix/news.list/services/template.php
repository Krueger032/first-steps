<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;

$elementEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
$elementDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE');
$elementDeleteParams = array('CONFIRM' => GetMessage('SERVICES_ITEM_DELETE_CONFIRM'));

$obName = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $this->GetEditAreaId($this->randString()));
$containerName = 'services-'.$obName;?>

<div class="row services" id="<?=$containerName?>">	
	<?foreach($arResult["ITEMS"] as $arItem) {
		$this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], $elementEdit);
		$this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], $elementDelete, $elementDeleteParams);?><!--
		--><div class="col-xs-12 col-md-3" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
			<a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="services-item">
				<?if(!empty($arItem["PREVIEW_PICTURE"])) {?>
					<div class="services-item__pic">
						<img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" width="<?=$arItem['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arItem['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arItem['NAME']?>" />
					</div>
				<?} elseif(!empty($arItem["DISPLAY_PROPERTIES"]["ICON"])) {?>
					<div class="services-item__icon-wrapper">
						<div class="services-item__icon">
							<i class="fa <?=$arItem['DISPLAY_PROPERTIES']['ICON']['VALUE']?>"></i>
						</div>
					</div>
				<?} else {?>
					<div class="services-item__pic"></div>
				<?}?>
				<div class="services-item__caption">
					<div class="services-item__title"><?=$arItem["NAME"]?></div>
					<?=(!empty($arItem["DISPLAY_PROPERTIES"]["SHORT_DESC"]) ? "<div class='services-item__text'>".$arItem["DISPLAY_PROPERTIES"]["SHORT_DESC"]["~VALUE"]["TEXT"]."</div>" : "");?>
				</div>
			</a>
		</div><!--
	--><?}
	if($arParams["DISPLAY_BOTTOM_PAGER"]) {
		if(!empty($arResult["NAV_STRING"])) {?>
			<div class="col-xs-12">
				<?=$arResult["NAV_STRING"];?>
			</div>
		<?}
	}?>
</div>

<script type="text/javascript">
	var <?=$obName?> = new JCNewsListServices({
		container: '<?=$containerName?>'
	});
</script>