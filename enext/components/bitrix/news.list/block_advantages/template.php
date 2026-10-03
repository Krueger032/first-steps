<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;

$elementEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
$elementDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE');
$elementDeleteParams = array('CONFIRM' => GetMessage('ADV_ITEM_DELETE_CONFIRM'));?>
	
<div class="hidden-xs hidden-sm advantages-wrapper">
	<div class="container-fluid">
		<div class="row advantages">					
			<?foreach($arResult["ITEMS"] as $arItem) {
				$this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], $elementEdit);
				$this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], $elementDelete, $elementDeleteParams);?><!--
				--><div class="col-xs-12 col-md-3" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
					<div class="adv-item__caption">
						<?if(!empty($arItem["DISPLAY_PROPERTIES"]["ICON"])) {?>
							<div class="adv-item__icon">
								<i class="fa <?=$arItem['DISPLAY_PROPERTIES']['ICON']['VALUE']?>"></i>
							</div>
						<?}?>
						<div class="adv-item__text"><?=$arItem["NAME"]?></div>
					</div>
				</div><!--				
			--><?}?>
		</div>
	</div>
</div>