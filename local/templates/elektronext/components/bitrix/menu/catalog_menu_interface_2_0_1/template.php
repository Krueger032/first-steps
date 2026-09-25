<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

global $arSettings;

$isSiteClosed = false;
if(COption::GetOptionString("main", "site_stopped") == "Y" && !$USER->CanDoOperation("edit_other_settings"))
	$isSiteClosed = true;

$obName = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $this->GetEditAreaId($this->randString()));
$containerName = 'catalog-menu-'.$obName;

if(!$isSiteClosed && !empty($arResult)) {?>
	<div class="catalog-menu" id="<?=$containerName?>" data-entity="dropdown-menu">
		<div class="hidden-md hidden-lg catalog-menu-title"><div class="catalog-menu-title-icon"><i class="icon-back"></i></div><div class="catalog-menu-title-text"><?=GetMessage("BM_CATALOG_SHORT")?></div></div>
		<ul>			
			<?$previousLevel = 0;					
			foreach($arResult as $arItem) {			
				if($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel)
					echo str_repeat("</ul></div></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));
				if($arItem["IS_PARENT"]) {?>
					<li<?=($arItem["SELECTED"] ? " class='active'" : "")?> data-entity="dropdown">
						<a href="<?=$arItem['LINK']?>">
							<?if(!empty($arItem["PARAMS"]["ICON"])) {?>
								<span class="catalog-menu-icon"><i class="<?=$arItem['PARAMS']['ICON']?>"></i></span>
							<?} elseif(is_array($arItem["PARAMS"]["PICTURE"])) {?>
								<span class="catalog-menu-pic">
									<img src="<?=$arItem['PARAMS']['PICTURE']['SRC']?>" width="<?=$arItem['PARAMS']['PICTURE']['WIDTH']?>" height="<?=$arItem['PARAMS']['PICTURE']['HEIGHT']?>" alt="<?=$arItem['PARAMS']['PICTURE']['ALT']?>" title="<?=$arItem['PARAMS']['PICTURE']['TITLE']?>" />
								</span>
							<?}?>
							<span class="catalog-menu-text"><?=$arItem["TEXT"]?></span>
							<span class="catalog-menu-arrow"><i class="icon-arrow-right"></i></span>
						</a>
						<div class="catalog-menu-dropdown-menu" data-entity="dropdown-menu">
							<ul>						
				<?} else {?>
					<li<?=$arItem["SELECTED"] ? " class='active'" : ""?>>
						<a href="<?=$arItem['LINK']?>">
							<?if(!empty($arItem["PARAMS"]["ICON"])) {?>
								<span class="catalog-menu-icon"><i class="<?=$arItem['PARAMS']['ICON']?>"></i></span>
							<?} elseif(is_array($arItem["PARAMS"]["PICTURE"])) {?>
								<span class="catalog-menu-pic">
									<img src="<?=$arItem['PARAMS']['PICTURE']['SRC']?>" width="<?=$arItem['PARAMS']['PICTURE']['WIDTH']?>" height="<?=$arItem['PARAMS']['PICTURE']['HEIGHT']?>" alt="<?=$arItem['PARAMS']['PICTURE']['ALT']?>" title="<?=$arItem['PARAMS']['PICTURE']['TITLE']?>" />
								</span>
							<?}?>
							<span class="catalog-menu-text"><?=$arItem["TEXT"]?></span>
						</a>
					</li>
				<?}
				$previousLevel = $arItem["DEPTH_LEVEL"];						
			}
			if($previousLevel > 1)
				echo str_repeat("</ul></div></li>", ($previousLevel - 1));?>
		</ul>
	</div>
	<script type="text/javascript">
		BX.message({
			MAIN_MENU: '<?=GetMessageJS("BM_MAIN_MENU")?>',
			CATALOG_FULL: '<?=GetMessageJS("BM_CATALOG_FULL")?>'
		});
		var <?=$obName?> = new JCCatalogMenu({
			container: '<?=$containerName?>'
		});
	</script>	
<?}?>