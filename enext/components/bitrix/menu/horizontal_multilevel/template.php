<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(empty($arResult))
	return;

$obName = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $this->GetEditAreaId($this->randString()));
$containerName = 'horizontal-multilevel-menu-'.$obName;?>

<ul class="horizontal-multilevel-menu" id="<?=$containerName?>">		
	<li<?=($APPLICATION->GetCurPage(true) == SITE_DIR."index.php" ? " class='active'" : "")?>><a href="<?=SITE_DIR?>"><?=GetMessage("BM_MAIN_ITEM")?></a></li>
	<?$previousLevel = 0;					
	foreach($arResult as $arItem) {
		if($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel) {
			echo str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));
		}
		if($arItem["IS_PARENT"]) {?>
			<li<?=($arItem["SELECTED"] ? " class='active'" : "")?> data-entity="dropdown">
				<a href="<?=$arItem['LINK']?>"><?=$arItem["TEXT"]?> <i class="icon-arrow-<?=($arItem['DEPTH_LEVEL'] == 1 ? 'down' : 'right');?>"></i></a>
				<ul class="horizontal-multilevel-dropdown-menu" data-entity="dropdown-menu">
		<?} else {?>
			<li<?=($arItem["SELECTED"] ? " class='active'" : "")?>>
				<a href="<?=$arItem['LINK']?>"><?=$arItem["TEXT"]?></a>
			</li>
		<?}
		$previousLevel = $arItem["DEPTH_LEVEL"];						
	}
	if($previousLevel > 1) {
		echo str_repeat("</ul></li>", ($previousLevel - 1));
	}?>
</ul>

<script type="text/javascript">		
	var <?=$obName?> = new JCHorizontalMultilevelMenu({
		container: '<?=$containerName?>'
	});
</script>