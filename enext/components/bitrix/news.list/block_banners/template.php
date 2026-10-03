<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;

$elementEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
$elementDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE');
$elementDeleteParams = array('CONFIRM' => GetMessage('ADV_ITEM_DELETE_CONFIRM'));?>

<div class="banners-wrapper">	
	<?$width = 0;
	foreach($arResult["ITEMS"] as $arItem) {
		$this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], $elementEdit);
		$this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], $elementDelete, $elementDeleteParams);		
		
		if(!isset($arItem["DISPLAY_PROPERTIES"]["WIDTH"]))
			continue;		
		
		if($width == 0)
			echo "<div class=\"banners-items\">";?>		
		
		<a class="banners-item-container" id="<?=$this->GetEditAreaId($arItem['ID'])?>" href="<?=(!empty($arItem['DISPLAY_PROPERTIES']['URL']) ? $arItem['DISPLAY_PROPERTIES']['URL']['VALUE'] : 'javascript:void(0)')?>"<?=(!empty($arItem["DISPLAY_PROPERTIES"]["WIDTH"]) ? " style='width:".$arItem["DISPLAY_PROPERTIES"]["WIDTH"]["VALUE"]."%;'" : "")?>>			
			<span class="banners-item">
				<span class="banners-item-pic"<?=(!empty($arItem["DISPLAY_PROPERTIES"]["BACKGROUND"]["VALUE"]) ? " style='background-color:".$arItem["DISPLAY_PROPERTIES"]["BACKGROUND"]["VALUE"].";'" : "")?>>
					<?if(is_array($arItem["PREVIEW_PICTURE"])) {?>
						<img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" width="<?=$arItem['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arItem['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arItem['NAME']?>" />
					<?}?>
				</span>
				<span class="banners-item-block-container">
					<span class="banners-item-block">
						<?if(!empty($arItem["DISPLAY_PROPERTIES"]["ICON"]["VALUE"])) {?>
							<span class="banners-item-icon"><i class="<?=$arItem['DISPLAY_PROPERTIES']['ICON']['VALUE']?>"></i></span>
						<?}?>
						<span class="banners-item-title"><?=$arItem["NAME"]?></span>					
						<?if(!empty($arItem["PREVIEW_TEXT"])) {?>
							<span class="banners-item-text"><?=$arItem["PREVIEW_TEXT"]?></span>
						<?}?>
					</span>				
				</span>
			</span>
		</a>
		
		<?$width += $arItem["DISPLAY_PROPERTIES"]["WIDTH"]["VALUE"];
		if($width == 100) {
			echo "</div>";
			$width = 0;
		}
	}?>
</div>