<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;

$elementEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
$elementDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE');
$elementDeleteParams = array('CONFIRM' => GetMessage('JOIN_US_ITEM_DELETE_CONFIRM'));?>

<p class="footer-phones">
    <a href="tel:+74951288690">8 (495) 128-86-90</a><br />
    <a href="tel:+79852883457">8 (985) 288-34-57</a>
</p>

<!--<ul class="join-us">-->
<ul class="footer-socials">
	<?foreach($arResult["ITEMS"] as $arItem) {?>
		<?$this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], $elementEdit);
		$this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], $elementDelete, $elementDeleteParams);?>
		<li id="<?=$this->GetEditAreaId($arItem['ID'])?>">
<!--			<a rel="nofollow" title="--><?//=$arItem['NAME']?><!--" href="--><?//=(!empty($arItem['DISPLAY_PROPERTIES']['URL']) ? $arItem['DISPLAY_PROPERTIES']['URL']['VALUE'] : 'javascript:void(0)');?><!--" target="_blank"--><?//=(!empty($arItem["DISPLAY_PROPERTIES"]["BACKGR_HOV"]) ? " style='background:#".$arItem["DISPLAY_PROPERTIES"]["BACKGR_HOV"]["VALUE"].";'" : "");?><!-- >-->
<!--                <i class="fa--><?//=(!empty($arItem['DISPLAY_PROPERTIES']['ICON']) ? ' '.$arItem['DISPLAY_PROPERTIES']['ICON']['VALUE'] : '');?><!--"></i>-->
<!--            </a>-->
			<a rel="nofollow" title="<?=$arItem['NAME']?>" href="<?=(!empty($arItem['DISPLAY_PROPERTIES']['URL']) ? $arItem['DISPLAY_PROPERTIES']['URL']['VALUE'] : 'javascript:void(0)');?>" target="_blank">
				<img src="<?=$arItem["PREVIEW_PICTURE"]['SRC']?>" alt="social" title="social">
            </a>
		</li>
	<?}?>
</ul>