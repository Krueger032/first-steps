<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

global $arSettings;

$isSiteClosed = false;
if(COption::GetOptionString("main", "site_stopped") == "Y" && !$USER->CanDoOperation("edit_other_settings"))
	$isSiteClosed = true;

$obName = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $this->GetEditAreaId($this->randString()));
$containerName = 'slide-menu-'.$obName;

if(!$isSiteClosed && !empty($arResult)) {?>
	<ul class="slide-menu scrollbar-inner" id="<?=$containerName?>">
		<?$previousLevel = 0;					
		foreach($arResult as $arItem) {			
			if($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel)
				echo str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));
			if($arItem["IS_PARENT"]) {?>
				<li<?=$arItem["SELECTED"] ? " class='active'" : ""?> data-entity="dropdown">
					<a href="<?=$arItem['LINK']?>">
						<?if(!empty($arItem["PARAMS"]["ICON"])) {?>
							<span class="slide-menu-icon">
								<i class="<?=$arItem['PARAMS']['ICON']?>"></i>
							</span>
						<?} elseif(is_array($arItem["PARAMS"]["PICTURE"])) {?>
							<span class="slide-menu-pic">
								<img src="<?=$arItem['PARAMS']['PICTURE']['SRC']?>" width="<?=$arItem['PARAMS']['PICTURE']['WIDTH']?>" height="<?=$arItem['PARAMS']['PICTURE']['HEIGHT']?>" alt="<?=$arItem['PARAMS']['PICTURE']['ALT']?>" title="<?=$arItem['PARAMS']['PICTURE']['TITLE']?>" />
							</span>
						<?}?>
						<span class="slide-menu-text"><?=$arItem["TEXT"]?></span>
						<span class="slide-menu-arrow"><i class="icon-arrow-right"></i></span>
					</a>
					<ul class="slide-menu-dropdown-menu scrollbar-inner" data-entity="dropdown-menu">
						<li data-entity="title">
							<i class="icon-arrow-left slide-menu-back"></i>
							<span class="slide-menu-title"><?=$arItem["TEXT"]?></span>
							<i class="icon-close slide-menu-close"></i>
						</li>
			<?} else {?>
				<li<?=$arItem["SELECTED"] ? " class='active'" : ""?>>
					<a href="<?=$arItem['LINK']?>">
						<?if(!empty($arItem["PARAMS"]["ICON"])) {?>
							<span class="slide-menu-icon">
								<i class="<?=$arItem['PARAMS']['ICON']?>"></i>
							</span>
						<?} elseif(is_array($arItem["PARAMS"]["PICTURE"])) {?>
							<span class="slide-menu-pic">
								<img src="<?=$arItem['PARAMS']['PICTURE']['SRC']?>" width="<?=$arItem['PARAMS']['PICTURE']['WIDTH']?>" height="<?=$arItem['PARAMS']['PICTURE']['HEIGHT']?>" alt="<?=$arItem['PARAMS']['PICTURE']['ALT']?>" title="<?=$arItem['PARAMS']['PICTURE']['TITLE']?>" />
							</span>
						<?}?>
						<span class="slide-menu-text"><?=$arItem["TEXT"]?></span>
						<?if($arItem["PARAMS"]["ELEMENT_CNT"] > 0) {?>
							<span class="slide-menu-count"><?=$arItem["PARAMS"]["ELEMENT_CNT"]?></span>
						<?}?>
					</a>
				</li>
			<?}
			$previousLevel = $arItem["DEPTH_LEVEL"];						
		}
		if($previousLevel > 1)
			echo str_repeat("</ul></li>", ($previousLevel - 1));?>
	</ul>
	<script type="text/javascript">
		BX.message({
			MAIN_MENU: '<?=GetMessageJS("BM_MAIN_MENU")?>'
		});
		var <?=$obName?> = new JCSlideMenu({
			setActive: <?=($arSettings["CATALOG_MENU_OPEN"]["VALUE"] == "ACTIVE_LEVEL" ? "true" : "false")?>,
			openLast: <?=($arSettings["CATALOG_MENU_NAV"]["VALUE"] == "LAST_ITEM" ? "true" : "false")?>,
			container: '<?=$containerName?>'
		});
	</script>
<?}?>