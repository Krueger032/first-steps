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
                    <a href="<?=$arItem['LINK']?>">
                        <? if($arItem["TEXT"] === "Акции"): ?>
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M13.838 0.94418C14.054 0.728186 14.054 0.377989 13.838 0.161995C13.622 -0.0539984 13.2718 -0.0539984 13.0558 0.161995L0.749646 12.4682C0.533656 12.6841 0.533656 13.0344 0.749646 13.2504C0.96564 13.4663 1.31584 13.4663 1.53184 13.2504L13.838 0.94418ZM3.45679 5.98025C4.755 5.98025 5.80741 4.92784 5.80741 3.62963C5.80741 2.33142 4.755 1.27901 3.45679 1.27901C2.15858 1.27901 1.10617 2.33142 1.10617 3.62963C1.10617 4.92784 2.15858 5.98025 3.45679 5.98025ZM3.45679 7.08642C5.36592 7.08642 6.91358 5.53877 6.91358 3.62963C6.91358 1.7205 5.36592 0.17284 3.45679 0.17284C1.54766 0.17284 0 1.7205 0 3.62963C0 5.53877 1.54766 7.08642 3.45679 7.08642ZM10.3704 12.8938C11.6686 12.8938 12.721 11.8414 12.721 10.5432C12.721 9.24498 11.6686 8.19259 10.3704 8.19259C9.07214 8.19259 8.01975 9.24498 8.01975 10.5432C8.01975 11.8414 9.07214 12.8938 10.3704 12.8938ZM10.3704 14C12.2795 14 13.8272 12.4523 13.8272 10.5432C13.8272 8.63409 12.2795 7.08642 10.3704 7.08642C8.46125 7.08642 6.91358 8.63409 6.91358 10.5432C6.91358 12.4523 8.46125 14 10.3704 14Z" fill="#FF1717"/>
                            </svg>
						<? endif; ?>
                        <?=$arItem["TEXT"]?>
                    </a>
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