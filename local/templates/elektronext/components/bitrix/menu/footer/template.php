<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(empty($arResult))
	return;?>

<ul class="footer-menu">
	<?foreach($arResult as $itemIdex => $arItem):?>
		<li>
			<a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?><i class="icon-arrow-right"></i></a>
		</li>
	<?endforeach;?>
</ul>